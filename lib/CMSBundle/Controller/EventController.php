<?php

namespace EgioDigital\CMSBundle\Controller;

use App\Repository\HeaderRepository;
use Doctrine\Common\Persistence\ObjectManager;
use EgioDigital\CMSBundle\Entity\CategoryEvent;
use EgioDigital\CMSBundle\Entity\Event;
use EgioDigital\CMSBundle\Entity\EventLike;
use EgioDigital\CMSBundle\Entity\EventPublished;
use EgioDigital\CMSBundle\Entity\EventView;
use EgioDigital\CMSBundle\Entity\Row;
use EgioDigital\CMSBundle\Entity\RowPublished;
use EgioDigital\CMSBundle\Repository\CategoryEventRepository;
use EgioDigital\CMSBundle\Repository\EventLikeRepository;
use EgioDigital\CMSBundle\Repository\EventPublishedRepository;
use EgioDigital\CMSBundle\Repository\EventViewRepository;
use EgioDigital\CMSBundle\Repository\EventRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use App\Service\Image as ImageService;

/**
 * Class EventController
 * @package Egio\CMSbundle\Controller
 */
class EventController extends AbstractController
{

    /**
     * @Route("{_locale}/events", name="cmsbundle_event_list")
     * @Route("/events", name="cmsbundle_event_list_default", defaults={"_locale"="fr"})
     * @param EventPublishedRepository $eventPublishedRepository
     * @param Request $request
     * @param CategoryEventRepository $categoryEventRepository
     * @param EventRepository $eventRepository
     * @param HeaderRepository $headerRepository
     * @return Response
     */
    public function listAction(
        EventPublishedRepository $eventPublishedRepository,
        Request $request,
        CategoryEventRepository $categoryEventRepository,
        HeaderRepository $headerRepository,
        EventRepository $eventRepository
    ) {
        $locale = $request->getLocale();
        $header = $headerRepository->findOneBy(['page' => 'events', 'lang' => $locale]);
        $categories = $request->query->get('categories');
        $categoriesString = !empty($currentCategories) ? implode(",", $currentCategories) : null;

        $query = $eventPublishedRepository->getAllQuery($locale, $categoriesString);
        $total = count($query->getResult());
        $paginator = $this->get('knp_paginator');
        $page = $request->query->getInt('page', 1);
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            9 /*limit per page*/
        );
        $paginationNext = $paginator->paginate(
            $query, /* query NOT result */
            $page + 1, /*page number*/
            9 /*limit per page*/
        );
        $more = 0;
        if (count($paginationNext) > 0) {
            $more = 1;
        }

        return $this->render('@CMS/front/event/list.html.twig', [
            'current_menu' => 'events',
            'header' => $header,
            'countResults' => $total,
            'pagination' => $pagination,
            'allCategories' => $categoryEventRepository->findBy(['lang' => $locale]),
            'currentCategories' => $categories,
            'more' => $more,
            'page' => $page+1,
        ]);
    }


    /**
     * @Route("{_locale}/events/scroll", name="cmsbundle_event_list_scroll")
     * @Route("/events/scroll", name="cmsbundle_event_list_scroll_default", defaults={"_locale"="fr"})
     * @param EventPublishedRepository $eventPublishedRepository
     * @param Request $request
     * @return Response
     */
    public function scrollEvents(
        Request $request,
        EventPublishedRepository $eventPublishedRepository
    ) {
        $locale = $request->getLocale();
        $categories = $request->query->get('categories');
        $categoriesString = !empty($categories) ? implode(",", $categories) : null;
        $page = $request->query->getInt('page', 1);
        $query = $eventPublishedRepository->getAllQuery($locale, $categoriesString);
        $total = count($query->getResult());
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $page, /*page number*/
            9 /*limit per page*/
        );
        $paginationNext = $paginator->paginate(
            $query, /* query NOT result */
            $page + 1, /*page number*/
            9 /*limit per page*/
        );
        $more = 0;
        if (count($paginationNext) > 0) {
            $more = 1;
        }
        return $this->render('@CMS/front/event/scroll.html.twig', [
            'current_menu' => 'events',
            'countResults' => $total,
            'pagination' => $pagination,
            'categories' => $categories,
            'more' => $more,
        ]);
    }

    /**
     * @Route("/events/update-status/{token}", name="event_update_status")
     * @param string $token
     * @param EventRepository $eventRepository
     * @param ObjectManager $manager
     * @param LoggerInterface $logger
     * @return JsonResponse
     * @throws \Exception
     */
    public function updateStatus(
        string $token,
        EventRepository $eventRepository,
        ObjectManager $manager,
        LoggerInterface $logger
    )
    {
        if ($token !== "GlMbDq3D9kk1y3DskUVh-he_XmepfscUBBOg3A1fmCc") {
            return $this->json([
                'message' => "token invalid",
            ], 403);
        }
        $events = $eventRepository->findAll();
        $now = new \DateTime();
        $array = [];
        foreach ($events as $event) {
            if ($event->getDateFin() < $now) {
                $event->setIsExpired(true);
            } else {
                $event->setIsExpired(false);
            }
            $array[$event->getId()] = ['title' => $event->getTitle(), 'isExpired' => $event->getIsExpired()];
        }
        $manager->flush();
        $logger->info("Cron de modification de status des evenement est passé");
        return $this->json([
            'events' => $array,
        ], 200);
    }

    /**
     * @Route("{_locale}/events/{id}-{slug}", name="cmsbundle_event_show")
     * @Route("/events/{id}-{slug}", name="cmsbundle_event_show_default", defaults={"_locale"="fr"})
     * @param int $id
     * @param string $slug
     * @param EventPublished $eventPublished
     * @param EventRepository $eventRepository
     * @param Request $request
     * @param EventViewRepository $eventViewRepository
     * @param ObjectManager $manager
     * @param EventPublishedRepository $eventPublishedRepository
     * @return Response
     */
    public function showAction(
        int $id,
        string $slug,
        EventPublished $eventPublished,
        Request $request,
        EventViewRepository $eventViewRepository,
        ObjectManager $manager,
        EventPublishedRepository $eventPublishedRepository,
        EventRepository $eventRepository,
        ImageService $imageService
    )
    {
        if ($eventPublished->getId() !== $id || $eventPublished->getSlug() !== $slug) {
           throw new NotFoundHttpException('Event not found');
        }

        if ($eventPublished->getIsActive() === false) {
            return $this->redirectToRoute('home');
        }

        $locale = $request->getLocale();
        //$ip = $request->getClientIp();
        //$oldView = $eventViewRepository->findOneBy(['event'=>$eventPublished, 'ip'=>$ip]);
        //$ev = $eventPublishedRepository->findOneBy(['lang' => $locale, 'slug' => $slug, 'id' => $id]);
        // if (!$oldView) {
        //    $view = new EventView();
        //    $view->setEvent($eventPublished)
        //       ->setIp($ip);
        //     $eventPublished->addView($view);
        //     $eventPublished->setCountViews($eventPublished->getCountViews()+1);
        //    $manager->persist($eventPublished);
        //    $manager->flush();
        // }

        $tags = [];
        if (!empty($eventPublished->getMetaTags())) {
            $tags = explode(',', $eventPublished->getMetaTags());
        }

        $blocks = [];
        foreach ($eventPublished->getBlocks() as $block) {
            $blocks[$block->getRow()][] = $block;
        }
        ksort($blocks);

        $rows = $this->getRowsInfos($id);
        list($width,$height) = $imageService->getimagesize('event', $eventPublished->getBannerDesktop());
        $events = $eventPublishedRepository->getEventSuggestion($eventPublished->getCategory(), $eventPublished->getId(), $locale);
        $metatitle = !empty($eventPublished->getMetaTitle()) ? $eventPublished->getMetaTitle() : $eventPublished->getTitle();
        $metadescription = $eventPublished->getMetaDescription();
        return $this->render('@CMS/front/event/show.html.twig', [
            'current_menu' => 'events',
            'event' => $eventPublished,
            'tags' => $tags,
            'blocks' => $blocks,
            'events' => $events,
            'count_events' => count($events),
            'rows' => $rows,
            'width' => $width,
            'height' => $height,
            'metatitle' => $metatitle,
            'metadescription' => $metadescription,
        ]);
    }

    /**
     * @Route("/boadmin/event/show/{id}-{slug}", name="cmsbundle_event_preview")
     * @param int $id
     * @param string $slug
     * @param Event $event
     * @param EventPublishedRepository $eventPublishedRepository
     * @return Response
     */
    public function previewAction(int $id, string $slug, Event $event, EventPublishedRepository $eventPublishedRepository)
    {
        if ($event->getId() !== $id || $event->getSlug() !== $slug) {
            throw new NotFoundHttpException('Event not found');
        }

        $tags = [];
        if (!empty($event->getMetaTags())) {
            $tags = explode(',', $event->getMetaTags());
        }

        $blocks = [];
        foreach ($event->getBlocks() as $block) {
            $blocks[$block->getRow()][] = $block;
        }
        ksort($blocks);

        $rows = $this->getRowsInfosPreview($id);

        return $this->render('@CMS/front/event/preview.html.twig', [
            'current_menu' => 'events',
            'event' => $event,
            'tags' => $tags,
            'blocks' => $blocks,
            'events' => $eventPublishedRepository->getEventSuggestion($event->getCategory(), $event->getId()),
            'rows' => $rows,
        ]);
    }

    /**
     * @Route("/boadmin/event/public/show/{id}-{slug}", name="cmsbundle_event_preview_public")
     * @param int $id
     * @param string $slug
     * @param EventPublished $eventPublished
     * @param EventPublishedRepository $eventPublishedRepository
     * @return Response
     */
    public function previewPublicAction(int $id, string $slug, EventPublished $eventPublished,
                                        EventPublishedRepository $eventPublishedRepository)
    {
        if ($eventPublished->getId() !== $id || $eventPublished->getSlug() !== $slug) {
            throw new NotFoundHttpException('Event not found');
        }

        $tags = explode(',', $eventPublished->getMetaTags());

        $blocks = [];
        foreach ($eventPublished->getBlocks() as $block) {
            $blocks[$block->getRow()][] = $block;
        }
        ksort($blocks);

        $rows = $this->getRowsInfos($id);

        return $this->render('@CMS/front/event/show.html.twig', [
            'current_menu' => 'events',
            'event' => $eventPublished,
            'tags' => $tags,
            'blocks' => $blocks,
            'events' => $eventPublishedRepository->getEventSuggestion($eventPublished->getCategory(), $eventPublished->getId()),
            'rows' => $rows,
        ]);
    }

    /**
     * @Route("/boadmin/event/list-categories-by-lang", name="list_categories_event_by_lang")
     * @param Request $request
     * @return JsonResponse
     */
    public function listCategoriesByLang(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repoCat = $em->getRepository(CategoryEvent::class);

        $categories = $repoCat->createQueryBuilder("c")
            ->where("c.lang = :lang")
            ->setParameter("lang", $request->query->get("lang"))
            ->getQuery()
            ->getResult();

        $responseArray = array();
        foreach ($categories as $category) {
            /* @var CategoryEvent $category */
            $responseArray[] = array(
                "id" => $category->getId(),
                "title" => $category->getTitle()
            );
        }

        return new JsonResponse($responseArray);
    }

    /**
     * @Route("{_locale}/events/{id}/like", name="event_like")
     * @Route("/events/{id}/like", name="event_like_default", defaults={"_locale"="fr"})
     * @param EventPublished $eventPublished
     * @param EventLikeRepository $eventLikeRepository
     * @param ObjectManager $manager
     * @return Response
     */
    public function like(EventPublished $eventPublished, EventLikeRepository $eventLikeRepository, ObjectManager $manager)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['code' => 403, 'message' => 'Unauthorized'], 403);
        }

        if ($eventPublished->isLikedByUser($user)) {
            $like = $eventLikeRepository->findOneBy(['event' => $eventPublished, 'user' => $user]);
            $manager->remove($like);
            $manager->flush();
            return $this->json([
                'code' => 200,
                'message' => 'like bien suprimé',
                'likes' => $eventLikeRepository->count(['event' => $eventPublished])
            ], 200);
        }

        $like = new EventLike();
        $like->setEvent($eventPublished)
            ->setUser($user);
        $manager->persist($like);
        $manager->flush();
        return $this->json([
            'code' => 200,
            'message' => 'like bien ajouté',
            'likes' => $eventLikeRepository->count(['event' => $eventPublished])
        ], 200);
    }

    /**
     * @Route("/boadmin/event/getRows", name="list_rows_event")
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
        foreach ($rows as $row) {
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
            ->setParameter("type", 'event')
            ->setParameter("idCms", $id)
            ->getQuery()
            ->getResult();

        $responseArray = array();
        foreach ($rows as $row) {
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
            ->setParameter("type", 'event')
            ->setParameter("idCms", $id)
            ->getQuery()
            ->getResult();

        $responseArray = array();
        foreach ($rows as $row) {
            /* @var Row $row */
            $responseArray[$row->getIndexRow()] = $row;
        }

        return $responseArray;
    }

    /**
     * @Route("/events/changeStatus", name="event_change_status")
     * @param EventRepository $eventRepository
     * @param ObjectManager $manager
     * @return Response
     */
    public function changeStatus(EventRepository $eventRepository, ObjectManager $manager)
    {
        /*$date = date('Y-m-d H:i:s');
        $events = $eventRepository->getEventChangeStatus($date);
        if($events) {
          foreach ($events as $event) {
            $event->setIsActive(false);
            $manager->persist($event);
            $manager->flush();
          }
        }

        return $this->json([
           'message' => 'MAJ STATUX TERMINÉ',
        ], 200);*/
    }
}