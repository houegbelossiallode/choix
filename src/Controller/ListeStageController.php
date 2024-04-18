<?php

namespace App\Controller;

use App\Entity\Notifications;
use App\Entity\Stage;
use App\Repository\MessagesRepository;
use App\Repository\StageRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\Address;
class ListeStageController extends AbstractController
{



 private EmailVerifier $emailVerifier;
    
    
    
    public function __construct(EmailVerifier $emailVerifier,private VerifyEmailHelperInterface $verifyEmailHelper,
    private MailerInterface $mailer,private EntityManagerInterface $entityManager
    )
    {
        $this->emailVerifier = $emailVerifier;
    }





    #[Route('/liste/stage', name: 'app_liste_stage')]
    public function index(StageRepository $stageRepository): Response
    {
        $entrepriseId= $this->getUser() ;
        $stages = $stageRepository->findByEntreprise($entrepriseId);
        
        return $this->render('liste_stage/index.html.twig', [
            'stages'=> $stages
        ]);
    }


    #[Route('/liste/stage/{id}', name: 'valider')]
    public function valider(StageRepository $stageRepository,int $id,ManagerRegistry $doctrine): Response
    {
        $stage = $stageRepository->find($id); 
        $manager = $doctrine->getManager();
        $stage->setValidated("1");
        $manager->persist($stage);
        $manager->flush();

        $message = new  Notifications();
        $user = $this->getUser();
        $manager = $doctrine->getManager();
        $message->setMessage($user->getNom(). " a validé vôtre demande de stage ");
        $message->setTitre("Message de confirmation");
        $message->setStatut("unread");
        $message->setUser($user);
        $message->setStage($stage);
        $manager->persist($message);
        $manager->flush();
        

          // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('houegbelossiallodegoldfroy@gmail.com', 'Goldfroy'))
                    ->to($user->getEmail())
                    ->subject('Validation')
                    ->htmlTemplate('registration/valide.html.twig')
                    
            );



        return $this->redirectToRoute('app_liste_stage');
    }

    #[Route('/liste/{id}/reject', name: 'reject')]
   public function reject(int $id,ManagerRegistry $doctrine,StageRepository $stageRepository,Request $request)
   {
    
        $ids = $stageRepository->find($id);     
        $manager = $doctrine->getManager();
        $mot = $request->get('rejet');
        $ids->setReason($mot);
        $manager->persist($ids);
        $manager->flush();
        
        $notif = new Notifications(); 
        
        $user = $this->getUser();
        
        $manager = $doctrine->getManager();
        $notif->setMessage($user->getNom(). " a rejeté vôtre demande de stage ");
        $notif->setTitre("Message de rejet");
        $notif->setStatut("unread");
        $notif->setUser($user);
        $notif->setStage($ids);
        $manager->persist($notif);
        $manager->flush();
        return $this->redirectToRoute('app_liste_stage');
        
        
    
    }
    
}