<?php

namespace  App\Controller;

use App\Entity\Message;
use App\Entity\MessageResponse;
use App\Form\MessageResponseType;
use App\Notification\EntrepreneurNotification;
use App\Repository\CompanyRepository;
use App\Repository\MessageRepository;
use App\Repository\ProjectRepository;
use Doctrine\Common\Persistence\ObjectManager;
use EgioDigital\CMSBundle\Repository\ArticlePublishedRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class InvestorController
 * @package App\Controller
 */
class InvestorController extends Controller
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
     * @var ArticlePublishedRepository
     */
    private $articlePublishedRepository;
    /**
     * @var ObjectManager
     */
    private $manager;
    /**
     * @var EntrepreneurNotification
     */
    private $notification;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * InvestorController constructor.
     *
     * @param MessageRepository $messageRepository
     * @param ObjectManager $manager
     * @param ProjectRepository $projectRepository
     * @param ArticlePublishedRepository $articlePublishedRepository
     * @param TranslatorInterface $translator
     * @param EntrepreneurNotification $notification
     * @param CompanyRepository $companyRepository
     */
    public function __construct(
        MessageRepository $messageRepository,
        ObjectManager $manager,
        ProjectRepository $projectRepository,
        ArticlePublishedRepository $articlePublishedRepository,
        TranslatorInterface $translator,
        EntrepreneurNotification $notification,
        CompanyRepository $companyRepository
    ) {
        $this->projectRepository = $projectRepository;
        $this->messageRepository = $messageRepository;
        $this->articlePublishedRepository = $articlePublishedRepository;
        $this->manager = $manager;
        $this->notification = $notification;
        $this->translator = $translator;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @Route("{_locale}/startuper/messages", name="investor_messages")
     * @Route("/startuper/messages", name="investor_messages_default", defaults={"_locale"="%locale%"})
     * @Security("is_granted('ROLE_STARTUPER')")
     * @param Request $request
     * @return Response
     */
    public function messages(Request $request)
    {
        $query = $this->messageRepository->getAllQueryByInvestor($this->getUser());
        $paginator  = $this->get('knp_paginator');
        $page = $request->query->getInt('page', 1);
        $pagination = $paginator->paginate(
            $query,
            $page,
            10
        );
        return $this->render('investor/messages.html.twig', [
            'project' => null,
            'projects' => $this->projectRepository->findAllByStartupNotDeleted($this->getUser()),
            'company' => null,
            'companies' => $this->companyRepository->findAllByStartupNotDeleted($this->getUser()),
            'pagination' => $pagination,
            'articles' => $this->articlePublishedRepository->getThreeArticlesSuggestion($request->getLocale()),
        ]);
    }

    /**
     * @Route("{_locale}/startuper/messages/{id}/reply", name="investor_reply_message")
     * @Route("/startuper/messages/{id}/reply", name="investor_reply_message_default", defaults={"_locale"="%locale%"})
     * @param  Message $message
     * @param  Request $request
     * @return Response
     */
    public function replyMessage(Message $message, Request $request)
    {
        if (!$message) {
            throw new NotFoundHttpException($this->translator->trans('Message not found'));
        }
        foreach ($message->getResponses() as $response) {
            if ($response->getUser() != $this->getUser()) {
                $response->setSeen(true);
                $this->manager->flush();
            }
        }
        $response = new MessageResponse();
        $form = $this->createForm(MessageResponseType::class, $response);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $response->setMessage($message)
                ->setUser($this->getUser());
            $this->manager->persist($response);
            $this->manager->flush();
            $project = $message->getProject();
            $projectLink = $this->generateUrl('startuper_project_dashboard_show', [
                'id' => $project->getId(),
                'slug' => $project->getSlug()
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            $projectMessagesLink = $this->generateUrl('startuper_project_messages', [
                    'id' => $project->getId(),
                    'slug' => $project->getSlug()
                ], UrlGeneratorInterface::ABSOLUTE_URL);
            $this->notification->notifyEntrepreneurOnResponse(
                $project, $projectLink, $message, $projectMessagesLink, $request->getLocale());
            $this->addFlash("success", $this->translator->trans("Your response has been successfully sent"));
            return $this->redirectToRoute("investor_messages");
        }
        return $this->render('investor/response.html.twig', [
            'project' => null,
            'projects' => $this->projectRepository->findAllByStartupNotDeleted($this->getUser()),
            'company' => null,
            'companies' => $this->companyRepository->findAllByStartupNotDeleted($this->getUser()),
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }
}