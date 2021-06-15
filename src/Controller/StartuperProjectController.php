<?php

namespace App\Controller;

use App\Entity\Faq;
use App\Entity\Project;
use App\Form\ChangeCoverProjectType;
use App\Form\ChangeLogoProjectType;
use App\Form\ChangeVideoProjectType;
use App\Form\QuestionType;
use App\Notification\AdminNotification;
use App\Notification\EntrepreneurNotification;
use App\Repository\ApprovedProjectRepository;
use App\Repository\ProjectRepository;
use App\Repository\StepRepository;
use App\Service\Comparison;
use App\Service\ProjectService;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ProjectController
 * @package App\Controller
 */
class StartuperProjectController extends Controller
{

    const STARTUPRT_PROJECT_SHOW = "startuper_project_show";
    const STARTUPRT_PROJECT_COMPLETE = "startuper_project_complete";
    const STARTUPRT_PROJECT_EDIT = "startuper_project_edit";
    private $manager;
    private $projectService;
    private $adminNotification;
    private $entrepreneurNotification;
    private $projectRepository;
    private $approvedProjectRepository;
    private $translator;
    private $projectNotFound;
    private $danger = "danger";
    private $success = "success";
    private $translationDomainMessage = "messages";
    private $strProject = "project";
    private $strToken = "token";

    /**
     * StartuperProjectController constructor.
     *
     * @param ObjectManager $manager
     * @param ProjectService $projectService
     * @param AdminNotification $adminNotification
     * @param EntrepreneurNotification $entrepreneurNotification
     * @param ProjectRepository $projectRepository
     * @param ApprovedProjectRepository $approvedProjectRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ObjectManager $manager,
        ProjectService $projectService,
        AdminNotification $adminNotification,
        EntrepreneurNotification $entrepreneurNotification,
        ProjectRepository $projectRepository,
        ApprovedProjectRepository $approvedProjectRepository,
        TranslatorInterface $translator
    ) {
        $this->manager = $manager;
        $this->projectService = $projectService;
        $this->adminNotification = $adminNotification;
        $this->entrepreneurNotification = $entrepreneurNotification;
        $this->projectRepository = $projectRepository;
        $this->approvedProjectRepository = $approvedProjectRepository;
        $this->translator = $translator;
        $this->projectNotFound = $this->translator->trans("Project not found", [], $this->translationDomainMessage);
    }

    /**
     * @Route("{_locale}/startuper/project/{id}-{slug}/skip", name="startuper_project_skip")
     * @Route("/startuper/project/{id}-{slug}/skip", name="startuper_project_skip_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Project $project
     * @return Response
     */
    public function projectSkipStep(Project $project)
    {
        if ($project->getStepCreating() === 7) {
            $project->setStepUpdating($project->getStepUpdating() + 1);
            $route = self::STARTUPRT_PROJECT_EDIT;
        } else {
            $project->setStepCreating($project->getStepCreating() + 1);
            $route = self::STARTUPRT_PROJECT_COMPLETE;
        }

        $this->manager->persist($project);
        $this->manager->flush();

        return $this->redirectToRoute($route, [
            'id' => $project->getId(),
            'slug' => $project->getSlug()
        ]);
    }

    /**
     * @Route("{_locale}/startuper/project/{id}-{slug}/previous", name="startuper_project_previous")
     * @Route("/startuper/project/{id}-{slug}/previous", name="startuper_project_previous_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Project $project
     * @return Response
     */
    public function projectPreviousStep(Project $project)
    {
        if ($project->getStepCreating() === 7) {
            $project->setStepUpdating($project->getStepUpdating() - 1);
            $route = self::STARTUPRT_PROJECT_EDIT;
        } else {
            $project->setStepCreating($project->getStepCreating() - 1);
            $route = self::STARTUPRT_PROJECT_COMPLETE;
        }

        $this->manager->persist($project);
        $this->manager->flush();

        return $this->redirectToRoute($route, [
            'id' => $project->getId(),
            'slug' => $project->getSlug()
        ]);
    }

    /**
     * @Route("{_locale}/startuper/project/{id}-{slug}/skip-to/{step}", name="startuper_project_skipto")
     * @Route("/startuper/project/{id}-{slug}/skip-to/{step}", name="startuper_project_skipto_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Project $project
     * @param int $step
     * @return Response
     */
    public function projectSkipTStep(Project $project, int $step)
    {

        if ($project->getStepCreating() === 7) {
            $project->setStepUpdating($step);
            $route = self::STARTUPRT_PROJECT_EDIT;
        } else {
            $project->setStepCreating($step);
            $route = self::STARTUPRT_PROJECT_COMPLETE;
        }

        $this->manager->persist($project);
        $this->manager->flush();

        return $this->redirectToRoute($route, [
            'id' => $project->getId(),
            'slug' => $project->getSlug()
        ]);
    }

    /**
     * @Route("{_locale}/startuper/project/{id}-{slug}/request-validation", name="startuper_project_request_validation")
     * @Route("/startuper/project/{id}-{slug}/request-validation", name="startuper_project_request_validation_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Project $project
     * @param Request $request
     * @return Response
     */
    public function projectValidate(Project $project, Request $request)
    {
        $step = $project->getStepCreating();
        if ($step < 6) {
            $route = self::STARTUPRT_PROJECT_COMPLETE;
        } elseif ($step > 6) {
            $route = self::STARTUPRT_PROJECT_SHOW;
        } else {
            $route = 'startuper_project_success_create';
        }
        return $this->redirectToRoute($route, [
            'id' => $project->getId(),
            'slug' => $project->getSlug()
        ]);
    }

    /**
     * @Route("{_locale}/startuper/project/{id}-{slug}/success-create", name="startuper_project_success_create")
     * @Route("/startuper/project/{id}-{slug}/success-create", name="startuper_project_success_create_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Project $project
     * @param Request $request
     * @return Response
     */
    public function projectSuccessCreate(Project $project, Request $request)
    {
        if (!$project) {
            throw new NotFoundHttpException($this->projectNotFound);
        }
        if (!$project->getIsDraft()) {
            return $this->redirectToRoute('startuper_project_dashboard_show', [
                'id' => $project->getId(),
                'slug' => $project->getSlug()
            ]);
        }
        $this->sendMailsOnCreatedSuccess($project, $request->getLocale());
        return $this->render('startuper/project/success_create.html.twig', [
            $this->strProject => $project,
        ]);
    }

    public function sendMailsOnCreatedSuccess(Project $project, $locale)
    {
        $step = $project->getStepCreating();
        $project->setStepCreating($step + 1);
        $project->setIsDraft(false);
        $this->manager->persist($project);
        $this->manager->flush();
        // send mail to admin
        $linkAdmin = $this->generateUrl(
        'sonata_admin_project_show',
        ['id' => $project->getId()],
        UrlGeneratorInterface::ABSOLUTE_URL);
        $link = $this->generateUrl(
        self::STARTUPRT_PROJECT_SHOW,
        ['id' => $project->getId(), 'slug' => $project->getSlug()],
        UrlGeneratorInterface::ABSOLUTE_URL);
        $this->adminNotification->notifyOnProjectCreated($project, $linkAdmin, $locale);
        $this->entrepreneurNotification->notifyOnProjectCreated($project, $link, $locale);
    }

    /**
     * @Route("{_locale}/startuper/project/{id}-{slug}/check-update", name="startuper_project_check_update")
     * @Route("/startuper/project/{id}-{slug}/check-update", name="startuper_project_check_update_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Project $project
     * @param Request $request
     * @return Response
     */
    public function projectChecksUpdate(Project $project, Request $request)
    {
        $approvedProject = $this->approvedProjectRepository->findOneBy([$this->strProject => $project]);
        if (!$approvedProject or $project->getIsUpdated() === false) {
            return $this->redirectToRoute('startuper_project_dashboard_show', [
                'id' => $project->getId(),
                'slug' => $project->getSlug()
            ]);
        }
        /*$checks = $this->projectService->checkUpdateProject($project);
        if (count($checks) > 0) {
                $session = $request->getSession();
                $session->set('checks', $checks);
            return $this->render('startuper/project/Intermediate_page.html.twig', [
                $this->strProject => $project,
                'updated' => true
            ]);
        }*/
        $this->sendMailsOnUpdateSuccess($project, $request->getLocale());
        return $this->render('startuper/project/success_update.html.twig', [
            $this->strProject => $project,
        ]);
    }

    /*
    /**
     * @Route("/project/{id}-{slug}/success-update", name="startuper_project_success_update")
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Project $project
     * @return Response
     */
    /*
    public function projectIntermediate(Project $project)
    {
        if (!$project) {
            throw new NotFoundHttpException($this->projectNotFound);
        }
        return $this->render('startuper/project/request_update.html.twig', [
            $this->strProject => $project,
        ]);
    }
    */

    /**
     * @param Project $project
     * @param $locale
     */
    public function sendMailsOnUpdateSuccess(Project $project, $locale)
    {
        $linkAdmin = $this->generateUrl(
                'sonata_admin_project_compare',
                ['id' => $project->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL);

        $link = $this->generateUrl(
            self::STARTUPRT_PROJECT_SHOW,
                ['id' => $project->getId(), 'slug' => $project->getSlug()],
                UrlGeneratorInterface::ABSOLUTE_URL);

        $this->adminNotification->askUpdateProject($project, $linkAdmin, $locale);
        $this->entrepreneurNotification->notifyOnModificationUnderStudy($project, $link, $locale);
    }

    /**
     * @Route("{_locale}/startuper/project/{id}-{slug}/show", name="startuper_project_show")
     * @Route("/startuper/project/{id}-{slug}/show", name="startuper_project_show_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Project $project
     * @param Request $request
     * @return Response
     */
    public function startuperShowProject(Project $project, Request $request)
    {
        if (!$project || $project->getIsDeleted()) {
            throw new NotFoundHttpException($this->projectNotFound);
        }

        $faq = new Faq();
        $form = $this->createForm(QuestionType::class, $faq);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $faq->setAuthor($this->getUser())
                ->setProject($project);
            $this->manager->persist($faq);
            $this->manager->flush();
            $msg = $this->translator->trans("Your question has been sent successfully", [], $this->translationDomainMessage);
            $this->addFlash($this->success, $msg);
        }

        $sectors = [];
        foreach ($project->getSectors() as $sector) {
            $sectors[] = $sector->getId();
        }

        return $this->render('project/show.html.twig', [
            $this->strProject => $project,
            'approvedProject' => false,
            'questions' => $project->getFAQs(),
            'form' => $form->createView(),
            'projects' => null,
            'metaTitle' => $project->getName(),
            'metaDescription' => $project->getDescription(),
        ]);
    }

    /**
     * @Route("{_locale}/startuper/project/{id}-{slug}/confirm-deletion", name="startuper_project_confirm_delete")
     * @Route("/startuper/project/{id}-{slug}/confirm-deletion", name="startuper_project_confirm_delete_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Project $project
     * @return Response
     */
    public function projectConfirmDeletion(Project $project)
    {
        if (!$project || $project->getIsDeleted()) {
            throw new NotFoundHttpException($this->projectNotFound);
        }
        return $this->render('startuper/project/confirm_delete.html.twig', [
            $this->strProject => $project
        ]);
    }

    /**
     * @Route("{_locale}/startuper/project/{id}-{slug}/delete", name="startuper_project_delete")
     * @Route("/startuper/project/{id}-{slug}/delete", name="startuper_project_delete_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Project $project
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteProject(Project $project, Request $request)
    {
        if (!$project || $project->getIsDeleted()) {
            throw new NotFoundHttpException($this->projectNotFound);
        }
        $submittedToken = $request->request->get($this->strToken);
        if ($this->isCsrfTokenValid('startuper-delete-project' . $project->getId(), $submittedToken)) {
            $approvedProject = $this->approvedProjectRepository->findOneBy([$this->strProject => $project]);
            if ($approvedProject) {
                $approvedProject->setIsDeleted(true);
            }
            $project->setIsApproved(false);
            $project->setIsDeleted(true);
            $this->manager->flush();
            $msg = $this->translator->trans("Project deleted successfully", ["%project_name%" => $project->getName()], $this->translationDomainMessage);
            $this->addFlash($this->success, $msg);
            return $this->redirectToRoute('startuper_project_dashboard');
        } else {
            $this->addFlash(
                $this->danger,
                $this->translator->trans("Token invalid", [], $this->translationDomainMessage)
            );
            return $this->redirectToRoute('startuper_project_confirm_delete', [
                'id' => $project->getId(),
                'slug' => $project->getSlug()
            ]);
        }
    }

    /**
     * @Route("{_locale}/startuper/project/{id}-{slug}/cover", name="startuper_project_update_cover")
     * @Route("/startuper/project/{id}-{slug}/cover", name="startuper_project_update_cover_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Project $project
     * @param Request $request
     * @param Comparison $comparison
     * @return Response
     */
    public function updateImageCoverProject(Project $project, Request $request, Comparison $comparison)
    {
        if (!$project || $project->getIsDeleted()) {
            throw new NotFoundHttpException($this->projectNotFound);
        }
        $form = $this->createForm(ChangeCoverProjectType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $updated = $comparison->compareProject($project);
            if ($updated === true) {
                $project->setIsUpdated(true);
                $this->manager->flush();
                return $this->redirectToRoute('startuper_project_check_update', [
                    'id' => $project->getId(),
                    'slug' => $project->getSlug()
                ]);
            }
            $this->manager->flush();
            $msg = $this->translator->trans("Your cover photo has been changed successfully", [], $this->translationDomainMessage);
            $this->addFlash($this->success, $msg);
            return $this->redirectToRoute(self::STARTUPRT_PROJECT_SHOW, [
                "id" => $project->getId(),
                'slug' => $project->getSlug()
            ]);
        }
        return $this->render('startuper/project/add_cover.html.twig', [
            'form' => $form->createView(),
            $this->strProject => $project
        ]);
    }

    /**
     * @Route("{_locale}/startuper/project/{id}-{slug}/logo", name="startuper_project_update_logo")
     * @Route("/startuper/project/{id}-{slug}/logo", name="startuper_project_update_logo_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Project $project
     * @param Request $request
     * @param Comparison $comparison
     * @return Response
     */
    public function updateLogoProject(Project $project, Request $request, Comparison $comparison)
    {
        if (!$project || $project->getIsDeleted()) {
            throw new NotFoundHttpException($this->projectNotFound);
        }
        $form = $this->createForm(ChangeLogoProjectType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $updated = $comparison->compareProject($project);
            if ($updated === true) {
                $project->setIsUpdated(true);
                $this->manager->flush();
                return $this->redirectToRoute('startuper_project_check_update', [
                    'id' => $project->getId(),
                    'slug' => $project->getSlug()
                ]);
            }
            $this->manager->flush();
            $msg = $this->translator->trans("Your logo has been successfully changed", [], $this->translationDomainMessage);
            $this->addFlash($this->success, $msg);
            return $this->redirectToRoute(self::STARTUPRT_PROJECT_SHOW, [
                "id" => $project->getId(),
                'slug' => $project->getSlug()
            ]);
        }
        return $this->render('startuper/project/add_logo.html.twig', [
            'form' => $form->createView(),
            $this->strProject => $project
        ]);
    }

    /**
     * @Route("{_locale}/startuper/project/{id}-{slug}/video", name="startuper_project_update_video")
     * @Route("/startuper/project/{id}-{slug}/video", name="startuper_project_update_video_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Project $project
     * @param Request $request
     * @param Comparison $comparison
     * @return Response
     */
    public function updateProjectVideo(Project $project, Request $request, Comparison $comparison)
    {
        if (!$project || $project->getIsDeleted()) {
            throw new NotFoundHttpException($this->projectNotFound);
        }
        $form = $this->createForm(ChangeVideoProjectType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $updated = $comparison->compareProject($project);
            if ($updated === true) {
                $project->setIsUpdated(true);
                $this->manager->flush();
                return $this->redirectToRoute('startuper_project_check_update', [
                    'id' => $project->getId(),
                    'slug' => $project->getSlug()
                ]);
            }
            $this->manager->flush();
            $msg = $this->translator->trans("Your Video has been successfully added", [], $this->translationDomainMessage);
            $this->addFlash($this->success, $msg);
            return $this->redirectToRoute(self::STARTUPRT_PROJECT_SHOW, [
                "id" => $project->getId(),
                'slug' => $project->getSlug()
            ]);
        }
        return $this->render('startuper/project/add_video.html.twig', [
            'form' => $form->createView(),
            $this->strProject => $project
        ]);
    }

}
