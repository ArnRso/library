<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }


    /**
     * Crée une méthode qui permet de faire une recherche dans la table author selon les critères
     * nom et prénom
     * @param $style
     * @param $title
     * @param $inStock
     * @return array
     */
    public function customSearch($field)
    {
        /**
         * Crée la requette SQL "
         * SELECT * IN book WHERE book.style = $style
         * AND WHERE book.title = $title"
         */
        $qb = $this->createQueryBuilder('author');
        $query = $qb->select('author')
            ->Where('author.name LIKE :name')
            ->setParameter('name', '%' . $field . '%')
            ->orWhere('author.firstName LIKE :firstName')
            ->setParameter('firstName', '%' . $field . '%');
        /**
         * Stock tous les résultats de la recherche sous forme d'array
         * dans la variable $resultats
         */
        $query = $qb->getQuery();
        $resultats = $query->getResult();
        return $resultats;
    }

    // /**
    //  * @return Author[] Returns an array of Author objects
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
    public function findOneBySomeField($value): ?Author
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
