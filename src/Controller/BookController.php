<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;

class BookController extends AbstractController
{
    /**
     * @Route("/books", name="books")
     * @param BookRepository $bookRepository
     * @return Response
     * Le varHint BookRepository permet de stocker dans $bookRepository une instance de la classe
     * BookRepository. C'est dans cette classe qu'est stocké la méthode findAll(). Le Repository construit les
     * requettes SQL pour accèder au contenu de l'entité Book
     */

    public function getBooks(BookRepository $bookRepository) {
        $books = $bookRepository->findAll();
        return $this->render('books.html.twig', ['books'=>$books]);
    }

    /**
     * @Route("/book/{id}", name="book")
     * @param BookRepository $bookRepository
     * @param $id
     * @return Response
     * Le varHint BookRepository permet de stocker dans $bookRepository une instance de la classe
     * BookRepository. C'est dans cette classe qu'est stocké la méthode find($id). Le Repository construit la
     * requette SQL pour accèder au contenu de l'entité Book dont l'ID est passé dans la WildCard de la route.
     */

    public function getBook(BookRepository $bookRepository, $id) {
        $book = $bookRepository->find($id);
        return $this->render('book.html.twig', ['book'=>$book]);
    }
}