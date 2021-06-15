<?php

namespace App\Repository;

use App\Entity\ReassuranceCol;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ReassuranceCol|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReassuranceCol|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReassuranceCol[]    findAll()
 * @method ReassuranceCol[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReassuranceColRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReassuranceCol::class);
    }

    // /**
    //  * @return ReassuranceCol[] Returns an array of ReassuranceCol objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReassuranceCol
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
