<?php

namespace App\Controller;

use App\Repository\NotificationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    #[Route('/menu', name: 'app_menu')]
    public function nav(NotificationsRepository $notificationsRepository): Response
    {
        
        
        return $this->render('menu/index.html.twig', [
            
        ]);
    }
}