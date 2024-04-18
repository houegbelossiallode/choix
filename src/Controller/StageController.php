<?php

namespace App\Controller;

use App\Entity\Stage;
use App\Form\StageType;
use App\Repository\StageRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;


use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class StageController extends AbstractController
{
    #[Route('/stage', name: 'app_stage')]
    public function index(Request $request,StageRepository $stageRepository,UserRepository $userRepository): Response
    {
        $etudiantId= $this->getUser() ;
        $users = $userRepository->findAll();
        $stages = $stageRepository->findByUser($etudiantId);
        return $this->render('stage/index.html.twig', [
            'users'=> $users,
            'stages'=> $stages,
            
        ]);
    }


    #[Route('/stage/add', name: 'add_stage')]
    public function add(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        
        $stage = new Stage();
        $user = $this->getUser();
       
        $form = $this->createForm(StageType::class,$stage);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {

            
          
            $cv = $form->get('cv')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($cv)
            {
                $originalFilename = pathinfo($cv->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cv->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $cv->move(
                        $this->getParameter('users_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $stage->setCv($newFilename);
            }
            
            $manager = $doctrine->getManager();
            $stage->setEtudiant($user);
            $manager->persist($stage);
            $manager->flush();
           
            $this->addFlash("error", "a été ajouté avec succès" );
            return $this->redirectToRoute('app_stage');
            
        }
       
        
        else{
            
            return $this->render('stage/new.html.twig', [
                'form' => $form->createView(),
                'stage'=> $stage
            ]);
            
        }
        
    }     
     

    #[Route('/stage/show/{id}', name: 'stage_show')]
    public function show(Request $request,StageRepository $stageRepository, int $id): Response
    {
        $stage = $stageRepository->find($id);
        return $this->render('stage/show.html.twig', [
            'stage'=> $stage
        ]);
    }


    #[Route('/stage/delete/{id}', name: 'stage_delete')]
    public function delete(Request $request,ManagerRegistry $doctrine, int $id): Response
    {
        $stage = new Stage();
        $stage = $doctrine->getRepository(Stage::class)->find($id);
   
        if($stage)
        {
            $manager = $doctrine->getManager();
            $manager->remove($stage);
            $manager->flush();
            $this->addFlash("success","Suppression réussi");
            
            return $this->redirectToRoute('app_stage');
           
        }else{
            $this->addFlash('error','Plat inexistant');
        }
    
          return $this->redirectToRoute('app_stage');
    }




    #[Route('stage/edit/{id}', name: 'stage_edit')]
    public function edit(Request $request,ManagerRegistry $doctrine,$id,SluggerInterface $slugger)
    {
        
        $stage = new Stage();
       $stage = $doctrine->getRepository(Stage::class)->find($id);
       $form = $this->createForm(StageType::class, $stage);
       $form->handleRequest($request);
     
       if($form->isSubmitted() && $form->isValid())
       {
          
        $cv = $form->get('cv')->getData();

        // this condition is needed because the 'brochure' field is not required
        // so the PDF file must be processed only when a file is uploaded
        if ($cv) {
            $originalFilename = pathinfo($cv->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$cv->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $cv->move(
                    $this->getParameter('users_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $stage->setCv($newFilename);
        }
        
           $manager = $doctrine->getManager();
           $manager->persist($stage);
           $manager->flush();
           return $this->redirectToRoute('app_stage');
       }
       return $this->render('stage/edit.html.twig',array(
           'form'=>$form->createView(),
           'plat' => $stage->getId()
       ));

        
    }


  

    
}