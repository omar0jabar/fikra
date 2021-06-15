<?php

namespace EgioDigital\CMSBundle\Controller;

use Cocur\Slugify\Slugify;
use EgioDigital\CMSBundle\Entity\BlockPublished;
use EgioDigital\CMSBundle\Entity\Event;
use EgioDigital\CMSBundle\Entity\EventPublished;
use EgioDigital\CMSBundle\Entity\Row;
use EgioDigital\CMSBundle\Entity\RowPublished;
use EgioDigital\CMSBundle\Form\EventType;
use EgioDigital\CMSBundle\Repository\CategoryEventRepository;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminEventController extends CRUDController
{
    /**
     * @var CategoryEventRepository
     */
    private $categoryEventRepository;

    /**
     * AdminEventController constructor.
     *
     * @param CategoryEventRepository $categoryEventRepository
     */
    public function __construct(
        CategoryEventRepository $categoryEventRepository
    ) {
        $this->categoryEventRepository = $categoryEventRepository;
    }

    protected function clearArticlePublic(EventPublished $eventPublished)
    {
        if ($eventPublished) {
            if ($eventPublished->getBannerDesktop()) {
                $path = "upload/cms/events-public/{$eventPublished->getBannerDesktop()}";
                if (is_file($path)) {
                    unlink($path);
                }
            }
            if ($eventPublished->getBannerMobile()) {
                $path = "upload/cms/events-public/{$eventPublished->getBannerMobile()}";
                if (is_file($path)) {
                    unlink($path);
                }
            }
            foreach ($eventPublished->getBlocks() as $block) {
                if ($block->getImageName()) {
                    $path = "upload/cms/events-public/blocks/{$block->getImageName()}";
                    if (is_file($path)) {
                        unlink($path);
                    }
                }
                $eventPublished->removeBlock($block);
            }
            $manager = $this->getDoctrine()->getManager();
            $repoRow = $this->getDoctrine()->getRepository(RowPublished::class);
            $rows = $repoRow->findBy(['idCms' => $eventPublished, 'type' => 'event']);
            foreach ($rows as $row) {
                $manager->remove($row);
            }
        }
    }

    protected function cloneInPublic(Event $event)
    {
        $eventPublicRepo = $this->getDoctrine()->getRepository(EventPublished::class);
        $eventPublished = $eventPublicRepo->findOneBy(['event' => $event]);
        if ($eventPublished) {
            //$this->clearArticlePublic($eventPublished);
            foreach ($eventPublished->getBlocks() as $block) {
                $eventPublished->removeBlock($block);
            }
            $manager = $this->getDoctrine()->getManager();
            $repoRow = $this->getDoctrine()->getRepository(RowPublished::class);
            $rows = $repoRow->findBy(['idCms' => $eventPublished, 'type' => 'event']);
            foreach ($rows as $row) {
                $manager->remove($row);
            }
        } else {
            $eventPublished = new EventPublished();
        }
        $eventPublished->setEvent($event)
            ->setTitle($event->getTitle())
            ->setCategory($event->getCategory())
            ->setLang($event->getLang())
            ->setSlug($event->getSlug())
            ->setUrl($event->getUrl())
            ->setLieu($event->getLieu())
            ->setDateDebut($event->getDateDebut())
            ->setDateFin($event->getDateFin())
            ->setHeureDebut($event->getHeureDebut())
            ->setHeureFin($event->getHeureFin())
            ->setMetaTitle($event->getMetaTitle())
            ->setMetaTags($event->getMetaTags())
            ->setMetaDescription($event->getMetaDescription())
            ->setHtmlIdAttr($event->getHtmlIdAttr())
            ->setHtmlClassAttr($event->getHtmlClassAttr())
            ->setContent($event->getContent())
            ->setIsActive(true)
            ->setBannerDesktop($event->getBannerDesktop())
            ->setBannerMobile($event->getBannerMobile())
            ;

        if ($event->getBannerDesktop()) {
            $pathOld = "upload/cms/events/{$event->getBannerDesktop()}";
            $pathNew = "upload/cms/events-public/{$event->getBannerDesktop()}";
            if (is_file($pathOld) && !is_file($pathNew)) {
                copy($pathOld, $pathNew);
            }
        }
        if ($event->getBannerMobile()) {
            $pathOld = "upload/cms/events/{$event->getBannerMobile()}";
            $pathNew = "upload/cms/events-public/{$event->getBannerMobile()}";
            if (is_file($pathOld) && !is_file($pathNew)) {
                copy($pathOld, $pathNew);
            }
        }
        $manager = $this->getDoctrine()->getManager();
        foreach ($event->getBlocks() as $block){
            //dd($block);
            $blockPublished = new BlockPublished();
            $blockPublished->setTitle($block->getTitle())
                ->setContent($block->getContent())
                ->setImageName($block->getImageName())
                ->setAlt($block->getAlt())
                ->setLinkImage($block->getLinkImage())
                ->setLegend($block->getLegend())
                ->setTextImage($block->getTextImage())
                ->setLinkVideo($block->getLinkVideo())
                ->setWidth($block->getWidth())
                ->setHeight($block->getHeight())
                ->setType($block->getType())
                ->setPage(null)
                ->setEvent($eventPublished)
                ->setPosition($block->getPosition())
                ->setRow($block->getRow())
                ->setColLarge($block->getColLarge())
                ->setClearfix($block->getClearfix())
            ;
            if ($block->getImageName()) {
                $pathOld = "upload/cms/blocks/{$block->getImageName()}";
                $pathNew = "upload/cms/events-public/blocks/{$block->getImageName()}";
                if (is_file($pathOld) && !is_file($pathNew)) {
                    copy($pathOld, $pathNew);
                }
            }
            $eventPublished->addBlock($blockPublished);
            $manager->persist($blockPublished);
        }
        $manager->flush();

        $repoRow = $this->getDoctrine()->getRepository(Row::class);
        $rows = $repoRow->findBy(['idCms' => $event->getId(), 'type' => 'event']);
        foreach ($rows as $row) {
            $rowPublished = new RowPublished();
            $rowPublished->setType('event')
                ->setIdCms($eventPublished->getId())
                ->setClass($row->getClass())
                ->setIdHtml($row->getIdHtml())
                ->setIndexRow($row->getIndexRow());
            $manager->persist($rowPublished);
        }
        $manager->persist($eventPublished);
        $manager->flush();
    }

    public function createAction()
    {
        $event = new Event();

        $request = $this->getRequest();
        $manager = $this->getDoctrine()->getManager();

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        $preview = false;
        $eventPublished = null;

        if ( $form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();

            if (empty($event->getSlug())){
                $slug = $slugify->slugify($event->getTitle());
            } else {
                $slug = $slugify->slugify($event->getSlug());
            }
            $event->setSlug($slug);
            $this->addHeureToDate($event);

            $event->setIsActive(false);

            if ($form->get('savePublic')->isClicked()) {
                $event->setIsActive(true);
            }

            $manager->persist($event);
            $manager->flush();

            $totalRow = $request->get('totalRow');
            for ($i = 1; $i<= $totalRow; $i++) {
                $class = !empty($request->get('classRow_'.$i)) ? $request->get('classRow_'.$i) : 'class'.$i;
                $id = !empty($request->get('idRow_'.$i)) ? $request->get('idRow_'.$i) : 'id'.$i;

                $row = new Row();
                $row->setClass($class)
                    ->setIndexRow($i)
                    ->setIdHtml($id)
                    ->setIdCms($event->getId())
                    ->setType('event');
                $manager->persist($row);
            }
            $manager->flush();
            $this->addFlash(
                'sonata_flash_success',
                'L\'evenement a été crée avec succès. Pensez a créer une version anglaise a votre evenement!'
            );

            if ($form->get('savePublic')->isClicked()) {
                $this->cloneInPublic($event);
            }
            return $this->redirectToRoute("sonata_admin_event_list");
        }

        return $this->renderWithExtraParams('@CMSBundleViews/views/admin/event/create.html.twig', [
            'form' => $form->createView(),
            'action' => 'create',
            'event' => $event,
            'metaTags' => [],
            'totalRow' => 0,
            'rows' => [],
            'idCms' => 0,
            'rowsValue' => 0,
            'preview' => $preview,
        ]);
    }

    /**
     * @param null $id
     * @return RedirectResponse|Response
     */
    public function editAction($id = null)
    {
        $repo = $this->getDoctrine()->getRepository(Event::class);
        $repoRow = $this->getDoctrine()->getRepository(Row::class);
        /** @var Event $event */
        $event = $repo->findOneBy(['id' => $id]);
        $rowsValue = [];

        if (!$event) {
            throw new NotFoundHttpException('Event not found');
        }
        $totalRow = $this->getRow($event);
        $request = $this->getRequest();
        $manager = $this->getDoctrine()->getManager();
        $rows = $repoRow->findBy([
            'idCms' => $event->getId(),
            'type' => 'event'
        ]);
        foreach ($rows as  $row) {
            $rowsValue[$row->getIndexRow()] = [
                'class' => $row->getClass(),
                'id' => $row->getIdHtml(),
            ];
        }
        $rowsValue = json_encode($rowsValue);

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
        $preview = false;
        $eventPublished = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();

            if (empty($event->getSlug())){
                $slug = $slugify->slugify($event->getTitle());
            } else {
                $slug = $slugify->slugify($event->getSlug());
            }
            $event->setSlug($slug);
            $this->addHeureToDate($event);

            $event->setIsActive(false);

            if ($form->get('savePublic')->isClicked()) {
                $event->setIsActive(true);
            }
            // Change category to bonnin is saveDraft is clicked
            if ($form->get('saveDraft')->isClicked()) {
                $bonninCategory = $this->categoryEventRepository->findOneBy(['title' => 'bonnin']);
                if ($bonninCategory) {
                    $event->setCategory($bonninCategory);
                }
            }

            $manager->persist($event);
            $manager->flush();

            $totalRow = $request->get('totalRow');
            for ($i = 1; $i<= $totalRow; $i++) {
                $class = !empty($request->get('classRow_'.$i)) ? $request->get('classRow_'.$i) : 'class'.$i;
                $id = !empty($request->get('idRow_'.$i)) ? $request->get('idRow_'.$i) : 'id'.$i;

                $row = new Row();
                $row->setClass($class)
                    ->setIndexRow($i)
                    ->setIdHtml($id)
                    ->setIdCms($event->getId())
                    ->setType('event');
                $manager->persist($row);
            }
            $manager->flush();
            $this->addFlash(
                'sonata_flash_success',
                'L\'evenement a été crée avec succès. Pensez a créer une version anglaise a votre article!'
            );

            if ($form->get('savePublic')->isClicked()) {
                $this->cloneInPublic($event);
            }
            return $this->redirectToRoute("sonata_admin_event_list");
        }
        $metaTags = explode(',', $event->getMetaTags());
        return $this->renderWithExtraParams('@CMSBundleViews/views/admin/event/create.html.twig', [
            'form' => $form->createView(),
            'action' => 'edit',
            'event' => $event,
            'metaTags' => $metaTags,
            'totalRow' => $totalRow,
            'rows' => $rows,
            'idCms' => $event->getId(),
            'rowsValue' => $rowsValue,
            'preview' => $preview,
            'eventPublished' => $eventPublished,
        ]);
    }

    /**
     * @param null $id
     * @return RedirectResponse
     */
    public function cloneAction($id = null)
    {
        $manager = $this->getDoctrine()->getManager();

        $repo = $this->getDoctrine()->getRepository(Event::class);

        $event = $repo->findOneBy(['id'=>$id]);
        $copy = clone $event;
        $copy->clearId();
        $copy->setIsActive(false);
        $url = $this->getDoctrine()
            ->getRepository(Event::class)
            ->getSlug($event->getSlug());
        $title = $this->getDoctrine()
            ->getRepository(Event::class)
            ->getTitle($event->getTitle());

        $copy->setUrl($url);
        $copy->setTitle($title);

        if ($event->getBannerDesktop()) {
            $newNameImage = $this->addCopiedToImageName($event->getBannerDesktop());
            $copy->setBannerDesktop($newNameImage);
            $pathOld = "upload/cms/events/{$event->getBannerDesktop()}";
            $pathNew = "upload/cms/events/{$newNameImage}";
            if (is_file($pathOld) && !is_file($pathNew)) {
                copy($pathOld, $pathNew);
            }
        }
        if ($event->getBannerMobile()) {
            $newNameImage = $this->addCopiedToImageName($event->getBannerMobile());
            $copy->setBannerMobile($newNameImage);
            $pathOld = "upload/cms/events/{$event->getBannerMobile()}";
            $pathNew = "upload/cms/events/{$newNameImage}";
            if (is_file($pathOld) && !is_file($pathNew)) {
                copy($pathOld, $pathNew);
            }
        }


        foreach ($event->getBlocks() as $block){
            $blockCopie = clone $block;
            $blockCopie->clearId();
            if ($block->getImageName()) {
                $newNameImage = $this->addCopiedToImageName($block->getImageName());
                $blockCopie->setImageName($newNameImage);
                $pathOld = "upload/cms/blocks/{$block->getImageName()}";
                $pathNew = "upload/cms/blocks/{$newNameImage}";
                if (is_file($pathOld) && !is_file($pathNew)) {
                    copy($pathOld, $pathNew);
                }
            }
            $manager->persist($blockCopie);

            $copy->addBlock($blockCopie);
        }
        $manager->persist($copy);
        $manager->flush();
        $this->addFlash(
            'sonata_flash_success',
            'L\'evenementa été dupliquer avec succés.'
        );
        return $this->redirectToRoute("sonata_admin_event_edit",['id' => $copy->getId()]);
    }

    public function getRow($event) {
        $blocks = $event->getBlocks();
        $row = 1;
        foreach ($blocks as $block) {
            $row = ($block->getRow() > $row) ? $block->getRow() : $row;
        }
        return $row;
    }

    /**
     * @param int|string|null $id
     * @return RedirectResponse|Response
     */
    public function deleteAction($id)
    {
        $EventRepo= $this->getDoctrine()->getRepository(Event::class);
        $event = $EventRepo->findOneBy(['id' => $id]);
        if (!$event) {
            throw new NotFoundHttpException('Event not found');
        }
        $manager = $this->getDoctrine()->getManager();

        $eventPublicRepo = $this->getDoctrine()->getRepository(EventPublished::class);
        $eventPublished = $eventPublicRepo->findOneBy(['event' => $event]);
        if ($eventPublished) {
            $this->clearArticlePublic($eventPublished);
            $manager->remove($eventPublished);
        }

        $repoRow = $this->getDoctrine()->getRepository(Row::class);
        $rows = $repoRow->findBy(['idCms' => $id, 'type' => 'event']);
        foreach ($rows as $row) {
            $manager->remove($row);
        }
        $manager->remove($event);
        $manager->flush();

        $this->addFlash(
            'sonata_flash_success',
            "L'evenement n° $id a été supprimé avec succés."
        );

        return $this->redirectToRoute("sonata_admin_event_list");
    }

    public function publishAction($id)
    {
        $repo = $this->getDoctrine()->getRepository(Event::class);
        $event = $repo->findOneBy(['id' => $id]);

        if (!$event) {
            throw new NotFoundHttpException('Event not found');
        }

        $eventPublishedRepo = $this->getDoctrine()->getRepository(EventPublished::class);
        $eventPublished = $eventPublishedRepo->findOneBy(['event' => $event]);

        if ($eventPublished && $event->getIsActive() == true) {
            $eventPublished->setIsActive(false);
            $event->setIsActive(false);
            $message = "L'evenement n° $id a été annulé avec succés.";
        } elseif ($eventPublished && $event->getIsActive() == false) {
            $this->cloneInPublic($event);
            $event->setIsActive(true);
            $eventPublished->setIsActive(true);
            $message = "L'evenement n° $id a été publié avec succés.";
        } else {
            $this->cloneInPublic($event);
            $event->setIsActive(true);
            $message = "L'evenement n° $id a été publié avec succés.";
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->flush();

        $this->addFlash(
            'sonata_flash_success',
            $message
        );

        return $this->redirectToRoute("sonata_admin_event_list");
    }

    public function addCopiedToImageName($imageName)
    {
        $array = explode('.', $imageName);
        $newNameImage = $array[0] . '-copied.' . $array[1];
        return $newNameImage;
    }

    private function addHeureToDate(Event $event)
    {
        $dateDebut = $event->getDateDebut();
        $heureDebut = $event->getHeureDebut();
        $dd = new \DateTime();
        $dd->setDate($dateDebut->format('Y'), $dateDebut->format('m'), $dateDebut->format('d'));
        if ($heureDebut) {
            $dd->setTime($heureDebut->format('H'), $heureDebut->format('i'), $dateDebut->format('s'));
        }
        $event->setDateDebut($dd);
        if ($event->getDateFin() && $event->getHeureFin()) {
            $dateFin = $event->getDateFin();
            $heureFin = $event->getHeureFin();
            $df = new \DateTime();
            $df->setDate($dateFin->format('Y'), $dateFin->format('m'), $dateFin->format('d'));
            if ($heureFin) {
                $df->setTime($heureFin->format('H'), $heureFin->format('i'), $heureFin->format('s'));
            }
            $event->setDateFin($df);
        }
    }
}
