<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/cost", name="cost")
     */
    public function cost()
    {
        return $this->render('home/cost.html.twig');
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contactAction(Request $request, \Swift_Mailer $mailer)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        if($request->getMethod() === "POST") {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {


                $em = $this->getDoctrine()->getManager();
                $contact->setCreatedAt(new \DateTime('now'));
                $em->persist($contact);
                $em->flush($contact);

                $message =  (new \Swift_Message('Nouveau message de la villadésirée.com'))
                    ->setFrom('villadesiree.guadeloupe@gmail.com')
                    ->setTo('villadesiree.guadeloupe@gmail.com')
                    ->setCC('bielawski.ov@gmail.com')
                    ->setBcc('zimzim62000@gmail.com')
                    ->setBody(
                        $this->renderView(
                            'home/newmessage.html.twig',
                            array(
                                'name' => $contact->getName(),
                                'email' => $contact->getEmail(),
                                'message' => $contact->getMessage()
                            )
                        ),
                        'text/html'
                    )
                ;

                $mailer->send($message);


                return $this->render('home/contact.html.twig', array(
                    'success' => true,
                    'contact' => $contact,
                    'form' => $form->createView()
                ));
            }
        }

        return $this->render('home/contact.html.twig', array(
            'contact' => $contact,
            'form' => $form->createView(),
        ));

    }
}
