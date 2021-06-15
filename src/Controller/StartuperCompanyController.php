<?php

namespace App\Controller;

use App\Form\ChangeCoverCompanyType;
use App\Form\ChangeLogoCompanyType;
use App\Notification\CompanyAdminNotification;
use App\Notification\CompanyEntrepreneurNotification;
use App\Repository\ApprovedCompanyRepository;
use App\Repository\CompanyRepository;
use App\Repository\FaqRepository;
use App\Repository\MessageRepository;
use App\Repository\ProjectRepository;
use App\Service\CompanyComparison;
use Doctrine\Common\Persistence\ObjectManager;
use EgioDigital\CMSBundle\Repository\ArticlePublishedRepository;
use App\Entity\Company;
use App\Entity\UseFund;
use App\Form\CompanyType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class StartuperCompanyController
 * @package App\Controller
 */
class StartuperCompanyController extends AbstractController
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
     * @var ProjectRepository
     */
    private $projectRepository;
    /**
     * @var MessageRepository
     */
    private $messageRepository;
    /**
     * @var FaqRepository
     */
    private $faqRepository;
    /**
     * @var ArticlePublishedRepository
     */
    private $articlePublishedRepository;
    /**
     * @var CompanyEntrepreneurNotification
     */
    private $notification;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    private $translationDomain = 'company';
    /**
     * @var string
     */
    private $strProjects = "projects";
    /**
     * @var CompanyComparison
     */
    private $companyComparison;
    /**
     * @var ApprovedCompanyRepository
     */
    private $approvedCompanyRepository;
    /**
     * @var CompanyAdminNotification
     */
    private $adminNotification;

    /**
     * CompanyController constructor.
     *
     * @param ObjectManager $objectManager
     * @param CompanyRepository $companyRepository
     * @param CompanyComparison $companyComparison
     * @param ApprovedCompanyRepository $approvedCompanyRepository
     * @param ProjectRepository $projectRepository
     * @param ArticlePublishedRepository $articleRepository
     * @param MessageRepository $messageRepository
     * @param FaqRepository $faqRepository
     * @param CompanyEntrepreneurNotification $notification
     * @param CompanyAdminNotification $adminNotification
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ObjectManager $objectManager,
        CompanyRepository $companyRepository,
        CompanyComparison $companyComparison,
        ApprovedCompanyRepository $approvedCompanyRepository,
        ProjectRepository $projectRepository,
        ArticlePublishedRepository $articleRepository,
        MessageRepository $messageRepository,
        FaqRepository $faqRepository,
        CompanyEntrepreneurNotification $notification,
        CompanyAdminNotification $adminNotification,
        TranslatorInterface $translator
    ) {
        $this->manager = $objectManager;
        $this->companyRepository = $companyRepository;
        $this->companyComparison = $companyComparison;
        $this->approvedCompanyRepository = $approvedCompanyRepository;
        $this->projectRepository = $projectRepository;
        $this->messageRepository = $messageRepository;
        $this->faqRepository = $faqRepository;
        $this->articlePublishedRepository = $articleRepository;
        $this->notification = $notification;
        $this->adminNotification = $adminNotification;
        $this->translator = $translator;
    }

    /**
     * @Route("{_locale}/startuper/company/create", name="startuper_company_create")
     * @Route("/startuper/company/create", name="default_startuper_company_create")
     * @Security("is_granted('ROLE_STARTUPER')")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $firstFund = $request->get("txt-first-fund");
                if ($firstFund != "") {
                    $fund = new UseFund();
                    $fund->setDescription($firstFund)
                        ->setCompany($company);
                    $company->addUseOfFundsCollected($fund);
                }
                foreach ($company->getUseOfFundsCollecteds() as $use) {
                    $use->setCompany($company);
                }
                foreach ($company->getDocuments() as $document) {
                    $document->setCompany($company);
                }
                $company->setUser($this->getUser());
                $this->manager->persist($company);
                $this->manager->flush();
                $this->notifyEntrepreneurAndAdmin($company, $request->getLocale(), "create");
                return $this->redirectToRoute("startuper_company_success_create", [
                    'id' => $company->getId(),
                    'slug' => $company->getSlug()
                ]);
            } catch (\Exception $exception) {
                $this->addFlash("danger", $exception->getMessage());
            }
        }
        return $this->render('startuper/company/create.html.twig', [
            'form' => $form->createView(),
            'action' => "create",
        ]);
    }

    /**
     * @Route("{_locale}/startuper/company/{id}-{slug}/success-create", name="startuper_company_success_create")
     * @Route("/startuper/company/{id}-{slug}/success-create", name="startuper_company_success_create_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === company.getUser()",
     *    message="This campaign is not yours."
     * )
     * @param Company $company
     * @return Response
     */
    public function companySuccessCreate(Company $company)
    {
        return $this->render('startuper/company/success_create.html.twig', [
            'company' => $company,
        ]);
    }

    /**
     * @Route("{_locale}/startuper/company/{id}-{slug}/edit", name="startuper_company_edit")
     * @Route("/startuper/company/{id}/edit", name="default_startuper_company_edit")
     * @Security("is_granted('ROLE_STARTUPER')")
     * @param Company $company
     * @param Request $request
     * @return Response
     */
    public function edit(Company $company, Request $request)
    {
        if (!$company || $company->getIsDeleted()) {
            throw new NotFoundHttpException();
        }
        if ($company->getUser() != $this->getUser()) {
            throw new AccessDeniedException();
        }
        if ($company->getIsLocked()) {
            $msg = $this->translator->trans("Your company is temporarily locked", [], $this->translationDomain);
            $this->addFlash("danger", $msg);
            return $this->redirectToRoute('startuper_company_show', [
                "id" => $company->getId(),
                'slug' => $company->getSlug()
            ]);
        }
        $form = $this->createForm(CompanyType::class, $company);
        $form->remove('isLegalRepresentativeOfTheAssociation')
            ->remove('isAcceptedTheConditionOfSecurity');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $firstFund = $request->get("txt-first-fund");
                if ($firstFund != "") {
                    $useFund = new UseFund();
                    $useFund->setDescription($firstFund);
                    $company->addUseOfFundsCollected($useFund);
                }
                foreach ($company->getUseOfFundsCollecteds() as $use) {
                    $use->setCompany($company);
                }
                foreach ($company->getDocuments() as $document) {
                    $document->setCompany($company);
                }
                $updated = $this->companyComparison->compareCompany($company);
                if ($updated) {
                    $company->setIsUpdated(true);
                }
                $this->manager->persist($company);
                $this->manager->flush();
                $msg = $this->translator->trans('Your company updated successfully', [], $this->translationDomain);
                $this->addFlash("success", $msg);
                return $this->redirectToRoute('startuper_company_check_update', [
                    'id' => $company->getId(),
                    'slug' => $company->getSlug()
                ]);
            } catch (\Exception $exception) {
                $this->addFlash(
                    "danger",
                    $this->translator->trans("Error registering company, please try again later", [], $this->translationDomain)
                );
            }
        }
        return $this->render('startuper/company/create.html.twig', [
            'form' => $form->createView(),
            'action' => "edit",
        ]);
    }

    /**
     * @Route("{_locale}/startuper/company/dashboard", name="startuper_company_dashboard")
     * @Route("/startuper/company/dashboard/{id}-{slug}", name="startuper_company_dashboard_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER')")
     * @param Request $request
     * @return Response
     */
    public function dashboardCompany(Request $request)
    {
        $projects = $this->projectRepository->findAllByStartupNotDeleted($this->getUser());
        $companies = $this->companyRepository->findAllByStartupNotDeleted($this->getUser());
        if (count($companies) == 0) {
            return $this->redirectToRoute("startuper_project_dashboard");
        }
        $company = $companies [count($companies) - 1];
        return $this->render('startuper/dashboard/dashboard.html.twig', [
            $this->strProjects => $projects,
            "companies" => $companies,
            'company' => $company,
            'approvedCompany' => $company->getApprovedCompany(),
            'project' => null,
            'messages' => [],
            'articles' => $this->articlePublishedRepository->getThreeArticlesSuggestion($request->getLocale()),
        ]);
    }

    /**
     * @Route("{_locale}/startuper/company/dashboard/{id}-{slug}", name="startuper_company_dashboard_show")
     * @Route("/startuper/company/dashboard/{id}-{slug}", name="startuper_company_dashboard_show_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER')")
     * @param $id
     * @param $slug
     * @param Request $request
     * @return Response
     */
    public function dashboardCompanyShow($id, $slug, Request $request)
    {
        $company = $this->companyRepository->findOneBy(['id' => $id, "slug" => $slug]);
        if (!$company) {
            throw new NotFoundHttpException();
        }
        if ($company->getUser() != $this->getUser()) {
            throw new AccessDeniedException();
        }
        $approvedCompany = $this->approvedCompanyRepository->findOneBy(['company' => $company]);
        $locale = $request->getLocale();
        $projects = $this->projectRepository->findAllByStartupNotDeleted($this->getUser());
        $companies = $this->companyRepository->findAllByStartupNotDeleted($this->getUser());
        return $this->render('startuper/dashboard/dashboard.html.twig', [
            $this->strProjects => $projects,
            "companies" => $companies,
            'company' => $company,
            'approvedCompany' => $approvedCompany,
            'project' => null,
            "messages" => [],
            'articles' => $this->articlePublishedRepository->getThreeArticlesSuggestion($locale),
        ]);
    }

    /**
     * @Route("{_locale}/startuper/company/{id}-{slug}", name="startuper_company_show")
     * @Route("/startuper/company/{id}-{slug}", name="startuper_company_show_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER')")
     * @param $id
     * @param $slug
     * @return Response
     */
    public function startuperCompanyShow($id, $slug)
    {
        $company = $this->companyRepository->findOneBy(['id' => $id, "slug" => $slug]);
        if (!$company || $company->getIsDeleted()) {
            throw new NotFoundHttpException();
        }
        if ($company->getUser() != $this->getUser()) {
            throw new AccessDeniedException();
        }
        return $this->render('company/show.html.twig', [
            'company' => $company,
            'approvedCompany' => false,
            'useOfFundsCollecteds' => $company->getUseOfFundsCollecteds(),
            'contributors' => $company->getContributors(),
            'comments' => $company->getComments(),
            'companies' => []
        ]);
    }

    /**
     * @Route("{_locale}/startuper/company/{id}-{slug}/update-cover", name="startuper_company_update_cover")
     * @Route("/startuper/company/{id}-{slug}/update-cover", name="startuper_company_update_cover_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER')")
     * @param $id
     * @param $slug
     * @param Request $request
     * @return Response
     */
    public function startuperUpdateCover($id, $slug, Request $request)
    {
        $company = $this->companyRepository->findOneBy(['id' => $id, "slug" => $slug]);
        if (!$company || $company->getIsDeleted()) {
            throw new NotFoundHttpException();
        }
        if ($company->getUser() != $this->getUser()) {
            throw new AccessDeniedException();
        }
        if ($company->getIsLocked()) {
            $msg = $this->translator->trans("Your company is temporarily locked", [], 'company');
            $this->addFlash("danger", $msg);
            return $this->redirectToRoute('startuper_company_show', [
                "id" => $company->getId(),
                'slug' => $company->getSlug()
            ]);
        }
        $form = $this->createForm(ChangeCoverCompanyType::class, $company);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $updated = $this->companyComparison->compareCompany($company);
            if ($updated === true) {
                $company->setIsUpdated(true);
                $this->manager->flush();
                return $this->redirectToRoute('startuper_company_check_update', [
                    'id' => $company->getId(),
                    'slug' => $company->getSlug()
                ]);
            }
            $this->manager->flush();
            $msg = $this->translator->trans("Your company cover photo  has been changed successfully", [], 'company');
            $this->addFlash("success", $msg);
            return $this->redirectToRoute('startuper_company_show', [
                "id" => $company->getId(),
                'slug' => $company->getSlug()
            ]);
        }
        return $this->render('startuper/company/add_cover.html.twig', [
            'form' => $form->createView(),
            'company' => $company
        ]);
    }

    /**
     * @Route("{_locale}/startuper/company/{id}-{slug}/update-logo", name="startuper_company_update_logo")
     * @Route("/startuper/company/{id}-{slug}/update-logo", name="startuper_company_update_logo_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER')")
     * @param $id
     * @param $slug
     * @param Request $request
     * @return Response
     */
    public function startuperUpdateLogo($id, $slug, Request $request)
    {
        $company = $this->companyRepository->findOneBy(['id' => $id, "slug" => $slug]);
        if (!$company || $company->getIsDeleted()) {
            throw new NotFoundHttpException();
        }
        if ($company->getUser() != $this->getUser()) {
            throw new AccessDeniedException();
        }
        if ($company->getIsLocked()) {
            $msg = $this->translator->trans("Your company is temporarily locked", [], 'company');
            $this->addFlash("danger", $msg);
            return $this->redirectToRoute('startuper_company_show', [
                "id" => $company->getId(),
                'slug' => $company->getSlug()
            ]);
        }
        $form = $this->createForm(ChangeLogoCompanyType::class, $company);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $updated = $this->companyComparison->compareCompany($company);
            if ($updated === true) {
                $company->setIsUpdated(true);
                $this->manager->flush();
                return $this->redirectToRoute('startuper_company_check_update', [
                    'id' => $company->getId(),
                    'slug' => $company->getSlug()
                ]);
            }
            $this->manager->flush();
            $msg = $this->translator->trans("Your company logo has been changed successfully", [], 'company');
            $this->addFlash("success", $msg);
            return $this->redirectToRoute('startuper_company_show', [
                "id" => $company->getId(),
                'slug' => $company->getSlug()
            ]);
        }
        return $this->render('startuper/company/add_logo.html.twig', [
            'form' => $form->createView(),
            'company' => $company
        ]);
    }

    /**
     * @Route("{_locale}/startuper/company/{id}-{slug}/check-update", name="startuper_company_check_update")
     * @Route("/startuper/company/{id}-{slug}/check-update", name="startuper_company_check_update_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === company.getUser()",
     *    message="This campaign is not yours."
     * )
     * @param Company $company
     * @param Request $request
     * @return Response
     */
    public function companyChecksUpdate(Company $company, Request $request)
    {
        $approvedCompany = $this->approvedCompanyRepository->findOneBy(["company" => $company]);
        if (!$approvedCompany or $company->getIsUpdated() === false) {
            return $this->redirectToRoute('startuper_company_dashboard_show', [
                'id' => $company->getId(),
                'slug' => $company->getSlug()
            ]);
        }
        $this->notifyEntrepreneurAndAdmin($company, $request->getLocale(), "edit");
        return $this->render('startuper/company/success_update.html.twig', [
            "company" => $company,
        ]);
    }

    /**
     * @Route("{_locale}/startuper/company/{id}-{slug}/confirm-delete", name="startuper_company_confirm_delete")
     * @Route("/startuper/company/{id}-{slug}/confirm-delete", name="startuper_company_confirm_delete_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER')")
     * @param $id
     * @param $slug
     * @return Response
     */
    public function startuperConfirmDeleteCompany($id, $slug)
    {
        $company = $this->companyRepository->findOneBy(['id' => $id, "slug" => $slug]);
        if (!$company || $company->getIsDeleted()) {
            throw new NotFoundHttpException();
        }
        if ($company->getUser() != $this->getUser()) {
            throw new AccessDeniedException();
        }
        return $this->render('startuper/company/confirm_delete.html.twig', [
            'company' => $company
        ]);
    }

    /**
     * @Route("{_locale}/startuper/company/{id}-{slug}/delete", name="startuper_company_delete")
     * @Route("/startuper/company/{id}-{slug}/delete", name="startuper_company_delete_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER')")
     * @param $id
     * @param $slug
     * @param Request $request
     * @return RedirectResponse
     */
    public function startuperDeleteCompany($id, $slug, Request $request)
    {
        $company = $this->companyRepository->findOneBy(['id' => $id, "slug" => $slug]);
        if (!$company || $company->getIsDeleted()) {
            throw new NotFoundHttpException();
        }
        if ($company->getUser() != $this->getUser()) {
            throw new AccessDeniedException();
        }
        $submittedToken = $request->request->get("token");
        if ($this->isCsrfTokenValid('startuper-delete-company' . $company->getId(), $submittedToken)) {
            $approvedProject = $this->approvedCompanyRepository->findOneBy(['company' => $company]);
            if ($approvedProject) {
                $approvedProject->setIsDeleted(true);
            }
            $company->setIsApproved(false);
            $company->setIsDeleted(true);
            $this->manager->flush();
            $msg = $this->translator->trans("Your company has been deleted successfully", [], 'company');
            $this->addFlash("success", $msg);
            return $this->redirectToRoute('startuper_company_dashboard');
        } else {
            $this->addFlash(
                "danger",
                $this->translator->trans("Token invalid", [])
            );
            return $this->redirectToRoute('startuper_company_confirm_delete', [
                'id' => $company->getId(),
                'slug' => $company->getSlug()
            ]);
        }
    }

    /**
     * @param Company $company
     * @param $locale
     * @param $action
     */
    protected function notifyEntrepreneurAndAdmin(Company $company, $locale, $action)
    {
        $linkAdmin = $this->generateUrl(
            'sonata_admin_company_show',
            ['id' => $company->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL);

        $authorLink = $this->generateUrl(
            'sonata_admin_startuper_show',
            ['id' => $company->getUser()->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL);

        $link = $this->generateUrl(
            "startuper_company_show",
            ['id' => $company->getId(), 'slug' => $company->getSlug()],
            UrlGeneratorInterface::ABSOLUTE_URL);

        if ($action == "edit") {
            $this->notification->notifyOnCompanyUpdated($company, $link, $locale);
            $this->adminNotification->askUpdateCompany($company, $linkAdmin, $locale);
        } else {
            $this->notification->notifyOnCompanyCreated($company, $link, $locale);
            $this->adminNotification->notifyOnCompanyCreated($company, $linkAdmin, $authorLink, $locale);
        }
    }

}
