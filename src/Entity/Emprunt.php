<?php

namespace App\Entity;

use App\Repository\EmpruntRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmpruntRepository::class)]
class Emprunt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
 
    #[ORM\ManyToOne(targetEntity:Client::class,inversedBy:'clients')]
    private $client;
    #[ORM\ManyToOne(targetEntity:Materiel::class,inversedBy:'materiels')]
    private $materiel;
    #[ORM\Column(length:40)]
    private string $dateEmprunt;
    #[ORM\Column(length:40)]
    private string $dateReteur;
    #[ORM\Column]
    private int $montantTotale;
   //geters and seters for client 
    public function setClient(?Client $client){
        $this->client=$client;
    }
    public function getClient():?Client{
        return $this->client;
    }
   //geters and seters for materil 
   public function setMateriel(?Materiel $materiel){
    $this->materiel=$materiel;
   }
   public function getMateriel():?Materiel{
    return $this->materiel;
   }
   //geters and seters for dateEmprunt 
   public function setDateEmprunt(?string $dateEmprunt){
    $this->dateEmprunt=$dateEmprunt;
   }
   public function getDateEmprunt():?string{
    return $this->dateEmprunt;
   }
   //geters and seters for dateReteur 
   public function setDateReteur(?string $dateReteur){
     $this->dateReteur=$dateReteur;
   }
   public function getDateReteur():?string{
    return $this->dateReteur;
   }
   //geters and seters for montantTotale 
   public function setMontantTotale(?int $montantTotale){
    $this->montantTotale=$montantTotale;
   }
   public function getMontantTotale():?int{
    return $this->montantTotale;
   }
    public function getId(): ?int
    {
        return $this->id;
    }
}
