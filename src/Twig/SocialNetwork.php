<?php
namespace App\Twig;

use App\Repository\BackgroundSliderRepository;
use App\Repository\ContactInfoRepository;
use App\Repository\SEORepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * Class SocialNetwork
 * @package App\Twig
 */
class SocialNetwork extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var ContactInfoRepository
     */
    protected $contactInfoRepository;

    /**
     * @var SEORepository
     */
    protected $SEORepository;

    /**
     * @var BackgroundSliderRepository
     */
    protected $backgroundSliderRepository;

    public function __construct(
        ContactInfoRepository $contactInfoRepository,
        SEORepository $SEORepository,
        BackgroundSliderRepository $backgroundSliderRepository
    )
    {
        $this->contactInfoRepository = $contactInfoRepository;
        $this->SEORepository = $SEORepository;
        $this->backgroundSliderRepository = $backgroundSliderRepository;
    }

    public function getGlobals()
    {
        $SEO = $this->SEORepository->findOneBy(['language' => 'fr']);
        $backgroundImage = $this->backgroundSliderRepository->findOneBy(['language' => 'fr']);
        $facebookLink = $this->getInfo('facebook');
        $linkedInLink = $this->getInfo('linkedin');
        $youtubeLink = $this->getInfo('youtube');
        $twitterLink = $this->getInfo('twitter');
        return [
            'SEOTitle' => $SEO ? $SEO->getTitle() : '',
            'SEOImage' => $backgroundImage ? $backgroundImage->getBannerDesktop() : '',
            'SEODescription' => $SEO ? $SEO->getDescription() : '',
            'facebookLink' => $facebookLink,
            'linkedInLink' => $linkedInLink,
            'youtubeLink' => $youtubeLink,
            'twitterLink' => $twitterLink,
        ];
    }

    private function getInfo($title)
    {
        $row = $this->contactInfoRepository->findOneBy(['title' => $title]);
        $link = !empty($row) ? $row->getInfo() : '#';
        return $link;
    }
}