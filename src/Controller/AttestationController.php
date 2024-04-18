<?php

namespace App\Controller;

use App\Entity\Attestation;
use App\Form\AttestationType;
use App\Repository\AttestationRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AttestationController extends AbstractController
{
    #[Route('/attestation', name: 'app_attestation')]
    public function index(AttestationRepository $attestationRepository): Response
    {
        $etudiantId= $this->getUser() ;
    
        $attestations = $attestationRepository->findByUser($etudiantId);
        return $this->render('attestation/index.html.twig', [
            'attestations'=> $attestations
        ]);
    }



    #[Route('/attestation/add', name: 'add_attestation')]
    public function add(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger,FileUploader $file_uploader): Response
    {
        
        $attestation = new Attestation();
        $user = $this->getUser();
        $form = $this->createForm(AttestationType::class,$attestation);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $contrat_stage = $form->get('contrat_stage')->getData();
            $carte_scolaire = $form->get('carte_scolaire')->getData();
            
            if ($contrat_stage && $carte_scolaire)
            {
                $originalFilename = pathinfo($contrat_stage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'. $contrat_stage->guessExtension();
                $originalFilename1 = pathinfo($carte_scolaire->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename1 = $slugger->slug($originalFilename1);
                $newFilename1 = $safeFilename1.'-'.uniqid().'.'. $carte_scolaire->guessExtension();
                try {
                    $contrat_stage->move(
                        $this->getParameter('users_directory'),
                        $newFilename);
                        $carte_scolaire->move(
                            $this->getParameter('users_directory'),
                            $newFilename1);
                } catch (FileException $e) {
    
                }
                
                $attestation->setContratStage($newFilename);
                $attestation->setCarteScolaire($newFilename1);
            }
              
               
            $manager = $doctrine->getManager();
            $attestation->setEtudiant($user);
            $manager->persist($attestation);
            $manager->flush();
           
            $this->addFlash("error", "a été ajouté avec succès" );
            return $this->redirectToRoute('app_attestation');
            
        }
       
        
        else{
            
            return $this->render('attestation/new.html.twig', [
                'form' => $form->createView(),
                
            ]);
            
        }
        
    }     



    #[Route('/attestation/edit/{id}', name: 'attestation_edit')]
    public function edit(Request $request,ManagerRegistry $doctrine,$id,SluggerInterface $slugger)
    {
        
       $attestation = new Attestation();
       $attestation = $doctrine->getRepository(Attestation::class)->find($id);
       $form = $this->createForm(AttestationType::class, $attestation);
       $form->handleRequest($request);
     
       if($form->isSubmitted() && $form->isValid())
       {
          
        $contrat_stage = $form->get('contrat_stage')->getData();
        $carte_scolaire = $form->get('carte_scolaire')->getData();
        
        if ($contrat_stage && $carte_scolaire)
        {
            $originalFilename = pathinfo($contrat_stage->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'. $contrat_stage->guessExtension();
            $originalFilename1 = pathinfo($carte_scolaire->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename1 = $slugger->slug($originalFilename1);
            $newFilename1 = $safeFilename1.'-'.uniqid().'.'. $carte_scolaire->guessExtension();
            try {
                $contrat_stage->move(
                    $this->getParameter('users_directory'),
                    $newFilename);
                    $carte_scolaire->move(
                        $this->getParameter('users_directory'),
                        $newFilename1);
            } catch (FileException $e) {

            }
            
            $attestation->setContratStage($newFilename);
            $attestation->setCarteScolaire($newFilename1);
        }
           $manager = $doctrine->getManager();
           $manager->persist($attestation);
           $manager->flush();
           return $this->redirectToRoute('app_attestation');
       }
       return $this->render('attestation/edit.html.twig',array(
           'form'=>$form->createView(),
           'attestation' => $attestation->getId()
       ));
    
    }


    #[Route('/attestation/delete/{id}', name: 'attestation_delete')]
    public function delete(Request $request,ManagerRegistry $doctrine, int $id): Response
    {
        $attestation = new Attestation();
        $attestation = $doctrine->getRepository(Attestation::class)->find($id);
   
        if($attestation)
        {
            $manager = $doctrine->getManager();
            $manager->remove($attestation);
            $manager->flush();
            $this->addFlash("success","Suppression réussi");
            
            return $this->redirectToRoute('app_attestation');
           
    
            
        }else{
            $this->addFlash('error','Plat inexistant');
        }
    
          return $this->redirectToRoute('app_attestation');
    }



    #[Route('/attestation/liste', name: 'liste_attestation')]
    public function list(AttestationRepository $attestationRepository): Response
    {
        
        $entrepriseId= $this->getUser() ;
        
        $attestations = $attestationRepository->findByEntreprise($entrepriseId);
        
        return $this->render('attestation/liste.html.twig', [
            'attestations'=> $attestations
        ]);
    }


    #[Route('/attestation/show/{id}', name: 'attestation_show')]
    public function show(Request $request,AttestationRepository $attestationRepository, int $id): Response
    {
        $attestation = $attestationRepository->find($id);
        return $this->render('attestation/show.html.twig', [
            'attestation'=> $attestation
        ]);
    }


    #[Route('/attestation/liste/sow/{id}', name: 'liste_attestation_show')]
    public function listes(AttestationRepository $attestationRepository,int $id): Response
    {
        
        
        
        $attestation = $attestationRepository->find($id);
        return $this->render('attestation/show.html.twig', [
            'attestation'=> $attestation
        ]);
    }









    
}