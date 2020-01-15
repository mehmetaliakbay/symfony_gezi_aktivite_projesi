<?php

namespace App\Controller;

use App\Entity\Admin\Comment;
use App\Entity\User;
use App\Form\Admin\CommentType;
use App\Form\UserType;
use App\Form\User1Type;
use App\Repository\Admin\CommentRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('user/show.html.twig');
    }

    /**
     * @Route("/comments", name="user_comments", methods={"GET"})
     */
    public function comments(CommentRepository $commentRepository): Response
    {
        $user = $this->getUser();
        $comments = $commentRepository->getAllCommentsUser($user->getId());
        return $this->render('user/comments.html.twig',[
            'comments' => $comments,
        ]);
    }


    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            //------------------Image Upload--------------//
            /** @var file $flie */
            $file = $form['image']->getData();
            if ($file) {
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();


                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('images_directory'), // in Service.yaml defined images directory
                        $fileName
                    );
                } catch (FileException $e) {
                    //..handle exception if something happens during file upload
                }
                $user->setImage($fileName);
            }
            //------------------Image Upload--------------//
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, $id, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        if ($user->getId() != $id) {
            return $this->redirectToRoute('home');
        }



        $form = $this->createForm(User1Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //------------------Image Upload--------------//
            /** @var file $flie */
            $file = $form['image']->getData();
            if ($file) {
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();


                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->getParameter('images_directory'), // in Service.yaml defined images directory
                        $fileName
                    );
                } catch (FileException $e) {
                    //..handle exception if something happens during file upload
                }
                $user->setImage($fileName);
            }
            //------------------Image Upload--------------//

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }



    /**
     * @Route("/newcomment/{id}", name="user_new_comment", methods={"GET","POST"})
     */
    public function newComment(Request $request, $id): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $submittedToken = $request->request->get('token');

        if ($form->isSubmitted() && $this->isCsrfTokenValid('comment', $submittedToken)) {


            $entityManager = $this->getDoctrine()->getManager();

            $comment->setStatus('New');
            $comment->setIp($_SERVER['REMOTE_ADDR']);
            $comment->setTravelid($id);
            $user = $this->getUser();
            $comment->setUserid($user->getId());


            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Your comment has been sent successfuly');

            return $this->redirectToRoute('travel_show', ['id' => $id]);
        }

        return $this->redirectToRoute('travel_show', ['id' => $id]);
    }
}
