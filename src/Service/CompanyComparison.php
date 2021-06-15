<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\CompanyDocument;
use App\Entity\Domain;
use App\Entity\UseFund;
use App\Repository\ApprovedCompanyRepository;

/**
 * Class CompanyComparison
 * @package App\Service
 */
class CompanyComparison
{
    /**
     * @var ApprovedCompanyRepository
     */
    private $approvedCompanyRepository;

    /**
     * CompanyComparison constructor.
     *
     * @param ApprovedCompanyRepository $approvedCompanyRepository
     */
    public function __construct(
        ApprovedCompanyRepository $approvedCompanyRepository
    ) {
        $this->approvedCompanyRepository = $approvedCompanyRepository;
    }

    /**
     * @param Company $company
     * @return bool
     */
    public function compareCompany(Company $company) {

        $approvedCompany = $this->approvedCompanyRepository->findOneBy(['company' => $company]);

        $results = [];

        if ($approvedCompany && $company->getIsApproved()) {
            $results["name"] = $this->compare($company->getName(), $approvedCompany->getName());
            $results["association_name"] = $this->compare($company->getAssociationName(), $approvedCompany->getAssociationName());
            $results["short_description"] = $this->compare($company->getDescription(), $approvedCompany->getDescription());
            $results["description"] = $this->compare($company->getText(), $approvedCompany->getText());
            $results["funding_objective"] = $this->compare($company->getFundingObjective(), $approvedCompany->getFundingObjective());
            $results["city"] = $this->compare($company->getCity(), $approvedCompany->getCity());
            $results["duration"] = $this->compare($company->getDuration(), $approvedCompany->getDuration());
            $results["RIB"] = $this->compare($company->getRIB(), $approvedCompany->getRIB());
            $results["web_site"] = $this->compare($company->getWebSite(), $approvedCompany->getWebSite());
            $results["cover_name"] = $this->compare($company->getCoverName(), $approvedCompany->getCoverName());
            $results["logo_name"] = $this->compare($company->getLogoName(), $approvedCompany->getLogoName());

            $results["uses_funds"] = $this->compareUseFunds($company->getUseFundsArray(), $approvedCompany->getUseFundsArray());
            $results["domains"] = $this->compareDomains($company->getDomain(), $approvedCompany->getDomain());
            $results["documents"] = $this->compareDocuments($company->getDocumentsArray(), $approvedCompany->getDocumentsArray());
        }

        if (in_array(true, $results)) {
            return true;
        }

        return false;
    }

    /**
     * @param $value1
     * @param $value2
     * @return bool
     */
    public function compare($value1, $value2)
    {
        return $value1 != $value2;
    }

    /**
     * @param $domains
     * @param $approvedDomains
     * @return bool
     */
    public function compareDomains($domains, $approvedDomains)
    {
        if (count($domains) != count($approvedDomains)) {
            return true;
        } else {
            $booleans = [];
            foreach ($domains as $key => $domain) {
                /**
                 * @var $domain Domain
                 */
                $booleans[] = $this->compare($domain->getId(), $approvedDomains[$key]->getId());

                if (in_array(true, $booleans)){
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $uses
     * @param $approvedUses
     * @return bool
     */
    public function compareUseFunds($uses, $approvedUses)
    {
        if (count($uses) != count($approvedUses)) {
            return true;
        } else {
            $booleans = [];
            $key = 0;
            foreach ($uses as $use) {
                /**
                 * @var $use UseFund
                 */
                $booleans[] = $this->compare($use->getDescription(), $approvedUses[$key]->getDescription());
                $key ++;

                if (in_array(true, $booleans)){
                    return true;
                }
            }
        }
        return false;
    }

    public function compareDocuments($documents, $approvedDocuments)
    {
        if (count($documents) != count($approvedDocuments)) {
            return true;
        } else {
            $booleans = [];
            $key = 0;
            foreach ($documents as $document) {
                /**
                 * @var $document CompanyDocument
                 */
                $booleans[] = $this->compare($document->getName(), $approvedDocuments[$key]->getName());
                $booleans[] = $this->compare($document->getType(), $approvedDocuments[$key]->getType());
                $key ++;

                if (in_array(true, $booleans)){
                    return true;
                }
            }
        }
        return false;
    }

}
