<?php

namespace App\Controller;

use App\Entity\ApprovedCompany;
use App\Entity\Contributor;
use App\Form\ContributorType;
use App\Helper\DataHelper;
use App\Repository\ApprovedCompanyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use App\Repository\CompanyRepository;
use EgioDigital\CMSBundle\Repository\PagePublishedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ContributorController
 * @package App\Controller
 */
class ContributorController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $manager;
    /**
     * @var CompanyRepository
     */
    private $companyRepository;
    /**
     * @var string
     */
    private $translationDomain = "company";
    /**
     * @var ApprovedCompanyRepository
     */
    private $approvedCompanyRepository;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var DataHelper
     */
    private $dataHelper;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var PagePublishedRepository
     */
    private $pagePublishedRepository;

    /**
     * CompanyController constructor.
     *
     * @param ObjectManager $objectManager
     * @param ApprovedCompanyRepository $approvedCompanyRepository
     * @param CompanyRepository $companyRepository
     * @param TranslatorInterface $translator
     * @param DataHelper $dataHelper
     * @param SessionInterface $session
     * @param PagePublishedRepository $pagePublishedRepository
     */
    public function __construct(
        ObjectManager $objectManager,
        ApprovedCompanyRepository $approvedCompanyRepository,
        CompanyRepository $companyRepository,
        TranslatorInterface $translator,
        DataHelper $dataHelper,
        SessionInterface $session,
        PagePublishedRepository $pagePublishedRepository
    ) {
        $this->manager = $objectManager;
        $this->approvedCompanyRepository = $approvedCompanyRepository;
        $this->companyRepository = $companyRepository;
        $this->translator = $translator;
        $this->dataHelper = $dataHelper;
        $this->session = $session;
        $this->pagePublishedRepository = $pagePublishedRepository;
    }

    /**
     * @Route("{_locale}/crowdfunding-appel-aux-dons/{id}-{slug}/contribute", name="company_contribute")
     * @Route("/crowdfunding-appel-aux-dons/{id}-{slug}/contribute", name="default_company_contribute")
     * @param ApprovedCompany $approvedCompany
     * @param Request $request
     * @return Response
     */
    public function contribute(ApprovedCompany $approvedCompany, Request $request)
    {
        if ($approvedCompany->getIsClosed()) {
            $message = $this->translator->trans("This campaign is closed", [], $this->translationDomain);
            $this->addFlash("danger", $message);
            return $this->redirectToRoute("company_show", [
                'id' => $approvedCompany->getCompany()->getId(),
                'slug' => $approvedCompany->getSlug()
            ]);
        }
        $contributor = new Contributor();
        $contributor->setContributionAmount(100);
        $form = $this->createForm(ContributorType::class, $contributor);
        $form->handleRequest($request);

        $sgvSlug = 'cgv';
        if ($request->getLocale() == "en") {
            $sgvSlug .= '-en';
        }

        $cgvPage = $this->pagePublishedRepository->findOneBy(['lang' => $request->getLocale(), 'slug' => $sgvSlug]);

        $cgvUrl = $cgvPage ? $this->generateUrl('cmsbundle_page_show', ["slug" => $cgvPage->getSlug()]) : '#';

        if ($form->isSubmitted() && $form->isValid()) {
            $cgv = $request->get("cgv", false);
            if ($cgv == "on") {
                $amountDebited = $request->get("amount_debited");
                if ($amountDebited) {
                    $contributor->setCompany($approvedCompany->getCompany())
                        ->setStatus($contributor->getPendingStatus());
                    $this->manager->persist($contributor);
                    $this->manager->flush();
                    $this->session->set('contributor', $contributor);
                    $this->session->set('amount_debited', $amountDebited);
                    return $this->forward('App\Controller\CmiPayController::requestPay', []);
                } else {
                    $this->addFlash(
                        'danger',
                        $this->translator->trans("Amount debited not found", [], $this->translationDomain)
                    );
                }
            } else {
                $this->addFlash(
                    'danger',
                    $this->translator->trans("Please accept the general sales conditions", [], $this->translationDomain)
                );
            }
        }

        return $this->render('company/contribute.html.twig', [
            'company' => $approvedCompany,
            'form' => $form->createView(),
            'cgvUrl' => $cgvUrl
        ]);
    }

    /**
     * @Route("{_locale}/cmi/success/pay", name="cmi_success_pay")
     * @Route("/cmi/success/pay", name="cmi_success_pay_default")
     * @return Response
     */
    public function cmiSuccessPay()
    {
        $successPay = $this->session->get('cmi_success_pay');
        if ($successPay) {
            /** @var Contributor $contributor */
            $contributor = $this->session->get('contributor');
            $approvedCompany = null;
            $cmiResponseData = $this->session->get('cmi_response_data');
            if ($contributor) {
                $company = $contributor->getCompany();
                if ($company) {
                    $approvedCompany = $company->getApprovedCompany();
                }
            }
            $this->session->remove('cmi_success_pay');
            return $this->render('company/cmi_success_pay.html.twig', [
                'company' => $approvedCompany,
                'cmiData' => $cmiResponseData,
            ]);
        }
        return $this->redirectToRoute("home");
    }

    /**
     * @Route("{_locale}/cmi/error/pay", name="cmi_error_pay")
     * @Route("/cmi/error/pay", name="cmi_error_pay_default")
     * @return Response
     */
    public function cmiErrorPay()
    {
        $errorPay = $this->session->get('cmi_error_pay');
        if ($errorPay) {
            /** @var Contributor $contributor */
            $contributor = $this->session->get('contributor');
            $approvedCompany = null;
            $cmiResponseData = $this->session->get('cmi_response_data');
            if ($contributor) {
                $company = $contributor->getCompany();
                if ($company) {
                    $approvedCompany = $company->getApprovedCompany();
                }
            }
            $this->session->remove('cmi_error_pay');
            return $this->render('company/cmi_error_pay.html.twig', [
                'companyName' => $this->session->get('companyName'),
                'contributor' => $contributor,
                'cmiData' => $cmiResponseData,
            ]);
        }
        return $this->redirectToRoute("home");
    }

}
