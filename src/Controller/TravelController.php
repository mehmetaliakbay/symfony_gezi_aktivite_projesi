<?php

namespace App\Controller;

use App\Entity\Travel;
use App\Form\Travel1Type;
use App\Repository\TravelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("user/travel")
 */
class TravelController extends AbstractController
{
    /**
     * @Route("/", name="user_travel_index", methods={"GET"})
     */
    public function index(TravelRepository $travelRepository): Response
    {
        $user = $this->getUser(); //Get login user data
        return $this->render('travel/index.html.twig', [
            'travels' => $travelRepository->findBy(['userid' => $user->getId()]),
        ]);
    }

    /**
     * @Route("/new", name="user_travel_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $travel = new Travel();
        $form = $this->createForm(Travel1Type::class, $travel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $user = $this->getUser(); //Get login user data
            $travel->setUserid($user->getId());

            $travel->setStatus("new"); // Kullanici yeni bir blog eklediginde statusu new olacak

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
                $travel->setImage($fileName);
            }
            //------------------Image Upload--------------//

            $entityManager->persist($travel);
            $entityManager->flush();

            return $this->redirectToRoute('user_travel_index');
        }

        return $this->render('travel/new.html.twig', [
            'travel' => $travel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_travel_show", methods={"GET"})
     */
    public function show(Travel $travel): Response
    {
        return $this->render('travel/show.html.twig', [
            'travel' => $travel,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_travel_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Travel $travel): Response
    {
        $form = $this->createForm(Travel1Type::class, $travel);
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
                $travel->setImage($fileName);
            }
            //------------------Image Upload--------------//

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_travel_index');
        }

        return $this->render('travel/edit.html.twig', [
            'travel' => $travel,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_travel_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Travel $travel): Response
    {
        if ($this->isCsrfTokenValid('delete' . $travel->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($travel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_travel_index');
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
}