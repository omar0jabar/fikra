<?php

namespace App\Repository;

use App\Entity\SalesChannels;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SalesChannels|null find($id, $lockMode = null, $lockVersion = null)
 * @method SalesChannels|null findOneBy(array $criteria, array $orderBy = null)
 * @method SalesChannels[]    findAll()
 * @method SalesChannels[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalesChannelsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SalesChannels::class);
    }

    // /**
    //  * @return SalesChannels[] Returns an array of SalesChannels objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SalesChannels
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
