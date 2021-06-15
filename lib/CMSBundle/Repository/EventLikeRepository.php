<?php

namespace EgioDigital\CMSBundle\Repository;

use EgioDigital\CMSBundle\Entity\EventLike;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EventLike|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventLike|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventLike[]    findAll()
 * @method EventLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventLikeRepository extends ServiceEntityRepository
{
   public function __construct(RegistryInterface $registry)
   {
      parent::__construct($registry, EventLike::class);
   }

}
