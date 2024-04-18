<?php

namespace App\Service;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function Upload(UploadedFile $file)
    {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $this->slugger->slug($originalFilename);
                $filename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                
                try {
                    $file->move(
                    $this->getTargetDirectory(''), $filename
                    );

            } catch (\Throwable $th) {
                //throw $th;
            }
            return $filename;
    }





public function getTargetDirectory()
{
    return $this->targetDirectory;
}




}