<?php

namespace App\Service;

use App\Entity\ApprovedTeamMember;
use App\Entity\Avantage;
use App\Entity\BusinessModel;
use App\Entity\Document;
use App\Entity\Project;
use App\Entity\ProjectFinance;
use App\Entity\SalesChannels;
use App\Entity\Sector;
use App\Entity\Service;
use App\Repository\ApprovedProjectRepository;

class Comparison
{

    private $repo;

    public function __construct(ApprovedProjectRepository $repo) {
        $this->repo = $repo;
    }

    /**
     * @param Project $project
     * @return bool
     */
    public function compareProject(Project $project) {

        $approvedProject = $this->repo->findOneBy(['project' => $project]);

        $results = [];

        if ($approvedProject && $project->getIsApproved()) {

            $results[] = $this->compare($project->getName(), $approvedProject->getName());
            $results[] = $this->compare($project->getDescription(), $approvedProject->getDescription());
            $results[] = $this->compare($project->getStep(), $approvedProject->getStep());
            $results[] = $this->compare($project->getEarned(), $approvedProject->getEarned());

            $results[] = $this->compareAvantages($project->getServices(), $approvedProject->getServices());

            $results[] = $this->compareSalesChannels($project->getSalesChannels(), $approvedProject->getSalesChannels());
            $results[] = $this->compare($project->getOtherSalesChannels(), $approvedProject->getOtherSalesChannels());
            $results[] = $this->compare($project->getMoreSalesChannels(), $approvedProject->getMoreSalesChannels());

            $results[] = $this->compareSectors($project->getSectors(), $approvedProject->getSectors());
            $results[] = $this->compare($project->getMoreSectors(), $approvedProject->getMoreSectors());

            $results[] = $this->compareBusinessModels($project->getBusinessModels(), $approvedProject->getBusinessModels());
            $results[] = $this->compare($project->getOtherBusinessModel(), $approvedProject->getOtherBusinessModel());
            $results[] = $this->compare($project->getMoreBusinessModel(), $approvedProject->getMoreBusinessModel());

            $results[] = $this->compareAvantages($project->getAvantages(), $approvedProject->getAvantages());

            $results[] = $this->compare($project->getHasNotAmount(), $approvedProject->getHasNotAmount());
            $results[] = $this->compare($project->getRaised(), $approvedProject->getRaised());
            $results[] = $this->compare($project->getAmount(), $approvedProject->getAmount());

            $results[] = $this->compareProjectFinances($project->getProjectFinances(), $approvedProject->getProjectFinances());

            $results[] = $this->compare($project->getSummary(), $approvedProject->getSummary());
            $results[] = $this->compare($project->getExpress(), $approvedProject->getExpress());

            $results[] = $this->compare($project->getStartup(), $approvedProject->getStartup());
            $results[] = $this->compare($project->getDenomination(), $approvedProject->getDenomination());

            $oldDate = $approvedProject->getCreatingDate();
            $newDate = $project->getCreatingDate();
            if ($oldDate instanceof \DateTime && $newDate instanceof \DateTime) {
                $diff = $oldDate->diff($newDate);
                if ($diff->days > 0) {
                    $results[] = true;
                }
            }

            $results[] = $this->compare($project->getRc(), $approvedProject->getRc());
            $results[] = $this->compare($project->getCity(), $approvedProject->getCity());

            $results[] = $this->compare($project->getImageCoverName(), $approvedProject->getImageCoverName());
            $results[] = $this->compare($project->getLogoName(), $approvedProject->getLogoName());

            $results[] = $this->compareTeamMembers($project->getTeamMembers(), $approvedProject->getTeamMembers());

            $results[] = $this->compareDocuments($project->getDocuments(), $approvedProject->getDocuments());

            $results[] = $this->compareGalleryPhotos($project->getGalleryPhotos(), $approvedProject->getGalleryPhotos());

            $results[] = $this->compare($project->getVideo(), $approvedProject->getVideo());
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
        if ($value1 === $value2) {
            return false;
        }
        return true;
    }

    /**
     * @param $approvedSalesChannels
     * @param $salesChannels
     * @return bool
     */
    public function compareSalesChannels($salesChannels, $approvedSalesChannels)
    {
        $res = $this->compare(count($salesChannels), count($approvedSalesChannels));

        if ($res === true) {
            return true;
        } else {
            $booleans = [];
            $key = 0;
            foreach ($salesChannels as $salesChannel) {
                /**
                 * @var $salesChannel SalesChannels
                 */
                $booleans[] = $this->compare($salesChannel->getId(), $approvedSalesChannels[$key]->getId());
                $key++;

                if (in_array(true, $booleans)){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $approvedSectors
     * @param $sectors
     * @return bool
     */
    public function compareSectors($sectors, $approvedSectors)
    {
        $res = $this->compare(count($sectors), count($approvedSectors));

        if ($res === true) {
            return true;
        } else {
            $booleans = [];
            foreach ($sectors as $key => $sector) {
                /**
                 * @var $sector Sector
                 */
                $booleans[] = $this->compare($sector->getId(), $approvedSectors[$key]->getId());

                if (in_array(true, $booleans)){
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $approvedBusinessModels
     * @param $businessModels
     * @return bool
     */
    public function compareBusinessModels($businessModels, $approvedBusinessModels)
    {
        $res = $this->compare(count($businessModels), count($approvedBusinessModels));

        if ($res === true) {
            return true;
        } else {
            $booleans = [];
            foreach ($businessModels as $key => $businessModel) {
                /**
                 * @var $businessModel BusinessModel
                 */
                $booleans[] = $this->compare($businessModel->getId(), $approvedBusinessModels[$key]->getId());
                if (in_array(true, $booleans)){
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $services
     * @param $approvedServices
     * @return bool
     */
    public function compareServices($services, $approvedServices)
    {
        $res = $this->compare(count($services), count($approvedServices));

        if ($res === true) {
            return true;
        } else {
            $booleans = [];
            $key = 0;
            foreach ($services as $service) {
                /**
                 * @var $service Service
                 */
                $booleans[] = $this->compare($service->getName(), $approvedServices[$key]->getName());
                $key ++;

                if (in_array(true, $booleans)){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $approvedAvantages
     * @param $avantages
     * @return bool
     */
    public function compareAvantages($avantages, $approvedAvantages)
    {
        $res = $this->compare(count($avantages), count($approvedAvantages));

        if ($res === true) {
            return true;
        } else {
            $booleans = [];
            $key = 0;
            foreach ($avantages as $avantage) {
                /**
                 * @var $avantage Avantage
                 */
                $booleans[] = $this->compare($avantage->getName(), $approvedAvantages[$key]->getName());
                $key ++;

                if (in_array(true, $booleans)){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $approvedFinances
     * @param $finances
     * @return bool
     */
    public function compareProjectFinances($finances, $approvedFinances)
    {
        $res = $this->compare(count($finances), count($approvedFinances));

        if ($res === true) {
            return true;
        } else {
            $booleans = [];
            $key = 0;
            foreach ($finances as $finance) {
                /**
                 * @var $finance ProjectFinance
                 */
                $booleans[] = $this->compare($finance->getDetail(), $approvedFinances[$key]->getDetail());
                $key ++;

                if (in_array(true, $booleans)){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $approvedTeamMembers
     * @param $teamMembers
     * @return bool
     */
    public function compareTeamMembers($teamMembers, $approvedTeamMembers)
    {
        $res = $this->compare(count($teamMembers), count($approvedTeamMembers));

        if ($res === true) {
            return true;
        } else {
            $booleans = [];
            $key = 0;

            foreach ($teamMembers as $teamMember) {
                /**
                 * @var $teamMember ApprovedTeamMember
                 */
                $booleans[] = $this->compare($teamMember->getFirstName(), $approvedTeamMembers[$key]->getFirstName());
                $booleans[] = $this->compare($teamMember->getLastName(), $approvedTeamMembers[$key]->getLastName());
                $booleans[] = $this->compare($teamMember->getPosition(), $approvedTeamMembers[$key]->getPosition());
                $booleans[] = $this->compare($teamMember->getBiography(),  $approvedTeamMembers[$key]->getBiography());
                $booleans[] = $this->compare($teamMember->getLinkedin(), $approvedTeamMembers[$key]->getLinkedin());
                $booleans[] = $this->compare($teamMember->getTwitter(), $approvedTeamMembers[$key]->getTwitter());
                $booleans[] = $this->compare($teamMember->getFacebook(), $approvedTeamMembers[$key]->getFacebook());
                $booleans[] = $this->compare($teamMember->getPhoto(), $approvedTeamMembers[$key]->getPhoto());
                $booleans[] = $this->compare($teamMember->getCv(), $approvedTeamMembers[$key]->getCv());
                $booleans[] = $this->compare($teamMember->getPorteur(), $approvedTeamMembers[$key]->getPorteur());
                $key ++;
                if (in_array(true, $booleans)){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $approvedDocuments
     * @param $documents
     * @return bool
     */
    public function compareDocuments($documents, $approvedDocuments)
    {
        $res = $this->compare(count($documents), count($approvedDocuments));

        if ($res === true) {
            return true;
        } else {
            $booleans = [];
            $key = 0;
            foreach ($documents as $document) {
                /**
                 * @var $document Document
                 */
                $booleans[] = $this->compare($document->getName(), $approvedDocuments[$key]->getName());
                $booleans[] = $this->compare($document->getDocumentType(), $approvedDocuments[$key]->getDocumentType());
                $booleans[] = $this->compare($document->getIsPrivate(), $approvedDocuments[$key]->getIsPrivate());
                $key ++;

                if (in_array(true, $booleans)){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $approvedGalleryPhotos
     * @param $galleryPhotos
     * @return bool
     */
    public function compareGalleryPhotos($galleryPhotos, $approvedGalleryPhotos)
    {
        $res = $this->compare(count($galleryPhotos), count($approvedGalleryPhotos));

        if ($res === true) {
            return true;
        } else {
            $booleans = [];
            $key = 0;
            foreach ($galleryPhotos as $photo) {
                $booleans[] = $this->compare($photo->getName(), $approvedGalleryPhotos[$key]->getName());
                $booleans[] = $this->compare($photo->getAlt(), $approvedGalleryPhotos[$key]->getAlt());
                $booleans[] = $this->compare($photo->getDescription(), $approvedGalleryPhotos[$key]->getDescription());
                $key ++;
                if (in_array(true, $booleans)){
                    return true;
                }
            }
        }
        return false;
    }
}
