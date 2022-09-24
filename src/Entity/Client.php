<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length:40)]
    private string $nom;

    #[ORM\Column(length:40)]
    private string $prenom;

    #[ORM\Column(length:40)]
    private string $cni;

    #[ORM\Column(length:70)]
    private string $gmail;

    #[ORM\Column(length:200)]
    private string $adresse;

    #[ORM\Column(length:70)]
    private string $tel;

    #[ORM\Column(length:120)]
    private string $photo;

    // get et set de cni 
    public function getCni():string{
        return $this->cni;
    }
    public function setCni($cni){
        $this->cni=$cni;
    }
    // get et set de nom 
    public function getNom():string{
        return $this->nom;
    }
    public function setNom($nom){
     $this->nom=$nom;
    }
    // get et set de prenom 
    public function getPrenom():string{
        return $this->prenom;
    }
    public function setPrenom($prenom){
     $this->prenom=$prenom;
    }
    // get et set de gmail 
    public function getGmail():string{
        return $this->gmail;
    }
    public function setGmail($gmail){
     $this->gmail=$gmail;
    }
    // get et set de photo 
    public function getPhoto():string{
        return $this->photo;
    }
    public function setPhoto($photo){
     $this->photo=$photo;
    }
    // get et set de adresse 
    public function getAdresse():string{
        return $this->adresse;
    }
    public function setAdresse($adresse){
     $this->adresse=$adresse;
    }
    // get et set de tel 
    public function getTele():string{
        return $this->tel;
    }
    public function setTel($tel){
     $this->tel=$tel;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
