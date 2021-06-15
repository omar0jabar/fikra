<?php

namespace App\Repository;

use App\Entity\Earned;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Earned|null find($id, $lockMode = null, $lockVersion = null)
 * @method Earned|null findOneBy(array $criteria, array $orderBy = null)
 * @method Earned[]    findAll()
 * @method Earned[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EarnedRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Earned::class);
    }

    // /**
    //  * @return Earned[] Returns an array of Earned objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Earned
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
