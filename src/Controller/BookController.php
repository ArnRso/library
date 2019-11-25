<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    public function getBooks(BookRepository $bookRepository)
    {
        $books = $bookRepository->findAll();
        return $this->render('books.html.twig', ['books' => $books]);
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

    public function getBook(BookRepository $bookRepository, $id)
    {
        $book = $bookRepository->find($id);
        return $this->render('book.html.twig', ['book' => $book]);
    }

    /**
     * @Route("/books/search", name="search_books")
     * @param BookRepository $bookRepository
     * @param Request $request
     * @return Response
     */
    public function getBooksByStyle(BookRepository $bookRepository, Request $request)
    {
        /**
         * Ajoute les valeurs du GET dans des variables puis ces variables dans un array, afin de
         * les passer à la vue pour traiter les attributs "Value" dans la vue twig
         */
        $get = [];
        $style = $request->query->get('style');
        $title = $request->query->get('title');
        $stock = $request->query->get('inStock');
        $get['style'] = $style;
        $get['title'] = $title;
        $get['stock'] = $stock;

        $books = $bookRepository->getByStyle($style, $title, $stock);
        return $this->render('books.html.twig', ['books' => $books, 'get' => $get]);

    }
}