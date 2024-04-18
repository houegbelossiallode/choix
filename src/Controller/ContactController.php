<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Services\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerService $mailer,ManagerRegistry $doctrine): Response
    {
        
        $contact = new Contact();
        $form = $this->createForm(ContactType::class,$contact);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();
            $nom = $contact->getNom();
            $from = $contact->getEmail();
            $to = $from;
            $subject = 'Demande de contact sur votre site ';
            $message = 'Bonjour  Mr/Mme  ' . $nom . '<br> 
             Votre commentaire sur ISGOSTAGE a été enregistré avec succès.....<br>
            Nous vous remercions de nous contacter à propos de "ISGOSTAGE ".<br>
            Un de nos membres de la satisfaction client vous contactera sous peu.<br>
            Merci de nous contacter!';
            $numero = $contact->getNumero();
            $mailer->sendEmail(subject: $subject, to: $to,content: $message);
            $manager = $doctrine->getManager();
            $manager->persist($contact);
            $manager->flush();
            $this->addFlash('success', 'Votre message a été envoyé');
            return $this->redirectToRoute('app_home');
        }
       
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}