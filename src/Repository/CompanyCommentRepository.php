<?php

namespace App\Repository;

use App\Entity\CompanyComment;
use App\Entity\Company;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CompanyComment|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyComment|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyComment[]    findAll()
 * @method CompanyComment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyCommentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CompanyComment::class);
    }

    /*
    public function getAllQuery(Project $project)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.project = :project')
            ->setParameter('project', $project)
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ;
    }

    public function getAllQueryByInvestor(User $user)
    {
        return $this->createQueryBuilder('m')
            ->join("m.project", "p")
            ->andWhere('m.author = :author')
            ->orWhere('p.author = :author')
            ->andWhere('p.isDeleted = 0')
            ->setParameter('author', $user)
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ;
    }

    public function getAllQueryByInvestor0(User $user)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.author = :author')
            ->setParameter('author', $user)
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ;
    }
    */
}
