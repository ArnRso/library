<?php


namespace App\Controller\admin;


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
 * @Route("/admin/book", name="admin_book_")
 */
class BookController extends AbstractController
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
        return $this->render('admin/book/books.html.twig', ['books' => $books]);
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
        return $this->render('admin/book/book.html.twig', ['book' => $book]);
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
        return $this->render('admin/book/books.html.twig', ['books' => $books, 'get' => $get]);
    }


    /**
     * @Route("/new", name="new")
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
            return $this->redirectToRoute('admin_book_all');
        }
        $bookFormView = $bookForm->createView();
        return $this->render('admin/book/book_form.html.twig', [
            'bookFormView' => $bookFormView,
        ]);
    }


    /**
     * @Route("/edit/{id}", name="edit")
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
            return $this->redirectToRoute('admin_book_all');
        }
        $bookFormView = $bookForm->createView();
        return $this->render('admin/book/book_form.html.twig', [
            'bookFormView' => $bookFormView,
        ]);

    }


    /**
     * @Route("/delete/{id}", name="delete")
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
        return $this->redirectToRoute('admin_book_all');
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
        return $this->render('admin/book/books.html.twig', ['books' => $books]);
    }
}