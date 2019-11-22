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
     */
    public function getBook(BookRepository $bookRepository, $id) {
        $book = $bookRepository->findOneBy(['id'=>$id]);
        return $this->render('book.html.twig', ['book'=>$book]);
    }
}