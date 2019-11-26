<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * Crée une méthode qui permet de faire une recherche dans la table book selon les critères
     * Style, title et le boolean inStock
     * @param $style
     * @param $title
     * @param $inStock
     * @return array
     */
    public function customSearch($style, $title, $inStock)
    {
        /**
         * Crée la requette SQL "
         * SELECT * IN book WHERE book.style = $style
         * AND WHERE book.title = $title"
         */
        $qb = $this->createQueryBuilder('book');
        $query = $qb->select('book')
            ->where('book.style LIKE :style')
            ->setParameter('style', '%' . $style . '%')
            ->andWhere('book.title LIKE :title')
            ->setParameter('title', '%' . $title . '%');

        if ($inStock === 'yes') {
            /**
             * Ajoute "AND WHERE book.in_stock = 1"
             */
            $query = $qb->andWhere('book.inStock = :stock')
                ->setParameter('stock', true);
        }

        /**
         * Stock tous les résultats de la recherche sous forme d'array
         * dans la variable $resultats
         */
        $query = $qb->getQuery();
        $resultats = $query->getResult();
        return $resultats;
    }

    // /**
    //  * @return Book[] Returns an array of Book objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
