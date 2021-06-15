<?php

namespace App\Repository;

use App\Entity\ApprovedCompany;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ApprovedCompany|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApprovedCompany|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApprovedCompany[]    findAll()
 * @method ApprovedCompany[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApprovedCompanyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ApprovedCompany::class);
    }


    public function getCompanies($domains, $city, $search, $offset, $order = null)
    {

        $qb = $this->createQueryBuilder('ap');
        if (!empty($domains)) {
            $qb->join('ap.domain', 'aps')
                ->where('aps.id IN (' . $domains . ')');
        }
        if ($city != null) {
            $qb->andWhere('ap.city = :city')
                ->setParameter('city', $city);
        }

        if ($search) {
            $qb->andWhere('ap.name LIKE :search')
                ->orWhere('ap.description LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
        if ($order) {
            if ($order == 2) {
                $qb->orderBy('ap.createdAt', 'ASC');
            } else if ($order == 3) {
                $qb->orderBy('ap.fundingObjective', 'ASC');
            } else if ($order == 4) {
                $qb->orderBy('ap.fundingObjective', 'DESC');
            } else {
                $qb->orderBy('ap.createdAt', "DESC");
            }
        } else {
            $qb
                ->join('ap.company', 'c')
                ->orderBy('c.endDate', "DESC");
        }
        return $qb
            ->andWhere('ap.isDeleted = 0')
            ->andWhere('ap.isApproved = 1')
            ->setFirstResult($offset)
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }

    public function getCompaniesForCount($domains, $city, $search, $order = null)
    {
        $qb = $this->createQueryBuilder('ap');
        if (!empty($domains)) {
            $qb->join('ap.domain', 'aps')
                ->where('aps.id IN (' . $domains . ')');
        }
        if ($city != null) {
            $qb->andWhere('ap.city = :city')
                ->setParameter('city', $city);
        }
        if ($search) {
            $qb->andWhere('ap.name LIKE :search')
                ->orWhere('ap.description LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
        if ($order) {
            if ($order == 2) {
                $qb->orderBy('ap.createdAt', 'ASC');
            } else if ($order == 3) {
                $qb->orderBy('ap.fundingObjective', 'ASC');
            } else if ($order == 4) {
                $qb->orderBy('ap.fundingObjective', 'DESC');
            } else {
                $qb->orderBy('ap.createdAt', "DESC");
            }
        } else {
            $qb
                ->join('ap.company', 'c')
                ->orderBy('c.endDate', "DESC");
        }
        return $qb
            ->andWhere('ap.isDeleted = 0')
            ->andWhere('ap.isApproved = 1')
            ->getQuery()
            ->getResult();
    }

    public function getSimilarCompanies($domains, $companyId, $limit = 3)
    {
        $qb =  $this->createQueryBuilder('app');
        if ($domains) {
            $qb->join('app.domain', 'd')
                ->where('d.id IN (' . $domains . ')');
        }
        return $qb
            ->join('app.company', 'c')
            ->andWhere('app.isDeleted = 0')
            ->andWhere('app.isApproved = 1')
            ->andWhere('app.id != :id')
            ->setParameters(['id' => $companyId])
            ->orderBy('c.endDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $limit
     * @return int|mixed|string
     */
    public function getCompaniesForHome(int $limit = 6)
    {
        $qb = $this->createQueryBuilder('ap');
        $today = new \DateTime();
        return $qb
            ->join('ap.company', 'c')
            ->andWhere('ap.isDeleted = 0')
            ->andWhere('ap.isApproved = 1')
            ->andWhere('c.endDate >= :endDate')
            ->orderBy('c.endDate', "DESC")
            ->setParameters(['endDate' =>  $today])
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

}
