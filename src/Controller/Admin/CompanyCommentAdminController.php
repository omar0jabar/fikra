<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Repository\CompanyCommentRepository;
use App\Repository\CompanyCommentResponseRepository;
use App\Repository\MessageRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CompanyCommentAdminController
 * @package App\Controller\Admin
 */
class CompanyCommentAdminController extends AbstractController
{
    /**
     * @var CompanyCommentRepository
     */
    private $companyCommentRepository;
    /**
     * @var ObjectManager
     */
    private $manager;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    private $translationDomain = 'company';
    /**
     * @var CompanyCommentResponseRepository
     */
    private $companyCommentResponseRepository;

    /**
     * CompanyCommentAdminController constructor.
     *
     * @param CompanyCommentRepository $companyCommentRepository
     * @param CompanyCommentResponseRepository $companyCommentResponseRepository
     * @param TranslatorInterface $translator
     * @param ObjectManager $manager
     */
    public function __construct(
        CompanyCommentRepository $companyCommentRepository,
        CompanyCommentResponseRepository $companyCommentResponseRepository,
        TranslatorInterface $translator,
        ObjectManager $manager
    ) {
        $this->companyCommentRepository = $companyCommentRepository;
        $this->companyCommentResponseRepository = $companyCommentResponseRepository;
        $this->translator = $translator;
        $this->manager = $manager;
    }

    /**
     * @Route("/boadmin/company/{id}/message/{commentId}/publish", name="admin_publish_company_comment")
     * @param Company $company
     * @param $commentId
     * @param Request $request
     * @return RedirectResponse
     */
    public function publishComment(Company $company, $commentId, Request $request)
    {
        $comment = $this->companyCommentRepository->find($commentId);
        if (!$comment) {
            throw new NotFoundHttpException("Comment not found");
        }
        if ($comment->getIsPublished()) {
            $comment->setIsPublished(false);
            $message = $this->translator->trans("Comment is no longer public", [], $this->translationDomain);
        } else {
            $comment->setIsPublished(true);
            $message = $this->translator->trans("The comment was successfully published", [], $this->translationDomain);
        }
        $this->manager->flush();
        $this->addFlash("success", "$message");
        return $this->redirect($request->headers->get("referer"));
    }

    /**
     * @Route("/boadmin/company/{id}/message/{commentId}/show", name="admin_show_company_comment")
     * @param Company $company
     * @param int $commentId
     * @return Response
     */
    public function showComment(Company $company, $commentId)
    {
        $comment = $this->companyCommentRepository->find($commentId);
        if (!$comment) {
            throw new NotFoundHttpException("Comment not found");
        }
        return $this->render("administration/company/comment.html.twig", [
            'company' => $company,
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/boadmin/company/{id}/response/{responseId}/comment/{commentId}", name="admin_publish_company_comment_response")
     * @param Company $company
     * @param $commentId
     * @param $responseId
     * @param Request $request
     * @return RedirectResponse
     */
    public function publishResponseComment(Company $company, $commentId, $responseId, Request $request)
    {
        $comment = $this->companyCommentRepository->find($commentId);
        if (!$comment) {
            throw new NotFoundHttpException("Comment not found");
        }
        $response = $this->companyCommentResponseRepository->find($responseId);
        if (!$response) {
            throw new NotFoundHttpException("Response not found");
        }

        if ($comment->getIsPublished()) {
            if ($response->getIsPublished()) {
                $response->setIsPublished(false);
                $message = $this->translator->trans("Comment is no longer public", [], $this->translationDomain);
            } else {
                $response->setIsPublished(true);
                $message = $this->translator->trans("The comment was successfully published", [], $this->translationDomain);
            }
            $this->manager->flush();
            $this->addFlash("success", "$message");
        } else {
            $message = $this->translator->trans("Parent comment is not published yet", [], $this->translationDomain);
            $this->addFlash("danger", "$message");
        }
        return $this->redirect($request->headers->get("referer"));
    }

}