<?php

namespace App\Repository;

use App\Entity\FondType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FondType|null find($id, $lockMode = null, $lockVersion = null)
 * @method FondType|null findOneBy(array $criteria, array $orderBy = null)
 * @method FondType[]    findAll()
 * @method FondType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FondTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FondType::class);
    }

    // /**
    //  * @return FondType[] Returns an array of FondType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FondType
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
