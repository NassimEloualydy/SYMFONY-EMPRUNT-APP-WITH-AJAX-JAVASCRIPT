<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Filesystem\Filesystem;
use App\Entity\Degat;
use App\Entity\Emprunt;
use App\Entity\Client;
use App\Entity\Materiel;

class DegatController extends AbstractController{
    /**
     * @Route("/Degat")
     * @Method({"GET"})
     */
    public function index(RequestStack $rs){
        $session=$rs->getSession();
        $nom=$session->get("nom","none");
        $prenom=$session->get("prenom","none");
        $path=$session->get("path","none");
        if($nom=="none")
        return $this->render("login.html.twig");
        return $this->render("degat.html.twig",array("nom"=>$nom,"prenom"=>$prenom,"path"=>$path));
    }
    /**
     * @Route("/getClientForDegat")
     * @Method({"GET"})
     */
    public function getClientForDegat(Request $r,PersistenceManagerRegistry $doctrine){
        $e=$doctrine->getManager();
        $query=$e->createQuery("SELECT DISTINCT c.id,c.nom,c.prenom FROM App\Entity\Client c INNER JOIN App\Entity\Emprunt e WITH e.client=c.id");
        $r=$query->getResult();
        $s="<option value=''>Choisire une Client</option>";
        foreach($r as $data){
            $s.="<option value='".$data["id"]."'>".$data["nom"]." ".$data["prenom"]."</option>";
        }                
        return new Response($s);
    }
    /**
     * @Method({"POST"})
     * @Route("/getMaterielForEmpruntClient/{id}")
     */
    public function getMaterielForEmpruntClient(Request $r,PersistenceManagerRegistry $doctrine,$id){
     $token=$r->request->get("token");
     if(!$this->isCsrfTokenValid('deagt-item',$token))
     return new Response("SVP le token est invalide !!");
     $e=$doctrine->getManager();
     $query=$e->createQuery("SELECT e.id,m.designation FROM App\Entity\Materiel m INNER JOIN App\Entity\Emprunt e WITH e.materiel=m.id WHERE e.client=:id")->setParameter("id",$id);       
     $r=$query->getResult();
     $s="<option value=''>Choisire une Client</option>";
     foreach($r as $data){
        $s.="<option value='".$data["id"]."'>".$data["designation"]."</option>";
     }
     return new Response($s);
    }
    /**
     * @Method({"POST"})
     * @Route("/addDegat")
     */
    public function addDegat(Request $r,PersistenceManagerRegistry $doctrine){
        $client=$r->request->get("client");
        $materiel=$r->request->get("materiel");
        $date=$r->request->get("date");
        $coutEstimer=$r->request->get("coutEstimer");
        $description=$r->request->get("description");
        $token=$r->request->get("token");
        $e=$doctrine->getManager();
        $em=$doctrine->getRepository(Emprunt::class)->find(intval($materiel));
        $d=new Degat();
        $d->setEmprunt($em);
        $d->setDescription(strval($description));
        $d->setCoutEstimer(intval($coutEstimer));
        $d->setDateDegat(strval($date));
        $e->persist($d);
        $e->flush();
        return new Response("valide");
    }
    /**
     * @Route("/getDataDegat/{ofsset}")
     * @Method({"GET"})
     */
    public function getDataDegat(Request $r,PersistenceManagerRegistry $doctrine,$ofsset){
        $e=$doctrine->getManager();
        $query=$e->createQuery("SELECT d.id,c.photo AS clientPhoto,c.nom,c.prenom,m.designation,d.dateDegat,d.coutEstimer FROM App\Entity\Emprunt e 
                                INNER JOIN App\Entity\Client c WITH c.id=e.client
                                INNER JOIN App\Entity\Materiel m WITH m.id=e.materiel
                                INNER JOIN App\Entity\Degat d WITH d.emprunt=e.id")->setMaxResults(5)->setFirstResult($ofsset);
        $data=$query->getResult();
        $s="";
        foreach($data as $d){
            $s.="<tr><td scope='row'><img src=".$d["clientPhoto"]." class='photoMateriel'></td><td>".$d["nom"]." ".$d["prenom"]."</td><td>".$d["designation"]."</td><td>".$d["dateDegat"]."</td><td>".$d["coutEstimer"]."</td><td><ion-icon onClick=deleteDegat(".$d["id"].") class='Icon Icon_delete' name='trash-outline'></ion-icon></td><td><ion-icon onClick=updateDegat(".$d["id"].")  class='Icon Icon_update' name='pencil-outline'></ion-icon> </td><td><ion-icon onClick=showDetailDegat(".$d["id"].") class='Icon Icon_details' name='information-circle-outline'></ion-icon>  </td></tr>";

        }
        return new Response($s);
    }
    /**
     * @Route("/deleteDegat/{id}")
     * @Method({"POST"})
     */
    public function deleteDegat(Request $r,PersistenceManagerRegistry $doctrine,$id){
     $d=$doctrine->getRepository(Degat::class)->find($id);
     $e=$doctrine->getManager();
     $e->remove($d);
     $e->flush();
     return new Response("valide");
    }
    /**
     * @Route("/getDegatById/{id}")
     * @Method({"GET"})
     */
     public function getDegatById(Request $r,PersistenceManagerRegistry $doctrine,$id){
      $e=$doctrine->getManager();
      $query=$e->createQuery("SELECT e.montantTotale,e.dateEmprunt,e.dateReteur,d.id AS idDegat ,d.description,d.coutEstimer,d.dateDegat, c.id AS idClient,c.photo AS clientPhoto,c.nom,c.prenom,c.cni,c.tel,c.gmail,c.adresse,m.id AS materielId,e.id AS empruntId,m.photo AS materielPhoto,m.matrecule,m.designation,m.prix,m.category,m.qte,m.datePeremption FROM App\Entity\Emprunt e 
                              INNER JOIN App\Entity\Client c WITH c.id=e.client
                              INNER JOIN App\Entity\Materiel m WITH  m.id=e.materiel 
                              INNER JOIN App\Entity\Degat d WITH d.emprunt=e.id WHERE d.id=:id")->setParameter("id",$id);
    $data=$query->getResult();
     return new Response(json_encode($data[0]));
    }
    /**
     * @Route("/updateDegat")
     * @Method({"POST"})
     */
     public function updateDegat(Request $r,PersistenceManagerRegistry $doctrine){
        $client=$r->request->get("client");
        $materiel=$r->request->get("materiel");
        $date=$r->request->get("date");
        $coutEstimer=$r->request->get("coutEstimer");
        $description=$r->request->get("description");
        $idDegat=$r->request->get("idDegat");
        $e=$doctrine->getManager();
        $d=$doctrine->getRepository(Degat::class)->find(intval($idDegat));
        $d->setDescription(strval($description));
        $d->setCoutEstimer(intval($coutEstimer));
        $d->setDateDegat(strval($date));
        $e->flush();
        return new Response("valide");
     }
     /**
      * @Route("/searchDegat")
      * @Method({"POST"})
      */
      public function searchDegat(Request $r,PersistenceManagerRegistry $doctrine){
        $cni=$r->request->get("cni");
        $nom=$r->request->get("nom");
        $prenom=$r->request->get("prenom");
        $email=$r->request->get("email");
        $tel=$r->request->get("tel");
        $adresse=$r->request->get("adresse");
        $matrecule=$r->request->get("matrecule");
        $designation=$r->request->get("designation");
        $prix=$r->request->get("prix");
        $date=$r->request->get("date");
        $qte=$r->request->get("qte");
        $category=$r->request->get("category");
        $dateEmprunt=$r->request->get("dateEmprunt");
        $dateReteur=$r->request->get("dateReteur");
        $montantTotal=$r->request->get("montantTotal");
        $dateDegat=$r->request->get("dateDegat");
        $coutEsitmer=$r->request->get("coutEsitmer");
        $description=$r->request->get("description");
        $e=$doctrine->getManager();
        $query=$e->createQuery("SELECT d.id,c.photo AS clientPhoto,c.nom,c.prenom,m.designation,d.dateDegat,d.coutEstimer FROM App\Entity\Emprunt e 
                                INNER JOIN App\Entity\Client c WITH c.id=e.client
                                INNER JOIN App\Entity\Materiel m WITH m.id=e.materiel
                                INNER JOIN App\Entity\Degat d WITH d.emprunt=e.id
                                WHERE 
                                c.cni like :cni and
                                c.nom like :nom and
                                c.prenom like :prenom and
                                c.gmail like :email and
                                c.tel like :tel and
                                c.adresse like :adresse and

                                m.matrecule like :matrecule and
                                m.designation like :designation and
                                m.prix like :prix and
                                m.datePeremption like :date and
                                m.qte like :qte and
                                m.category like :category and

                                e.dateEmprunt like :dateEmprunt and
                                e.dateReteur like :dateReteur and
                                e.montantTotale like :montantTotal and
                                d.dateDegat like :dateDegat and
                                d.coutEstimer like :coutEsitmer and
                                d.description like :description 
                                ")
                                ->setParameter("cni",'%'.$cni.'%')
                                ->setParameter("nom",'%'.$nom.'%')
                                ->setParameter("prenom",'%'.$prenom.'%')
                                ->setParameter("email",'%'.$email.'%')
                                ->setParameter("tel",'%'.$tel.'%')
                                ->setParameter("adresse",'%'.$adresse.'%')
                                ->setParameter("matrecule",'%'.$matrecule.'%')
                                ->setParameter("designation",'%'.$designation.'%')
                                ->setParameter("prix",'%'.$prix.'%')
                                ->setParameter("date",'%'.$date.'%')
                                ->setParameter("qte",'%'.$qte.'%')
                                ->setParameter("category",'%'.$category.'%')
                                ->setParameter("dateEmprunt",'%'.$dateEmprunt.'%')
                                ->setParameter("dateReteur",'%'.$dateReteur.'%')
                                ->setParameter("montantTotal",'%'.$montantTotal.'%')
                                ->setParameter("dateDegat",'%'.$dateDegat.'%')
                                ->setParameter("coutEsitmer",'%'.$coutEsitmer.'%')
                                ->setParameter("description",'%'.$description.'%')
                                ;
        $data=$query->getResult();
        $s="";
        foreach($data as $d){
            $s.="<tr><td scope='row'><img src=".$d["clientPhoto"]." class='photoMateriel'></td><td>".$d["nom"]." ".$d["prenom"]."</td><td>".$d["designation"]."</td><td>".$d["dateDegat"]."</td><td>".$d["coutEstimer"]."</td><td><ion-icon onClick=deleteDegat(".$d["id"].") class='Icon Icon_delete' name='trash-outline'></ion-icon></td><td><ion-icon onClick=updateDegat(".$d["id"].")  class='Icon Icon_update' name='pencil-outline'></ion-icon> </td><td><ion-icon onClick=showDetailDegat(".$d["id"].") class='Icon Icon_details' name='information-circle-outline'></ion-icon>  </td></tr>";

        }
        return new Response($s);
      }
      /**
       * @Method("GET")
       * @Route("/nbrDegatParClient")
       */
      public function nbrDegatParClient(Request $r,PersistenceManagerRegistry $doctrine){
        $e=$doctrine->getManager();
        $query=$e->createQuery("SELECT c.nom,c.prenom,COUNT(d.id) AS nbrDegat FROM App\Entity\Client c INNER JOIN App\Entity\Emprunt e WITH e.client=c.id
                                INNER JOIN App\Entity\Degat d WITH d.emprunt=e.id GROUP BY c.nom,c.prenom");
        return new Response(json_encode($query->getResult()));
      }
      /**
       * @Method({"GET"})
       * @Route("/nbrMaterielDomager")
       */
      public function nbrMaterielDomager(Request $r,PersistenceManagerRegistry $doctrine){
        $e=$doctrine->getManager();
        $query=$e->createQuery("SELECT m.designation,COUNT(d.id) AS nbrDomager FROM App\Entity\Materiel m INNER JOIN App\Entity\Emprunt e WITH m.id=e.materiel
                                INNER JOIN App\Entity\Degat d WITH d.emprunt=e.id GROUP BY m.designation");
        return new Response(json_encode($query->getResult()));
      }
      /**
       * @Method({"GET"})
       * @Route("/nbrTotaleDegat")
       */
      public function nbrTotaleDegat(Request $r,PersistenceManagerRegistry $doctrine){
        $e=$doctrine->getManager();
        $query=$e->createQuery("SELECT d FROM App\Entity\Degat d");
        return new Response(count($query->getResult()));
      }
      /**
       * @Method({"GET"})
       * @Route("/LatestAddedDegat")
       */
      public function LatestAddedDegat(Request $r,PersistenceManagerRegistry $doctrine){
        $e=$doctrine->getManager();
        $query=$e->createQuery("SELECT c.photo AS clientPhoto,m.designation FROM App\Entity\Degat d INNER JOIN App\Entity\Emprunt e WITH e.id=d.emprunt INNER JOIN App\Entity\Client c WITH c.id=e.client INNER JOIN App\Entity\Materiel m WITH m.id=e.materiel ORDER BY d.id DESC");
        $data=$query->getResult();
        $s="";
        foreach($data as $l){
            $s.="<div class='itemLatestAdded'><img src=".$l["clientPhoto"]." class='photoMateriel'> &nbsp;<div>".$l["designation"]."</div></div>";
        }
        return new Response($s);
      }

    }