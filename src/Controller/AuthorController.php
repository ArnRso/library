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

class AuthorController extends AbstractController
{
    /**
     * @Route("/authors", name="authors")
     * @param AuthorRepository $authorRepository
     * @return Response
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
     */
    public function getAuthorById(AuthorRepository $authorRepository, $id)
    {
        $author = $authorRepository->find($id);
        return $this->render('author.html.twig', ['author' => $author]);
    }

    /**
     * @Route("/books/search", name="search_author")
     * @param AuthorRepository $authorRepository
     * @param Request $request
     * @return Response
     */
    public function searchInAuthors(AuthorRepository $authorRepository, Request $request)
    {
        /**
         * Ajoute les valeurs du GET dans des variables puis ces variables dans un array, afin de
         * les passer à la vue pour traiter les attributs "Value" dans la vue twig
         */
        $get = [];
        $nameAndOrFirstName = $request->query->get('nameAndOrFirstName');


        $get['nameAndOrFirstName'] = $nameAndOrFirstName;

        $authors = $authorRepository->customSearch($nameAndOrFirstName);
        return $this->render('authors.html.twig', ['authors' => $authors, 'get' => $get]);

    }

    /**
     * @Route("/author/insert", name="author_insert")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return Response
     * @throws Exception
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
     * @Route("/author/new", name="author_new")
     */
    public function newAuthor()
    {
        return $this->render('author_form.html.twig');
    }

    /**
     * @Route("/author/delete/{id}", name="author_delete")
     * @param EntityManagerInterface $entityManager
     * @param AuthorRepository $authorRepository
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAuthor(EntityManagerInterface $entityManager, AuthorRepository $authorRepository, $id)
    {
        $author = $authorRepository->find($id);
        $entityManager->remove($author);
        $entityManager->flush();
        return $this->redirectToRoute('authors');
    }
}
