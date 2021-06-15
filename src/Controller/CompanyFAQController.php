<?php

namespace App\Controller;

use App\Repository\ApprovedCompanyRepository;
use App\Repository\CompanyFAQRepository;
use App\Repository\GarantiesBlockRepository;
use App\Repository\HeaderRepository;
use App\Repository\ReassuranceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CompanyFAQController
 * @package App\Controller
 */
class CompanyFAQController extends AbstractController
{
    /**
     * @var HeaderRepository
     */
    private $headerRepository;
    /**
     * @var CompanyFAQRepository
     */
    private $companyFAQRepository;

    /**
     * CompanyController constructor.
     *
     * @param HeaderRepository $headerRepository
     * @param CompanyFAQRepository $companyFAQRepository
     */
    public function __construct(
        HeaderRepository $headerRepository,
        CompanyFAQRepository $companyFAQRepository
    ) {
        $this->headerRepository = $headerRepository;
        $this->companyFAQRepository = $companyFAQRepository;
    }

    /**
     * @Route("{_locale}/faq", name="company_faq")
     * @Route("/faq", name="default_company_faq")
     * @param Request $request
     * @return Response
     */
    public function FAQ(Request $request)
    {
        $locale = $request->getLocale();
        $header = $this->headerRepository->findOneBy(['page' => 'companyFAQ', 'lang' => $locale]);
        return $this->render('company/faq.html.twig', [
            'current_menu' => 'campaign_faq',
            'header' => $header,
            'FAQs' => $this->companyFAQRepository->findBy(
                ['language' => $locale, 'isPublished' => true],
                ['createdAt' => 'DESC'])
        ]);
    }

}
