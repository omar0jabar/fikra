<?php

namespace App\Controller\Admin;


use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MessageAdminController extends AbstractController
{
    private $messageRepository;

    public function __construct(MessageRepository $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    /**
     * @Route("/boadmin/message/{idMessage}", name="admin_show_message")
     * @param int $idMessage
     * @return Response
     */
    public function showMessage(int $idMessage)
    {
        $message = $this->messageRepository->find($idMessage);
        if (!$message) {
            throw new NotFoundHttpException("Message not found");
        }
        $project = $message->getProject();
        return $this->render("administration/project/message.html.twig", [
            'project' => $project,
            'message' => $message,
        ]);
    }
}