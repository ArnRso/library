<?php


namespace App\Controller;


use App\Entity\Author;
use App\Form\AuthorType;
use App\Form\BookType;
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
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     *
     * Permet d'afficher la vue book_form.html.twig
     * avec les champs du formulaire vides
     */
    public function newBook(Request $request, EntityManagerInterface $entityManager)
    {
        $book = new Book;
        $bookForm = $this->createForm(BookType::class, $book);
        $bookForm->handleRequest($request);
        if ($bookForm->isSubmitted() && $bookForm->isValid()) {
            $book = $bookForm->getData();
            $entityManager->persist($book);
            $entityManager->flush();
            return $this->redirectToRoute('authors');
        }
        $bookFormView = $bookForm->createView();
        return $this->render('book_form.html.twig', [
            'authorFormView' => $bookFormView,
        ]);
    }


    /**
     * @Route("/book/edit/{id}", name="book_edit")
     * @param $id
     * @param Request $request
     * @param BookRepository $bookRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     *
     * Permet d'appeler la vue book_form.html.twig
     * préremplie avec le livre dont l'ID est
     * dans la WildCard de la route
     */
    public function editBook($id, Request $request, BookRepository $bookRepository, EntityManagerInterface $entityManager)
    {
        $book = $bookRepository->find($id);
        $bookForm = $this->createForm(BookType::class, $book);
        $bookForm->handleRequest($request);
        if ($bookForm->isSubmitted() && $bookForm->isValid()) {
            $book = $bookForm->getData();
            $entityManager->persist($book);
            $entityManager->flush();
            return $this->redirectToRoute('books');
        }
        $bookFormView = $bookForm->createView();
        return $this->render('book_form.html.twig', [
            'bookFormView' => $bookFormView,
        ]);

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