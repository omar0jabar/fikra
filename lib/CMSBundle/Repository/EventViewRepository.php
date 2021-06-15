<?php

namespace EgioDigital\CMSBundle\Repository;

use EgioDigital\CMSBundle\Entity\EventView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EventView|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventView|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventView[]    findAll()
 * @method EventView[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventViewRepository extends ServiceEntityRepository
{
   public function __construct(RegistryInterface $registry)
   {
      parent::__construct($registry, EventView::class);
   }

}
