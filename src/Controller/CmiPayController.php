<?php

namespace App\Controller;

use App\Entity\Contributor;
use App\Repository\CompanyRepository;
use App\Repository\ContributorRepository;
use CmiPayBundle\CmiPay;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class CmiPayController
 * @package App\Controller
 */
class CmiPayController extends \CmiPayBundle\Controller\CmiPayController
{
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var ContributorRepository
     */
    private $contributorRepository;
    /**
     * @var CompanyRepository
     */
    private $companyRepository;
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * CmiPayController constructor.
     *
     * @param SessionInterface $session
     * @param ContributorRepository $contributorRepository
     * @param CompanyRepository $companyRepository
     * @param ObjectManager $manager
     */
    public function __construct(
        SessionInterface $session,
        ContributorRepository $contributorRepository,
        CompanyRepository $companyRepository,
        ObjectManager $manager
    )
    {
        $this->session = $session;
        $this->contributorRepository = $contributorRepository;
        $this->companyRepository = $companyRepository;
        $this->manager = $manager;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function requestPay(Request $request)
    {
        /** @var Contributor $contributor */
        $contributor = $this->session->get('contributor');
        $amountDebited = $this->session->get('amount_debited');
        $params = new CmiPay();
        // Setup new payment parameters
        $okUrl = $this->generateUrl('cmi_pay_okFail', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $shopUrl = $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        $failUrl = $this->generateUrl('cmi_pay_okFail', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $callbackUrl = $this->generateUrl('cmi_pay_callback', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $rnd = microtime();
        $params->setGatewayurl('https://testpayment.cmi.co.ma/fim/est3Dgate')
            ->setclientid('600001712')
            //->setTel('0612345678')
            ->setEmail($contributor->getEmail())
            ->setBillToName($this->clean($contributor->getFullName()))
            /*->setBillToCompany('BillToCompany')
            ->setBillToStreet1('BillToStreet1')
            ->setBillToStateProv('BillToStateProv')
            ->setBillToPostalCode('BillToPostalCode')
            ->setBillToCity('BillToCity')*/
            ->setBillToCountry('MA')
            ->setOid($contributor->getId())
            ->setCurrency('504')
            ->setAmount($amountDebited)
            ->setOkUrl($okUrl)
            ->setCallbackUrl($callbackUrl)
            ->setFailUrl($failUrl)
            ->setShopurl($shopUrl)
            ->setEncoding('UTF-8')
            ->setStoretype('3D_PAY_HOSTING')
            ->setHashAlgorithm('ver3')
            ->setTranType('PreAuth')
            ->setRefreshtime('5')
            ->setLang('fr')
            ->setRnd($rnd);
        $data = $this->convertData($params);
        $hash = $this->hashValue($data);
        $data['HASH'] = $hash;
        $data = $this->unsetData($data);
        return $this->render('bundles/CmiPay/payrequest.html.twig', [
            'data' => $data,
            'url' => $params->getGatewayurl()
        ]);
    }

    /**
     * @param $data
     * @return string
     */
    public function hashValue($data)
    {
        $params = new CmiPay();
        $params->setSecretKey('JMpmoiDvCu7W');
        $storeKey = $params->getSecretKey();
        $data = $this->unsetData($data);
        $postParams = array();
        foreach ($data as $key => $value) {
            array_push($postParams, $key);
        }
        natcasesort($postParams);

        $hashval = "";
        foreach ($postParams as $param) {
            $paramValue = trim(html_entity_decode(preg_replace("/\n$/", "", $data[$param]), ENT_QUOTES, 'UTF-8'));
            $escapedParamValue = str_replace("|", "\\|", str_replace("\\", "\\\\", $paramValue));
            $escapedParamValue = preg_replace('/document(.)/i', 'document.', $escapedParamValue);

            $lowerParam = strtolower($param);
            if ($lowerParam != "hash" && $lowerParam != "encoding") {
                $hashval = $hashval . $escapedParamValue . "|";
            }
        }


        $escapedStoreKey = str_replace("|", "\\|", str_replace("\\", "\\\\", $storeKey));
        $hashval = $hashval . $escapedStoreKey;

        $calculatedHashValue = hash('sha512', $hashval);
        $hash = base64_encode(pack('H*', $calculatedHashValue));

        return $hash;
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function okFail(Request $request)
    {
        $postData = $request->request->all();
        $type = 'danger';
        $response = null;
        /** @var Contributor $contributor */
        $contributor = $this->session->get('contributor');
        $contributorId = (int)$postData['oid'];
        if (!$contributor) {
            $contributor = $this->contributorRepository->find($contributorId);
        }

        if ($contributor) {
            if ($postData) {
                $actualHash = $this->hashValue($postData);
                $retrievedHash = $postData["HASH"];
                $this->session->set("cmi_response_data", $postData);
                $this->session->set('contributor', $contributor);
                if ($retrievedHash == $actualHash && $postData["ProcReturnCode"] == "00") {
                    $contributor->setStatus($contributor->getConfirmedStatus());
                    $this->session->set("cmi_success_pay", true);
                    $this->manager->flush();
                    return $this->redirectToRoute('cmi_success_pay');
                } else {
                    $contributor->setStatus($contributor->getCancelledStatus());
                    //$response = "Security Alert. The digital signature is not valid";
                    $this->session->set("cmi_error_pay", true);
                    $this->session->set("companyName", $contributor->getCompany()->getApprovedCompany()->getName());
                    $this->manager->flush();
                    return $this->redirectToRoute('cmi_error_pay');
                }
            } else {
                $response = "No Data POST";
            }

            $this->addFlash(
                $type,
                $response
            );

            $company = $contributor->getCompany();
            $id = $company->getId();
            $slug = $company->getSlug();
            $this->session->remove('contributor');
            return $this->redirectToRoute('company_show', [
                'id' => $id,
                'slug' => $slug
            ]);
        } else {
            $this->addFlash(
                $type,
                "Cannot find contribution by this id:" . $contributorId
            );
        }
        $this->session->remove('contributor');
        return $this->redirectToRoute('home');
    }

    private function clean($string) {
        $string = htmlentities($string);
        return preg_replace("/&([a-z])[a-z]+;/i", "$1", $string);
    }

}