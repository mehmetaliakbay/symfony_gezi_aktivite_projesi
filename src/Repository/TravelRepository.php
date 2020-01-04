<?php

namespace App\Repository;

use App\Entity\Travel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Travel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Travel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Travel[]    findAll()
 * @method Travel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TravelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Travel::class);
    }

    // /**
    //  * @return Travel[] Returns an array of Travel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Travel
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    // *** LEFT JOIN WITH SQL ******
    public function getAllTravels(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql= '
        SELECT t.*,c.title as catname FROM travel t
        JOIN category c  ON c.id = t.category_id
        ORDER BY c.title ASC
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // return an array of arrays (i.e a raw data set)

        return $stmt->fetchAll();


    }

    
}
