<?php

namespace EgioDigital\CMSBundle\Controller;

use Cocur\Slugify\Slugify;
use EgioDigital\CMSBundle\Entity\Article;
use EgioDigital\CMSBundle\Entity\ArticlePublished;
use EgioDigital\CMSBundle\Entity\BlockPublished;
use EgioDigital\CMSBundle\Entity\Row;
use EgioDigital\CMSBundle\Entity\RowPublished;
use EgioDigital\CMSBundle\Form\ArticleType;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminArticleController extends CRUDController
{

    protected function clearArticlePublic(ArticlePublished $articlePublished)
    {
        if ($articlePublished) {
            if ($articlePublished->getBannerDesktop()) {
                $path = "upload/cms/articles-public/{$articlePublished->getBannerDesktop()}";
                if (is_file($path)) {
                    unlink($path);
                }
            }
            if ($articlePublished->getBannerMobile()) {
                $path = "upload/cms/articles-public/{$articlePublished->getBannerMobile()}";
                if (is_file($path)) {
                    unlink($path);
                }
            }
            foreach ($articlePublished->getBlocks() as $block) {
                if ($block->getImageName()) {
                    $path = "upload/cms/articles-public/blocks/{$block->getImageName()}";
                    if (is_file($path)) {
                        unlink($path);
                    }
                }
                $articlePublished->removeBlock($block);
            }
            $manager = $this->getDoctrine()->getManager();
            $repoRow = $this->getDoctrine()->getRepository(RowPublished::class);
            $rows = $repoRow->findBy(['idCms' => $articlePublished, 'type' => 'article']);
            foreach ($rows as $row) {
                $manager->remove($row);
            }
        }
    }

    protected function cloneInPublic(Article $article)
    {
        $articlePublicRepo = $this->getDoctrine()->getRepository(ArticlePublished::class);
        $articlePublished = $articlePublicRepo->findOneBy(['article' => $article]);
        if ($articlePublished) {
            //$this->clearArticlePublic($articlePublished);
            foreach ($articlePublished->getBlocks() as $block) {
                $articlePublished->removeBlock($block);
            }
            $manager = $this->getDoctrine()->getManager();
            $repoRow = $this->getDoctrine()->getRepository(RowPublished::class);
            $rows = $repoRow->findBy(['idCms' => $articlePublished, 'type' => 'article']);
            foreach ($rows as $row) {
                $manager->remove($row);
            }
        } else {
            $articlePublished = new ArticlePublished();
        }
        $articlePublished->setArticle($article)
            ->setTitle($article->getTitle())
            ->setCategory($article->getCategory())
            ->setLang($article->getLang())
            ->setSlug($article->getSlug())
            ->setMetaTitle($article->getMetaTitle())
            ->setMetaTags($article->getMetaTags())
            ->setMetaDescription($article->getMetaDescription())
            ->setHtmlIdAttr($article->getHtmlIdAttr())
            ->setHtmlClassAttr($article->getHtmlClassAttr())
            ->setContent($article->getContent())
            ->setIsActive(true)
            ->setBannerDesktop($article->getBannerDesktop())
            ->setBannerMobile($article->getBannerMobile())
            ->setDateTri($article->getDateTri())
            ;

        if ($article->getBannerDesktop()) {
            $pathOld = "upload/cms/articles/{$article->getBannerDesktop()}";
            $pathNew = "upload/cms/articles-public/{$article->getBannerDesktop()}";
            if (is_file($pathOld) && !is_file($pathNew)) {
                copy($pathOld, $pathNew);
            }
        }
        if ($article->getBannerMobile()) {
            $pathOld = "upload/cms/articles/{$article->getBannerMobile()}";
            $pathNew = "upload/cms/articles-public/{$article->getBannerMobile()}";
            if (is_file($pathOld) && !is_file($pathNew)) {
                copy($pathOld, $pathNew);
            }
        }
        $manager = $this->getDoctrine()->getManager();
        foreach ($article->getBlocks() as $block){
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
                ->setArticle($articlePublished)
                ->setPosition($block->getPosition())
                ->setRow($block->getRow())
                ->setColLarge($block->getColLarge())
                ->setClearfix($block->getClearfix())
            ;
            if ($block->getImageName()) {
                $pathOld = "upload/cms/blocks/{$block->getImageName()}";
                $pathNew = "upload/cms/articles-public/blocks/{$block->getImageName()}";
                if (is_file($pathOld) && !is_file($pathNew)) {
                    copy($pathOld, $pathNew);
                }
            }
            $articlePublished->addBlock($blockPublished);
            $manager->persist($blockPublished);
        }
        $manager->flush();

        $repoRow = $this->getDoctrine()->getRepository(Row::class);
        $rows = $repoRow->findBy(['idCms' => $article->getId(), 'type' => 'article']);
        foreach ($rows as $row) {
            $rowPublished = new RowPublished();
            $rowPublished->setType('article')
                ->setIdCms($articlePublished->getId())
                ->setClass($row->getClass())
                ->setIdHtml($row->getIdHtml())
                ->setIndexRow($row->getIndexRow());
            $manager->persist($rowPublished);
        }
        $manager->persist($articlePublished);
        $manager->flush();
    }

    public function createAction()
    {
        $article = new Article();

        $request = $this->getRequest();
        $manager = $this->getDoctrine()->getManager();

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        $preview = false;
        $articlePublished = null;

        if ( $form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();

            if (empty($article->getSlug())){
                $slug = $slugify->slugify($article->getTitle());
            } else {
                $slug = $slugify->slugify($article->getSlug());
            }
            $article->setSlug($slug);

            $article->setIsActive(false);

            if ($form->get('savePublic')->isClicked()) {
                $article->setIsActive(true);
            }

            $manager->persist($article);
            $manager->flush();

            $totalRow = $request->get('totalRow');
            for ($i = 1; $i<= $totalRow; $i++) {
                $class = !empty($request->get('classRow_'.$i)) ? $request->get('classRow_'.$i) : 'class'.$i;
                $id = !empty($request->get('idRow_'.$i)) ? $request->get('idRow_'.$i) : 'id'.$i;

                $row = new Row();
                $row->setClass($class)
                    ->setIndexRow($i)
                    ->setIdHtml($id)
                    ->setIdCms($article->getId())
                    ->setType('article');
                $manager->persist($row);
            }
            $manager->flush();
            $this->addFlash(
                'sonata_flash_success',
                'L\'Article a été crée avec succès. Pensez a créer une version anglaise a votre article!'
            );

            if ($form->get('savePublic')->isClicked()) {
                $this->cloneInPublic($article);
            }
            return $this->redirectToRoute("sonata_admin_article_list");
        }

        return $this->renderWithExtraParams('@CMSBundleViews/views/admin/article/create.html.twig', [
            'form' => $form->createView(),
            'action' => 'create',
            'article' => $article,
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
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $repoRow = $this->getDoctrine()->getRepository(Row::class);
        $article = $repo->findOneBy(['id' => $id]);
        $rowsValue = [];

        if (!$article) {
            throw new NotFoundHttpException('Article not found');
        }
        $totalRow = $this->getRow($article);
        $request = $this->getRequest();
        $manager = $this->getDoctrine()->getManager();
        $rows = $repoRow->findBy([
            'idCms' => $article->getId(),
            'type' => 'article'
        ]);
        foreach ($rows as  $row) {
            $rowsValue[$row->getIndexRow()] = [
                'class' => $row->getClass(),
                'id' => $row->getIdHtml(),
            ];
        }
        $rowsValue = json_encode($rowsValue);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        $preview = false;
        $articlePublished = null;
        if ( $form->isSubmitted() && $form->isValid()) {
            $totalRow = $request->get('totalRow');
            for ($i = 1; $i<= $totalRow; $i++) {
                $class = !empty($request->get('classRow_'.$i)) ? $request->get('classRow_'.$i) : 'class'.$i;
                $idhtml = !empty($request->get('idRow_'.$i)) ? $request->get('idRow_'.$i) : 'id'.$i;
                $row = $repoRow->findOneBy(['idCms' => $article->getId(), 'indexRow' => $i, 'type' => 'article']);
                if(empty($row)) {
                    $row = new Row();
                }

                $row->setClass($class)
                    ->setIndexRow($i)
                    ->setIdHtml($idhtml)
                    ->setIdCms($article->getId())
                    ->setType('article');
                $manager->persist($row);
                $manager->flush();
            }

            $slugify = new Slugify();

            if (empty($article->getSlug())){
                $slug = $slugify->slugify($article->getTitle());
            } else {
                $slug = $slugify->slugify($article->getSlug());
            }
            $article->setSlug($slug);

            $article->setIsActive(false);

            if ($form->get('savePublic')->isClicked()) {
                $article->setIsActive(true);
            }
            $articlePublicRepo = $this->getDoctrine()->getRepository(ArticlePublished::class);
            $articlePublished = $articlePublicRepo->findOneBy(['article' => $article]);

            if ($form->get('saveDraft')->isClicked()) {
                $article->setIsActive(false);
                if ($articlePublished) {
                    //$articlePublished->setIsActive(false);
                }
            }

            $manager->persist($article);
            $manager->flush();
            $this->addFlash(
                'sonata_flash_success',
                'L\'article a été modifié avec succés.'
            );

            if ($form->get('saveDraft')->isClicked()) {
                return $this->redirectToRoute("sonata_admin_article_list");
            }

            if (!$form->get('preview')->isClicked()) {
                $this->cloneInPublic($article);
                if ($articlePublished) {
                    $articlePublished->setIsActive(true);
                }
                $manager->flush();
                return $this->redirectToRoute("sonata_admin_article_list");
            }

            $preview = true;
            //$this->cloneInPublic($article);
            //$articlePublished = $articlePublicRepo->findOneBy(['article' => $article]);
            //$articlePublished->setIsActive(false);
            $manager->flush();
        }
        $metaTags = explode(',', $article->getMetaTags());
        return $this->renderWithExtraParams('@CMSBundleViews/views/admin/article/create.html.twig', [
            'form' => $form->createView(),
            'action' => 'edit',
            'article' => $article,
            'metaTags' => $metaTags,
            'totalRow' => $totalRow,
            'rows' => $rows,
            'idCms' => $article->getId(),
            'rowsValue' => $rowsValue,
            'preview' => $preview,
            'articlePublished' => $articlePublished,
        ]);
    }

    /**
     * @param null $id
     * @return RedirectResponse
     */
    public function cloneAction($id = null)
    {
        $manager = $this->getDoctrine()->getManager();

        $repo = $this->getDoctrine()->getRepository(Article::class);

        $article = $repo->findOneBy(['id'=>$id]);

        $copy = clone $article;
        $copy->clearId();
        $copy->setIsActive(false);
        $slug = $this->getDoctrine()
            ->getRepository(Article::class)
            ->getSlug($article->getSlug());
        $title = $this->getDoctrine()
            ->getRepository(Article::class)
            ->getTitle($article->getTitle());

        $copy->setSlug($slug);
        $copy->setTitle($title);

        if ($article->getBannerDesktop()) {
            $newNameImage = $this->addCopiedToImageName($article->getBannerDesktop());
            $copy->setBannerDesktop($newNameImage);
            $pathOld = "upload/cms/articles/{$article->getBannerDesktop()}";
            $pathNew = "upload/cms/articles/{$newNameImage}";
            if (is_file($pathOld) && !is_file($pathNew)) {
                copy($pathOld, $pathNew);
            }
        }
        if ($article->getBannerMobile()) {
            $newNameImage = $this->addCopiedToImageName($article->getBannerMobile());
            $copy->setBannerMobile($newNameImage);
            $pathOld = "upload/cms/articles/{$article->getBannerMobile()}";
            $pathNew = "upload/cms/articles/{$newNameImage}";
            if (is_file($pathOld) && !is_file($pathNew)) {
                copy($pathOld, $pathNew);
            }
        }


        foreach ($article->getBlocks() as $block){
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
            'L\'article a été dupliquer avec succés.'
        );
        return $this->redirectToRoute("sonata_admin_article_edit",['id' => $copy->getId()]);
    }

    public function getRow($article) {
        $blocks = $article->getBlocks();
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
        $repoArticle = $this->getDoctrine()->getRepository(Article::class);
        $article = $repoArticle->findOneBy(['id' => $id]);
        if (!$article) {
            throw new NotFoundHttpException('Article not found');
        }
        $manager = $this->getDoctrine()->getManager();

        $articlePubRepo = $this->getDoctrine()->getRepository(ArticlePublished::class);
        $articlePub = $articlePubRepo->findOneBy(['article' => $article]);
        if ($articlePub) {
            $this->clearArticlePublic($articlePub);
            $manager->remove($articlePub);
            $manager->flush();

        }

        $repoRow = $this->getDoctrine()->getRepository(Row::class);
        $rows = $repoRow->findBy(['idCms' => $id, 'type' => 'article']);
        foreach ($rows as $row) {
            $manager->remove($row);
            $manager->flush();
        }
        $manager->remove($article);
        $manager->flush();

        $this->addFlash(
            'sonata_flash_success',
            "L'article n° $id a été supprimé avec succés."
        );

        return $this->redirectToRoute("sonata_admin_article_list");
    }

    public function publishAction($id)
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $article = $repo->findOneBy(['id' => $id]);

        if (!$article) {
            throw new NotFoundHttpException('Article not found');
        }

        $repoArticlePublished = $this->getDoctrine()->getRepository(ArticlePublished::class);
        $articlePublished = $repoArticlePublished->findOneBy(['article' => $article]);

        if ($articlePublished && $article->getIsActive() == true) {
            $article->setIsActive(false);
            $articlePublished->setIsActive(false);
            $message = "L'article n° $id a été annulé avec succés.";
        } elseif ($articlePublished && $article->getIsActive() == false) {
            $this->cloneInPublic($article);
            $article->setIsActive(true);
            $articlePublished->setIsActive(true);
            $message = "L'article n° $id a été publié avec succés.";
        } else {
            $this->cloneInPublic($article);
            $article->setIsActive(true);
            $message = "L'article n° $id a été publié avec succés.";
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->flush();

        $this->addFlash(
            'sonata_flash_success',
            $message
        );

        return $this->redirectToRoute("sonata_admin_article_list");
    }

    public function addCopiedToImageName($imageName)
    {
        $array = explode('.', $imageName);
        $newNameImage = $array[0] . '-copied.' . $array[1];
        return $newNameImage;
    }
}
