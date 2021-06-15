<?php

namespace App\Controller;

use App\Entity\ApprovedCompany;
use App\Entity\CompanyComment;
use App\Entity\CompanyCommentResponse;
use App\Entity\CompanyLike;
use App\Form\CompanyCommentType;
use App\Notification\CompanyEntrepreneurNotification;
use App\Repository\CityRepository;
use App\Repository\ApprovedCompanyRepository;
use App\Repository\CompanyLikeRepository;
use App\Repository\DomainRepository;
use App\Repository\HeaderRepository;
use Doctrine\Common\Persistence\ObjectManager;
use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CompanyController
 * @package App\Controller
 */
class CompanyController extends AbstractController
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
     * @var HeaderRepository
     */
    private $headerRepository;
    /**
     * @var DomainRepository
     */
    private $domainRepository;
    /**
     * @var ApprovedCompanyRepository
     */
    private $approvedCompanyRepository;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var CompanyLikeRepository
     */
    private $companyLikeRepository;
    /**
     * @var CityRepository
     */
    private $cityRepository;
    /**
     * @var CompanyEntrepreneurNotification
     */
    private $notificator;

    /**
     * CompanyController constructor.
     *
     * @param ObjectManager $objectManager
     * @param HeaderRepository $headerRepository
     * @param DomainRepository $domainRepository
     * @param ApprovedCompanyRepository $approvedCompanyRepository
     * @param CompanyRepository $companyRepository
     * @param TranslatorInterface $translator
     * @param CompanyLikeRepository $companyLikeRepository
     * @param CityRepository $cityRepository
     * @param CompanyEntrepreneurNotification $notificator
     */
    public function __construct(
        ObjectManager $objectManager,
        HeaderRepository $headerRepository,
        DomainRepository $domainRepository,
        ApprovedCompanyRepository $approvedCompanyRepository,
        CompanyRepository $companyRepository,
        TranslatorInterface $translator,
        CompanyLikeRepository $companyLikeRepository,
        CityRepository $cityRepository,
        CompanyEntrepreneurNotification $notificator
    ) {
        $this->manager = $objectManager;
        $this->headerRepository = $headerRepository;
        $this->domainRepository = $domainRepository;
        $this->approvedCompanyRepository = $approvedCompanyRepository;
        $this->companyRepository = $companyRepository;
        $this->translator = $translator;
        $this->companyLikeRepository = $companyLikeRepository;
        $this->cityRepository = $cityRepository;
        $this->notificator = $notificator;
    }

    /**
     * @Route("{_locale}/startuper/submit-project", name="submit_project_index")
     * @Route("/startuper/submit-project", name="default_submit_project_index")
     */
    public function choiceWichProjectType()
    {
        return $this->render('project/intermediate.html.twig', []);
    }

    /**
     * @Route("{_locale}/crowdfunding-campagnes-dons", name="companies_list")
     * @Route("/crowdfunding-campagnes-dons", name="default_companies_list")
     * @param Request $request
     * @return Response
     */
    public function list(Request $request)
    {
        $arrayDomains = $request->query->get("domains");
        $offset = (int)$request->query->get("offset");
        //$order = (int)$request->query->get("order");
        //$verified = $request->query->get("verified");
        $search = $request->query->get("search");
        $city = $request->query->get("city");
        $domains = !empty($arraySectors) ? implode(",", $arrayDomains) : null;

        $offset = !empty($offset) ? $offset : 0;
        //$order = !empty($order) ? $order : null;
        $search = !empty($search) ? $search : null;

        $companies = $this->approvedCompanyRepository->getCompanies($domains, $city, $search, $offset);
        $allCompanies = $this->approvedCompanyRepository->getCompaniesForCount($domains, $city, $search);
        $moreCompanies = $this->approvedCompanyRepository->getCompanies($domains, $city, $search, $offset + 6);
        $more = count($moreCompanies) > 0;

        $this->manageCompaniesPrints($companies);

        // header
        $locale = $request->getLocale();
        $header = $this->headerRepository->findOneBy(['page' => 'companies', 'lang' => $locale]);
        return $this->render('company/list.html.twig', [
            'current_menu' => 'companies',
            'header' => $header,
            'domains' => $this->domainRepository->findAll(),
            'choicesDomains' => [],
            'current_domains' => null,
            'cities' => $this->cityRepository->findAll(),
            'choiceCity' => $city,
            'search' => null,
            'verified' => null,
            'order' => null,
            'companies' => $companies,
            'countResults' => count($allCompanies),
            'offset' => 6,
            'showMore' => $more,
            'more' => $more,
        ]);
    }

    /**
     * @Route("{_locale}/crowdfunding-campagnes-dons/more-companies", name="company_more_companies")
     * @Route("/crowdfunding-campagnes-dons/more-companies", name="company_more_companies_default", defaults={"_locale"="%locale%"})
     * @param Request $request
     * @return Response
     */
    public function moreCompanies(Request $request)
    {
        $arrayDomains = $request->query->get("domains");
        $offset = (int)$request->query->get("offset");
        //$order = (int)$request->query->get("order");
        //$verified = $request->query->get("verified");
        $search = $request->query->get("search");
        $city = $request->query->get("city");

        $domains = !empty($arrayDomains) ? implode(",", $arrayDomains) : null;
        $offset = !empty($offset) ? $offset : 0;
        //$order = !empty($order) ? $order : null;
        $search = !empty($search) ? $search : null;

        $companies = $this->approvedCompanyRepository->getcompanies($domains, $city, $search, $offset);
        $results = $this->approvedCompanyRepository->getCompaniesForCount($domains, $city, $search);
        $moreCompanies = $this->approvedCompanyRepository->getcompanies($domains, $city, $search, $offset + 6);

        $showMore = count($moreCompanies) > 0;

        $this->manageCompaniesPrints($companies);

        return $this->render('company/_results.html.twig', [
            "companies" => $companies,
            "showMore" => $showMore,
            "offset" => $offset,
            "countResults" => count($results),
            'more' => $showMore,
        ]);
    }

    /**
     * @Route("{_locale}/crowdfunding-campagnes-dons/search", name="companies_search")
     * @Route("/crowdfunding-campagnes-dons/search", name="companies_search_default", defaults={"_locale"="%locale%"})
     * @param Request $request
     * @return Response
     */
    public function searchCompanies(Request $request)
    {
        $arrayDomains = $request->query->get("domains");
        $offset = (int)$request->query->get("offset");
        //$order = (int)$request->query->get("order");
        //$verified = $request->query->get("verified");
        $search = $request->query->get("search");
        $city = $request->query->get("city");
        $domains = !empty($arraySectors) ? implode(",", $arrayDomains) : null;

        $offset = !empty($offset) ? $offset : 0;
        //$order = !empty($order) ? $order : null;
        $search = !empty($search) ? $search : null;

        $companies = $this->approvedCompanyRepository->getCompanies($domains, $city, $search, $offset);
        $results = $this->approvedCompanyRepository->getCompaniesForCount($domains, $city, $search);
        $moreCompanies = $this->approvedCompanyRepository->getCompanies($domains, $city, $search, $offset + 6);

        $showMore = count($moreCompanies) > 0;

        $this->manageCompaniesPrints($companies);
        // header
        $locale = $request->getLocale();
        $header = $this->headerRepository->findOneBy(['page' => 'companies', 'lang' => $locale]);
        return $this->render('company/list.html.twig', [
            'current_menu' => 'companies',
            'header' => $header,
            'domains' => $this->domainRepository->findAll(),
            'choicesDomains' => $arrayDomains,
            'current_domains' => $domains,
            'cities' => $this->cityRepository->findAll(),
            'choiceCity' => $city,
            'search' => $search,
            //'verified' => $verified,
            //'order' => $order,
            'companies' => $companies,
            'countResults' => count($results),
            'offset' => $offset + 6,
            'showMore' => $showMore,
            'more' => $showMore,
        ]);
    }

    /**
     * @Route("{_locale}/crowdfunding-appel-aux-dons/{id}-{slug}", name="company_show")
     * @Route("/crowdfunding-appel-aux-dons/{id}-{slug}", name="company_show_default", defaults={"_locale"="%locale%"})
     * @param int $id
     * @param string $slug
     * @param Request $request
     * @return Response
     */
    public function ShowCompany($id, $slug, Request $request)
    {
        $approvedCompany = $this->approvedCompanyRepository->findOneBy(['company' => $id, 'slug' => $slug]);
        if (!$approvedCompany || $approvedCompany->getIsDeleted()) {
            throw new NotFoundHttpException();
        }
        $company = $approvedCompany->getCompany();

        $comment = new CompanyComment();
        $form = $this->createForm(CompanyCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser()) {
                $comment->setAuthor($this->getUser())
                ->setCompany($company);
                $this->manager->persist($comment);
                $this->manager->flush();
                unset($entity);
                unset($form);
                $comment = new CompanyComment();
                $form = $this->createForm(CompanyCommentType::class, $comment);
                $message = $this->translator->trans("Your comment has been sent successfully", [], $this->translationDomain);
                $this->addFlash('success', $message);
            } else {
                $message = $this->translator->trans("You cannot send comment without login", [], $this->translationDomain);
                $this->addFlash('danger', $message);
            }
        }

        $session = $this->get("session");
        $companiesViews = $session->get("companiesViews");
        if ($companiesViews === null) {
            $session->set("companiesViews", []);
            $companiesViews = $session->get("companiesViews");
        }

        if (!$companiesViews || !in_array($company->getId(), $companiesViews)) {
            $companiesViews[] = $company->getId();
            $session->set("companiesViews", $companiesViews);

            $views = $company->getViews();
            $company->setViews($views + 1);
            $this->manager->flush();
        }

        $domains = [];
        foreach ($approvedCompany->getDomain() as $domain) {
            $domains[] = $domain->getId();
        }
        $metaTitle = ($company->getMetaTitle() !== null) ?  $company->getMetaTitle() : $company->getName();
        $metaDescription = ($company->getMetaDescription() !== null) ?  $company->getMetaDescription() : $company->getDescription();
        $width = $height = 0;
        if (file_exists($company->getCoverPath())) {
            list($width,$height) =  getimagesize($company->getCoverPath());
        }
        $contributors = $approvedCompany->getCompany()->getContributorsByStatus('CONFIRMED');
        return $this->render('company/show.html.twig', [
            'company' => $approvedCompany,
            'approvedCompany' => true,
            'useOfFundsCollecteds' => $approvedCompany->getUseOfFundsCollected(),
            'contributors' => $contributors->slice(0, 16),
            'totalContributors' => $contributors->count(),
            'current_menu' => "companies",
            'comments' => $company->getComments(),
            'form' => $form->createView(),
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'width' => $width,
            'height' => $height,
            'companies' => $this->approvedCompanyRepository->getSimilarCompanies(implode(",", $domains), $approvedCompany->getId())
        ]);
    }

    /**
     * @Route("{_locale}/companies/{id}/like", name="company_like")
     * @Route("/companies/{id}/like", name="company_like_default", defaults={"_locale"="%locale%"})
     * @param ApprovedCompany $approvedCompany
     * @param Request $request
     * @return Response
     */
    public function like(ApprovedCompany $approvedCompany, Request $request)
    {
        $error = false;
        $msg = null;
        $code = null;
        if (!$approvedCompany || $approvedCompany->getIsDeleted()) {
            $error = true;
            $msg = $this->translator->trans("Company not found", [], $this->translationDomain);
            $code = 404;
        }
        $user = $this->getUser();
        if (!$user) {
            $error = true;
            $msg = $this->translator->trans("Unauthorized", [], $this->translationDomain);
            $code = 403;
        }
        if ($error) {
            return $this->json([
                'code' => $code,
                'message' => $msg
            ], $code);
        }
        $company = $approvedCompany->getCompany();
        if ($company->isLikedByUser($user)) {
            $like = $this->companyLikeRepository->findOneBy(['company' => $company, 'user' => $user]);
            $this->manager->remove($like);
            $this->manager->flush();
            return $this->json([
                'code' => 200,
                'message' => $this->translator->trans('Like removed', [], $this->translationDomain),
                'label' => $this->translator->trans('Like', [], $this->translationDomain),
                'likes' => $this->companyLikeRepository->count(['company' => $company])
            ], 200);
        }

        $like = new CompanyLike();
        $like->setCompany($company)
            ->setUser($user);
        $this->manager->persist($like);
        $this->manager->flush();

        $link = $this->generateUrl(
            "startuper_company_dashboard_show",
            ['id' => $company->getId(), 'slug' => $company->getSlug()],
            UrlGeneratorInterface::ABSOLUTE_URL);
        $this->notificator->notifyOnCompanyLiked($approvedCompany, $user, $link, $request->getLocale());

        return $this->json([
            'code' => 200,
            'message' => $this->translator->trans('Like added', [], $this->translationDomain),
            'label' => $this->translator->trans('Unlike', [], $this->translationDomain),
            'likes' => $this->companyLikeRepository->count(['company' => $company])
        ], 200);
    }

    /**
     * @Route("{_locale}/companies/comment/{id}/response", name="company_comment_response")
     * @Route("/companies/comment/{id}/response", name="company_comment_response_default", defaults={"_locale"="%locale%"})
     * @param CompanyComment $companyComment
     * @param Request $request
     * @return Response
     */
    public function responseComment(CompanyComment $companyComment, Request $request)
    {
        if (!$companyComment) {
            throw new NotFoundHttpException();
        }
        $user = $this->getUser();
        if (!$user) {
            $msg = $this->translator->trans("You cannot respond without login", [], $this->translationDomain);
            $this->addFlash("danger", $msg);
        } else {
            $content = $request->get("response_comment");
            if ($content) {
                $responseComment = new CompanyCommentResponse();
                $responseComment->setUser($user)
                    ->setCompanyComment($companyComment)
                    ->setContent($content);
                $this->manager->persist($responseComment);
                $this->manager->flush();
                $msg = $this->translator->trans("Your response has been sent successfully", [], $this->translationDomain);
                $this->addFlash("success", $msg);
            } else {
                $msg = $this->translator->trans("We cannot save an empty response", [], $this->translationDomain);
                $this->addFlash("danger", $msg);
            }
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/crowdfunding-appel-aux-dons/{id}/Contributors", name="company_more_contributors")
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function moreContributors(int $id, Request $request)
    {
        $approvedCompany = $this->approvedCompanyRepository->findOneBy(['company' => $id]);
        if (!$approvedCompany || $approvedCompany->getIsDeleted()) {
            return $this->json([
                'success' => false,
                'message' => $this->translator->trans("Company not found", [], $this->translationDomain)
            ]);
        }
        $offset = (int)$request->request->get('offset');
        $contributors = $approvedCompany->getCompany()->getContributorsByStatus('CONFIRMED');
        $response = $this->renderView('company/_contributors.html.twig', [
            'contributors' => $contributors->slice($offset, 16)
        ]);
        $more = false;
        if ($contributors->slice($offset + 16, 16)) {
            $more = true;
            $offset = $offset + 16;
        }
        return $this->json([
            'success' => true,
            'html' =>$response,
            'more' => $more,
            'offset' => $offset
        ]);
    }

    /**
     * @param $companies
     */
    private function manageCompaniesPrints($companies)
    {
        $session = $this->get("session");
        $companiesPrints = $session->get("companiesPrints");
        if ($companiesPrints === null) {
            $session->set("companiesPrints", []);
            $companiesPrints = $session->get("companiesPrints");
        }
        foreach ($companies as $approvedCompany) {
            /* @var ApprovedCompany $approvedCompany */
            $company = $approvedCompany->getCompany();

            if (!$companiesPrints || !in_array($company->getId(), $companiesPrints)) {
                $companiesPrints[] = $company->getId();
                $session->set("companiesPrints", $companiesPrints);

                $count = $company->getPrints();
                $company->setPrints($count + 1);
                $this->manager->flush();
            }
        }
    }
}
