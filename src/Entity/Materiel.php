<?php

namespace App\Entity;

use App\Repository\MaterielRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterielRepository::class)]
class Materiel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
 
    #[ORM\Column(length:100)]
    private string $photo;

    #[ORM\Column(length:80)]
    private string $matrecule;

    #[ORM\Column(length:80)]
    private string $designation;
    
    #[ORM\Column]
    private int $prix; 

    #[ORM\Column]
    private string $datePeremption;
     
    #[ORM\Column]
    private int $qte;

    #[ORM\Column(length:80)]
    private string $category;

   public function getMatrecule():?string{
      return $this->matrecule;
   }
  public function setMatrecule($matrecule){
    $this->matrecule=$matrecule;
  }    
public function getCategory():?string{
  return $this->category;
}
public function setCategory($category){
    $this->category=$category;
}
 public function getPhoto():?string{
    return $this->photo;
 }
 
 public function getDesignation():?string{
    return $this->designation;
 }
 public function getPrix():?int{
    return $this->prix;
 }
 public function getdatePeremption():?string{
  return $this->datePeremption;
 } 
 public function getQte():?int{
    return $this->qte;
 }

 public function setPhoto($photo){
    $this->photo=$photo;
 }
 public function setDesignation($designation){
  $this->designation=$designation;
 }
 public function setPrix($prix){
  $this->prix=$prix;   
 }
 public function setDatePeremption($datePeremption){
    $this->datePeremption=$datePeremption;
 }
 public function setQte($qte){
    $this->qte=$qte;
 }
    public function getId(): ?int
    {
        return $this->id;
    }

}
