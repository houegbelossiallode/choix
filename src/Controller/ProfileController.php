<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(UserRepository $userRepository): Response
    {
        $user = new User();
        $id = $this->getUser();
       
        $profiles = $userRepository->findBy(['id'=>$id]);
        return $this->render('profile/index.html.twig', [
            'profiles' => $profiles,
        ]);
    }


    #[Route('profile/edit/{id}', name: 'profile_edit')]
    public function edit(Request $request,ManagerRegistry $doctrine,$id,SluggerInterface $slugger)
    {
        
       $user = new User();
       $user = $doctrine->getRepository(User::class)->find($id);
       $form = $this->createForm(RegistrationFormType::class, $user);
       
       $form->handleRequest($request);
     
       if($form->isSubmitted() && $form->isValid())
       {
          
        $image = $form->get('image')->getData();

        // this condition is needed because the 'brochure' field is not required
        // so the PDF file must be processed only when a file is uploaded
        if ($image) {
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
        
           $manager = $doctrine->getManager();
           $manager->persist($user);
           $manager->flush();
           return $this->redirectToRoute('app_profile');
       }
       return $this->render('profile/edit.html.twig',array(
           'form'=>$form->createView(),
           
       ));

        
    }


    
    
}