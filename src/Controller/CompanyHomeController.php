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
 * Class CompanyHomeController
 * @package App\Controller
 */
class CompanyHomeController extends AbstractController
{
    /**
     * @var HeaderRepository
     */
    private $headerRepository;
    /**
     * @var ApprovedCompanyRepository
     */
    private $approvedCompanyRepository;
    /**
     * @var ReassuranceRepository
     */
    private $reassuranceRepository;
    /**
     * @var GarantiesBlockRepository
     */
    private $warrantyBlockRepository;
    /**
     * @var CompanyFAQRepository
     */
    private $companyFAQRepository;

    /**
     * CompanyController constructor.
     *
     * @param HeaderRepository $headerRepository
     * @param ReassuranceRepository $reassuranceRepository
     * @param ApprovedCompanyRepository $approvedCompanyRepository
     * @param GarantiesBlockRepository $warrantyBlockRepository
     * @param CompanyFAQRepository $companyFAQRepository
     */
    public function __construct(
        HeaderRepository $headerRepository,
        ReassuranceRepository $reassuranceRepository,
        ApprovedCompanyRepository $approvedCompanyRepository,
        GarantiesBlockRepository $warrantyBlockRepository,
        CompanyFAQRepository $companyFAQRepository
    ) {
        $this->headerRepository = $headerRepository;
        $this->reassuranceRepository = $reassuranceRepository;
        $this->approvedCompanyRepository = $approvedCompanyRepository;
        $this->warrantyBlockRepository = $warrantyBlockRepository;
        $this->companyFAQRepository = $companyFAQRepository;
    }

    /**
     * @Route("{_locale}/crowdfunding-appel-aux-dons", name="company_home")
     * @Route("/crowdfunding-appel-aux-dons", name="default_company_home")
     * @param Request $request
     * @return Response
     */
    public function companyHome(Request $request)
    {
        $locale = $request->getLocale();
        $header = $this->headerRepository->findOneBy(['page' => 'companyHome', 'lang' => $locale]);
        return $this->render('company/home.html.twig', [
            'current_menu' => 'companies_home',
            'header' => $header,
            'reassuranceBlock' => $this->reassuranceRepository->findOneBy(['slug' => 'company-home', 'lang' => $locale]),
            'companies' => $this->approvedCompanyRepository->getCompaniesForHome(),
            'warrantyBlock' => $this->warrantyBlockRepository->findOneBy(['slug' => 'company-home', 'lang' => $locale]),
            'FAQs' => $this->companyFAQRepository->findBy(
                ['language' => $locale, 'isPublished' => true],
                ['createdAt' => 'DESC'], 3)
        ]);
    }

}
