<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('houegbelossiallode@gmail.com', 'Stage'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

   

    #[Route('/register/etudiant', name: 'app_etudiant')]
    public function etudiant(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        
        $form->remove('num_enregistrement');
        $form->remove('ifu');
        $form->remove('rccm');
    
        $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) 
            {
                
                $image = $form->get('image')->getData();
                
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($image) 
            {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $image->move(
                        $this->getParameter('users_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setImage($newFilename);
                
                }
                
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    );
                    $user->setType('Etudiant');
                    $entityManager->persist($user);
                    $entityManager->flush();



                    // generate a signed url and email it to the user
              $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
             (new TemplatedEmail())
            ->from(new Address('houegbelossiallode@gmail.com', 'Stage'))
            ->to($user->getEmail())
            ->subject('Please Confirm your Email')
            ->htmlTemplate('registration/confirmation_email.html.twig')
         );
         // do anything else you need here, like send an email
                     
                    return $this->redirectToRoute('app_login');
                    
                }
                

                return $this->render('registration/register_etudiant.html.twig', [
                    'registrationForm'=> $form->createView()
                ]); 

                
            }



            #[Route('/register/entreprise', name: 'app_entreprise')]
            public function entreprise(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
            {
                $user = new User();
                $form = $this->createForm(RegistrationFormType::class, $user);
                
                $form->remove('num_enregistrement');
                $form->remove('prenom');
                $form->remove('sexe');
                $form->remove('filiere');
                $form->remove('date_naissance');
                $form->remove('ecole');
                
            
                $form->handleRequest($request);
                
                    if ($form->isSubmitted() && $form->isValid()) 
                    {
                        
                        $image = $form->get('image')->getData();
                        $ifu = $form->get('ifu')->getData();
                        $rccm = $form->get('rccm')->getData();
                        
                    // this condition is needed because the 'brochure' field is not required
                    // so the PDF file must be processed only when a file is uploaded
                    if ($image && $ifu && $rccm) 
                    {
                        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                        $safeFilename = $slugger->slug($originalFilename);
                        $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
                        
                        $originalFilename1 = pathinfo($ifu->getClientOriginalName(), PATHINFO_FILENAME);
                        $safeFilename1 = $slugger->slug($originalFilename1);
                        $newFilename1 = $safeFilename1.'-'.uniqid().'.'.$ifu->guessExtension();

                        $originalFilename2 = pathinfo($rccm->getClientOriginalName(), PATHINFO_FILENAME);
                        $safeFilename2 = $slugger->slug($originalFilename2);
                        $newFilename2 = $safeFilename2.'-'.uniqid().'.'.$rccm->guessExtension();
        
                        // Move the file to the directory where brochures are stored
                        try {
                            $image->move(
                                $this->getParameter('users_directory'),
                                $newFilename
                            );
                            $ifu->move(
                                $this->getParameter('users_directory'),
                                $newFilename1
                            );
                            $rccm->move(
                                $this->getParameter('users_directory'),
                                $newFilename2
                            );
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }
        
                        // updates the 'brochureFilename' property to store the PDF file name
                        // instead of its contents
                        $user->setImage($newFilename);
                        $user->setIfu($newFilename1);
                        $user->setRccm($newFilename2);
                        
                        }
                        
                            $user->setPassword(
                                $userPasswordHasher->hashPassword(
                                    $user,
                                    $form->get('plainPassword')->getData()
                                )
                            );
                            $user->setType('Entreprise');
                            $entityManager->persist($user);
                            $entityManager->flush();
        
        
        
                            // generate a signed url and email it to the user
                      $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                     (new TemplatedEmail())
                    ->from(new Address('houegbelossiallode@gmail.com', 'Stage'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                 );
                 // do anything else you need here, like send an email
                             
                            return $this->redirectToRoute('app_login');
                            
                        }
                        
        
                        return $this->render('registration/register_entreprise.html.twig', [
                            'registrationForm'=> $form->createView()
                        ]); 
        
                        
                    }



                    #[Route('/register/universite', name: 'app_universite')]
                    public function universite(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
                    {
                        $user = new User();
                        $form = $this->createForm(RegistrationFormType::class, $user);
                        
                            $form->remove('ifu');
                            $form->remove('prenom');
                            $form->remove('sexe');
                            $form->remove('filiere');
                            $form->remove('date_naissance');
                            $form->remove('ecole');
                        
                        
                    
                        $form->handleRequest($request);
                        
                            if ($form->isSubmitted() && $form->isValid()) 
                            {
                                
                                $image = $form->get('image')->getData();
                                
                                $rccm = $form->get('rccm')->getData();
                                
                            // this condition is needed because the 'brochure' field is not required
                            // so the PDF file must be processed only when a file is uploaded
                            if ($image  && $rccm) 
                            {
                                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                                $safeFilename = $slugger->slug($originalFilename);
                                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
                                
                               
        
                                $originalFilename2 = pathinfo($rccm->getClientOriginalName(), PATHINFO_FILENAME);
                                $safeFilename2 = $slugger->slug($originalFilename2);
                                $newFilename2 = $safeFilename2.'-'.uniqid().'.'.$rccm->guessExtension();
                
                                // Move the file to the directory where brochures are stored
                                try {
                                    $image->move(
                                        $this->getParameter('users_directory'),
                                        $newFilename
                                    );
                                   
                                    $rccm->move(
                                        $this->getParameter('users_directory'),
                                        $newFilename2
                                    );
                                } catch (FileException $e) {
                                    // ... handle exception if something happens during file upload
                                }
                
                                // updates the 'brochureFilename' property to store the PDF file name
                                // instead of its contents
                                $user->setImage($newFilename);
                                
                                $user->setRccm($newFilename2);
                                
                                }
                                
                                    $user->setPassword(
                                        $userPasswordHasher->hashPassword(
                                            $user,
                                            $form->get('plainPassword')->getData()
                                        )
                                    );
                                    $user->setType('Universite');
                                    $entityManager->persist($user);
                                    $entityManager->flush();
                
                
                
                                    // generate a signed url and email it to the user
                              $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                             (new TemplatedEmail())
                            ->from(new Address('houegbelossiallode@gmail.com', 'Stage'))
                            ->to($user->getEmail())
                            ->subject('Please Confirm your Email')
                            ->htmlTemplate('registration/confirmation_email.html.twig')
                         );
                         // do anything else you need here, like send an email
                                     
                                    return $this->redirectToRoute('app_login');
                                    
                                }
                                
                
                                return $this->render('registration/register_universite.html.twig', [
                                    'registrationForm'=> $form->createView()
                                ]); 
                
                                
                            }






            



            #[Route('/verify/email', name: 'app_verify_email')]
            public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
            {
                $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
                // validate email confirmation link, sets User::isVerified=true and persists
                try {
                    $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
                } catch (VerifyEmailExceptionInterface $exception) {
                    $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
        
                    return $this->redirectToRoute('app_dashboard');
                }
        
                // @TODO Change the redirect on success and handle or remove the flash message in your templates
                $this->addFlash('success', 'Your email address has been verified.');
        
                return $this->redirectToRoute('app_dashboard');
            }




            




    
}