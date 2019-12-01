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
 * @Route("/admin/author", name="admin_author_")
 * Controlleur des Auteurs
 */
class AuthorController extends AbstractController
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
        return $this->render('admin/author/authors.html.twig', ['authors' => $authors]);
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
        return $this->render('admin/author/author.html.twig', ['author' => $author]);
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
        return $this->render('admin/author/authors.html.twig', ['authors' => $authors, 'get' => $get]);

    }


    /**
     * @Route("/new", name="new")
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
            $this->addFlash('success', 'Auteur créé avec succès');
            return $this->redirectToRoute('admin_author_all');
        }
        $authorFormView = $authorForm->createView();
        return $this->render('admin/author/author_form.html.twig', [
            'authorFormView' => $authorFormView,
        ]);
    }



    /**
     * @Route("/edit/{id}", name="edit")
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
            $this->addFlash('success', 'Auteur édité avec succès');
            return $this->redirectToRoute('admin_author_all');
        }
        $authorFormView = $authorForm->createView();
        return $this->render('admin/author/author_form.html.twig', [
            'authorFormView' => $authorFormView,
        ]);

    }


    /**
     * @Route("/delete/{id}", name="delete")
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
        $this->addFlash('success', 'Auteur supprimé avec succès');
        return $this->redirectToRoute('admin_author_all');
    }
}
