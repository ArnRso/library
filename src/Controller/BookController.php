<?php


namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;
use App\Entity\Book;

/**
 * Controlleur des Livres
 */
class BookController extends AbstractController
{
    /**
     * @Route("/books", name="books")
     * @param BookRepository $bookRepository
     * @return Response
     *
     * Affiche tous les livres de l'entité Book
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
     *
     * Affiche un seul livre de l'entité Book,
     * celui dont l'ID est précisé dans la WildCard
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
        return $this->render('books.html.twig', ['books' => $books, 'get' => $get]);
    }


    /**
     * @Route("/book/new", name="book_new")
     *
     * Permet d'afficher la vue book_form.html.twig
     * avec les champs du formulaire vides
     */
    public function newBook()
    {
        return $this->render('book_form.html.twig');
    }


    /**
     * @Route("/book/insert", name="book_insert")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     *
     * Insert un livre dans la BDD avec les informations fournies
     * dans le formulaire de la vue book_form.html.twig(en POST)
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
     * @Route("/book/edit/{id}", name="book_edit")
     * @param BookRepository $bookRepository
     * @param $id
     * @return Response
     *
     * Permet d'appeler la vue book_form.html.twig
     * préremplie avec le livre dont l'ID est
     * dans la WildCard de la route
     */
    public function editBook(BookRepository $bookRepository, $id)
    {
        $book = $bookRepository->find($id);
        return $this->render('book_form.html.twig', ['book' => $book]);
    }


    /**
     * @Route("/book/update", name="book_update")
     * @param BookRepository $bookRepository
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return RedirectResponse
     *
     * Permet de prendre les données envoyées via la vue
     * book_form.html.twig (en POST) et utilise les méthodes "setters"
     * de l'entité Book pour modifier les données inscrites en BDD
     */
    public function updateBook(BookRepository $bookRepository, EntityManagerInterface $entityManager, Request $request)
    {
        $id = $request->request->get('id');
        $title = $request->request->get('title');
        $style = $request->request->get('style');
        $nbPages = $request->request->get('nbPages');
        $inStock = $request->request->get('inStock');

        $book = $bookRepository->find($id);


        $book->setTitle($title);
        $book->setStyle($style);
        $book->setNbPages($nbPages);

        if ($inStock == 1) {
            $book->setInStock(true);
        } else {
            $book->setInStock(false);
        }

        $entityManager->persist($book);
        $entityManager->flush();
        return $this->redirectToRoute('books');
    }


    /**
     * @Route("/book/delete/{id}", name="book_delete")
     * @param EntityManagerInterface $entityManager
     * @param BookRepository $bookRepository
     * @param $id
     * @return RedirectResponse
     *
     * Supprime le livre de la BDD dont l'ID est indiqué dans la WildCard
     */
    public function deleteBook(EntityManagerInterface $entityManager, BookRepository $bookRepository, $id)
    {
        $book = $bookRepository->find($id);
        $entityManager->remove($book);
        $entityManager->flush();
        return $this->redirectToRoute('books');
    }
}