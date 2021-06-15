<?php

namespace EgioDigital\CMSBundle\Controller;

use App\Entity\OfferRequest;
use App\Notification\AdminNotification;
use App\Notification\EntrepreneurNotification;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use EgioDigital\CMSBundle\Entity\CategoryPage;
use EgioDigital\CMSBundle\Entity\Page;
use EgioDigital\CMSBundle\Entity\PagePublished;
use EgioDigital\CMSBundle\Entity\Row;
use EgioDigital\CMSBundle\Entity\PageView;
use EgioDigital\CMSBundle\Entity\RowPublished;
use EgioDigital\CMSBundle\Repository\CategoryPageRepository;
use EgioDigital\CMSBundle\Repository\PagePublishedRepository;
use EgioDigital\CMSBundle\Repository\PageRepository;
use EgioDigital\CMSBundle\Repository\PageViewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PageController
 * @package Egio\CMSbundle\Controller
 */
class PageController extends AbstractController
{
    /**
     * @Route("{_locale}/pages/scroll", name="cmsbundle_page_list_scroll")
     * @Route("/pages/scroll", name="cmsbundle_page_list_scroll_default", defaults={"_locale"="fr"})
     * @param PagePublishedRepository $pagePublishedRepository
     * @param Request $request
     * @return Response
     */
    public function scrollPages(PagePublishedRepository $pagePublishedRepository, Request $request)
    {
        $categories = $request->query->get('category');

        $query = $pagePublishedRepository->getAllQuery($categories);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            9 /*limit per page*/
        );
        return $this->render('@CMS/front/page/scroll.html.twig', [
            'current_menu' => 'pages',
            'pagination' => $pagination,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/boadmin/page/list-categories-by-lang", name="list_categories_page_by_lang")
     * @param Request $request
     * @return JsonResponse
     */
    public function listCategoriesByLang(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repoCat = $em->getRepository(CategoryPage::class);

        $categories = $repoCat->createQueryBuilder("c")
            ->where("c.lang = :lang")
            ->setParameter("lang", $request->query->get("lang"))
            ->getQuery()
            ->getResult();

        $responseArray = array();
        foreach($categories as $category){
            /* @var CategoryPage $category */
            $responseArray[] = array(
                "id" => $category->getId(),
                "title" => $category->getTitle()
            );
        }
        return new JsonResponse($responseArray);
    }

    /**
     * @Route("{_locale}/page/{slug}", name="cmsbundle_page_show")
     * @Route("/page/{slug}", name="cmsbundle_page_show_default", defaults={"_locale"="fr"})
     * @param string $slug
     * @param PagePublished $pagePublished
     * @return Response
     */
    public function showAction(string $slug, PagePublished $pagePublished)
    {
        //if ($pagePublished->getLang() !== $lang || $pagePublished->getSlug() !== $slug) {
        if ($pagePublished->getSlug() !== $slug) {
            throw new NotFoundHttpException('Page not found');
        }

        if ($pagePublished->getIsActive() === false) {
            return $this->redirectToRoute('home');
        }

        $blocks = [];
        foreach ($pagePublished->getBlocks() as $block) {
            $blocks[$block->getRow()][] = $block;
        }
        ksort($blocks);

        $rows = $this->getRowsInfos($pagePublished->getId());

        if ($pagePublished->getIsPageSimple()) {
            return $this->render('@CMS/front/page/show-simple.html.twig', [
                'current_menu' => 'pages',
                'page' => $pagePublished,
                'blocks' => $blocks,
                'rows' => $rows,
            ]);
        }

        return $this->render('@CMS/front/page/show.html.twig', [
            'current_menu' => 'pages',
            'page' => $pagePublished,
            'blocks' => $blocks,
            'rows' => $rows,
        ]);
    }

    /**
     * @Route("/boadmin/page/show/{id}-{slug}", name="cmsbundle_page_preview")
     * @param int $id
     * @param string $slug
     * @param Page $page
     * @return Response
     */
    public function previewAction(int $id, string $slug, Page $page)
    {
        if ($page->getId() !== $id || $page->getSlug() !== $slug) {
            throw new NotFoundHttpException('Page not found');
        }

        $blocks = [];
        foreach ($page->getBlocks() as $block) {
            $blocks[$block->getRow()][] = $block;
        }
        ksort($blocks);

        $rows = $this->getRowsInfosPreview($id);

        if ($page->getIsPageSimple()) {
            return $this->render('@CMS/front/page/show-simple.html.twig', [
                'current_menu' => 'pages',
                'page' => $page,
                'blocks' => $blocks,
                'rows' => $rows,
            ]);
        }

        return $this->render('@CMS/front/page/preview.html.twig', [
            'current_menu' => 'pages',
            'page' => $page,
            'blocks' => $blocks,
            'rows' => $rows,
        ]);
    }

    /**
     * @Route("/boadmin/page/public/show/{id}-{slug}", name="cmsbundle_page_preview_public")
     * @param int $id
     * @param string $slug
     * @param PagePublished $pagePublished
     * @return Response
     */
    public function previewPublicAction(int $id, string $slug, PagePublished $pagePublished)
    {
        if ($pagePublished->getId() !== $id || $pagePublished->getSlug() !== $slug) {
            throw new NotFoundHttpException('Page not found');
        }

        $tags = explode(',', $pagePublished->getMetaTags());

        $blocks = [];
        foreach ($pagePublished->getBlocks() as $block) {
            $blocks[$block->getRow()][] = $block;
        }
        ksort($blocks);

        $rows = $this->getRowsInfos($id);

        if ($pagePublished->getIsPageSimple()) {
            return $this->render('@CMS/front/page/show-simple.html.twig', [
                'current_menu' => 'pages',
                'page' => $pagePublished,
                'blocks' => $blocks,
                'rows' => $rows,
            ]);
        }

        return $this->render('@CMS/front/page/show.html.twig', [
            'current_menu' => 'articles',
            'page' => $pagePublished,
            'tags' => $tags,
            'blocks' => $blocks,
            'rows' => $rows,
        ]);
    }

    /**
     * @Route("/boadmin/pages/getRows", name="list_page_rows")
     * @param Request $request
     * @return JsonResponse
     */
    public function getRows(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repoRow = $em->getRepository(Row::class);

        $rows = $repoRow->createQueryBuilder("r")
            ->where("r.type = :type")
            ->Andwhere("r.idCms = :idCms")
            ->setParameter("type", $request->query->get("type"))
            ->setParameter("idCms", $request->query->get("idCms"))
            ->getQuery()
            ->getResult();

        $responseArray = array();
        foreach($rows as $row){
            /* @var Row $row */
            $responseArray[] = array(
                "id" => $row->getId(),
                "class" => $row->getClass(),
                "idHtml" => $row->getIdHtml(),
            );
        }

        return new JsonResponse($responseArray);
    }

    public function getRowsInfos($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repoRow = $em->getRepository(RowPublished::class);

        $rows = $repoRow->createQueryBuilder("r")
            ->where("r.type = :type")
            ->Andwhere("r.idCms = :idCms")
            ->setParameter("type", 'page')
            ->setParameter("idCms", $id)
            ->getQuery()
            ->getResult();

        $responseArray = array();
        foreach($rows as $row){
            /* @var Row $row */
            $responseArray[$row->getIndexRow()] = $row;
        }

        return $responseArray;
    }

    public function getRowsInfosPreview($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repoRow = $em->getRepository(Row::class);

        $rows = $repoRow->createQueryBuilder("r")
            ->where("r.type = :type")
            ->Andwhere("r.idCms = :idCms")
            ->setParameter("type", 'page')
            ->setParameter("idCms", $id)
            ->getQuery()
            ->getResult();

        $responseArray = array();
        foreach($rows as $row){
            /* @var Row $row */
            $responseArray[$row->getIndexRow()] = $row;
        }

        return $responseArray;
    }

    /**
     * @Route("{_locale}/pages/receive-offer", name="receive_offer")
     * @Route("/pages/receive-offer", name="receive_offer_default", defaults={"_locale"="fr"})
     * @param Request $request
     * @param ObjectManager $manager
     * @param TranslatorInterface $translator
     * @param AdminNotification $adminNotification
     * @param EntrepreneurNotification $entrepreneurNotification
     * @return JsonResponse
     */
    public function receiveOffer(Request $request, ObjectManager$manager, TranslatorInterface $translator,
                                 AdminNotification $adminNotification, EntrepreneurNotification $entrepreneurNotification)
    {
        $firstName = $request->get('firstName');
        $lastName = $request->get('lastName');
        $email = $request->get('email');
        $type = $request->get('type');
        $block = $request->get('block');
        $message = $request->get('message');

        $errors = [];
        if (empty($firstName)) {
            $errors[] = $translator->trans('First name is required', [], 'messages');
        }
        if (empty($lastName)) {
            $errors[] = $translator->trans('Last name is required', [], 'messages');
        }
        if (empty($email)) {
            $errors[] = $translator->trans('Email is required', [], 'messages');
        }
        if (empty($type)) {
            $errors[] = $translator->trans('Profile is required', [], 'messages');
        }

        if (count($errors) > 0) {
            return $this->json([
                'message' => $errors[0],
            ], 500);
        }

        $offerRequest = new \EgioDigital\CMSBundle\Entity\OfferRequest();
        $offerRequest->setFirstName($firstName)
            ->setLastName($lastName)
            ->setEmail($email)
            ->setType($type)
            ->setBlock($block)
            ->setMessage($message)
            ;
        $manager->persist($offerRequest);
        $manager->flush();
        $link = $this->generateUrl('sonata_admin_offer_request_show', [
            'id' => $offerRequest->getId()
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        $adminNotification->notifyOnAskReceiveOffer($offerRequest, $link, $request->getLocale());
        $entrepreneurNotification->notifyOnAskReceiveOffer($offerRequest, $request->getLocale());
        $msg = $translator->trans('Your offer request has been successfully sent', [], 'offerRequest');
        return $this->json([
                'message' => $msg,
        ], 200);
    }
}