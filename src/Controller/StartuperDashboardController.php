<?php

namespace App\Controller;

use App\Entity\MessageResponse;
use App\Entity\Project;
use App\Form\MessageResponseType;
use App\Form\ResponseType;
use App\Notification\EntrepreneurNotification;
use App\Repository\ApprovedProjectRepository;
use App\Repository\FaqRepository;
use App\Repository\MessageRepository;
use App\Repository\ProjectRepository;
use Doctrine\Common\Persistence\ObjectManager;
use EgioDigital\CMSBundle\Repository\ArticlePublishedRepository;
use App\Repository\CompanyRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class StartuperDashboardController
 * @package App\Controller
 */
class StartuperDashboardController extends Controller
{
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
     * @var ApprovedProjectRepository
     */
    private $approvedProjectRepository;
    private $notification;
    private $translator;
    private $translationDomain = "messages";
    private $strMessages = "messages";
    private $strProjects = "projects";
    private $strProject = "project";
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    public function __construct(
        CompanyRepository $companyRepository,
        ProjectRepository $projectRepository,
        ArticlePublishedRepository $articleRepository,
        MessageRepository $messageRepository,
        FaqRepository $faqRepository,
        EntrepreneurNotification $notification,
        TranslatorInterface $translator,
        ApprovedProjectRepository $approvedProjectRepository
    ) {
        $this->companyRepository = $companyRepository;
        $this->projectRepository = $projectRepository;
        $this->messageRepository = $messageRepository;
        $this->faqRepository = $faqRepository;
        $this->articlePublishedRepository = $articleRepository;
        $this->approvedProjectRepository = $approvedProjectRepository;
        $this->notification = $notification;
        $this->translator = $translator;
    }

    /**
     * @Route("{_locale}/startuper/project/dashboard", name="startuper_project_dashboard")
     * @Route("/startuper/project/dashboard", name="startuper_project_dashboard_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER')",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Request $request
     * @return Response
     */
    public function dashboard(Request $request)
    {
        $locale = $request->getLocale();
        $projects = $this->projectRepository->findAllByStartupNotDeleted($this->getUser());
        $lastProject = null;
        if (count($projects) > 0) {
            $lastProject = $projects[count($projects) - 1];
        }
        $companies = $this->companyRepository->findAllByStartupNotDeleted($this->getUser());
        $messages = $this->messageRepository->findBy([$this->strProject => $lastProject]);
        return $this->render('startuper/dashboard/dashboard.html.twig', [
            $this->strProjects => $projects,
            "companies" => $companies,
            $this->strProject => $lastProject,
            "company" => null,
            $this->strMessages => $messages,
            'articles' => $this->articlePublishedRepository->getThreeArticlesSuggestion($locale),
        ]);
    }

    /**
     * @Route("{_locale}/startuper/project/dashboard/{id}-{slug}", name="startuper_project_dashboard_show")
     * @Route("/startuper/project/dashboard/{id}-{slug}", name="startuper_project_dashboard_show_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Project $project
     * @param Request $request
     * @return Response
     */
    public function dashboardProjectShow(Project $project, Request $request)
    {
        $project = $this->projectRepository->findOneNotDeletedByStartuper($project->getId(), $this->getUser());

        if (!$project) {
            throw new NotFoundHttpException($this->translator->trans("Project not found", [], $this->translationDomain));
        }
        $locale = $request->getLocale();
        $projects = $this->projectRepository->findAllByStartupNotDeleted($this->getUser());
        $companies = $this->companyRepository->findAllByStartupNotDeleted($this->getUser());
        $messages = $this->messageRepository->findBy([$this->strProject => $project]);
        return $this->render('startuper/dashboard/dashboard.html.twig', [
            $this->strProjects => $projects,
            "companies" => $companies,
            $this->strProject => $project,
            "company" => null,
            $this->strMessages => $messages,
            'articles' => $this->articlePublishedRepository->getThreeArticlesSuggestion($locale),
        ]);
    }

    /**
     * @Route("{_locale}/startuper/project/dashboard/{id}-{slug}/questions", name="startuper_project_dashboard_questions")
     * @Route("/startuper/project/dashboard/{id}-{slug}/questions", name="startuper_project_dashboard_questions_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Project $project
     * @return Response
     */
    public function questions(Project $project)
    {
        return $this->render('startuper/dashboard/questions.html.twig', [
            $this->strProjects => $this->projectRepository->findAllByStartupNotDeleted($this->getUser()),
            $this->strProject => $project,
        ]);
    }

    /**
     * @Route("{_locale}/startuper/project/dashboard/{id}-{slug}/question/{idFaq}", name="startuper_project_dashboard_response")
     * @Route("/startuper/project/dashboard/{id}-{slug}/question/{idFaq}", name="startuper_project_dashboard_response_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param int $idFaq
     * @param Project $project
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function replyQuestion(Project $project, int $idFaq, Request $request, ObjectManager $manager)
    {
        $faq = $this->faqRepository->findOneBy(['id' => $idFaq], ["createdAt" => "DESC"]);
        $faq->setSeen(true);
        $manager->flush();

        $form = $this->createForm(ResponseType::class, $faq);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            $this->addFlash(
                'success',
                $this->translator->trans("Your answer has been successfully registered", [], $this->translationDomain)
            );
            return $this->redirectToRoute('startuper_project_dashboard_questions', [
                'id' => $project->getId(),
                'slug' => $project->getSlug()
            ]);
        }

        return $this->render('startuper/dashboard/response.html.twig', [
            $this->strProjects => $this->projectRepository->findAllByStartupNotDeleted($this->getUser()),
            $this->strProject => $project,
            'question' => $faq,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("{_locale}/startuper/project/dashboard/{id}-{slug}/messages", name="startuper_project_messages")
     * @Route("/startuper/project/dashboard/{id}-{slug}/messages", name="startuper_project_messages_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param Project $project
     * @param Request $request
     * @return Response
     */
    public function messages(Project $project, Request $request)
    {
        $query = $this->messageRepository->getAllQuery($project);
        $paginator  = $this->get('knp_paginator');
        $page = $request->query->getInt('page', 1);
        $pagination = $paginator->paginate(
            $query,
            $page,
            10
        );
        return $this->render('startuper/dashboard/messages.html.twig', [
            $this->strProjects => $this->projectRepository->findAllByStartupNotDeleted($this->getUser()),
            $this->strProject => $project,
            'pagination' => $pagination,
            'company' => null,
            'companies' => $this->companyRepository->findAllByStartupNotDeleted($this->getUser()),
        ]);
    }

    /**
     * @Route("{_locale}/startuper/project/dashboard/{id}-{slug}/message/{idMessage}", name="startuper_project_reply_message")
     * @Route("/startuper/project/dashboard/{id}-{slug}/message/{idMessage}", name="startuper_project_reply_message_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER') and user === project.getAuthor()",
     *    message="Ce Projet ne vous appartient pas."
     * )
     * @param int $idMessage
     * @param Project $project
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function replyMessage(Project $project, int $idMessage, Request $request, ObjectManager $manager)
    {
        $message = $this->messageRepository->findOneBy(['id' => $idMessage]);
        $message->setSeen(true);
        foreach ($message->getResponses() as $response) {
            if ($response->getUser() != $this->getUser()) {
                $response->setSeen(true);
            }
        }
        $manager->flush();

        $response = new MessageResponse();
        $response->setMessage($message)
            ->setUser($this->getUser());
        $form = $this->createForm(MessageResponseType::class, $response);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($response);
            $manager->flush();
            $approvedProject = $this->approvedProjectRepository->findOneBy([$this->strProject => $project]);
            $projectLink = $this->generateUrl('project_show', [
                'id' => $approvedProject->getProject()->getId(),
                'slug' => $approvedProject->getSlug()
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            $messagesLink = $this->generateUrl('investor_messages', []
                , UrlGeneratorInterface::ABSOLUTE_URL);
            $this->notification->notifyInvestorOnResponse(
                $approvedProject, $projectLink, $message, $messagesLink,
                $message->getAuthor(), $request->getLocale());
            $this->addFlash(
                'success',
                $this->translator->trans("Your message has been sent successfully", [], $this->translationDomain)
            );
            return $this->redirectToRoute('startuper_project_messages', [
                'id' => $project->getId(),
                'slug' => $project->getSlug()
            ]);
        }

        return $this->render('startuper/dashboard/message.html.twig', [
            $this->strProjects => $this->projectRepository->findAllByStartupNotDeleted($this->getUser()),
            $this->strProject => $project,
            'message' => $message,
            'form' => $form->createView()
        ]);
    }
}
