<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
     
    #[ORM\Column(length:100)]
    private string $path;
    
    #[ORM\Column(length:100)]
    private string $nom;
    
    #[ORM\Column(length:100)]
    private string $prenom;
    
    #[ORM\Column(length:100)]
    private string $email;
    
    #[ORM\Column(length:100)]
    private string $pw;
    
    public function getPath():?string{
        return $this->path;
    }

    public function getNom():?string{
        return $this->nom;
    }

    public function getPrenom():?string{
        return $this->prenom;
    }

    public function getEmail():?string{
        return $this->email;
    }

    public function getPw():?string{
        return $this->pw;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setNom($nom){
        $this->nom=$nom;
    }
    public function setPath($path){
        $this->path=$path;
    }
    public function setPrenom($prenom){
        $this->prenom=$prenom;
    }
    public function setEmail($email){
        $this->email=$email;
    }
    public function setPw($pw){
        $this->pw=$pw;
    }

}
