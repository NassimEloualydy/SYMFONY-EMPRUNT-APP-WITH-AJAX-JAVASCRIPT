<?php

namespace App\Entity;

use App\Repository\DegatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DegatRepository::class)]
class Degat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\ManyToOne(targetEntity:Emprunt::class,inversedBy:'emprunts')]
    private $emprunt;
    
    #[ORM\Column(length:300)]
    private ?String $description;

    #[ORM\Column]
    private ?int  $coutEstimer;

    #[ORM\Column(length:40)]
    private ?String $dateDegat;
    
    //  geters and seters of emprunt
    public function getEmprunt():?Emprunt{
        return $this->emprunt;
    }
    public function setEmprunt(?Emprunt $emprunt){
        $this->emprunt=$emprunt;
    }
    //  geters and seters of description
    public function setDescription(?String $description){
        $this->description=$description;
    }
    public function getDescription():?String{
        return $this->description;
    }
    //  geters and seters of coutEstimer
    public function setCoutEstimer(?String $coutEstimer){
        $this->coutEstimer=$coutEstimer;
    }
    public function getCoutEstimer():?String{
      return $this->coutEstimer;
    }
    //  geters and seters of dateDegat
    public function setDateDegat(?String $dateDegat){
        $this->dateDegat=$dateDegat;
    }
    public function getDateDegat():?String{
        return $this->dateDegat;
    }
    public function getId(): ?int
    {
        return $this->id;
    }
}
