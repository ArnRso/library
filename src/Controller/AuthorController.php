<?php


namespace App\Controller;


use App\Entity\Author;
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
     * @Route("/authors", name="authors")
     * @param AuthorRepository $authorRepository
     * @return Response
     *
     * Affiche tous les auteurs de l'entité Authors
     */
    public function getAuthors(AuthorRepository $authorRepository)
    {
        $authors = $authorRepository->findAll();
        return $this->render('authors.html.twig', ['authors' => $authors]);
    }


    /**
     * @Route("/author/show/{id}", name="author")
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
        return $this->render('author.html.twig', ['author' => $author]);
    }


    /**
     * @Route("/authors/search", name="search_author")
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
        return $this->render('authors.html.twig', ['authors' => $authors, 'get' => $get]);

    }


    /**
     * @Route("/author/new", name="author_new")
     *
     * Permet d'afficher la vue author_form.html.twig
     * avec les champs du formulaire vides
     */
    public function newAuthor()
    {
        return $this->render('author_form.html.twig');
    }


    /**
     * @Route("/author/insert", name="author_insert")
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

        return $this->render('author.html.twig', [
            'author' => $author,
            'message' => "votre auteur a bien été inséré"
        ]);
    }


    /**
     * @Route("/author/edit/{id}", name="author_edit")
     * @param AuthorRepository $authorRepository
     * @param $id
     * @return Response
     *
     * Permet d'appeler la vue author_form.html.twig
     * préremplie avec l'auteur dont l'ID est
     * dans la WildCard de la route
     */
    public function editAuthor(AuthorRepository $authorRepository, $id)
    {
        $author = $authorRepository->find($id);
        return $this->render('author_form.html.twig', ['author' => $author]);
    }


    /**
     * @Route("/author/update", name="author_update")
     * @param AuthorRepository $authorRepository
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     *
     * Peret de prendre les données envoyées via la vue
     * author_form.html.twig (en POST) et utilise les méthodes "setters"
     * de l'entité Book pour modifier les données inscrites en BDD
     */
    public function updateAuthor(AuthorRepository $authorRepository, EntityManagerInterface $entityManager, Request $request)
    {
        $id = $request->request->get('id');
        $name = $request->request->get('name');
        $firstName = $request->request->get('firstName');
        $birthDate = $request->request->get('birthDate');
        $deathDate = $request->request->get('deathDate');

        $author = $authorRepository->find($id);


        $author->setName($name);
        $author->setFirstName($firstName);
        if ($birthDate) {
            $author->setBirthDate(new DateTime($birthDate));
        } else {
            $author->setBirthDate(null);
        }

        if ($deathDate) {
            $author->setDeathDate(new DateTime($deathDate));
        } else {
            $author->setDeathDate(null);
        }

        $entityManager->persist($author);
        $entityManager->flush();
        return $this->redirectToRoute('authors');
    }



    /**
     * @Route("/author/delete/{id}", name="author_delete")
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
        return $this->redirectToRoute('authors');
    }




}
