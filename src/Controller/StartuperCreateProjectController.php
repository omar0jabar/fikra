<?php

namespace App\Controller;

use App\Entity\Avantage;
use App\Entity\Faq;
use App\Entity\Project;
use App\Entity\ProjectFinance;
use App\Entity\Service;
use App\Form\ChangeCoverProjectType;
use App\Form\ChangeLogoProjectType;
use App\Form\ChangeVideoProjectType;
use App\Form\QuestionType;
use App\Notification\AdminNotification;
use App\Notification\EntrepreneurNotification;
use App\Repository\ApprovedProjectRepository;
use App\Repository\DocumentTypeRepository;
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
class StartuperCreateProjectController extends Controller
{
    const STARTUPER_PROJECT_SHOW = "startuper_project_show";
    const STARTUPER_PROJECT_COMPLETE = "startuper_project_complete";
    const STARTUPER_PROJECT_EDIT = "startuper_project_edit";

    private $manager;
    private $projectService;
    private $adminNotification;
    private $entrepreneurNotification;
    private $projectRepository;
    private $approvedProjectRepository;
    private $translator;
    private $projectNotFound;
    private $msgAccessDenied;
    private $translationDomainProject = "project";
    private $translationDomainMessage = "messages";
    private $tplFormProject = 'startuper/project/form_project.html.twig';
    private $strDanger = "danger";
    private $strProject = "project";
    private $strAction = "action";
    private $strSteps = "steps";
    private $maxPhotos = 10;

    public function __construct(ObjectManager $manager, ProjectService $projectService,
                                AdminNotification $adminNotification, EntrepreneurNotification $entrepreneurNotification,
                                ProjectRepository $projectRepository,ApprovedProjectRepository $approvedProjectRepository,
                                TranslatorInterface $translator)
    {
        $this->manager = $manager;
        $this->projectService = $projectService;
        $this->adminNotification = $adminNotification;
        $this->entrepreneurNotification = $entrepreneurNotification;
        $this->projectRepository = $projectRepository;
        $this->approvedProjectRepository = $approvedProjectRepository;
        $this->translator = $translator;
        $this->projectNotFound = $this->translator->trans("Project not found", [], $this->translationDomainMessage);
        $this->msgAccessDenied = $this->translator->trans("Access denied", [], $this->translationDomainMessage);
    }

    public function getFormFlow()
    {
        return $this->get('app.form.create_project');
    }

    /**
     * @Route("{_locale}/startuper/project/create", name="startuper_project_create")
     * @Route("/startuper/project/create", name="startuper_project_create_default", defaults={"_locale"="%locale%"})
     * @param Request $request
     * @param StepRepository $stepRepository
     * @return RedirectResponse|Response
     */
    public function createProjectAction(Request $request, StepRepository $stepRepository) {
        $project = new Project();

        $flow = $this->getFormFlow(); // must match the flow's service id
        $flow->bind($project);
        $form = $flow->createForm();
        
        $project->setLanguage($request->getLocale());

        if ($flow->isValid($form)) {

            $flow->saveCurrentStepData($form);

            $name = $request->get('txt-first-service');
            $this->addService($project, $name);

            $error = $this->checkStartup($project);

            if (!$error) {
                $project->setAuthor($this->getUser());
                $project->setStepCreating(2);

                $project->setIsDraft(true);
                $project->setStepCreating(2);
                $this->manager->persist($project);
                $this->manager->flush();

                $flow->reset();

                return $this->redirectToRoute(self::STARTUPER_PROJECT_COMPLETE, [
                    'id' => $project->getId(),
                    'slug' => $project->getSlug()
                ]);
            }

        }

        return $this->render($this->tplFormProject, [
            'form' => $form->createView(),
            'flow' => $flow,
            $this->strProject => $project,
            $this->strAction => 'create',
            $this->strSteps => $stepRepository->findAll()
        ]);
    }

    public function transAndAddFlashWithParam(string $name)
    {
        $field = $this->translator->trans($name, [], $this->translationDomainProject);
        $message = $this->translator->trans("Is required", ['%field%' => $field], $this->translationDomainMessage);
        $this->addFlash($this->strDanger, $message);
    }

    public function transAndAddFlash(string $message)
    {
        $msg = $this->translator->trans($message, [], $this->translationDomainMessage);
        $this->addFlash($this->strDanger, $msg);
    }

    public function checkStartup(Project $project)
    {
        $error = false;
        if (count($project->getSectors()) === 0 && $project->getMoreSectors() === null) {
            $error = true;
            $this->transAndAddFlash("Please select at least one sector");
        }
        if (count($project->getServices()) > 5) {
            $error = true;
            $this->transAndAddFlash("You can add only 5 services to the maximum");
        } else {
            foreach ($project->getServices() as $service) {
                if ($service->getName() === null) {
                    $project->removeAvantage($service);
                } else {
                    $service->setProject($project);
                    $this->manager->persist($service);
                }
            }
        }
        if ($project->getStartup() === true) {
            if (empty($project->getDenomination())) {
                $error = true;
                $this->transAndAddFlashWithParam("form.denomination.name");
            }
            if (empty($project->getCreatingDate())) {
                $error = true;
                $this->transAndAddFlashWithParam("form.creatingDate.name");
            }
            if (empty($project->getRc())) {
                $error = true;
                $this->transAndAddFlashWithParam("form.rc.name");
            }
            if (empty($project->getCity())) {
                $error = true;
                $this->transAndAddFlashWithParam("form.city.name");
            }
        } else {
            $project->setDenomination(null)
                ->setCreatingDate(null)
                ->setRc(null)
                ->setCity(null);
        }
        return $error;
    }

    public function checkStep2(Project $project)
    {
        $error = false;
        if ($project->getOtherSalesChannels() === false) {
            $project->setMoreSalesChannels(null);
        }
        if ($project->getOtherBusinessModel() === false) {
            $project->setMoreBusinessModel(null);
        }
        if ($project->getOtherCountry() === false) {
            $project->setForeignCountry(null);
        }
        if (count($project->getSalesChannels()) === 0 && $project->getMoreSalesChannels() === null) {
            $error = true;
            $this->transAndAddFlash("Please select at least one sales channel");
        }
        if (count($project->getBusinessModels()) === 0 && $project->getMoreBusinessModel() === null) {
            $error = true;
            $this->transAndAddFlash("Please select at least one business model");
        }
        if (count($project->getAvantages()) > 5) {
            $error = true;
            $this->transAndAddFlash("You can add only 5 benefits to the maximum");
        }
        if (count($project->getProjectFinances()) > 5) {
            $error = true;
            $this->transAndAddFlash("You can add only 5 financing details to the maximum");
        }
        if ($error === false) {
            if ($project->getHasNotAmount() === true) {
                $project->setAmount(0);
                $project->setBudget(0);
            }
            foreach ($project->getAvantages() as $avantage) {
                if ($avantage->getName() === null) {
                    $project->removeAvantage($avantage);
                } else {
                    $avantage->setProject($project);
                    $this->manager->persist($avantage);
                }
            }
            foreach ($project->getProjectFinances() as $projectFinance) {
                if ($projectFinance->getDetail() === null) {
                    $project->removeAvantage($projectFinance);
                } else {
                    $projectFinance->setProject($project);
                    $this->manager->persist($projectFinance);
                }
            }
        }
        return $error;
    }

    public function checkProjectTeam(Project $project, int $max)
    {
        $error = false;
        if (count($project->getTeamMembers()) > $max) {
            $error = true;
            $this->transAndAddFlash("You can add only 5 members to the maximum");
        }
        if (count($project->getTeamMembers()) < 1) {
            $error = true;
            $this->transAndAddFlash("Please enter at least the project leader");
        }
        if ($error === false) {
            $array = [];
            foreach ($project->getTeamMembers() as $member) {
                $member->setProject($project);
                $this->manager->persist($member);
                $array[] = $member->getPorteur();
            }
            if (!in_array(true, $array)) {
                $error = true;
                $this->transAndAddFlash("Please choose the project leader");
            }
        }
        return $error;
    }

    public function checkProjectDocuments(Project $project, int $max)
    {
        $error = false;
        if (count($project->getDocuments()) > $max) {
            $error = true;
            $this->transAndAddFlash( "You have exceeded the allowed number");
        } else {
            $array = [];
            foreach ($project->getDocuments() as $document) {
                $type = $document->getDocumentType()->getId();
                if (empty($document->getName()) && empty($document->getFile())) {
                    $project->removeDocument($document);
                } elseif (in_array($type, $array)) {
                    $error = true;
                    $this->transAndAddFlash("You have duplicate a document type");
                } else {
                    $array[] = $type;
                    $document->setProject($project);
                    $this->manager->persist($document);
                }
            }
        }
        return $error;
    }

    public function checkProjectPhotos(Project $project)
    {
        $error = false;
        if (count($project->getGalleryPhotos()) > $this->maxPhotos) {
            $error = true;
            $msg = $this->translator->trans('You can add only 10 photos to the maximum', ['%max%' => $this->maxPhotos]);
            $this->addFlash($this->strDanger, $msg);
        } else {
            foreach ($project->getGalleryPhotos() as $photo) {
                if (empty($photo->getName()) && empty($photo->getImageFile())) {
                    $project->removeGalleryPhoto($photo);
                } else {
                    $photo->setProject($project);
                    $this->manager->persist($photo);
                }
            }
        }
        return $error;
    }

    public function addService(Project $project, string $name)
    {
        $length = strlen($name);
        if (!empty($name) && $length < 255) {
            $service = new Service();
            $service->setName($name);
            $project->addService($service);
        }
    }

    public function addAvantage(Project $project, string $name)
    {
        $length = strlen($name);
        if (!empty($name) && $length < 255) {
            $avantage = new Avantage();
            $avantage->setName($name);
            $project->addAvantage($avantage);
        }
    }

    public function addFinance(Project $project, string $name)
    {
        $length = strlen($name);
        if (!empty($name) && $length < 255) {
            $finance = new ProjectFinance();
            $finance->setDetail($name);
            $project->addProjectFinance($finance);
        }
    }

    /**
     * @Route("{_locale}/startuper/project/create/{id}-{slug}", name="startuper_project_complete")
     * @Route("/startuper/project/create/{id}-{slug}", name="startuper_project_complete_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Project $project
     * @param Request $request
     * @param StepRepository $stepRepository
     * @param Comparison $comparison
     * @param DocumentTypeRepository $documentTypeRepository
     * @return RedirectResponse|Response
     */
    public function completeProjectAction(Project $project, Request $request, StepRepository $stepRepository,
                                          Comparison $comparison, DocumentTypeRepository $documentTypeRepository) {
        if ($project->getIsDeleted()) {
            throw new NotFoundHttpException('project not found');
        }
        $route = $request->get('_route');
        $flow = $this->getFormFlow(); // must match the flow's service id
        $flow->bind($project);

        if ($project->getStepCreating() >= 6) {
            return $this->redirectToRoute('startuper_project_request_validation', [
                'id' => $project->getId(),
                'slug' => $project->getSlug()
            ]);
        }
        $action = "complete";
        $num = $project->getStepCreating();
        for ($i = 1; $i < $num; $i++) {
            $flow->nextStep();
        }
        $max = count($documentTypeRepository->findAll());
        $form = $flow->createForm();
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);
            if ($flow->nextStep()) {
                $currentStep = $flow->getCurrentStepNumber();
                $error = false;
                if ($currentStep === 2) {
                    $name = $request->get('txt-first-service');
                    $this->addService($project, $name);
                    $error = $this->checkStartup($project);
                } elseif ($currentStep === 3) {
                    $name = $request->get('txt-first-avantage');
                    $this->addAvantage($project, $name);
                    $name = $request->get('txt-first-finance');
                    $this->addFinance($project, $name);
                    $error = $this->checkStep2($project);
                } elseif ($currentStep === 4) {
                    $error = $this->checkProjectTeam($project, 5);
                } elseif ($currentStep === 5) {
                    $error = $this->checkProjectDocuments($project, $max);
                }
                if ($error === false) {
                    if ($project->getIsLocked() === true) {
                        throw new AccessDeniedHttpException($this->msgAccessDenied);
                    } else {
                        if ($form->get('saveWithoutSkip1')->isClicked() || $form->get('saveWithoutSkip2')->isClicked()) {
                            $project->setStepCreating($project->getStepCreating());
                        } else {
                            $project->setStepCreating($currentStep);
                        }
                        $this->manager->persist($project);
                        $this->manager->flush();
                    }
                }
                return $this->redirectToRoute($route, [
                    'id' => $project->getId(),
                    'slug' => $project->getSlug()
                ]);
            } else {
                $error = $this->checkProjectPhotos($project);
                if (!$error) {
                    if ($project->getIsLocked() === true) {
                        throw new AccessDeniedHttpException($this->msgAccessDenied);
                    }
                    $this->manager->persist($project);
                    $this->manager->flush();
                    if ($form->get('saveWithoutSkip1')->isClicked() || $form->get('saveWithoutSkip2')->isClicked()) {
                        $project->setStepCreating($project->getStepCreating());
                        $this->manager->flush();
                    } else {
                        $project->setStepCreating(6);
                        $flow->reset();
                        $this->manager->flush();
                        return $this->redirectToRoute('startuper_project_success_create', [
                            'id' => $project->getId(),
                            'slug' => $project->getSlug()
                        ]);
                    }
                }
            }
        }
        return $this->render('startuper/project/form_project.html.twig', [
            'form' => $form->createView(),
            'flow' => $flow,
            $this->strProject => $project,
            $this->strAction => $action,
            $this->strSteps => $stepRepository->findAll(),
            'maxDocuments' => $max,
            'maxPhotos' => $this->maxPhotos
        ]);
    }

    /**
     * @Route("{_locale}/startuper/project/{id}-{slug}/edit", name="startuper_project_edit")
     * @Route("/startuper/project/{id}-{slug}/edit", name="startuper_project_edit_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Project $project
     * @param Request $request
     * @param StepRepository $stepRepository
     * @param Comparison $comparison
     * @param DocumentTypeRepository $documentTypeRepository
     * @return RedirectResponse|Response
     */
    public function editProjectAction(Project $project, Request $request, StepRepository $stepRepository,
                                          Comparison $comparison, DocumentTypeRepository $documentTypeRepository)
    {
        if ($project->getIsDeleted()) {
            throw new NotFoundHttpException('project not found');
        }
        $route = $request->get('_route');
        $flow = $this->getFormFlow(); // must match the flow's service id
        $flow->bind($project);
        $action = "edit";
        $num = $project->getStepUpdating();
        if ($project->getStepUpdating() > 5) {
            $project->setStepUpdating(1);
        }
        for ($i = 1; $i < $num; $i++) {
            $flow->nextStep();
        }
        $max = count($documentTypeRepository->findAll());
        $form = $flow->createForm();
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);
            if ($flow->nextStep()) {
                $currentStep = $flow->getCurrentStepNumber();
                $error = false;
                if ($currentStep === 2) {
                    $name = $request->get('txt-first-service');
                    $this->addService($project, $name);
                    $error = $this->checkStartup($project);
                }
                if ($currentStep === 3) {
                    $name = $request->get('txt-first-avantage');
                    $this->addAvantage($project, $name);
                    $name = $request->get('txt-first-finance');
                    $this->addFinance($project, $name);
                    $error = $this->checkStep2($project);
                } elseif ($currentStep === 4) {
                    $error = $this->checkProjectTeam($project, 5);
                } elseif ($currentStep === 5) {
                    $error = $this->checkProjectDocuments($project, $max);
                }
                if ($error === false) {
                    if ($project->getIsLocked() === true) {
                        throw new AccessDeniedHttpException($this->msgAccessDenied);
                    }
                    if ($form->get('saveWithoutSkip1')->isClicked() || $form->get('saveWithoutSkip2')->isClicked()) {
                        $project->setStepUpdating($project->getStepUpdating());
                    } else {
                        $project->setStepUpdating($currentStep);
                    }
                    $this->manager->persist($project);
                    $this->manager->flush();
                }
                return $this->redirectToRoute($route, [
                    'id' => $project->getId(),
                    'slug' => $project->getSlug()
                ]);
            } else {
                $error = $this->checkProjectPhotos($project);
                if (!$error) {
                    if ($project->getIsLocked() === true) {
                        throw new AccessDeniedHttpException($this->msgAccessDenied);
                    } else {
                        $this->manager->persist($project);
                        $this->manager->flush();
                        if ($form->get('saveWithoutSkip1')->isClicked() || $form->get('saveWithoutSkip2')->isClicked()) {
                            $project->setStepUpdating($project->getStepUpdating());
                            $this->manager->flush();
                        } else {
                            if ($comparison->compareProject($project)) {
                                $this->sendMailsOnUpdateSuccess($project, $request->getLocale());
                                return $this->render('startuper/project/success_update.html.twig', [
                                    $this->strProject => $project,
                                ]);
                            }
                            $flow->reset();
                            return $this->redirectToRoute('startuper_project_check_update', [
                                'id' => $project->getId(),
                                'slug' => $project->getSlug()
                            ]);
                        }
                    }
                }
            }
        }
        return $this->render($this->tplFormProject, [
            'form' => $form->createView(),
            'flow' => $flow,
            $this->strProject => $project,
            $this->strAction => $action,
            $this->strSteps => $stepRepository->findAll(),
            'maxDocuments' => $max,
            'maxPhotos' => $this->maxPhotos
        ]);
    }

    /**
     * @param Project $project
     * @param $locale
     */
    protected function sendMailsOnUpdateSuccess(Project $project, $locale)
    {
        $linkAdmin = $this->generateUrl(
            'sonata_admin_project_compare',
            ['id' => $project->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL);

        $link = $this->generateUrl(
            self::STARTUPER_PROJECT_SHOW,
            ['id' => $project->getId(), 'slug' => $project->getSlug()],
            UrlGeneratorInterface::ABSOLUTE_URL);

        $this->adminNotification->askUpdateProject($project, $linkAdmin, $locale);
        $this->entrepreneurNotification->notifyOnModificationUnderStudy($project, $link, $locale);
    }

}
