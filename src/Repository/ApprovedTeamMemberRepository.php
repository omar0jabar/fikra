<?php

namespace App\Repository;

use App\Entity\ApprovedTeamMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ApprovedTeamMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApprovedTeamMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApprovedTeamMember[]    findAll()
 * @method ApprovedTeamMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApprovedTeamMemberRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ApprovedTeamMember::class);
    }

    // /**
    //  * @return ApprovedTeamMember[] Returns an array of ApprovedTeamMember objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ApprovedTeamMember
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
