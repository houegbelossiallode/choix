<?php

namespace App\Controller;

use App\Repository\AttestationRepository;
use App\Repository\MessagesRepository;
use App\Repository\NotificationsRepository;
use App\Repository\OffreRepository;
use App\Repository\StageRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(UserRepository $userRepository,AttestationRepository $attestationRepository
    ,StageRepository $stageRepository,OffreRepository $offreRepository,MessagesRepository $messagesRepository): Response
    {
       
        $user = $userRepository->getNb();
        $attestation = $attestationRepository->getNb();
        $stage = $stageRepository->getNb();
        $offre = $offreRepository->getNb();
        return $this->render('dashboard/index.html.twig', [
            'user'=>$user,
            'attestation'=> $attestation,
            'stage'=> $stage,
            'offre'=> $offre
        ]);
    }




    
}