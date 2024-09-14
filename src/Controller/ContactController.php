<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\UserRepository;
use App\Services\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\String\Slugger\SluggerInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer,ManagerRegistry $doctrine,SluggerInterface $slugger): Response
    {
       // $user = $userRepository->findBy(['email'=>'ASC']);
        $contact = new Contact();
        $form = $this->createForm(ContactType::class,$contact);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
          $image = $form->get('image')->getData();

          if ($image) 
          {
              $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
              // this is needed to safely include the file name as part of the URL
              $safeFilename = $slugger->slug($originalFilename);
              $newFilename = $safeFilename.'-'.'.'.$image->guessExtension();

              // Move the file to the directory where brochures are stored
              try {
                  $image->move(
                      $this->getParameter('users_directory'),
                      $newFilename
                  );
              } catch (FileException $e) {
                 $this->addFlash('success', 'Une erreur est survenue lors du téléchargement du fichier');
              }
              $contact->setImage($newFilename);
              // updates the 'brochureFilename' property to store the PDF file name
              // instead of its contents
              $pdfPath = $this->getParameter('users_directory').'/'.$newFilename;
              
              }
              
        $email = (new Email())
       ->from('houegbelossiallode@gmail.com')
       ->to($contact->getEmail())
       ->subject('Demande de contact')
       ->text('commentaire');
       if(isset($pdfPath)){
        $email->attachFromPath($pdfPath);
        //dd($pdfPath);
       }
       $manager = $doctrine->getManager();
       $manager->persist($contact);
       $manager->flush();
       $mailer->send($email);
       $this->addFlash('success', 'Votre message a été envoyé');
      return $this->redirectToRoute('app_contact');
       }
       
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}