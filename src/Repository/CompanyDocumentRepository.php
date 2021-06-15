<?php

namespace App\Repository;

use App\Entity\CompanyDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CompanyDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompanyDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompanyDocument[]    findAll()
 * @method CompanyDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompanyDocumentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CompanyDocument::class);
    }
}
