<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\NotificationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class Notification
{
   
public function notifi(NotificationsRepository $notificationsRepository):array
{
    
    $id = $this->getUser();
        
     $notification = $notificationsRepository->findByNotification($id);
     return $notification;
}





    
}