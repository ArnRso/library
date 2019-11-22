<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;

class BookController extends AbstractController
{
    /**
     * @Route("/books", name="books")
     */
    public function getBooks(BookRepository $bookRepository) {
        $books = $bookRepository->findAll();
        dump($books);die;
        return $this->render('books.html.twig', ['books'=>$books]);
    }
}