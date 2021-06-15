<?php

namespace App\Repository;

use App\Entity\RequestDocAccepted;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RequestDocAccepted|null find($id, $lockMode = null, $lockVersion = null)
 * @method RequestDocAccepted|null findOneBy(array $criteria, array $orderBy = null)
 * @method RequestDocAccepted[]    findAll()
 * @method RequestDocAccepted[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestDocAcceptedRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RequestDocAccepted::class);
    }

    // /**
    //  * @return RequestDocAccepted[] Returns an array of RequestDocAccepted objects
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
    public function findOneBySomeField($value): ?RequestDocAccepted
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
