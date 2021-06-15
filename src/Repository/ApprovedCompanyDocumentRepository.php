<?php

namespace App\Repository;

use App\Entity\ApprovedCompanyDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ApprovedCompanyDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApprovedCompanyDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApprovedCompanyDocument[]    findAll()
 * @method ApprovedCompanyDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApprovedCompanyDocumentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ApprovedCompanyDocument::class);
    }
}
