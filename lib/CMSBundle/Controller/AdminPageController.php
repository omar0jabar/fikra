<?php

namespace EgioDigital\CMSBundle\Controller;

use Cocur\Slugify\Slugify;
use EgioDigital\CMSBundle\Entity\BlockPublished;
use EgioDigital\CMSBundle\Entity\Page;
use EgioDigital\CMSBundle\Entity\PagePublished;
use EgioDigital\CMSBundle\Entity\Row;
use EgioDigital\CMSBundle\Entity\RowPublished;
use EgioDigital\CMSBundle\Form\PageType;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminPageController extends CRUDController
{

    protected function clearPagePublic(PagePublished $pagePublished)
    {
        if ($pagePublished) {
            foreach ($pagePublished->getBlocks() as $block) {
                if ($block->getImageName()) {
                    $path = "upload/cms/pages-public/blocks/{$block->getImageName()}";
                    if (is_file($path)) {
                        unlink($path);
                    }
                }
                $pagePublished->removeBlock($block);
            }

            if ($pagePublished->getBannerDesktop()) {
                $path = "upload/cms/pages-public/{$pagePublished->getBannerDesktop()}";
                if (is_file($path)) {
                    unlink($path);
                }
            }
            if ($pagePublished->getBannerMobile()) {
                $path = "upload/cms/pages-public/{$pagePublished->getBannerMobile()}";
                if (is_file($path)) {
                    unlink($path);
                }
            }
            $manager = $this->getDoctrine()->getManager();
            $repoRow = $this->getDoctrine()->getRepository(RowPublished::class);
            $rows = $repoRow->findBy(['idCms' => $pagePublished, 'type' => 'page']);
            foreach ($rows as $row) {
                $manager->remove($row);
            }
        }
    }

    protected function cloneInPublic(Page $page)
    {
        $pagePublishedRepo = $this->getDoctrine()->getRepository(PagePublished::class);
        $pagePublished = $pagePublishedRepo->findOneBy(['page' => $page]);
        if ($pagePublished) {
            //$this->clearPagePublic($pagePublished);
            foreach ($pagePublished->getBlocks() as $block) {
                $pagePublished->removeBlock($block);
            }
            $manager = $this->getDoctrine()->getManager();
            $repoRow = $this->getDoctrine()->getRepository(RowPublished::class);
            $rows = $repoRow->findBy(['idCms' => $pagePublished, 'type' => 'page']);
            foreach ($rows as $row) {
                $manager->remove($row);
            }
        } else {
            $pagePublished = new PagePublished();
        }
        $pagePublished->setPage($page)
            ->setTitle($page->getTitle())
            ->setCategory($page->getCategory())
            ->setLang($page->getLang())
            ->setSlug($page->getSlug())
            ->setMetaTitle($page->getMetaTitle())
            ->setMetaTags($page->getMetaTags())
            ->setMetaDescription($page->getMetaDescription())
            ->setHtmlIdAttr($page->getHtmlIdAttr())
            ->setHtmlClassAttr($page->getHtmlClassAttr())
            ->setIsActive(true)
            ->setIsPageSimple($page->getIsPageSimple())
            ->setBannerDesktop($page->getBannerDesktop())
            ->setBannerMobile($page->getBannerMobile())
            ->setContentBanner($page->getContentBanner())
        ;

        if ($page->getBannerDesktop()) {
            $pathOld = "upload/cms/pages/{$page->getBannerDesktop()}";
            $pathNew = "upload/cms/pages-public/{$page->getBannerDesktop()}";
            if (is_file($pathOld) && !is_file($pathNew)) {
                copy($pathOld, $pathNew);
            }
        }
        if ($page->getBannerMobile()) {
            $pathOld = "upload/cms/pages/{$page->getBannerMobile()}";
            $pathNew = "upload/cms/pages-public/{$page->getBannerMobile()}";
            if (is_file($pathOld) && !is_file($pathNew)) {
                copy($pathOld, $pathNew);
            }
        }
        $manager = $this->getDoctrine()->getManager();
        foreach ($page->getBlocks() as $block){
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
                ->setPage($pagePublished)
                ->setArticle(null)
                ->setPosition($block->getPosition())
                ->setRow($block->getRow())
                ->setColLarge($block->getColLarge())
                ->setClearfix($block->getClearfix())
            ;
            if ($block->getImageName()) {
                $pathOld = "upload/cms/blocks/{$block->getImageName()}";
                $pathNew = "upload/cms/pages-public/blocks/{$block->getImageName()}";
                if (is_file($pathOld) && !is_file($pathNew)) {
                    copy($pathOld, $pathNew);
                }
            }
            $pagePublished->addBlock($blockPublished);
            $manager->persist($blockPublished);
        }
        $manager->flush();

        $repoRow = $this->getDoctrine()->getRepository(Row::class);
        $rows = $repoRow->findBy(['idCms' => $page->getId(), 'type' => 'page']);
        foreach ($rows as $row) {
            $rowPublished = new RowPublished();
            $rowPublished->setType('page')
                ->setIdCms($pagePublished->getId())
                ->setClass($row->getClass())
                ->setIdHtml($row->getIdHtml())
                ->setIndexRow($row->getIndexRow());
            $manager->persist($rowPublished);
        }
        $manager->persist($pagePublished);
        $manager->flush();
    }

    public function createAction()
    {
        $page = new Page();
        $request = $this->getRequest();
        $manager = $this->getDoctrine()->getManager();

        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid()) {

            $slugify = new Slugify();

            if (empty($page->getSlug())){
                $slug = $slugify->slugify($page->getTitle());
            } else {
                $slug = $slugify->slugify($page->getSlug());
            }
            $page->setSlug($slug);

            $page->setIsActive(false);

            if ($form->get('savePublic')->isClicked()) {
                $page->setIsActive(true);
            }
            $manager->persist($page);
            $manager->flush();

            $totalRow = $request->get('totalRow');
            for ($i = 1; $i<= $totalRow; $i++) {
                $class = !empty($request->get('classRow_'.$i)) ? $request->get('classRow_'.$i) : 'class'.$i;
                $id = !empty($request->get('idRow_'.$i)) ? $request->get('idRow_'.$i) : 'id'.$i;
                $row = new Row();
                $row->setClass($class)
                    ->setIndexRow($i)
                    ->setIdHtml($id)
                    ->setIdCms($page->getId())
                    ->setType('page');
                $manager->persist($row);
            }

            $manager->flush();

            $this->addFlash(
                'sonata_flash_success',
                'La Page a été créé avec succès.'
            );

            if ($form->get('savePublic')->isClicked()) {
                $this->cloneInPublic($page);
            }
            return $this->redirectToRoute("sonata_admin_page_list");
        }
        return $this->renderWithExtraParams('@CMSBundleViews/views/admin/page/create.html.twig', [
            'form' => $form->createView(),
            'totalBlocks' => 0,
            'action' => 'create',
            'page' => $page,
            'metaTags' => [],
            'totalRow' => 0,
            'rows' => [],
            'idCms' => 0,
            'rowsValue' => 0,
            'preview' => false
        ]);
    }

    public function editAction($id = null)
    {
        $repo = $this->getDoctrine()->getRepository(Page::class);
        $repoRow = $this->getDoctrine()->getRepository(Row::class);
        $page = $repo->findOneBy(['id' => $id]);
        $rowsValue = [];

        if (!$page) {
            throw new NotFoundHttpException('Page not found');
        }

        $request = $this->getRequest();
        $manager = $this->getDoctrine()->getManager();

        $totalRow = $this->getRow($page);
        $rows = $repoRow->findBy([
            'idCms' => $page->getId(),
            'type' => 'page'
        ]);

        foreach ($rows as  $row) {
            $rowsValue[$row->getIndexRow()] = [
                'class' => $row->getClass(),
                'id' => $row->getIdHtml(),
            ];
        }
        $rowsValue = json_encode($rowsValue);

        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);
        $preview = false;
        $pagePublished = null;

        if ( $form->isSubmitted() && $form->isValid()) {

            $totalRow = $request->get('totalRow');
            for ($i = 1; $i<= $totalRow; $i++) {
                $class = !empty($request->get('classRow_'.$i)) ? $request->get('classRow_'.$i) : 'class'.$i;
                $id = !empty($request->get('idRow_'.$i)) ? $request->get('idRow_'.$i) : 'id'.$i;
                $row = $repoRow->findOneBy(['idCms' => $page->getId(), 'indexRow' => $i, 'type' => 'page']);
                if(empty($row)) {
                    $row = new Row();
                }
                $row->setClass($class)
                    ->setIndexRow($i)
                    ->setIdHtml($id)
                    ->setIdCms($page->getId())
                    ->setType('page');

                $manager->persist($row);
            }

            $slugify = new Slugify();

            if (empty($page->getSlug())){
                $slug = $slugify->slugify($page->getTitle());
            } else {
                $slug = $slugify->slugify($page->getSlug());
            }
            $page->setSlug($slug);

            $page->setIsActive(false);

            if ($form->get('savePublic')->isClicked()) {
                $page->setIsActive(true);
            }

            $pagePublishedRepo = $this->getDoctrine()->getRepository(PagePublished::class);
            $pagePublished = $pagePublishedRepo->findOneBy(['page' => $page]);

            if ($form->get('saveDraft')->isClicked()) {
                $page->setIsActive(false);
                if ($pagePublished) {
                    //$pagePublished->setIsActive(false);
                }
            }

            $manager->persist($page);
            $manager->flush();
            $this->addFlash(
                'sonata_flash_success',
                'La Page a été créé avec succès.'
            );

            if ($form->get('saveDraft')->isClicked()) {
                return $this->redirectToRoute("sonata_admin_page_list");
            }

            if (!$form->get('preview')->isClicked()) {
                $this->cloneInPublic($page);
                //$pagePublished->setIsActive(true);
                $manager->flush();
                return $this->redirectToRoute("sonata_admin_page_list");
            }

            $preview = true;
            //$this->cloneInPublic($page);
            //$pagePublished = $pagePublishedRepo->findOneBy(['page' => $page]);
            //$pagePublished->setIsActive(false);
            $manager->flush();
        }
        // sort($forms)
        $metaTags = explode(',', $page->getMetaTags());

        return $this->renderWithExtraParams('@CMSBundleViews/views/admin/page/create.html.twig', [
            'form' => $form->createView(),
            'action' => 'edit',
            'page' => $page,
            'metaTags' => $metaTags,
            'totalRow' => $totalRow,
            'rows' => $rows,
            'idCms' => $page->getId(),
            'rowsValue' => $rowsValue,
            'preview' => $preview,
            'pagePublished' => $pagePublished,
        ]);
    }

    public function cloneAction($id = null)
    {
        $repo = $this->getDoctrine()->getRepository(Page::class);
        $page = $repo->findOneBy(['id'=>$id]);

        if (!$page) {
            throw new NotFoundHttpException('Page not found');
        }

        $manager = $this->getDoctrine()->getManager();

        $copy = clone $page;
        $copy->clearId();
        $copy->setIsActive(false);
        $slug = $this->getDoctrine()
            ->getRepository(Page::class)
            ->getSlug($page->getSlug());

        $title = $this->getDoctrine()
            ->getRepository(Page::class)
            ->getTitle($page->getTitle());

        $copy->setSlug($slug);
        $copy->setTitle($title);

        if ($page->getBannerDesktop()) {
            $newNameImage = $this->addCopiedToImageName($page->getBannerDesktop());
            $copy->setBannerDesktop($newNameImage);
            $pathOld = "upload/cms/pages/{$page->getBannerDesktop()}";
            $pathNew = "upload/cms/pages/{$newNameImage}";
            if (is_file($pathOld) && !is_file($pathNew)) {
                copy($pathOld, $pathNew);
            }
        }
        if ($page->getBannerMobile()) {
            $newNameImage = $this->addCopiedToImageName($page->getBannerMobile());
            $copy->setBannerMobile($newNameImage);
            $pathOld = "upload/cms/pages/{$page->getBannerMobile()}";
            $pathNew = "upload/cms/pages/{$newNameImage}";
            if (is_file($pathOld) && !is_file($pathNew)) {
                copy($pathOld, $pathNew);
            }
        }

        foreach ($page->getBlocks() as $block){
            $blockCopie = clone $block;
            $blockCopie->clearId();
            $manager->persist($blockCopie);
            if ($block->getImageName()) {
                $newNameImage = $this->addCopiedToImageName($block->getImageName());
                $blockCopie->setImageName($newNameImage);
                $pathOld = "upload/cms/blocks/{$block->getImageName()}";
                $pathNew = "upload/cms/blocks/{$newNameImage}";
                if (is_file($pathOld) && !is_file($pathNew)) {
                    copy($pathOld, $pathNew);
                }
            }
            $copy->addBlock($blockCopie);
        }
        $manager->persist($copy);
        $manager->flush();

        $this->addFlash(
            'sonata_flash_success',
            'La Page a été Dupliquer avec succéss.'
        );
        return $this->redirectToRoute("sonata_admin_page_edit",['id' => $copy->getId()]);
    }

    public function deleteAction($id)
    {
        $pageRepo = $this->getDoctrine()->getRepository(Page::class);
        $page = $pageRepo->findOneBy(['id' => $id]);
        if (!$page) {
            throw new NotFoundHttpException('Page not found');
        }

        $array = [1085,1087,1088,1089,1093,1094,1095,1096,1099,1100,1102,1103,1104,1105,1106,1107];
        if (in_array($id, $array)) {
            $this->addFlash(
                'danger',
                "La page n° $id fait partie de la configuration du site."
            );
        } else {
            $manager = $this->getDoctrine()->getManager();
            $pagePubRepo = $this->getDoctrine()->getRepository(PagePublished::class);
            $pagePublished = $pagePubRepo->findOneBy(['page' => $page]);
            if ($pagePublished) {
                $this->clearPagePublic($pagePublished);
                $manager->remove($pagePublished);
            }

            $repoRow = $this->getDoctrine()->getRepository(Row::class);
            $rows = $repoRow->findBy(['idCms' => $id, 'type' => 'page']);
            foreach ($rows as $row) {
                $manager->remove($row);
            }
            $manager->remove($page);
            $manager->flush();
            $this->addFlash(
                'sonata_flash_success',
                "La page n° $id a été supprimé avec succés."
            );
        }
        return $this->redirectToRoute("sonata_admin_page_list");
    }

    public function publishAction($id)
    {
        $repo = $this->getDoctrine()->getRepository(Page::class);
        $page = $repo->findOneBy(['id' => $id]);

        if (!$page) {
            throw new NotFoundHttpException('Page not found');
        }

        $pagePubRepo = $this->getDoctrine()->getRepository(PagePublished::class);
        $pagePub = $pagePubRepo->findOneBy(['page' => $page]);

        if ($pagePub && $page->getIsActive() == true) {
            $pagePub->setIsActive(false);
            $page->setIsActive(false);
            $message = "La page n° $id a été annulé avec succés.";
        } elseif ($pagePub && $page->getIsActive() == false) {
            $this->cloneInPublic($page);
            $page->setIsActive(true);
            $pagePub->setIsActive(true);
            $message = "La page n° $id a été publié avec succés.";
        }else {
            $this->cloneInPublic($page);
            $page->setIsActive(true);
            $message = "La page n° $id a été publié avec succés.";
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->flush();

        $this->addFlash(
            'sonata_flash_success',
            $message
        );

        return $this->redirectToRoute("sonata_admin_page_list");
    }

    public function getRow($page) {
        $blocks = $page->getBlocks();
        $row = 1;
        foreach ($blocks as $block) {
            $row = ($block->getRow() > $row) ? $block->getRow() : $row;
        }
        return $row;
    }

    public function addCopiedToImageName($imageName)
    {
        $array = explode('.', $imageName);
        $newNameImage = $array[0] . '-copied.' . $array[1];
        return $newNameImage;
    }

}
