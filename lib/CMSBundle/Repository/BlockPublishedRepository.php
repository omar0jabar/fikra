<?php

namespace EgioDigital\CMSBundle\Repository;

use EgioDigital\CMSBundle\Entity\BlockPublished;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BlockPublished|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlockPublished|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlockPublished[]    findAll()
 * @method BlockPublished[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlockPublishedRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BlockPublished::class);
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
