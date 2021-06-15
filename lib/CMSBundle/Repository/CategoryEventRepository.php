<?php

namespace EgioDigital\CMSBundle\Repository;

use EgioDigital\CMSBundle\Entity\CategoryEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CategoryEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryEvent[]    findAll()
 * @method CategoryEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryEventRepository extends ServiceEntityRepository
{
   public function __construct(RegistryInterface $registry)
   {
      parent::__construct($registry, CategoryEvent::class);
   }

   // /**
   //  * @return Page[] Returns an array of Page objects
   //  */
   /*
   public function findByExampleField($value)
   {
       return $this->createQueryBuilder('p')
           ->andWhere('p.exampleField = :val')
           ->setParameter('val', $value)
           ->orderBy('p.id', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }
   */

   /*
   public function findOneBySomeField($value): ?Page
   {
       return $this->createQueryBuilder('p')
           ->andWhere('p.exampleField = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }
   */
}
