<?php


namespace App\Controller\front;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;

/**
 * Controlleur des Livres
 * @Route("/book", name="book_")
 */
class BookFrontController extends AbstractController
{
    /**
     * @Route("s", name="all")
     * @param BookRepository $bookRepository
     * @return Response
     *
     * Affiche tous les livres de l'entité Book
     */
    public function getBooks(BookRepository $bookRepository)
    {
        $books = $bookRepository->findAll();
        return $this->render('book/books.html.twig', ['books' => $books]);
    }


    /**
     * @Route("/show/{id}", name="show")
     * @param BookRepository $bookRepository
     * @param $id
     * @return Response
     *
     * Affiche un seul livre de l'entité Book,
     * celui dont l'ID est précisé dans la WildCard
     */
    public function getBook(BookRepository $bookRepository, $id)
    {
        $book = $bookRepository->find($id);
        return $this->render('book/book.html.twig', ['book' => $book]);
    }


    /**
     * @Route("/search", name="search")
     * @param BookRepository $bookRepository
     * @param Request $request
     * @return Response
     *
     * Affiche les livres qui correspondent aux données transmises
     * via le formulaire de recherche(en GET)
     */
    public function searchInBooks(BookRepository $bookRepository, Request $request)
    {
        $get = [];
        $style = $request->query->get('style');
        $title = $request->query->get('title');
        $stock = $request->query->get('inStock');

        $get['style'] = $style;
        $get['title'] = $title;
        $get['stock'] = $stock;

        $books = $bookRepository->customSearch($style, $title, $stock);
        return $this->render('book/books.html.twig', ['books' => $books, 'get' => $get]);
    }

    /**
     * @Route("s/byAuthor/{id}", name="byAuthor")
     * @param $id
     * @param BookRepository $bookRepository
     * @return Response
     */
    public function showBooksByAuthor($id, BookRepository $bookRepository)
    {
        $books = $bookRepository->findBy(['author' => $id]);
        return $this->render('book/books.html.twig', ['books' => $books]);
    }
}