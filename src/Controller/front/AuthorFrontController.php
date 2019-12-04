<?php


namespace App\Controller\front;


use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/author", name="author_")
 * Controlleur des Auteurs
 */
class AuthorFrontController extends AbstractController
{
    /**
     * @Route("s", name="all")
     * @param AuthorRepository $authorRepository
     * @return Response
     *
     * Affiche tous les auteurs de l'entité Authors
     */
    public function getAuthors(AuthorRepository $authorRepository)
    {
        $authors = $authorRepository->findAll();
        return $this->render('author/authors.html.twig', ['authors' => $authors]);
    }


    /**
     * @Route("/show/{id}", name="show")
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
        return $this->render('author/author.html.twig', ['author' => $author]);
    }

    /**
     * @Route("/search", name="search")
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
        return $this->render('author/authors.html.twig', ['authors' => $authors, 'get' => $get]);

    }
}