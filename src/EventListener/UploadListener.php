<?php

namespace App\EventListener;

use App\Entity\Document;
use App\Entity\GalleryPhoto;
use App\Entity\TeamMember;
use App\Service\Comparison;
use Doctrine\Common\Persistence\ObjectManager;
use Vich\UploaderBundle\Event\Event;

class UploadListener
{
    private $comparison;
    private $manager;

    public function __construct(Comparison $comparison, ObjectManager $manager)
    {
        $this->comparison = $comparison;
        $this->manager = $manager;
    }

    public function onVichUploaderPostUpload(Event $event)
    {
        $object = $event->getObject();

        if ($object instanceof TeamMember || $object instanceof Document || $object instanceof GalleryPhoto) {
            $project = $object->getProject();
            $updated = $this->comparison->compareProject($project);
            if ($updated) {
                $project->setIsUpdated(true);
                $this->manager->flush();
            }
        }

    }

}