<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Entity\Offre;
use App\Form\OffreType;
use App\Repository\MessagesRepository;
use App\Repository\NotificationsRepository;
use App\Repository\OffreRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OffreController extends AbstractController
{
    #[Route('/offre', name: 'app_offre')]
    public function index(OffreRepository $offreRepository,MessagesRepository $messagesRepository): Response
    {
        $entrepriseId= $this->getUser() ;
        
        $offres = $offreRepository->findByUser($entrepriseId);
        
        return $this->render('offre/index.html.twig', [
            'offres'=> $offres,
        
            
        ]);
    }



    #[Route('/offre/add', name: 'add_offre')]
    public function add(Request $request, ManagerRegistry $doctrine): Response
    {
       
        $offre = new Offre();
        $user = $this->getUser();
       
        $form = $this->createForm(OffreType::class,$offre);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            
            $manager = $doctrine->getManager();
            $offre->setEntreprise($user);
            $manager->persist($offre);
            $manager->flush();
           
            $this->addFlash("success", "a été ajouté avec succès" );
            return $this->redirectToRoute('app_offre');
            
        }
       
            return $this->render('offre/new.html.twig', [
                'form' => $form->createView(),
                
            ]);
            
    }


    #[Route('/offre/show/{id}', name: 'offre_show')]
    public function show(OffreRepository $offreRepository, int $id): Response
    {
        $offre = $offreRepository->find($id);
        return $this->render('offre/show.html.twig', [
            'offre'=> $offre
        ]);
    }


    #[Route('/offre/delete/{id}', name: 'offre_delete')]
    public function delete(Request $request,ManagerRegistry $doctrine, int $id): Response
    {
        $offre = new Offre();
        $offre = $doctrine->getRepository(Offre::class)->find($id);
   
        if($offre)
        {
            $manager = $doctrine->getManager();
            $manager->remove($offre);
            $manager->flush();
            $this->addFlash("success","Suppression réussi");
            
            return $this->redirectToRoute('app_offre');
           
        }else{
            $this->addFlash('error','Plat inexistant');
        }
    
          return $this->redirectToRoute('app_offre');
    }



    #[Route('offre/edit/{id}', name: 'offre_edit')]
    public function edit(Request $request,ManagerRegistry $doctrine,$id)
    {
        
    $offre = new Offre();
       $offre = $doctrine->getRepository(Offre::class)->find($id);
       $form = $this->createForm(OffreType::class, $offre);
       $form->handleRequest($request);
     
       if($form->isSubmitted() && $form->isValid())
       {
          
           $manager = $doctrine->getManager();
           $manager->persist($offre);
           $manager->flush();
           return $this->redirectToRoute('app_offre');
       }
       return $this->render('offre/edit.html.twig',array(
           'form'=>$form->createView(),
           
       ));
    }



    #[Route('/offre/liste', name: 'liste_offre')]
    public function list(OffreRepository $offreRepository,NotificationsRepository $notificationsRepository): Response
    {
        $id = $this->getUser();
        $offres = $offreRepository->findAll();
        $notifications = $notificationsRepository->findByNotification($id);
        
        return $this->render('offre/liste.html.twig', [
            'offres'=> $offres,
            'notifications'=> $notifications
        ]);
    }

    
    #[Route('/offre/{id}/postuler', name: 'postuler')]
   public function postuler(int $id,OffreRepository $offreRepository, Request $request,ManagerRegistry $doctrine)
    {
    
        $offre = $offreRepository->find($id); 
        $manager = $doctrine->getManager();
        $offre->setValidated("1");
        $manager->persist($offre);
        $manager->flush();

        $message = new Messages();
        $user = $this->getUser();
        $manager = $doctrine->getManager();
        $message->setMessage(" Mr/Mme " . $user->getNom(). " " . $user->getPrenom(). "  à postulé à votre offre de demande de stage");
        $message->setTitre("Message de confirmation");
        $message->setStatut("unread");
        $message->setUser($user);
        $message->setOffre($offre);
        $manager->persist($message);
        $manager->flush();
        
        return $this->redirectToRoute('liste_offre');
    }


  
    
    

    
}