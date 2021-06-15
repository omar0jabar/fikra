<?php

namespace EgioDigital\CMSBundle\Controller;

use App\Repository\HeaderRepository;
use Doctrine\Common\Persistence\ObjectManager;
use EgioDigital\CMSBundle\Entity\Article;
use EgioDigital\CMSBundle\Entity\ArticleLike;
use EgioDigital\CMSBundle\Entity\ArticlePublished;
use EgioDigital\CMSBundle\Entity\Row;
use EgioDigital\CMSBundle\Entity\ArticleView;
use EgioDigital\CMSBundle\Entity\CategoryArticle;
use EgioDigital\CMSBundle\Entity\RowPublished;
use EgioDigital\CMSBundle\Repository\ArticleLikeRepository;
use EgioDigital\CMSBundle\Repository\ArticlePublishedRepository;
use EgioDigital\CMSBundle\Repository\ArticleRepository;
use EgioDigital\CMSBundle\Repository\ArticleViewRepository;
use EgioDigital\CMSBundle\Repository\CategoryArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Image as ImageService;

/**
 * Class ArticleController
 * @package Egio\CMSbundle\Controller
 */
class ArticleController extends AbstractController
{
   /**
    * @Route("{_locale}/articles", name="cmsbundle_article_list")
    * @Route("/articles", name="cmsbundle_article_list_default", defaults={"_locale"="fr"})
    * @param ArticlePublishedRepository $articleRepository
    * @param Request $request
    * @param CategoryArticleRepository $categArticleRepo
    * @param HeaderRepository $headerRepository
    * @return Response
    */
   public function listAction(
       Request $request,
       HeaderRepository $headerRepository,
       CategoryArticleRepository $categArticleRepo,
       ArticlePublishedRepository $articleRepository
   ) {
       $locale = $request->getLocale();
       $header = $headerRepository->findOneBy(['page' => 'articles', 'lang' => $locale]);
       $currentCategories = $request->query->get('categories');
       $categoriesString = !empty($currentCategories) ? implode(",", $currentCategories) : null;
       $allCategories = $categArticleRepo->findAll();

       $query = $articleRepository->getAllQuery($locale, $categoriesString);

       $paginator  = $this->get('knp_paginator');
       $page = $request->query->getInt('page', 1);
       $pagination = $paginator->paginate(
           $query, /* query NOT result */
           $page, /*page number*/
           9 /*limit per page*/
       );
       $paginationNext = $paginator->paginate(
           $query, /* query NOT result */
           $page+1, /*page number*/
           9 /*limit per page*/
       );
       $more = 0;
       if (count($paginationNext) > 0) {
           $more = 1;
       }
      return $this->render('@CMS/front/article/list.html.twig', [
         'current_menu' => 'articles',
         'header' => $header,
         'countResults' => count($articleRepository->findBy(['lang' => $locale, 'isActive' => true])),
         'pagination' => $pagination,
         'allCategories' => $allCategories,
         'currentCategories' => $currentCategories,
         'more' => $more,
      ]);
   }



   /**
    * @Route("{_locale}/articles/scroll", name="cmsbundle_article_list_scroll")
    * @Route("/articles/scroll", name="cmsbundle_article_list_scroll_default", defaults={"_locale"="fr"})
    * @param ArticlePublishedRepository $articleRepository
    * @param Request $request
    * @return Response
    */
   public function scrollArticles(ArticlePublishedRepository $articleRepository, Request $request)
   {
       $categories = $request->query->get('categories');
       $categoriesString = !empty($categories) ? implode(",", $categories) : null;
       $locale = $request->getLocale();
       $query = $articleRepository->getAllQuery($locale, $categoriesString);
       $paginator  = $this->get('knp_paginator');
       $page = $request->query->getInt('page', 1);
       $pagination = $paginator->paginate(
           $query, /* query NOT result */
           $page, /*page number*/
           9 /*limit per page*/
       );
       $paginationNext = $paginator->paginate(
           $query, /* query NOT result */
           $page+1, /*page number*/
           9 /*limit per page*/
       );
       $more = 0;
       if (count($paginationNext) > 0) {
           $more = 1;
       }
      return $this->render('@CMS/front/article/scroll.html.twig', [
         'current_menu' => 'articles',
         'countResults' => count($pagination),
         'pagination' => $pagination,
          'categories' => $categories,
          'more' => $more,
      ]);
   }

   /**
    * @Route("{_locale}/articles/{id}-{slug}", name="cmsbundle_article_show")
    * @Route("/articles/{id}-{slug}", name="cmsbundle_article_show_default", defaults={"_locale"="fr"})
    * @param int $id
    * @param string $slug
    * @param ArticlePublished $article
    * @param Request $request
    * @param ArticleViewRepository $articleViewRepository
    * @param ObjectManager $manager
    * @param ArticlePublishedRepository $articleRepository
    * @return Response
    */
   public function showAction(int $id, string $slug, ArticlePublished $article, Request $request,
                              ArticleViewRepository $articleViewRepository, ObjectManager $manager,
                              ArticlePublishedRepository $articleRepository,
                              ImageService $imageService)
   {
      if ($article->getId() !== $id || $article->getSlug() !== $slug) {
         throw new NotFoundHttpException('Article not found');
      }

       if ($article->getIsActive() === false) {
           return $this->redirectToRoute('home');
       }

      $ip = $request->getClientIp();
      $locale = $request->getLocale();
      $oldView = $articleViewRepository->findOneBy(['article'=>$article, 'ip'=>$ip]);
      if (!$oldView) {
         $view = new ArticleView();
         $view->setArticle($article)
            ->setIp($ip);
         $article->addView($view);
         $article->setCountViews($article->getCountViews()+1);
         $manager->persist($article);
         $manager->flush();
      }

       $tags = [];
       if (!empty($article->getMetaTags())){
           $tags = explode(',', $article->getMetaTags());
       }

      $blocks = [];
      foreach ($article->getBlocks() as $block) {
          $blocks[$block->getRow()][] = $block;
      }
       ksort($blocks);

      $rows = $this->getRowsInfos($id);
       $width = null;
       $height = null;
      if ($article->getBannerDesktop()) {
          list($width,$height) = $imageService->getimagesize('article', $article->getBannerDesktop());
      }
      $metatitle = !empty($article->getMetaTitle()) ? $article->getMetaTitle() : $article->getTitle();
      $metadescription = !empty($article->getMetaDescription()) ? $article->getMetaDescription() : $article->getDescription();
      return $this->render('@CMS/front/article/show.html.twig', [
         'current_menu' => 'articles',
         'article' => $article,
         'tags' => $tags,
         'width' => $width,
         'height' => $height,
         'blocks' => $blocks,
         'articles' => $articleRepository->getArticlesSuggestion($article->getCategory(), $article->getId(), $locale),
          'rows' => $rows,
          'metatitle' => $metatitle,
          'metadescription' => $metadescription,
      ]);
   }

    /**
     * @Route("/boadmin/article/show/{id}-{slug}", name="cmsbundle_article_preview")
     * @param int $id
     * @param string $slug
     * @param Article $article
     * @param ArticlePublishedRepository $articleRepository
     * @return Response
     */
    public function previewAction(int $id, string $slug, Article $article, ArticlePublishedRepository $articleRepository)
    {
        if ($article->getId() !== $id || $article->getSlug() !== $slug) {
            throw new NotFoundHttpException('Article not found');
        }

        $tags = [];
        if (!empty($article->getMetaTags())){
            $tags = explode(',', $article->getMetaTags());
        }

        $blocks = [];
        foreach ($article->getBlocks() as $block) {
            $blocks[$block->getRow()][] = $block;
        }
        ksort($blocks);

        $rows = $this->getRowsInfosPreview($id);

        return $this->render('@CMS/front/article/preview.html.twig', [
            'current_menu' => 'articles',
            'article' => $article,
            'tags' => $tags,
            'blocks' => $blocks,
            'articles' => $articleRepository->getArticlesSuggestion($article->getCategory(), $article->getId()),
            'rows' => $rows,
        ]);
    }

    /**
     * @Route("/boadmin/article/public/show/{id}-{slug}", name="cmsbundle_article_preview_public")
     * @param int $id
     * @param string $slug
     * @param ArticlePublished $article
     * @param ArticlePublishedRepository $articleRepository
     * @return Response
     */
    public function previewPublicAction(int $id, string $slug, ArticlePublished $article,
                               ArticlePublishedRepository $articleRepository)
    {
        if ($article->getId() !== $id || $article->getSlug() !== $slug) {
            throw new NotFoundHttpException('Article not found');
        }

        $tags = explode(',', $article->getMetaTags());

        $blocks = [];
        foreach ($article->getBlocks() as $block) {
            $blocks[$block->getRow()][] = $block;
        }
        ksort($blocks);

        $rows = $this->getRowsInfos($id);

        return $this->render('@CMS/front/article/show.html.twig', [
            'current_menu' => 'articles',
            'article' => $article,
            'tags' => $tags,
            'blocks' => $blocks,
            'articles' => $articleRepository->getArticlesSuggestion($article->getCategory(), $article->getId()),
            'rows' => $rows,
        ]);
    }

   /**
    * @Route("/boadmin/article/list-categories-by-lang", name="list_categories_by_lang")
    * @param Request $request
    * @return JsonResponse
    */
   public function listCategoriesByLang(Request $request)
   {
      $em = $this->getDoctrine()->getManager();
      $repoCat = $em->getRepository(CategoryArticle::class);

      $categories = $repoCat->createQueryBuilder("c")
         ->where("c.lang = :lang")
         ->setParameter("lang", $request->query->get("lang"))
         ->getQuery()
         ->getResult();

      $responseArray = array();
      foreach($categories as $category){
         /* @var CategoryArticle $category */
         $responseArray[] = array(
            "id" => $category->getId(),
            "title" => $category->getTitle()
         );
      }

      return new JsonResponse($responseArray);
   }

    /**
     * @Route("{_locale}/articles/{id}/like", name="article_like")
     * @Route("/articles/{id}/like", name="article_like_default", defaults={"_locale"="fr"})
     * @param ArticlePublished $article
     * @param ArticleLikeRepository $articleLikeRepository
     * @param ObjectManager $manager
     * @return Response
     */
   public function like(ArticlePublished $article, ArticleLikeRepository $articleLikeRepository, ObjectManager $manager)
   {
       $user = $this->getUser();
       if (!$user) {
           return $this->json(['code' => 403, 'message' => 'Unauthorized'], 403);
       }

       if ($article->isLikedByUser($user)) {
           $like = $articleLikeRepository->findOneBy(['article' => $article, 'user' => $user]);
           $manager->remove($like);
           $manager->flush();
           return $this->json([
               'code' => 200,
               'message' => 'like bien suprimé',
               'likes' => $articleLikeRepository->count(['article' => $article])
           ], 200);
       }

       $like = new ArticleLike();
       $like->setArticle($article)
           ->setUser($user);
       $manager->persist($like);
       $manager->flush();
       return $this->json([
           'code' => 200,
           'message' => 'like bien ajouté',
           'likes' => $articleLikeRepository->count(['article' => $article])
       ], 200);
   }

    /**
     * @Route("/boadmin/articles/getRows", name="list_rows_articles")
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
            ->setParameter("type", 'article')
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
            ->setParameter("type", 'article')
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
}