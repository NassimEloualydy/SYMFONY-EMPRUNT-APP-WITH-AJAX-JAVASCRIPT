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
use App\Entity\Client;
use App\Entity\Materiel;
use App\Entity\Emprunt;
class EmpruntController extends AbstractController{
    /**
     * @Route("/emprunt")
     * @Method({"GET"})
     */
    public function index(RequestStack $rs){
        $session=$rs->getSession();
        $nom=$session->get("nom","none");
        $prenom=$session->get("prenom","none");
        $path=$session->get("path","none");
        if($nom=="none")      
           return $this->render("login.html.twig");
          return $this->render("emprunt.html.twig",
                          array('nom'=>$nom,
                                'prenom'=>$prenom,
                                'path'=>$path
                          ));          
        }
        /**
         * @Route("/getClientForEmprunt")
         * @Method({"POST"})
         */
      public function getClientForEmprunt(Request $r,PersistenceManagerRegistry $doctrine){
       $e=$doctrine->getManager();
       $query=$e->createQuery("SELECT c FROM App\Entity\Client c");
       $r=$query->getResult();
       $s="<option value=''>Choisire une Client</option>";
       foreach($r as $c){
        $s.="<option value='".$c->getId()."'>".$c->getNom()." ".$c->getPrenom()."</option>";
       }
      return new Response($s);
      } 
      /**
       * @Method({"POST"})
       * @Route("/getMaterielForEmprunt")
       */
      public function getMaterielForEmprunt(Request $r,PersistenceManagerRegistry $doctrine){
        $e=$doctrine->getManager();
        $query=$e->createQuery("SELECT m FROM App\Entity\Materiel m");
        $r=$query->getResult();
        $s="<option value=''>Choisire une Materiel</option>";
        foreach($r as $m){
            $s.="<option value='".$m->getId()."'>".$m->getDesignation()."</option>";
        }
        return new Response($s);
      }
      /**
       * @Route("/addEmprunt/{id}",defaults={"id" = 0},)
       * @Method("POST")
       */
      public function addEmprunt(Request $r,PersistenceManagerRegistry $doctrine){
        $client=$r->request->get("client");
        $materiel=$r->request->get("materiel");
        $dateEmprunt=$r->request->get("dateEmprunt");
        $dateReteur=$r->request->get("dateReteur");
        $montentTotale=$r->request->get("montentTotale");
        $e=$doctrine->getManager();
        $query=$e->createQuery("SELECT e FROM App\Entity\Emprunt e WHERE e.client=:client AND e.materiel=:materiel AND e.dateEmprunt=:dateEmprunt")
        ->setParameter("client",$client)
        ->setParameter("materiel",$materiel)
        ->setParameter("dateEmprunt",$dateEmprunt);
        if(count($query->getResult())!=0)
         return new Response("SVP cet emprunt exist deja !!");
         $c=$doctrine->getRepository(Client::Class)->find(intval($client));
         $m=$doctrine->getRepository(Materiel::class)->find(intval($materiel));
         $emprunt=new Emprunt();
         $emprunt->setClient($c);
         $emprunt->setMateriel($m);
         $emprunt->setDateEmprunt(strval($dateEmprunt));
         $emprunt->setDateReteur(strval($dateReteur));
         $emprunt->setMontantTotale(intval($montentTotale));
         $e->persist($emprunt);
         $e->flush();
          return new Response("valide");
              }
        /**
         * @Method({"POST"})
         * @Route("/getDataEmprunt/{offsete}")
         */
        public function getDataEmprunt(Request $r,PersistenceManagerRegistry $doctrine,$offsete){
          $e=$doctrine->getManager();
          $query=$e->createQuery("SELECT e.id,e.montantTotale,e.dateEmprunt,e.dateReteur,m.photo AS materielPhoto,m.matrecule,m.designation,m.prix,m.category,c.photo AS clientPhoto,c.nom,c.prenom,c.cni,c.tel,c.gmail,c.adresse FROM App\Entity\Emprunt e INNER JOIN App\Entity\Client c WITH c.id=e.client INNER JOIN App\Entity\Materiel m WITH m.id=e.materiel")
          ->setMaxResults(5)
          ->setFirstResult($offsete);
          
          $r=$query->getResult();
          $s=json_encode($r);
          $result="";
          foreach($r as $em){
            $result.="<tr><td scope='row'><img src=".$em["clientPhoto"]." class='photoMateriel'></td><td>".$em["nom"]." ".$em["prenom"]."</td><td>".$em["designation"]."</td><td>".$em["dateEmprunt"]."</td><td>".$em["dateReteur"]."</td><td><ion-icon onClick=deleteMateriel(".$em["id"].") class='Icon Icon_delete' name='trash-outline'></ion-icon></td><td><ion-icon onClick=updateEmprunt(".$em["id"].")  class='Icon Icon_update' name='pencil-outline'></ion-icon> </td><td><ion-icon onClick=showDetailEmprunt(".$em["id"].") class='Icon Icon_details' name='information-circle-outline'></ion-icon>  </td></tr>";
          }
          return new Response($result);
        }
        /**
         * @Method({"POST"})
         * @Route("/deleteMateriel/{id}")
         */
        public function deleteMateriel(Request $r,PersistenceManagerRegistry $doctrine,$id){
           $e=$doctrine->getManager();
           $emprunt=$doctrine->getRepository(Emprunt::class)->find($id);
           $e->remove($emprunt);
           $e->flush();
           return new Response("valide");
        }
        /**
         * @Method({"GET"})
         * @Route("/GetEmprunt/{id}")
         */
        public function GetEmprunt(Request $r,PersistenceManagerRegistry $doctrine,$id){
          $e=$doctrine->getManager();
          $query=$e->createQuery("SELECT e.id,c.id as client_id,m.id as materiel_id,e.montantTotale,e.dateEmprunt,e.dateReteur,m.photo AS materielPhoto,m.datePeremption,m.qte,m.matrecule,m.designation,m.prix,m.category,c.photo AS clientPhoto,c.nom,c.prenom,c.cni,c.tel,c.gmail,c.adresse FROM App\Entity\Emprunt e INNER JOIN App\Entity\Client c WITH c.id=e.client INNER JOIN App\Entity\Materiel m WITH m.id=e.materiel WHERE e.id=:id")->setParameter("id",$id);
          $data=$query->getResult();
          return new Response(json_encode($data[0])); 
        }
        /**
         * @Method({"POST"})
         * @Route("/updateEmprunt")
         */
        public function updateEmprunt(Request $r,PersistenceManagerRegistry $doctrine){
          $id=$r->request->get("id");
          $client=$r->request->get("client");
          $materiel=$r->request->get("materiel");
          $dateEmprunt=$r->request->get("dateEmprunt");
          $dateReteur=$r->request->get("dateReteur");
          $montentTotale=$r->request->get("montentTotale");
          $em=$doctrine->getRepository(Emprunt::class)->find(intval($id));
          $n_client=$doctrine->getRepository(Client::class)->find(intval($client));
          $n_materiel=$doctrine->getRepository(Materiel::class)->find(intval($materiel));
          $e=$doctrine->getManager();
          $query=$e->createQuery("SELECT e FROM App\Entity\Emprunt e WHERE e.client=:client AND e.materiel=:materiel AND e.dateEmprunt=:dateEmprunt AND e.id!=:id")
          ->setParameter("client",$client)
          ->setParameter("materiel",$materiel)
          ->setParameter("id",$id)
          ->setParameter("dateEmprunt",$dateEmprunt);
          if(count($query->getResult())!=0)
          return new Response("SVP cet emprunt exist deja !!");
         $em->setClient($n_client);
         $em->setMateriel($n_materiel);
         $em->setDateEmprunt(strval($dateEmprunt));
         $em->setDateReteur(strval($dateReteur));
         $em->setMontantTotale(intval($montentTotale));
         $e->flush();
          return new Response("valide");
          
        }
        /**
         * @Route("/searchEmprunt")
         * @Method({"POST"})
         */
        public function searchEmprunt(Request $r,PersistenceManagerRegistry $doctrine){
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
          $e=$doctrine->getManager();
          
          $query=$e->createQuery("SELECT e.id,e.montantTotale,e.dateEmprunt,e.dateReteur,m.photo AS materielPhoto,m.matrecule,m.designation,m.prix,m.category,c.photo AS clientPhoto,c.nom,c.prenom,c.cni,c.tel,c.gmail,c.adresse FROM App\Entity\Emprunt e 
                                  INNER JOIN App\Entity\Client c WITH c.id=e.client 
                                  INNER JOIN App\Entity\Materiel m WITH m.id=e.materiel
                                  WHERE 
                                  c.cni LIKE :cni AND
                                  c.nom LIKE :nom AND
                                  c.prenom LIKE :prenom AND
                                  c.gmail LIKE :email AND
                                  c.tel LIKE :tel AND
                                  c.adresse LIKE :adresse AND
                                  m.matrecule LIKE :matrecule AND
                                  m.designation LIKE :designation AND
                                  m.prix LIKE :prix AND
                                  m.datePeremption LIKE :date AND
                                  m.qte LIKE :qte AND
                                  m.category LIKE :category AND
                                  e.dateEmprunt LIKE :dateEmprunt AND
                                  e.dateReteur LIKE :dateReteur AND
                                  e.montantTotale LIKE :montantTotal 
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
                                  ->setParameter("montantTotal",'%'.$montantTotal.'%');
                                  $r=$query->getResult();
                                  $s=json_encode($r);
                                  $result="";
                                  foreach($r as $em){
                                    $result.="<tr><td scope='row'><img src=".$em["clientPhoto"]." class='photoMateriel'></td><td>".$em["nom"]." ".$em["prenom"]."</td><td>".$em["designation"]."</td><td>".$em["dateEmprunt"]."</td><td>".$em["dateReteur"]."</td><td><ion-icon onClick=deleteMateriel(".$em["id"].") class='Icon Icon_delete' name='trash-outline'></ion-icon></td><td><ion-icon onClick=updateEmprunt(".$em["id"].")  class='Icon Icon_update' name='pencil-outline'></ion-icon> </td><td><ion-icon onClick=showDetailEmprunt(".$em["id"].") class='Icon Icon_details' name='information-circle-outline'></ion-icon>  </td></tr>";
                                  }
                                  return new Response($result);
                                                           

        }
        /**
         * @Method({"POST"})
         * @Route("/nbrEmpruntParClient")
         */
        public function nbrEmpruntParClient(Request $r,PersistenceManagerRegistry $doctrine){
          $e=$doctrine->getManager();
          $query=$e->createQuery("SELECT c.nom,c.prenom,COUNT(e.id) AS nbrEmprunt FROM App\Entity\Emprunt e INNER JOIN App\Entity\Client c WITH c.id=e.client GROUP BY c.nom,c.prenom ORDER BY COUNT(e.id) DESC");
          return new Response(json_encode($query->getResult()));
        }
        /**
         * @Method({"POST"})
         * @Route("/labnbrMaterielClientels")
         */
        public function labnbrMaterielClientels(Request $r,PersistenceManagerRegistry $doctrine){
          $e=$doctrine->getManager();
          $query=$e->createQuery("SELECT m.designation,count(e.id) AS nbrMaterielClient FROM App\Entity\Materiel m 
          INNER JOIN App\Entity\Emprunt e WITH m.id=e.materiel                     
          GROUP BY m.designation
          ORDER BY count(e.id) DESC");
          return new Response(json_encode($query->getResult()));
        }
        /**
         * @Method({"POST"})
         * @Route("/getMaterielForclinetByEmprunt/{id}")
         */
        public function getMaterielForclinetByEmprunt(Request $r,PersistenceManagerRegistry $doctrine,$id){
         $e=$doctrine->getManager(); 
         $query=$e->createQuery("SELECT m.photo AS materielPhoto,m.datePeremption,m.qte,m.matrecule,m.designation,m.prix,m.category FROM App\Entity\Materiel m 
                                 INNER JOIN App\Entity\Emprunt e WITH e.materiel=m.id WHERE e.client=:id")->setParameter("id",$id);
        $r=$query->getResult();
        $s=json_encode($r);
        $result="";
        foreach($r as $em){
          $result.="<div class='itemLatestAdded'><img  src=".$em["materielPhoto"]." class='photoMateriel'> &nbsp;<div>".$em["designation"]."</div></div>";
        }
        return new Response($result);  
        }

}

