<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/authors", name="author_index", methods={"GET"})
     */
    public function allAuthors(UserRepository $userRepository): Response
    {
        $role = 'AUTHOR';
        return $this->render('user/all_authors.html.twig', [
            'users' => $userRepository->findByRole($role),
        ]);
    }

    /**
     * @Route("/new_author", name="author_new", methods={"GET","POST"})
     */
    public function newAuthor(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setPassword(password_hash($user->getPassword(), 1));
            $user->setRoles(['ROLE_AUTHOR']);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('author_index');
        }

        return $this->render('user/new_author.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('author_index');
    }
}
