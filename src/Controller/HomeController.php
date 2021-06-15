<?php

namespace App\Controller;

use App\Repository\ApprovedProjectRepository;
use App\Repository\BackgroundSliderRepository;
use App\Repository\CommentCaMarcheBlockRepository;
use App\Repository\GarantiesBlockRepository;
use App\Repository\PartnerRepository;
use App\Repository\ReassuranceRepository;
use App\Repository\SectorRepository;
use App\Repository\SEORepository;
use App\Repository\SliderRepository;
use App\Repository\TestimonialRepository;
use App\Repository\VideoRepository;
use EgioDigital\CMSBundle\Repository\EventPublishedRepository;
use EgioDigital\CMSBundle\Repository\EventRepository;
use EgioDigital\CMSBundle\Repository\ArticlePublishedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Controller
 */
class HomeController extends AbstractController
{
    /**
     * @var PartnerRepository
     */
    private $partnerRepository;

    /**
     * @var TestimonialRepository
     */
    private $testimonialRepository;

    /**
     * HomeController constructor.
     * @param PartnerRepository $partnerRepository
     * @param TestimonialRepository $testimonialRepository
     */
    public function __construct(
        PartnerRepository $partnerRepository,
        TestimonialRepository $testimonialRepository
    ) {
        $this->partnerRepository = $partnerRepository;
        $this->testimonialRepository = $testimonialRepository;
    }

    /**
     * @Route("/", name="home_default")
     * @Route("/home", name="home_page")
     * @Route("/{_locale}/home", name="home")
     * @param Request $request
     * @param SEORepository $SEORepository
     * @param SectorRepository $sectorRepository
     * @param ApprovedProjectRepository $approvedProjectRepository
     * @param ArticlePublishedRepository $articleRepository
     * @param CommentCaMarcheBlockRepository $commentRepository
     * @param GarantiesBlockRepository $garantiesBlockRepository
     * @param ReassuranceRepository $reassuranceRepository
     * @param BackgroundSliderRepository $backgroundSliderRepository
     * @param SliderRepository $sliderRepository
     * @return Response
     */
    public function index(
        Request $request,
        SEORepository $SEORepository,
        SectorRepository $sectorRepository,
        ApprovedProjectRepository $approvedProjectRepository,
        ArticlePublishedRepository $articleRepository,
        CommentCaMarcheBlockRepository $commentRepository,
        GarantiesBlockRepository $garantiesBlockRepository,
        ReassuranceRepository $reassuranceRepository,
        BackgroundSliderRepository $backgroundSliderRepository,
        SliderRepository $sliderRepository
    ) {
        $locale = $request->getLocale();
        $backgroundSlider = $backgroundSliderRepository->findOneBy(['language'=>$locale]);
        return $this->render('home.html.twig', [
            'current_menu' => 'home',
            'seo' => $SEORepository->findOneBy(['language' => $locale]),
            'background' => $backgroundSlider,
            'sliders' => $sliderRepository->findBy(['lang' => $locale]),
            'sectors' => $sectorRepository->getSectorsHome(),
            'commentCaMarcheCards' => $commentRepository->findBy(['lang' => $locale]),
            'testimonials' => $this->testimonialRepository->findAll(),
            'garantiesBlock' => $garantiesBlockRepository->findOneBy(['slug' => 'home', 'lang' => $locale]),
            'reassuranceBlock' => $reassuranceRepository->findOneBy(['slug' => 'home', 'lang' => $locale]),
            'projects' => $approvedProjectRepository->getRecentProjects(),
            'articles' => $articleRepository->getArticlesForHome($locale, 4),
            'partners' => $this->partnerRepository->findAll()
        ]);
    }

    /**
     * @Route("/language/{language}", name="set_locale_language")
     * @param Request $request
     * @param string $language
     * @return Response
     */
    public function setLocaleAction(Request $request, $language = null)
    {
        $languages = ['fr', 'en'];
        if ($language != null && in_array($language, $languages)) {
            $this->get('session')->set('_locale', $language);
        }
        $url = $request->headers->get('referer');
        if ($language === "fr") {
            $url = str_replace('/en/', '/fr/', $url);
        } else {
            $url = str_replace('/fr/', '/en/', $url);
        }
        if(empty($url)) {
            $url = $this->generateUrl('home', ['_locale'=>$language]);
        }
        return $this->redirect($url);
    }

}
