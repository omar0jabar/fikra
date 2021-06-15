<?php

namespace App\Controller;

use EgioDigital\CMSBundle\Entity\Row;
use EgioDigital\CMSBundle\Entity\RowPublished;
use EgioDigital\CMSBundle\Repository\PagePublishedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\Image as ImageService;

class CommentCaMarcheController extends AbstractController
{
    private $repository;
    private $translator;
    private $pageNotFound;
    private $paramCurrentMenu = "current_menu";
    private $paramBlocks = "blocks";
    private $currentMenu = "pages";

    public function __construct(PagePublishedRepository $repository, TranslatorInterface $translator)
    {
        $this->repository = $repository;
        $this->translator = $translator;
        $this->pageNotFound = $this->translator->trans('Page not found');
    }

    /**
     * @Route("{_locale}/pages/comment-ca-marche/{actor}", name="comment_ca_marche")
     * @Route("/pages/comment-ca-marche/{actor}", name="comment_ca_marche_default", defaults={"_locale"="%locale%"})
     * @param string $actor
     * @param Request $request
     * @return Response
     */
    public function commentCaMarche(string $actor, Request $request,ImageService $imageService)
    {
        $page = $this->repository->findOneBy(['slug' => 'comment-ca-marche-'.$actor, 'lang' => $request->getLocale()]);
        if (!$page) {
            throw new NotFoundHttpException($this->pageNotFound);
        }
        list($width,$height) = $imageService->getimagesize('page', $page->getBannerDesktop());

        $blocks = [];
        foreach ($page->getBlocks() as $block) {
            $blocks[$block->getRow()][] = $block;
        }
        ksort($blocks);

        $rows = $this->getRowsInfos($page->getId());
        $template = "@CMS/front/page/show.html.twig";
        if ($page->getIsPageSimple()) {
            $template = "@CMS/front/page/show-simple.html.twig";
        }
        return $this->render($template, [
            $this->paramCurrentMenu => "how-it-works",
            'page' => $page,
            'width' => $width,
            'height' => $height,
            $this->paramBlocks => $blocks,
            'rows' => $rows,
            'current_menu' => 'comment_ca_marche',
        ]);
    }

    /**
     * @Route("{_locale}/pages/services/{actor}", name="services")
     * @Route("/pages/services/{actor}", name="services_default", defaults={"_locale"="%locale%"})
     * @param string $actor
     * @param Request $request
     * @return Response
     */
    public function services(string $actor, Request $request, ImageService $imageService)
    {
        $page = $this->repository->findOneBy(['slug' => 'services-'.$actor, 'lang' => $request->getLocale()]);
        if (!$page) {
            throw new NotFoundHttpException($this->pageNotFound);
        }
        list($width,$height) = $imageService->getimagesize('page', $page->getBannerDesktop());
        $blocks = [];
        foreach ($page->getBlocks() as $block) {
            $blocks[$block->getRow()][] = $block;
        }
        ksort($blocks);

        $rows = $this->getRowsInfos($page->getId());
        $template = "@CMS/front/page/show.html.twig";
        if ($page->getIsPageSimple()) {
            $template = "@CMS/front/page/show-simple.html.twig";
        }
        return $this->render($template, [
            $this->paramCurrentMenu => "services",
            'page' => $page,
            'width' => $width,
            'height' => $height,
            $this->paramBlocks => $blocks,
            'rows' => $rows,
        ]);
    }

    /**
     * @Route("{_locale}/pages/about-us", name="about_us")
     * @Route("/pages/about-us", name="about_us_default", defaults={"_locale"="%locale%"})
     * @param Request $request
     * @return Response
     */
    public function aboutUs(Request $request,ImageService $imageService)
    {
        $page = $this->repository->findOneBy(['slug' => 'about-us', 'lang' => $request->getLocale()]);
        if (!$page) {
            throw new NotFoundHttpException($this->pageNotFound);
        }
        list($width,$height) = $imageService->getimagesize('page', $page->getBannerDesktop());
        $blocks = [];
        foreach ($page->getBlocks() as $block) {
            $blocks[$block->getRow()][] = $block;
        }
        ksort($blocks);

        $rows = $this->getRowsInfos($page->getId());
        $template = "@CMS/front/page/show.html.twig";
        if ($page->getIsPageSimple()) {
            $template = "@CMS/front/page/show-simple.html.twig";
        }
        return $this->render($template, [
            $this->paramCurrentMenu => 'about-us',
            'page' => $page,
            'width' => $width,
            'height' => $height,
            $this->paramBlocks => $blocks,
            'rows' => $rows,
            'current_menu' => 'about_us',
        ]);
    }

    /**
     * @Route("{_locale}/pages/legal-notice", name="legal_notice")
     * @Route("/pages/legal-notice", name="legal_notice_default", defaults={"_locale"="%locale%"})
     * @param Request $request
     * @return Response
     */
    public function legalNotice(Request $request)
    {
        $page = $this->repository->findOneBy(['slug' => 'legal-notice', 'lang' => $request->getLocale()]);
        if (!$page) {
            throw new NotFoundHttpException($this->pageNotFound);
        }
        $blocks = [];
        foreach ($page->getBlocks() as $block) {
            $blocks[$block->getRow()][] = $block;
        }
        ksort($blocks);

        $rows = $this->getRowsInfos($page->getId());
        $template = "@CMS/front/page/show.html.twig";
        if ($page->getIsPageSimple()) {
            $template = "@CMS/front/page/show-simple.html.twig";
        }
        return $this->render($template, [
            $this->paramCurrentMenu => 'legal-notice',
            'page' => $page,
            $this->paramBlocks => $blocks,
            'rows' => $rows,
        ]);
    }

    /**
     * @Route("{_locale}/pages/cgu", name="cgu")
     * @Route("/pages/cgu", name="cgu_default", defaults={"_locale"="%locale%"})
     * @param Request $request
     * @return Response
     */
    public function cgu(Request $request)
    {
        $page = $this->repository->findOneBy(['slug' => 'cgu', 'lang' => $request->getLocale()]);
        if (!$page) {
            throw new NotFoundHttpException($this->pageNotFound);
        }
        $blocks = [];
        foreach ($page->getBlocks() as $block) {
            $blocks[$block->getRow()][] = $block;
        }
        ksort($blocks);

        $rows = $this->getRowsInfos($page->getId());
        $template = "@CMS/front/page/show.html.twig";
        if ($page->getIsPageSimple()) {
            $template = "@CMS/front/page/show-simple.html.twig";
        }
        return $this->render($template, [
            $this->paramCurrentMenu => 'cgu',
            'page' => $page,
            $this->paramBlocks => $blocks,
            'rows' => $rows,
        ]);
    }

    /**
     * @Route("{_locale}/pages/engagement-crpi", name="engagement")
     * @Route("/pages/cgu", name="cgu_default", defaults={"_locale"="%locale%"})
     * @param Request $request
     * @return Response
     */
    public function engagement(Request $request)
    {
        $page = $this->repository->findOneBy(['slug' => 'engagement-crpi', 'lang' => $request->getLocale()]);
        if (!$page) {
            throw new NotFoundHttpException($this->pageNotFound);
        }
        $blocks = [];
        foreach ($page->getBlocks() as $block) {
            $blocks[$block->getRow()][] = $block;
        }
        ksort($blocks);

        $rows = $this->getRowsInfos($page->getId());
        $template = "@CMS/front/page/show.html.twig";
        if ($page->getIsPageSimple()) {
            $template = "@CMS/front/page/show-simple.html.twig";
        }
        return $this->render($template, [
            $this->paramCurrentMenu => 'cgu',
            'page' => $page,
            $this->paramBlocks => $blocks,
            'rows' => $rows,
        ]);
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
}
