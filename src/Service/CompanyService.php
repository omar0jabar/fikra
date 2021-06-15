<?php

namespace App\Service;

use App\Entity\ApprovedAvantage;
use App\Entity\ApprovedCompany;
use App\Entity\ApprovedCompanyDocument;
use App\Entity\ApprovedDocument;
use App\Entity\ApprovedGalleryPhoto;
use App\Entity\ApprovedProject;
use App\Entity\ApprovedProjectFinance;
use App\Entity\ApprovedService;
use App\Entity\ApprovedTeamMember;
use App\Entity\ApprovedUseFund;
use App\Entity\Company;
use App\Entity\Project;
use App\Entity\TeamMember;
use App\Repository\ApprovedCompanyRepository;
use App\Repository\ApprovedProjectRepository;
use App\Repository\ProjectRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CompanyService
{

    private $approvedCompanyRepository;
    private $manager;

    public function __construct(
        ApprovedCompanyRepository $approvedCompanyRepository,
        ObjectManager $manager
    ) {
        $this->approvedCompanyRepository = $approvedCompanyRepository;
        $this->manager = $manager;
    }

    public function checkProject(Project $project)
    {
        $array = [];
        if (empty($project->getName())) {
            $array[] = 'name';
        }
        if (empty($project->getDescription())) {
            $array[] = 'description';
        }
        if (empty($project->getStep())) {
            $array[] = 'step';
        }
        if (empty($project->getEarned())) {
            $array[] = 'earned';
        }
        if ($project->getStartup()) {
            if (empty($project->getDenomination())) {
                $array[] = 'denomination';
            }
            if (empty($project->getCreatingDate())) {
                $array[] = 'creating date';
            }
            if (empty($project->getRc())) {
                $array[] = 'rc';
            }
            if (empty($project->getCity())) {
                $array[] = 'city';
            }
        }
        if (!$project->getOtherSalesChannels() && count($project->getSalesChannels()) == 0) {
            $array[] = 'sales channels';
        }
        if (count($project->getSectors()) == 0) {
            $array[] = 'sectors';
        }
        if (!$project->getOtherBusinessModel() && count($project->getBusinessModels()) == 0) {
            $array[] = 'business models';
        }
        if ($project->getOtherCountry() && empty($project->getForeignCountry())) {
            $array[] = 'foreign country';
        }
        if (empty($project->getAmount())) {
            $array[] = 'amount';
        }
        if (count($project->getTeamMembers()) == 0) {
            $array[] = 'team members';
        }
        return $array;
    }

    public function checkUpdateProject(Project $project)
    {
        $array = $this->checkProject($project);
        if (empty($project->getSummary())) {
            $array[] = 'summary';
        }
        if (count($project->getAvantages()) == 0) {
            $array[] = 'avantages';
        }
        if (count($project->getProjectFinances()) == 0) {
            $array[] = 'project finance';
        }
        return $array;
    }

    public function checkTeamProjectCustomization(Project $project)
    {
        $array = [];
        if (count($project->getTeamMembers()) < 3) {
            $array[] = 'Team members';
        }
        return $array;
    }


    public function checkProjectCustomization(Project $project)
    {
        $array = [];
        if (empty($project->getLogoName())) {
            $array[] = "logo";
        }
        if (empty($project->getImageCoverName())) {
            $array[] = "cover";
        }
        if (count($project->getGalleryPhotos()) == 0) {
            $array[] = "gallery";
        }
        return $array;
    }

    /**
     * @param Company $company
     * @throws \Exception
     */
    public function approveCompany(Company $company)
    {
        if (!$company) {
            throw new NotFoundHttpException('Company not found');
        }
        $newApprovedCompany = $this->approvedCompanyRepository->findOneBy(['company' => $company]);
        if (!$newApprovedCompany) {
            $newApprovedCompany = new ApprovedCompany();
        }

        // when approve first time
        if (!$company->getStartDate()) {
            // set start date
            $startDate = new \DateTime();
            $company->setStartDate($startDate);
        }
        // calculate end date
        $endDate = new \DateTime($company->getStartDate()->format("Y-m-d h:i:s"));
        $month = (int)$company->getDuration();
        $month = $month > 1 ? $month . ' months' : $month .' month';
        $endDate->modify('+' . $month);
        date_time_set($endDate,23,59,59);
        $company->setEndDate($endDate);

        $newApprovedCompany
            ->setName($company->getName())
            ->setCompany($company)
            ->setDescription($company->getDescription())
            ->setAssociationName($company->getAssociationName())
            ->setText($company->getText())
            ->setFundingObjective($company->getFundingObjective())
            ->setCity($company->getCity())
            ->setDuration($company->getDuration())
            ->setRIB($company->getRIB())
            ->setWebSite($company->getWebSite())
            ->setCoverName($company->getCoverName())
            ->setLogoName($company->getLogoName())
            ->setSlug($company->getSlug())
            ->setIsVerified($company->getIsVerified())
            ->setIsDeleted($company->getIsDeleted())
            ->setUser($company->getUser())
            ->setIsApproved(true)
        ;

        $this->createDirectories();

        if ($company->getCoverName()) {
            $path = "upload/company/cover/{$company->getCoverName()}";
            if (is_file($path)) {
                copy($path, "upload/company/approved-images/{$company->getCoverName()}");
            }
        }

        if ($company->getLogoName()) {
            $path = "upload/company/logo/{$company->getLogoName()}";
            if (is_file($path)) {
                copy($path, "upload/company/approved-images/{$company->getLogoName()}");
            }
        }

        if (count($newApprovedCompany->getUseOfFundsCollected()) > 0) {
            foreach ($newApprovedCompany->getUseOfFundsCollected() as $use) {
                $newApprovedCompany->removeUseOfFundsCollected($use);
            }
        }

        foreach ($company->getUseOfFundsCollecteds() as $use) {
            $approvedUseFund = new ApprovedUseFund();
            $approvedUseFund->setDescription($use->getDescription())
                ->setApprovedCompany($newApprovedCompany);
            $newApprovedCompany->addUseOfFundsCollected($approvedUseFund);
            $this->manager->persist($approvedUseFund);
        }

        foreach ($company->getDomain() as $domain) {
            $newApprovedCompany->addDomain($domain);
        }

        if (count($newApprovedCompany->getDocuments()) > 0) {
            foreach ($newApprovedCompany->getDocuments() as $document) {
                if (is_file($document->getDocumentPath())) {
                    unlink($document->getDocumentPath());
                }
                $newApprovedCompany->removeDocument($document);
            }
        }
        foreach ($company->getDocuments() as $document) {
            $path = "upload/company/document/{$document->getName()}";
            if (is_file($path)) {
                copy($path, "upload/company/approved-documents/{$document->getName()}");
            }
            $approvedCompanyDocument = new ApprovedCompanyDocument();
            $approvedCompanyDocument->setName($document->getName())
                ->setType($document->getType())
                ->setApprovedCompany($newApprovedCompany)
            ;
            $newApprovedCompany->addDocument($approvedCompanyDocument);
            $this->manager->persist($approvedCompanyDocument);
        }

        // save approved project
        $this->manager->persist($newApprovedCompany);
        $this->manager->flush();
    }

    public function createDirectory($pathToDir)
    {
        if (!file_exists($pathToDir)) {
            mkdir($pathToDir, 0777, true);
        }
    }

    public function createDirectories()
    {
        $directories = [];
        $directories[] = "upload/company";
        $directories[] = "upload/company/approved-images";
        $directories[] = "upload/company/approved-documents";

        foreach ($directories as $directory) {
            $this->createDirectory("$directory");
        }
    }


    /**
     * @param ApprovedCompany $approvedCompany
     */
    public function disapproveCompany(ApprovedCompany $approvedCompany)
    {
        if (!$approvedCompany) {
            throw new NotFoundHttpException('Approved company not found');
        }

        foreach ($approvedCompany->getDomain() as $domain) {
            $approvedCompany->removeDomain($domain);
        }

        if ($approvedCompany->getCoverName()) {
            $path = "upload/company/approved-images/{$approvedCompany->getCoverName()}";
            if (is_file($path)) {
                unlink($path);
            }
        }

        if ($approvedCompany->getLogoName()) {
            $path = "upload/company/approved-images/{$approvedCompany->getLogoName()}";
            if (is_file($path)) {
                unlink($path);
            }
        }

        $this->manager->flush();
    }

}