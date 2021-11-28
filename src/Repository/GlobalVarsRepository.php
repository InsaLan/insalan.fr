<?php

// NOUVEAU FICHIER TEST DONC PAS FORCEMENT A PRENDRE EN COMPTE



namespace App\Repository;

use App\Entity\GlobalVars;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GlobalVars|null find($id, $lockMode = null, $lockVersion = null)
 * @method GlobalVars|null findOneBy(array $criteria, array $orderBy = null)
 * @method GlobalVars[]    findAll()
 * @method GlobalVars[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GlobalVarsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GlobalVars::class);
    }

    // /**
    //  * @return GlobalVars[] Returns an array of GlobalVars objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GlobalVars
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /**
    * Get GlobalVars matching with keys.
    *
    * @param Array $keys
    * @return Array
    */
    public function getGlobalVars(Array $keys)
    {
      $globalVars = array();

      foreach ($keys as $k) {
          $globVar = $this->findOneByGlobalKey($k);
          if ($globVar != null) { // Check to avoid exceptions if it doesn't exist.
            $globalVars[$k] = $globVar->getGlobalValue();
          } else {
              $globalVars[$k] = ""; // To avoid Twig exceptions.
          }
        }
        return $globalVars;
    }
}
