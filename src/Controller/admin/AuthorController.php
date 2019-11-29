<?php


namespace App\Controller\admin;


use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controlleur des Auteurs
 */
class AuthorController extends AbstractController
{
    /**
     * @Route("/admin/authors", name="admin_authors")
     * @param AuthorRepository $authorRepository
     * @return Response
     *
     * Affiche tous les auteurs de l'entité Authors
     */
    public function getAuthors(AuthorRepository $authorRepository)
    {
        $authors = $authorRepository->findAll();
        return $this->render('admin/author/authors.html.twig', ['authors' => $authors]);
    }


    /**
     * @Route("/admin/author/show/{id}", name="admin_author")
     * @param AuthorRepository $authorRepository
     * @param $id
     * @return Response
     *
     * Affiche un seul auteur de l'entité Authors,
     * celui dont l'ID est précisé dans la WildCard
     */
    public function getAuthorById(AuthorRepository $authorRepository, $id)
    {
        $author = $authorRepository->find($id);
        return $this->render('admin/author/author.html.twig', ['author' => $author]);
    }


    /**
     * @Route("/admin/authors/search", name="admin_search_author")
     * @param AuthorRepository $authorRepository
     * @param Request $request
     * @return Response
     *
     * Affiche les auteurs qui correspondent aux données transmises via le formulaire de recherche (en GET)
     */
    public function searchInAuthors(AuthorRepository $authorRepository, Request $request)
    {
        $get = [];
        $nameAndOrFirstName = $request->query->get('nameAndOrFirstName');
        $get['nameAndOrFirstName'] = $nameAndOrFirstName;

        $authors = $authorRepository->customSearch($nameAndOrFirstName);
        return $this->render('admin/author/authors.html.twig', ['authors' => $authors, 'get' => $get]);

    }


    /**
     * @Route("/admin/author/new", name="admin_author_new")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function newAuthor(Request $request, EntityManagerInterface $entityManager)
    {
        $author = new Author;
        $authorForm = $this->createForm(AuthorType::class, $author);
        $authorForm->handleRequest($request);
        if ($authorForm->isSubmitted() && $authorForm->isValid()) {
            $author = $authorForm->getData();
            $entityManager->persist($author);
            $entityManager->flush();
            return $this->redirectToRoute('admin_authors');
        }
        $authorFormView = $authorForm->createView();
        return $this->render('admin/author/author_form.html.twig', [
            'authorFormView' => $authorFormView,
        ]);
    }

    /**
     * @Route("/admin/author/insert", name="admin_author_insert")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     * @throws Exception
     *
     * Insert un auteur dans la BDD avec les informations fournies
     * via le formulaire de la vue book_form.html.twig (en POST)
     */
    public function insertAuthor(EntityManagerInterface $entityManager, Request $request)
    {
        $name = $request->request->get('name');
        $firstName = $request->request->get('firstName');
        $birthDate = $request->request->get('birthDate');
        $deathDate = $request->request->get('deathDate');
        $author = new Author();
        $author->setName($name)
            ->setFirstName($firstName)
            ->setBirthDate(new DateTime($birthDate))
            ->setDeathDate(new DateTime($deathDate));

        $entityManager->persist($author);
        $entityManager->flush();

        return $this->render('admin/author/author.html.twig', [
            'author' => $author,
            'message' => "votre auteur a bien été inséré"
        ]);
    }


    /**
     * @Route("/admin/author/edit/{id}", name="admin_author_edit")
     * @param $id
     * @param Request $request
     * @param AuthorRepository $authorRepository
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse|Response
     */
    public function editAuthor($id, Request $request, AuthorRepository $authorRepository, EntityManagerInterface $entityManager)
    {
        $author = $authorRepository->find($id);
        $authorForm = $this->createForm(AuthorType::class, $author);
        $authorForm->handleRequest($request);
        if ($authorForm->isSubmitted() && $authorForm->isValid()) {
            $author = $authorForm->getData();
            $entityManager->persist($author);
            $entityManager->flush();
            return $this->redirectToRoute('admin_authors');
        }
        $authorFormView = $authorForm->createView();
        return $this->render('admin/author/author_form.html.twig', [
            'authorFormView' => $authorFormView,
        ]);

    }


    /**
     * @Route("/admin/author/delete/{id}", name="admin_author_delete")
     * @param EntityManagerInterface $entityManager
     * @param AuthorRepository $authorRepository
     * @param $id
     * @return RedirectResponse
     *
     * Supprime l'auteur de la BDD dont l'ID est indiqué dans la WildCard
     */
    public function deleteAuthor(EntityManagerInterface $entityManager, AuthorRepository $authorRepository, $id)
    {
        $author = $authorRepository->find($id);
        $entityManager->remove($author);
        $entityManager->flush();
        return $this->redirectToRoute('admin_authors');
    }
}
