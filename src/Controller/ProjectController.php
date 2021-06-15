<?php

namespace App\Controller;

use App\Entity\ApprovedProject;
use App\Entity\Faq;
use App\Entity\Message;
use App\Entity\ProjectLike;
use App\Entity\RequestDocumentation;
use App\Form\MessageType;
use App\Form\QuestionType;
use App\Notification\AdminNotification;
use App\Notification\EntrepreneurNotification;
use App\Repository\ApprovedDocumentRepository;
use App\Repository\ApprovedProjectRepository;
use App\Repository\DocumentTypeRepository;
use App\Repository\FundingObjectiveRepository;
use App\Repository\HeaderRepository;
use App\Repository\ProjectLikeRepository;
use App\Repository\ProjectRepository;
use App\Repository\RequestDocumentationRepository;
use App\Repository\SectorRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\Image as ImageService;

/**
 * Class ProjectController
 * @package App\Controller
 */
class ProjectController extends AbstractController
{

    private $manager;
    private $projectRepository;
    private $approvedProjectRepository;
    private $approvedDocumentRepository;
    private $projectLikeRepository;
    private $translator;
    private $msgProjectNotFound;
    private $msgUnauthorized;
    private $translationDomain = "messages";
    private $strOffset = "offset";
    private $strMessage = "message";
    private $strProject = "project";
    private $strProjects = "projects";
    private $strProjectPrints = "projectsPrints";
    private $strProjectView = "projectsView";
    private $strCurrentMenu = "current_menu";
    private $strOrder = "order";
    private $strSearch = "search";
    private $strVerified = "verified";
    private $strLanguage = "language";
    private $strCountResults = "countResults";
    private $strShowMore = "showMore";
    private $strSectors = "sectors";
    private $strSession = "session";

    public function __construct(
        ObjectManager $manager,
        ProjectRepository $projectRepository,
        ApprovedProjectRepository $approvedProjectRepository,
        ApprovedDocumentRepository $approvedDocumentRepository,
        ProjectLikeRepository $projectLikeRepository,
        TranslatorInterface $translator
    ) {
        $this->manager = $manager;
        $this->projectRepository = $projectRepository;
        $this->approvedProjectRepository = $approvedProjectRepository;
        $this->approvedDocumentRepository = $approvedDocumentRepository;
        $this->projectLikeRepository = $projectLikeRepository;
        $this->translator = $translator;
        $this->msgProjectNotFound = $this->translator->trans("Project not found", [], $this->translationDomain);
        $this->msgUnauthorized = $this->translator->trans("Unauthorized", [], $this->translationDomain);
    }

    /**
     * @Route("{_locale}/projects/more-projects", name="project_more_projects")
     * @Route("/projects/more-projects", name="project_more_projects_default", defaults={"_locale"="%locale%"})
     * @param Request $request
     * @param FundingObjectiveRepository $fundingObjectiveRepository
     * @return Response
     */
    public function moreProjects(Request $request, FundingObjectiveRepository $fundingObjectiveRepository)
    {
        $arraySectors = $request->query->get($this->strSectors);
        $offset = (int)$request->query->get($this->strOffset);
        $order = (int)$request->query->get($this->strOrder);
        $verified = $request->query->get($this->strVerified);
        $idRaised = (int)$request->query->get("raised");
        $language = $request->query->get($this->strLanguage);
        $search = $request->query->get($this->strSearch);

        $sectors = !empty($arraySectors) ? implode(",", $arraySectors) : null;
        $offset = !empty($offset) ? $offset : 0;
        $order = !empty($order) ? $order : null;
        $language = !empty($language) ? $language : null;
        $search = !empty($search) ? $search : null;

        $raised = $fundingObjectiveRepository->findOneBy(['id' => $idRaised]);
        $array = [];
        if ($raised) {
            $array['min'] = $raised->getMin();
            $array['max'] = $raised->getMax();
        }

        $projects = $this->approvedProjectRepository->getProjects($sectors, $verified, $array, $language, $search, $offset, $order);
        $results = $this->approvedProjectRepository->getProjectsForCount($sectors, $verified, $array, $language, $search, $order);
        $countResults = count($results);
        $moreProjects = $this->approvedProjectRepository->getProjects($sectors, $verified, $array, $language, $search, $offset + 6, $order);

        if (count($moreProjects) > 0) {
            $showMore = 1;
        } else {
            $showMore = 0;
        }

        $session = $this->get($this->strSession);
        $projectsPrints = $session->get($this->strProjectPrints);
        if ($projectsPrints === null) {
            $session->set($this->strProjectPrints, []);
            $projectsPrints = $session->get($this->strProjectPrints);
        }

        foreach ($projects as $approvedProject) {
            /* @var ApprovedProject $approvedProject */
            $project = $approvedProject->getProject();

            if (!in_array($project->getId(), $projectsPrints)) {
                $projectsPrints[] = $project->getId();
                $session->set($this->strProjectPrints, $projectsPrints);

                $count = $project->getPrints();
                $project->setPrints($count + 1);
                $this->manager->flush();
            }
        }

        return $this->render('project/_results.html.twig', [
            $this->strProjects => $projects,
            $this->strShowMore => $showMore,
            $this->strOffset => $offset,
            $this->strCountResults => $countResults,
            'more' => $showMore,
        ]);
    }

    /**
     * @Route("{_locale}/projects/{id}-{slug}", name="project_show")
     * @Route("/projects/{id}-{slug}", name="project_show_default", defaults={"_locale"="%locale%"})
     * @param int $id
     * @param string $slug
     * @param Request $request
     * @param DocumentTypeRepository $documentTypeRepository
     * @return Response
     */
    public function ShowProject(
        int $id,
        string $slug,
        Request $request,
        DocumentTypeRepository $documentTypeRepository,
        ImageService $imageService
    )
    {
        $approvedProject = $this->approvedProjectRepository->findOneBy([$this->strProject => $id, 'slug' => $slug]);
        if (!$approvedProject || $approvedProject->getIsDeleted()) {
            $message = $this->msgProjectNotFound;
            throw new NotFoundHttpException($message);
        }

        $project = $approvedProject->getProject();

        $faq = new Faq();
        $form = $this->createForm(QuestionType::class, $faq);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $faq->setAuthor($this->getUser())
                ->setProject($project)
                ->setSeen(false);
            $this->manager->persist($faq);
            $this->manager->flush();
            $message = $this->translator->trans("Your question has been sent successfully", [], $this->translationDomain);
            $this->addFlash('success', $message);
        }

        $session = $this->get($this->strSession);
        $projectsView = $session->get($this->strProjectView);
        if ($projectsView === null) {
            $session->set($this->strProjectView, []);
            $projectsView = $session->get($this->strProjectView);
        }

        if (!in_array($project->getId(), $projectsView)) {
            $projectsView[] = $project->getId();
            $session->set($this->strProjectView, $projectsView);

            $views = $project->getViews();
            $project->setViews($views + 1);
            $this->manager->flush();
        }

        $sectors = [];
        foreach ($approvedProject->getSectors() as $sector) {
            $sectors[] = $sector->getId();
        }
        $metaTitle = ($project->getMetaTitle() !== null) ?  $project->getMetaTitle() : $project->getName();
        $metaDescription = ($project->getMetaDescription() !== null) ?  $project->getMetaDescription() : $project->getDescription();
        $width = null;
        $height = null;
        if ($project->getImageCoverName()) {
            list($width,$height) = $imageService->getimagesize('project', $project->getImageCoverName());
        }
        return $this->render('project/show.html.twig', [
            $this->strProject => $approvedProject,
            'approvedProject' => true,
            $this->strCurrentMenu => $this->strProjects,
            'questions' => $project->getFAQs(),
            'form' => $form->createView(),
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'width' => $width,
            'height' => $height,
            'documentTypes' => $documentTypeRepository->findAll(),
            'projects' => $this->approvedProjectRepository->getSimilarProjects(implode(",", $sectors), $approvedProject->getId())
        ]);
    }

    //@Route("{_locale}/projects/{id}", name="project_list_by_sector", requirements={"_locale": "\w{2,3}"})

    /**
     * @Route("{_locale}/projects", name="project_list")
     * @Route("/projects", name="project_list_default", defaults={"_locale"="%locale%"})
     * @param SectorRepository $repoSector
     * @param HeaderRepository $headerRepository
     * @param FundingObjectiveRepository $fundingObjectiveRepository
     * @param Request $request
     * @return Response
     */
    public function listProject(SectorRepository $repoSector,
                                HeaderRepository $headerRepository, Request $request,
                                FundingObjectiveRepository $fundingObjectiveRepository)
    {
        $idSlug = $request->get($this->strSectors);
        $sector = $repoSector->findOneBy(['id' => $idSlug]);
        $idSlug = $sector !== null ? $sector->getId() : null;

        $offset = 0;
        $order = null;

        $projects = $this->approvedProjectRepository->getProjects($idSlug, null, [], null, null, $offset, $order);
        $allProjects = $this->approvedProjectRepository->getProjectsForCount($idSlug, null, [], null, null);
        $countResults = count($allProjects);
        $moreProjects = $this->approvedProjectRepository->getProjects($idSlug, null, [], null, null, $offset + 6, $order);

        if (count($moreProjects) > 0) {
            $showMore = 1;
        } else {
            $showMore = 0;
        }

        $session = $this->get($this->strSession);
        $projectsPrints = $session->get($this->strProjectPrints);
        if ($projectsPrints === null) {
            $session->set($this->strProjectPrints, []);
            $projectsPrints = $session->get($this->strProjectPrints);
        }

        foreach ($projects as $approvedProject) {
            /* @var ApprovedProject $approvedProject */
            $project = $approvedProject->getProject();

            if (!in_array($project->getId(), $projectsPrints)) {
                $projectsPrints[] = $project->getId();
                $session->set($this->strProjectPrints, $projectsPrints);

                $count = $project->getPrints();
                $project->setPrints($count + 1);
                $this->manager->flush();
            }
        }

        // header
        $locale = $request->getLocale();
        $header = $headerRepository->findOneBy(['page' => $this->strProjects, 'lang' => $locale]);

        return $this->render('project/list.html.twig', [
            $this->strCurrentMenu => $this->strProjects,
            'header' => $header,
            $this->strSectors => $repoSector->findAll(),
            'choicesSectors' => [$idSlug],
            'current_sectors' => $idSlug,
            $this->strSearch => null,
            $this->strVerified => null,
            'amount' => null,
            $this->strLanguage => null,
            $this->strOrder => null,
            'choicesFundingObjectives' => $fundingObjectiveRepository->findAll(),
            $this->strCountResults => $countResults,
            $this->strProjects => $projects,
            $this->strShowMore => $showMore,
            $this->strOffset => 6,
            'more' => $showMore,
        ]);
    }

    /**
     * @Route("{_locale}/search/projects", name="project_search")
     * @Route("/search/projects", name="project_search_default", defaults={"_locale"="%locale%"})
     * @param SectorRepository $repoSector
     * @param HeaderRepository $headerRepository
     * @param FundingObjectiveRepository $fundingObjectiveRepository
     * @param Request $request
     * @return Response
     */
    public function searchProjects(SectorRepository $repoSector,
                                   HeaderRepository $headerRepository, Request $request,
                                   FundingObjectiveRepository $fundingObjectiveRepository)
    {
        $arraySectors = $request->query->get($this->strSectors);
        $offset = (int)$request->query->get($this->strOffset);
        $order = (int)$request->query->get($this->strOrder);
        $verified = $request->query->get($this->strVerified);
        $idRaised = (int)$request->query->get("raised");
        $language = $request->query->get($this->strLanguage);
        $search = $request->query->get($this->strSearch);
        $sectors = !empty($arraySectors) ? implode(",", $arraySectors) : null;

        $offset = !empty($offset) ? $offset : 0;
        $order = !empty($order) ? $order : 1;
        $language = !empty($language) ? $language : null;
        $search = !empty($search) ? $search : null;

        $raised = $fundingObjectiveRepository->findOneBy(['id' => $idRaised]);
        $array = [];

        if ($raised) {
            $array['min'] = $raised->getMin();
            $array['max'] = $raised->getMax();
        }

        $projects = $this->approvedProjectRepository->getProjects($sectors, $verified, $array, $language, $search, $offset, $order);
        $allProjects = $this->approvedProjectRepository->getProjectsForCount($sectors, $verified, $array, $language, $search);
        $countResults = count($allProjects);
        $moreProjects = $this->approvedProjectRepository->getProjects($sectors, $verified, $array, $language, $search, $offset + 6, $order);

        if (count($moreProjects) > 0) {
            $showMore = 1;
        } else {
            $showMore = 0;
        }

        $session = $this->get($this->strSession);
        $projectsPrints = $session->get($this->strProjectPrints);
        if ($projectsPrints === null) {
            $session->set($this->strProjectPrints, []);
            $projectsPrints = $session->get($this->strProjectPrints);
        }

        foreach ($projects as $approvedProject) {
            /* @var ApprovedProject $approvedProject */
            $project = $approvedProject->getProject();

            if (!in_array($project->getId(), $projectsPrints)) {
                $projectsPrints[] = $project->getId();
                $session->set($this->strProjectPrints, $projectsPrints);

                $count = $project->getPrints();
                $project->setPrints($count + 1);
                $this->manager->flush();
            }
        }

        // header
        $locale = $request->getLocale();
        $header = $headerRepository->findOneBy(['page' => $this->strProjects, 'lang' => $locale]);

        return $this->render('project/list.html.twig', [
            $this->strCurrentMenu => $this->strProjects,
            'header' => $header,
            $this->strSectors => $repoSector->findAll(),
            'choicesSectors' => $arraySectors,
            'current_sectors' => $sectors,
            $this->strSearch => $search,
            $this->strVerified => $verified,
            'amount' => $idRaised,
            $this->strLanguage => $language,
            $this->strOrder => $order,
            'choicesFundingObjectives' => $fundingObjectiveRepository->findAll(),
            $this->strCountResults => $countResults,
            $this->strProjects => $projects,
            $this->strShowMore => $showMore,
            $this->strOffset => 6,
            'more' => false,
        ]);
    }

    /**
     * @Route("{_locale}/projects/{id}-{slug}/contact", name="project_contact")
     * @Route("/projects/{id}-{slug}/contact", name="project_contact_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER')", message="you have not access to this page.")
     * @param Int $id
     * @param string $slug
     * @param Request $request
     * @param EntrepreneurNotification $notification
     * @return Response
     */
    public function sendMessage(Int $id, string $slug, Request $request, EntrepreneurNotification $notification)
    {
        $approvedProject = $this->approvedProjectRepository->findOneBy([$this->strProject => $id, 'slug' => $slug]);
        if (!$approvedProject || $approvedProject->getIsDeleted()) {
            $message = $this->msgProjectNotFound;
            throw new NotFoundHttpException($message);
        }

        if ($approvedProject->getAuthor() === $this->getUser()) {
            $message = $this->translator->trans("You have not access", [], $this->translationDomain);
            throw new AccessDeniedException($message);
        }

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setProject($approvedProject->getProject())
                ->setAuthor($this->getUser())
                ->setSeen(false);
            $this->manager->persist($message);
            $this->manager->flush();
            $local = $request->getLocale();
            $link = $this->generateUrl('startuper_project_messages', [
                'id' => $approvedProject->getProject()->getId(),
                'slug' => $approvedProject->getSlug()
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            $notification->notifyOnProjectReceiveMessage($approvedProject, $this->getUser(), $link, $local);
            $message = $this->translator->trans("Your message has been sent successfully", [], $this->translationDomain);
            $this->addFlash('success', $message);
            return $this->redirectToRoute('project_show', [
                'id' => $approvedProject->getProject()->getId(),
                'slug' => $slug
            ]);
        }

        return $this->render('project/contact.html.twig', [
            'form' => $form->createView(),
            $this->strProject => $approvedProject,
        ]);

    }

    /**
     * @Route("{_locale}/projects/{slug}/make-request-documentation", name="make_request_documentation")
     * @Route("/projects/{slug}/make-request-documentation", name="make_request_documentation_default", defaults={"_locale"="%locale%"})
     * @param ApprovedProject $approvedProject
     * @param Request $request
     * @param RequestDocumentationRepository $requestDocumentationRepository
     * @param AdminNotification $adminNotification
     * @param EntrepreneurNotification $entrepreneurNotification
     * @param DocumentTypeRepository $documentTypeRepository
     * @return Response
     */
    public function sendRequestDocumentation(ApprovedProject $approvedProject, Request $request,
                                             RequestDocumentationRepository $requestDocumentationRepository,
                                             AdminNotification $adminNotification,
                                             EntrepreneurNotification $entrepreneurNotification,
                                             DocumentTypeRepository $documentTypeRepository)
    {

        $token = $request->get('token');
        $message = $request->get($this->strMessage);
        $typesIds = $request->get('ids');
        $accept = (int)$request->get('accept');
        $email = $this->getUser()->getEmail();

        $errors = false;
        $error = null;
        $code = null;

        if (!$accept) {
            $errors = true;
            $error = $this->translator->trans("You must accept Confidentiality & PI engagement", [], $this->translationDomain);
            $code = 403;
        }

        if (!$approvedProject || $approvedProject->getIsDeleted()) {
            $errors = true;
            $error = $this->msgProjectNotFound;
            $code = 403;
        }

        if (!$this->getUser()) {
            $errors = true;
            $error = $this->msgUnauthorized;
            $code = 403;
        }
        if ($approvedProject->getAuthor()->getEmail() === $email) {
            $errors = true;
            $error = $this->translator->trans("The author of the project has no right to request the documentation of his project", [], "messages");
            $code = 403;
        }
        if (!$this->isCsrfTokenValid('make-request-documentation', $token)) {
            $errors = true;
            $error = $this->translator->trans("Token invalid", [], $this->translationDomain);
            $code = 500;
        }
        if (empty($typesIds) && empty($message)) {
            $errors = true;
            $error = $this->translator->trans("Your request can not be null", [], $this->translationDomain);
            $code = 403;
        }

        $oldRequest = $requestDocumentationRepository->findOneBy([
            'user' => $this->getUser(),
            $this->strProject => $approvedProject->getProject()
        ], ['id' => 'DESC']);

        if ($oldRequest) {
            $errors = true;
            $error = $this->translator->trans("You already have a request", [], $this->translationDomain);
            $code = 500;
        }

        if ($errors) {
            return $this->json([
                $this->strMessage => $error
            ], $code);
        }

        $requestDoc = new RequestDocumentation();
        $requestDoc->setUser($this->getUser())
            ->setProject($approvedProject->getProject())
            ->setMessage($message)
            ->setIsAccepted(null);
        if (!empty($typesIds)) {
            foreach ($typesIds as $type) {
                $document = $documentTypeRepository->findOneBy(['id' => $type]);
                $requestDoc->addDocument($document);
            }
        }
        $this->manager->persist($requestDoc);
        $this->manager->flush();

        $requestLink = $this->generateUrl("sonata_admin_request_documentation_show", [
            'id' => $requestDoc->getId()
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $projectLink = $this->generateUrl("project_show", [
            'id' => $approvedProject->getProject()->getId(),
            'slug' => $approvedProject->getSlug(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        if (count($approvedProject->getDocuments()) == 0) {
            $entrepreneurNotification->askDocumentation($approvedProject, $projectLink, $request->getLocale());
        }
        $adminNotification->askDocumentation($approvedProject, $projectLink, $this->getUser(), $requestLink, $request->getLocale());
        $entrepreneurNotification->askDocumentInProgress($approvedProject, $projectLink, $this->getUser(), $request->getLocale());
        return $this->json([
            $this->strMessage => $this->translator->trans("Your request has been successfully sent", [], $this->translationDomain)
        ], 200);
    }

    /**
     * @Route("{_locale}/projects/{slug}/send-message", name="send_message_project")
     * @Route("/projects/{slug}/send-message", name="send_message_project_default", defaults={"_locale"="%locale%"})
     * @param ApprovedProject $approvedProject
     * @param Request $request
     * @param EntrepreneurNotification $notification
     * @return Response
     */
    public function sendMessageFromPopup(ApprovedProject $approvedProject, Request $request,
                                         EntrepreneurNotification $notification)
    {
        $token = $request->get('token');
        $object = $request->get('object');
        $message = $request->get($this->strMessage);
        $email = $this->getUser()->getEmail();

        $error = false;
        $msg = null;
        $code = null;

        if (!$approvedProject || $approvedProject->getIsDeleted()) {
            $msg = $this->msgProjectNotFound;
            $error = true;
            $code = 404;
        }
        if (!$this->getUser()) {
            $error = true;
            $msg = $this->msgUnauthorized;
            $code = 403;
        } elseif ($approvedProject->getAuthor()->getEmail() === $email) {
            $error = true;
            $msg = $this->translator->trans("The author of the project has no right to request the documentation of his project", [], $this->translationDomain);
            $code = 403;
        } elseif (!$this->isCsrfTokenValid('send-message', $token)) {
            $error = true;
            $msg = $this->translator->trans("Token invalid", [], $this->translationDomain);
            $code = 500;
        }

        if ($error) {
            return $this->json([
                $this->strMessage => $msg,
            ], $code);
        }

        $messageSend = new Message();
        $messageSend
            ->setObject($object)
            ->setContent($message)
            ->setAuthor($this->getUser())
            ->setProject($approvedProject->getProject())
            ->setSeen(false);
        $this->manager->persist($messageSend);
        $this->manager->flush();

        $local = $request->getLocale();
        $link = $this->generateUrl('startuper_project_messages', [
            'id' => $approvedProject->getProject()->getId(),
            'slug' => $approvedProject->getSlug()
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        $notification->notifyOnProjectReceiveMessage($approvedProject, $this->getUser(), $link, $local);
        return $this->json([
            $this->strMessage => $this->translator->trans("Your message has been sent successfully", [], $this->translationDomain)
        ], 200);
    }

    /**
     * @Route("{_locale}/projects/{id}/like", name="project_like")
     * @Route("/projects/{id}/like", name="project_like_default", defaults={"_locale"="%locale%"})
     * @param ApprovedProject $approvedProject
     * @return Response
     */
    public function like(ApprovedProject $approvedProject)
    {
        $error = false;
        $msg = null;
        $code = null;
        if (!$approvedProject || $approvedProject->getIsDeleted()) {
            $error = true;
            $msg = $this->msgProjectNotFound;
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
                $this->strMessage => $msg
            ], $code);
        }
        $project = $approvedProject->getProject();
        if ($project->isLikedByUser($user)) {
            $like = $this->projectLikeRepository->findOneBy([$this->strProject => $project, 'user' => $user]);
            $this->manager->remove($like);
            $this->manager->flush();
            return $this->json([
                'code' => 200,
                $this->strMessage => $this->translator->trans('Like removed', [], $this->translationDomain),
                'label' => $this->translator->trans('Like', [], $this->strProject),
                'likes' => $this->projectLikeRepository->count([$this->strProject => $project])
            ], 200);
        }

        $like = new ProjectLike();
        $like->setProject($project)
            ->setUser($user);
        $this->manager->persist($like);
        $this->manager->flush();
        return $this->json([
            'code' => 200,
            $this->strMessage => $this->translator->trans('Like added', [], $this->translationDomain),
            'label' => $this->translator->trans('Unlike', [], $this->strProject),
            'likes' => $this->projectLikeRepository->count([$this->strProject => $project])
        ], 200);
    }

    /**
     * @Route("{_locale}/request/documentation/{id}/{idDocument}", name="investor_download_documentation")
     * @Route("/request/documentation/{id}/{idDocument}", name="investor_download_documentation_default", defaults={"_locale"="%locale%"})
     * @param RequestDocumentation $requestDocumentation
     * @param int $idDocument
     * @return Response
     * @throws \Exception
     */
    public function downloadDocument(RequestDocumentation $requestDocumentation, int $idDocument)
    {
        if (!$requestDocumentation) {
            throw new NotFoundHttpException($this->translator->trans('Request not found'));
        }
        $now = new \DateTime();
        $acceptedAt = $requestDocumentation->getAcceptedAt();
        $diff = $acceptedAt->diff($now);
        if ($diff->days >= 7) {
            throw new AccessDeniedHttpException($this->translator->trans("Download link has expired"));
        }
        $project = $requestDocumentation->getProject();
        $approvedProject = $this->approvedProjectRepository->findOneBy([$this->strProject => $project]);
        if (!$approvedProject) {
            throw new NotFoundHttpException($this->msgProjectNotFound);
        }
        foreach ($approvedProject->getDocuments() as $document) {
            if ($document->getId() === $idDocument) {
                $file = "upload/request-doc-accepted/" . $document->getName();
                if (is_file($file)) {
                    return $this->file($file);
                }
                throw new NotFoundHttpException("file not exist");
            }
        }
        throw new NotFoundHttpException($this->translator->trans('Document not found'));
    }

    /**
     * @Route("{_locale}/download/document/{id}", name="investor_download_document_public")
     * @Route("/download/document/{id}", name="investor_download_document_public_default", defaults={"_locale"="%locale%"})
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Exception
     */
    public function downloadDocumentPublic(int $id)
    {
        $document = $this->approvedDocumentRepository->find($id);
        if (!$document) {
            throw new NotFoundHttpException($this->translator->trans('Document not found'));
        }

        if ($document->getIsPrivate()) {
            throw new AccessDeniedException($this->translator->trans('This document is reserved for users authorized by the project leader'));
        }

        $approvedProject = $document->getApprovedProject();
        if (!$approvedProject) {
            throw new NotFoundHttpException($this->msgProjectNotFound);
        }

        $file = "upload/approved-projects/approved-project-documents/" . $document->getName();
        if (is_file($file)) {
            return $this->file($file);
        }
        throw new NotFoundHttpException("file not exist");

    }

}
