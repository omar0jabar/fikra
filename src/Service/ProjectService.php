<?php

namespace App\Service;

use App\Entity\ApprovedAvantage;
use App\Entity\ApprovedDocument;
use App\Entity\ApprovedGalleryPhoto;
use App\Entity\ApprovedProject;
use App\Entity\ApprovedProjectFinance;
use App\Entity\ApprovedService;
use App\Entity\ApprovedTeamMember;
use App\Entity\Project;
use App\Entity\TeamMember;
use App\Repository\ApprovedProjectRepository;
use App\Repository\ProjectRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectService
{

    private $projectRepository;
    private $approvedProjectRepository;
    private $manager;

    public function __construct(ProjectRepository $projectRepository,
                                ApprovedProjectRepository $approvedProjectRepository,
                                ObjectManager $manager)
    {
        $this->projectRepository = $projectRepository;
        $this->approvedProjectRepository = $approvedProjectRepository;
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
     * @param int $id
     */
    public function approveProject(int $id)
    {
        $project = $this->projectRepository->findOneBy(['id' => $id]);

        if (!$project) {
            throw new NotFoundHttpException('project not found');
        }
        $newApprovedProject = $this->approvedProjectRepository->findOneBy(['project' => $project]);
        if (!$newApprovedProject) {
            $newApprovedProject = new ApprovedProject();
        }

        $newApprovedProject
            ->setLanguage($project->getLanguage())
            ->setName($project->getName())
            ->setProject($project)
            ->setDescription($project->getDescription())
            ->setStep($project->getStep())
            ->setEarned($project->getEarned())
            ->setOtherSalesChannels($project->getOtherSalesChannels())
            ->setMoreSalesChannels($project->getMoreSalesChannels())
            ->setMoreSectors($project->getMoreSectors())
            ->setOtherBusinessModel($project->getOtherBusinessModel())
            ->setMoreBusinessModel($project->getMoreBusinessModel())
            ->setMorocco($project->getMorocco())
            ->setOtherCountry($project->getOtherCountry())
            ->setForeignCountry($project->getForeignCountry())
            ->setMarketResearch($project->getMarketResearch())
            ->setHasNotAmount($project->getHasNotAmount())
            ->setBudget($project->getBudget())
            ->setRaised($project->getRaised())
            ->setAmount($project->getAmount())
            ->setSummary($project->getSummary())
            ->setExpress($project->getExpress())
            ->setStartup($project->getStartup())
            ->setDenomination($project->getDenomination())
            ->setCreatingDate($project->getCreatingDate())
            ->setRc($project->getRc())
            ->setCity($project->getCity())
            ->setAuthor($project->getAuthor())
            ->setSlug($project->getSlug())
            ->setImageCoverName($project->getImageCoverName())
            ->setLogoName($project->getLogoName())
            ->setVideo($project->getVideo())
            ->setIsVerified($project->getIsVerified())
            ->setIsApproved(true)
            ->setOrderBy($project->getOrderBy())
        ;

        $this->createDirectories();

        if ($project->getImageCoverName()) {
            $path = "upload/projects/gallery-photo/{$project->getImageCoverName()}";
            if (is_file($path)) {
                copy($path, "upload/approved-projects/approved-gallery-photo/{$project->getImageCoverName()}");
            }
        }

        if ($project->getLogoName()) {
            $path = "upload/projects/project-logo/{$project->getLogoName()}";
            if (is_file($path)) {
                copy($path, "upload/approved-projects/approved-gallery-photo/{$project->getLogoName()}");
            }
        }

        foreach ($project->getServices() as $service) {
            $approvedService = new ApprovedService();
            $approvedService->setName($service->getName())
                ->setApprovedProject($newApprovedProject);
            $newApprovedProject->addService($approvedService);
            $this->manager->persist($approvedService);
        }

        foreach ($project->getSalesChannels() as $salesChannel) {
            $newApprovedProject->addSalesChannel($salesChannel);
        }
        foreach ($project->getSectors() as $sector) {
            $newApprovedProject->addSector($sector);
        }
        foreach ($project->getBusinessModels() as $businessModel) {
            $newApprovedProject->addBusinessModel($businessModel);
        }

        foreach ($project->getAvantages() as $avantage) {
            $approvedAvantage = new ApprovedAvantage();
            $approvedAvantage->setName($avantage->getName())
                ->setApprovedProject($newApprovedProject);
            $newApprovedProject->addAvantage($approvedAvantage);
            $this->manager->persist($approvedAvantage);
        }

        foreach ($project->getProjectFinances() as $projectFinance) {
            $approvedProjectFinance = new ApprovedProjectFinance();
            $approvedProjectFinance->setDetail($projectFinance->getDetail())
                ->setApprovedProject($newApprovedProject);
            $newApprovedProject->addApprovedProjectFinance($approvedProjectFinance);
            $this->manager->persist($approvedProjectFinance);
        }

        foreach ($project->getTeamMembers() as $teamMember) {
            /**
             * @var $teamMember TeamMember
             */
            $member = new ApprovedTeamMember();
            $member->setFirstName($teamMember->getFirstName())
                ->setLastName($teamMember->getLastName())
                ->setPosition($teamMember->getPosition())
                ->setBiography($teamMember->getBiography())
                ->setPhoto($teamMember->getPhoto())
                ->setCv($teamMember->getCv())
                ->setLinkedin($teamMember->getLinkedin())
                ->setTwitter($teamMember->getTwitter())
                ->setFacebook($teamMember->getFacebook())
                ->setPorteur($teamMember->getPorteur())
                ->setCreatedAt($teamMember->getCreatedAt())
                ->setUpdatedAt($teamMember->getUpdatedAt())
                ->setApprovedProject($newApprovedProject)
            ;
            $newApprovedProject->addTeamMember($member);
            $this->manager->persist($member);
        }

        foreach ($project->getDocuments() as $document) {
            $approvedDocument = new ApprovedDocument();
            $approvedDocument->setName($document->getName())
                ->setDocumentType($document->getDocumentType())
                ->setIsPrivate($document->getIsPrivate())
                ->setApprovedProject($newApprovedProject)
            ;
            $newApprovedProject->addDocument($approvedDocument);
            $this->manager->persist($approvedDocument);
        }

        foreach ($project->getGalleryPhotos() as $galleryPhoto) {
            $approvedPhoto = new ApprovedGalleryPhoto();
            $approvedPhoto->setName($galleryPhoto->getName())
                ->setAlt($galleryPhoto->getAlt())
                ->setDescription($galleryPhoto->getDescription())
                ->setApprovedProject($newApprovedProject)
            ;
            $newApprovedProject->addGalleryPhoto($approvedPhoto);
            $this->manager->persist($approvedPhoto);
        }

        // call function for copy photos & documents
        $this->copyMedia($project);

        // save approved project
        $this->manager->persist($newApprovedProject);
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
        $directories[] = "upload/approved-projects";
        $directories[] = "upload/approved-projects/approved-member-photo";
        $directories[] = "upload/approved-projects/approved-member-cv";
        $directories[] = "upload/approved-projects/approved-project-documents";
        $directories[] = "upload/approved-projects/approved-gallery-photo";

        foreach ($directories as $directory) {
            $this->createDirectory("$directory");
        }
    }

    /**
     * @param Project $project
     */
    public function copyMedia(Project $project)
    {
        foreach ($project->getTeamMembers() as $member) {
            $path = "upload/projects/member-photo/{$member->getPhoto()}";
            if (is_file($path)) {
                copy($path, "upload/approved-projects/approved-member-photo/{$member->getPhoto()}");
            }

            $path = "upload/projects/member-cv/{$member->getCv()}";
            if (is_file($path)) {
                copy($path, "upload/approved-projects/approved-member-cv/{$member->getCv()}");
            }
        }

        foreach ($project->getDocuments() as $document) {
            $path = "upload/projects/documents/{$document->getName()}";
            if (is_file($path)) {
                copy($path, "upload/approved-projects/approved-project-documents/{$document->getName()}");
            }
        }

        foreach ($project->getGalleryPhotos() as $galleryPhoto) {
            $path = "upload/projects/gallery-photo/{$galleryPhoto->getName()}";
            if (is_file($path)) {
                copy($path, "upload/approved-projects/approved-gallery-photo/{$galleryPhoto->getName()}");
            }
        }
    }

    /**
     * @param ApprovedProject $approvedProject
     */
    public function disapproveProject(ApprovedProject $approvedProject)
    {
        if (!$approvedProject) {
            throw new NotFoundHttpException('approved project not found');
        }

        foreach ($approvedProject->getSectors() as $sector) {
            $approvedProject->removeSector($sector);
        }

        foreach ($approvedProject->getSalesChannels() as $salesChannel) {
            $approvedProject->removeSalesChannel($salesChannel);
        }

        foreach ($approvedProject->getBusinessModels() as $businessModel) {
            $approvedProject->removeBusinessModel($businessModel);
        }

        if ($approvedProject->getImageCoverName()) {
            $path = "upload/approved-projects/approved-gallery-photo/{$approvedProject->getImageCoverName()}";
            if (is_file($path)) {
                unlink($path);
            }
        }

        if ($approvedProject->getLogoName()) {
            $path = "upload/approved-projects/approved-gallery-photo/{$approvedProject->getLogoName()}";
            if (is_file($path)) {
                unlink($path);
            }
        }

        $this->manager->flush();
        // call function for deleting medias
        $this->deleteMedia($approvedProject);

    }

    /**
     * @param ApprovedProject $approvedProject
     */
    public function deleteMedia(ApprovedProject $approvedProject)
    {
        foreach ($approvedProject->getTeamMembers() as $member) {
            $path = "upload/approved-projects/approved-member-photo/{$member->getPhoto()}";
            if (is_file($path)) {
                unlink($path);
            }

            $path = "upload/approved-projects/approved-member-cv/{$member->getCv()}";
            if (is_file($path)) {
                unlink($path);
            }
            $approvedProject->removeTeamMember($member);
        }

        foreach ($approvedProject->getDocuments() as $document) {
            $path = "upload/approved-projects/approved-project-documents/{$document->getName()}";
            if (is_file($path)) {
                unlink($path);
            }
            $approvedProject->removeDocument($document);
        }
        foreach ($approvedProject->getGalleryPhotos() as $photo) {
            $path = "upload/approved-projects/approved-gallery-photo/{$photo->getName()}";
            if (is_file($path)) {
                unlink($path);
            }
            $approvedProject->removeGalleryPhoto($photo);
        }

        foreach ($approvedProject->getServices() as $service) {
            $approvedProject->removeService($service);
        }
        foreach ($approvedProject->getAvantages() as $avantage) {
            $approvedProject->removeAvantage($avantage);
        }
        foreach ($approvedProject->getProjectFinances() as $projectFinance) {
            $approvedProject->removeApprovedProjectFinance($projectFinance);
        }
        $this->manager->flush();
    }

}