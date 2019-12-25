<?php

namespace App\Controller;

use App\Entity\Admin\Messages;
use App\Entity\Travel;
use App\Form\Admin\MessagesType;
use App\Repository\SettingRepository;
use App\Repository\TravelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SettingRepository $settingRepository, TravelRepository $travelRepository)
    {
        $data = $settingRepository->findBy(['id' => 1]);
        $slider = $travelRepository->findBy([], ['title' => 'ASC'], 6);
        $blogs = $travelRepository->findBy([], ['title' => 'ASC'], 9);

        // dump($slider);
        // die();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'data' => $data,
            'slider' => $slider,
            'blogs' => $blogs,
        ]);
    }


    /**
     * @Route("travel/{id}", name="travel_show", methods={"GET"})
     */
    public function show(Travel $travel): Response
    {
        return $this->render('home/travelshow.html.twig', [
            'travel' => $travel,
        ]);
    }


    /**
     * @Route("aboutus", name="home_about")
     */
    public function aboutus(SettingRepository $settingRepository)
    {
        $setting = $settingRepository->findAll();
        return $this->render('home/aboutus.html.twig', [
            'setting' => $setting,
        ]);
    }


    /**
     * @Route("contact", name="home_contact", methods={"GET","POST"})
     */
    public function contact(SettingRepository $settingRepository, Request $request): Response
    {
        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);
        $submittedToken = $request->request->get('token');
        $setting = $settingRepository->findAll();


        if ($form->isSubmitted()) {

            if ($this->isCsrfTokenValid('form-message', $submittedToken)) {
                $entityManager = $this->getDoctrine()->getManager();

                $message->setStatus('New');
                $message->setIp($_SERVER['REMOTE_ADDR']);

                $entityManager->persist($message);
                $entityManager->flush();

                $this->addFlash('success', 'Your message has been sent successfully');

                //--------------------Send Email------------------//

                $email = (new Email())

                    ->from($setting[0]->getSmtpemail())
                    ->to($form['email']->getData())
                    ->subject('Time for Symfony Mailer!')
                    //->text('Sending emails is fun again!')
                    ->html(
                        "Dear" . $form['name']->getData() . "<br>
                            <p> We will evalute your requests and contact you as soon as possible</p>
                            Thank you <br>
                            ==========================================================
                            <br>" . $setting[0]->getCompany() . "<br>
                            Address:" . $setting[0]->getCompany() . "<br>
                            Phone:" . $setting[0]->getPhone() . "<br>"
                    );
                $transport = new GmailSmtpTransport($setting[0]->getSmtpemail(), $setting[0]->getSmtppassword());
                $mailer = new Mailer($transport);
                $mailer->send($email);

                //--------------------Send Email------------------//


                return $this->redirectToRoute('home_contact');
            }
        }

        return $this->render('home/contact.html.twig', [
            'setting' => $setting,
            'form' => $form->createView(),
        ]);
    }
}
