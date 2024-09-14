<?php

namespace App\Controller;

use App\Entity\User;

use App\Form\EntrepriseType;
use App\Form\EtudiantType;
use App\Form\RegistrationFormType;
use App\Form\VerificationFormType;
use App\Form\UniversiteType;
use App\Repository\UserRepository;
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
    public function select(Request $request): Response
    {
       

   

        return $this->render('registration/select.html.twig' );
    }

   
    #[Route('/register/etudiant', name: 'register_etudiant')]
    public function etudiant(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
    {
        $user = new User();
        $form = $this->createForm(EtudiantType::class, $user);  
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
                     //Generer code de vérification
                     $verificationCode = random_int(100000,999999);
                     $user->setVerificationCode($verificationCode);
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
            ->context([
                'verificationCode'=> $verificationCode
               ])
         );
         // do anything else you need here, like send an email
                     
         return $this->redirectToRoute('app_verify_code');
                    
                }
                

                return $this->render('registration/register_etudiant.html.twig', [
                    'form'=> $form->createView()
                ]); 

                
            }



            #[Route('/register/entreprise', name: 'register_entreprise')]
            public function entreprise(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
            {
                $user = new User();
                $form = $this->createForm(EntrepriseType::class, $user);
               
                
            
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
                             //Generer code de vérification
                             $verificationCode = random_int(100000,999999);
                             $user->setVerificationCode($verificationCode);
                            $user->setType('Entreprise');
                            $entityManager->persist($user);
                            $entityManager->flush();
        
        
        
                            // generate a signed url and email it to the user
                      $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                     ( new TemplatedEmail())
                   ->from(new Address('houegbelossiallode@gmail.com', 'Stage'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                    ->context([
                        'verificationCode'=> $verificationCode
                       ])
                 );
                 // do anything else you need here, like send an email
                             
                 return $this->redirectToRoute('app_verify_code');
                            
                        }
                        
        
                        return $this->render('registration/register_entreprise.html.twig', [
                            'form'=> $form->createView()
                        ]); 
        
                        
                    }



                    #[Route('/register/universite', name: 'register_universite')]
                    public function universite(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,SluggerInterface $slugger): Response
                    {
                        $user = new User();
                        $form = $this->createForm(UniversiteType::class, $user);
                        
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
                                    
                                    //Generer code de vérification
                                    $verificationCode = random_int(100000,999999);
                                    $user->setVerificationCode($verificationCode);
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
                            ->context([
                                'verificationCode'=> $verificationCode
                               ])
                         );
                         // do anything else you need here, like send an email
                                     
                                    return $this->redirectToRoute('app_verify_code');
                                    
                                }
                                
                
                                return $this->render('registration/register_universite.html.twig', [
                                    'form'=> $form->createView()
                                ]); 
                
                                
                            }

                            #[Route('/verify-code', name: 'app_verify_code')]
                            public function verify(Request $request, UserRepository   $userRepository, EntityManagerInterface $entityManager): Response
                        {
                            $form = $this->createForm(VerificationFormType::class);
                            $form->handleRequest($request);

                            if ($form->isSubmitted() && $form->isValid()) {
                                $verificationCode = $form->get('VerificationCode')->getData();
                                $user = $userRepository->findOneBy(['VerificationCode' => $verificationCode]);

                                if ($user) {
                                    // Verify user
                                    $user->setIsVerified(true);
                                    $user->setVerificationCode(null);
                                    $entityManager->flush();

                                    // Redirect to login page
                                    $this->addFlash('success', 'Vôtre compte a été vérifié avec succès');
                                    return $this->redirectToRoute('app_login');
                                } else {
                                    $this->addFlash('error', 'Invalid verification code');
                                }
                            }

                            return $this->render('registration/verify.html.twig', [
                                'form' => $form->createView(),
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