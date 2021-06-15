<?php

namespace App\Repository;

use App\Entity\MessageResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MessageResponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageResponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageResponse[]    findAll()
 * @method MessageResponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageResponseRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MessageResponse::class);
    }

    // /**
    //  * @return MessageResponse[] Returns an array of MessageResponse objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MessageResponse
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
