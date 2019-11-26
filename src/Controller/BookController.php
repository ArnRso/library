<?php


namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;
use App\Entity\Book;

class BookController extends AbstractController
{
    /**
     * @Route("/books", name="books")
     * @param BookRepository $bookRepository
     * @return Response
     * Le varHint BookRepository permet de stocker dans $bookRepository une instance de la classe
     * BookRepository. C'est dans cette classe qu'est stocké la méthode findAll().
     * Le Repository construit les
     * requettes SQL pour accèder au contenu de l'entité Book
     */

    public function getBooks(BookRepository $bookRepository)
    {
        $books = $bookRepository->findAll();
        return $this->render('books.html.twig', ['books' => $books]);
    }

    /**
     * @Route("/book/show/{id}", name="book")
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
    public function searchInBooks(BookRepository $bookRepository, Request $request)
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

        $books = $bookRepository->customSearch($style, $title, $stock);
        return $this->render('books.html.twig', ['books' => $books, 'get' => $get]);

    }

    /**
     * @Route("/book/insert", name="book_insert")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     */
    public function insertBook(EntityManagerInterface $entityManager, Request $request)
    {
        $title = $request->request->get('title');
        $nbPages = $request->request->get('nbPages');
        $inStock = $request->request->get('inStock');
        $style = $request->request->get('style');
        $book = new Book();
        $book->setTitle($title)
            ->setNbPages($nbPages)
            ->setInStock($inStock)
            ->setStyle($style);

        $entityManager->persist($book);
        $entityManager->flush();

        return $this->render('book.html.twig', [
            'book' => $book,
            'message' => "votre livre a bien été inséré"
        ]);
    }

    /**
     * @Route("/book/new", name="book_new")
     */
    public function newBook()
    {
        return $this->render('book_form.html.twig');
    }

    /**
     * @Route("/book/delete/{id}", name="book_delete")
     */
    public function deleteBook(EntityManagerInterface $entityManager, BookRepository $bookRepository, $id)
    {
        $book = $bookRepository->find($id);
        $entityManager->remove($book);
        $entityManager->flush();
        return $this->redirectToRoute('books');
    }


}