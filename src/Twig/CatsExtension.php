<?php

namespace App\Twig;

use App\Entity\Messages;
use App\Entity\Notifications;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CatsExtension extends AbstractExtension
{
    private $em;
    
    public function __construct(EntityManagerInterface $em, private  Security $security)
    {
        $this->em = $em;
       
        
    }


    public function getFunctions() : array
    {
        return[
          new TwigFunction('cats',[$this,'getNotifications'])  
        ];
    }

        public function getNotifications()
        {
            
        $id = $this->security->getUser();

        return $this->em->getRepository(Notifications::class)->findByNotification($id);
            
        }

    
public function getFunction() : array
{
    return[
      new TwigFunction('cats',[$this,'getMessages'])  
    ];
}


    public function getMessages()
    {
        
    $id = $this->security->getUser();

    return $this->em->getRepository(Messages::class)->findByMessage($id);
        
    }



    public function getFilters()
    {
        return[
          new TwigFunction('cats',[$this,'getNombre'])  
        ];
    }


    
    public function getNombre()
    {
        
    $id = $this->security->getUser();
     
     $count = $this->em->getRepository(Messages::class)->getNb($id);
        
     return $count;
     
    }


    public function getNbNotification()
    {
        return[
          new TwigFunction('cats',[$this,'getNotifi'])  
        ];
    }


    
    public function getNotifi()
    {
        
    $id = $this->security->getUser();
     
     $count = $this->em->getRepository(Notifications::class)->getNb($id);
        
     return $count;
     
    }













    

}







?>