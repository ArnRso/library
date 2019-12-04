<?php


namespace App\Controller;


use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfileController
 * @package App\Controller
 * @Route("/profile", name="profile_")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/show", name="show")
     */
    public function profileShow()
    {
        $user = $this->getUser();
        return $this->render('profile/show.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/edit", name="edit")
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function profileEdit(UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request)
    {
        $user = $this->getUser();
        $userForm = $this->createForm(UserType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user = $userForm->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Auteur créé avec succès');
            return $this->redirectToRoute('profile_show');
        }
        return $this->render('profile/user_form.html.twig', [
            'form' => $userForm->createView(),
        ]);
    }

    /**
     * @Route("/delete", name="delete")
     * @param EntityManagerInterface $entityManager
     * @return RedirectResponse
     */
    public function profileDelete(EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('index');
    }
}