<?php

namespace App\Repository;

use App\Entity\CompanyCommentResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CompanyCommentResponse|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyCommentResponse|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyCommentResponse[]    findAll()
 * @method CompanyCommentResponse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyCommentResponseRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CompanyCommentResponse::class);
    }

}
