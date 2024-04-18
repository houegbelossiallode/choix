<?php

namespace App\Controller;

use App\Repository\MessagesRepository;
use App\Repository\NotificationsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PartialController extends AbstractController
{
    #[Route('/partial', name: 'app_partial')]
    public function index(UserRepository $userRepository,NotificationsRepository $notificationsRepository): Response
    {
        $id = $this->getUser();
        $listes = $notificationsRepository->findByNotification($id);
    
        return $this->render('partial/navbar.html.twig', [
            'listes'=> $listes,
        ]);
    }


    


    
}