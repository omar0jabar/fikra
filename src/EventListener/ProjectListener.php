<?php

namespace App\EventListener;

use App\Entity\Project;
use App\Repository\ApprovedProjectRepository;
use App\Service\Comparison;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;

class ProjectListener
{
    private $compare;
    private $approvedProjectRepository;
    private $manager;

    public function __construct(
        Comparison $compare,
        ApprovedProjectRepository $approvedProjectRepository,
        ObjectManager $manager
    ) {
        $this->compare = $compare;
        $this->approvedProjectRepository = $approvedProjectRepository;
        $this->manager = $manager;
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof Project) {
            return;
        }

        $project = $entity;
        $updated = $this->compare->compareProject($project);
        if ($updated) {
            $project->setIsUpdated(true);
            $this->manager->persist($project);
        }
        $approvedProject = $this->approvedProjectRepository->findOneBy(['project' => $entity]);
        if ($approvedProject) {
            $approvedProject->setOrderBy($entity->getOrderBy());
        }
        $this->manager->flush();
    }

}