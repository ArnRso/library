<?php


namespace App\Controller;


use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/author/{id}", name="author")
     * @param AuthorRepository $authorRepository
     * @param $id
     * @return Response
     */
    public function getAuthorById(AuthorRepository $authorRepository, $id)
    {
        $author = $authorRepository->findOneBy(['id' => $id]);
        return $this->render('author.html.twig', ['author' => $author]);
    }
}
