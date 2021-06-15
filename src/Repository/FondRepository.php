<?php

namespace App\Repository;

use App\Entity\Fond;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Fond|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fond|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fond[]    findAll()
 * @method Fond[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FondRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Fond::class);
    }

    
    public function getFonds($data, $limit = 0, $from = 0)
    {
        $query = $this->createQueryBuilder('f');
        $local = isset($data['local']) ? $data['local'] : 'fr';
        $title = isset($data['title']) ? $data['title'] : '';
        $fondId = isset($data['fondId']) ? (int)$data['fondId'] : 0;
        $secteurType = (isset($data['secteurType']) && $data['secteurType'] != "0") ? $data['secteurType'] : '';
        $fondPhases = isset($data['fondPhases']) ? $data['fondPhases'] : '';
        $fondType = isset($data['fondType']) ? $data['fondType'] : 0;
        $gestionnaire = isset($data['gestionnaire']) ? $data['gestionnaire'] : 0;
        $financement = (isset($data['financementType']) && $data['financementType'] != '') ? $data['financementType'] : '';
        $minMax = isset($data['min']) ? $data['min'] : 0;
        $minMax = !is_null($minMax) ? $minMax : 0;
        $active = true;
        $query->where('f.local = :local')
                ->setParameter('local', $local);

        $query->andwhere('f.active = :active')
                ->setParameter('active', $active);

        if(!empty($title)) {
            $query->andWhere('f.title LIKE :title')
            ->setParameter('title', "%".$title."%");
            $query->orWhere('f.sortDesctiption LIKE :sortDesctiption')
            ->setParameter('sortDesctiption', "%".$title."%");
        }       
         
        if ( $minMax > 0 ) {
            $query->andWhere('f.min ='.(int)$minMax);
                //->OrWhere('f.max ='.(int)$minMax);
        }

        if ( !empty($fondPhases) ) {
            $query->join('f.fondPhases', 'fp')
                ->andwhere('fp.id IN ('.trim($fondPhases).')');
        }

        if ( !empty($financement) ) {
            $query->join('f.financements', 'ff')
                ->andwhere('ff.id IN ('.trim($financement).')');
        }

        if ( !empty($secteurType) ) {
            $query->join('f.secteurType', 'fs')
                ->andwhere('fs.id IN ('.trim($secteurType).')');
        }

        if ( $fondType > 0 ) {
            $query->andWhere('f.fondType in ('.trim($fondType).') ');
        }
        if ( is_array($gestionnaire) > 0 || !empty($gestionnaire)) {
            $gestionnaire_ids = is_array($gestionnaire) ? implode(',', $gestionnaire) : $gestionnaire;
            if( !(is_array($gestionnaire) && in_array(0, $gestionnaire)) && !empty($gestionnaire_ids)) {
                $query->join('f.gestionnaires', 'fg')
                ->andwhere('fg.id IN ('.$gestionnaire_ids.')');
            }
        }
        
         

        if($fondId > 0) {
            $query->andWhere('f.id !='.$fondId);
        }
        
        $query->orderBy('f.title', 'asc');
        if($limit > 0 ) {
            //$query->setFirstResult($from);
            //->setMaxResults($limit);
        }
        $results = $query->getQuery()
                    ->getResult();
        if($limit > 0 ) {
            $results = array_slice($results,$from,$limit);
        }
        
        return $results;
    }

    // /**
    //  * @return Fond[] Returns an array of Fond objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Fond
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
