<?php

namespace App\Repository;

use App\Entity\CommentCaMarcheBlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CommentCaMarcheBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentCaMarcheBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentCaMarcheBlock[]    findAll()
 * @method CommentCaMarcheBlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentCaMarcheBlockRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CommentCaMarcheBlock::class);
    }

    // /**
    //  * @return CommentSaMarcheBlock[] Returns an array of CommentSaMarcheBlock objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CommentSaMarcheBlock
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
