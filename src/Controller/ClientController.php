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

class ClientController extends AbstractController{
    /**
     * @Route("/client")
     * @Method({"GET"})
     */
    public function index(RequestStack $rs){
        $session=$rs->getSession();
        $nom=$session->get("nom","none");
        $prenom=$session->get("prenom","none");
        $path=$session->get("path","none");
        if($nom=="none")      
           return $this->render("login.html.twig");
          return $this->render("client.html.twig",
                          array('nom'=>$nom,
                                'prenom'=>$prenom,
                                'path'=>$path
                          ));
          
        }
    /**
     * @Route("/addClient")
     * @Method({"POST"})
     */
    public function addClient(Request $r,PersistenceManagerRegistry $doctrine){
        $nom=$r->request->get("nom");
        $prenom=$r->request->get("prenom");
        $gmail=$r->request->get("email");
        $tel=$r->request->get("tel");
        $cni=$r->request->get("cni");
        $adresse=$r->request->get("adresse");
        $e=$doctrine->getManager();
        $query=$e->createQuery("SELECT c FROM App\Entity\Client c WHERE c.cni=:cni")->setParameter("cni",$cni);
        if(count($query->getResult())!=0)
        return new Response("SVP cet cni exit deja exist !!");
        $query=$e->createQuery("SELECT c FROM App\Entity\Client c WHERE c.nom=:nom AND c.prenom=:prenom")
        ->setParameter("nom",$nom)
        ->setParameter("prenom",$prenom);
        if(count($query->getResult())!=0)
        return new Response("SVP le nom et le prenom exist deja !!");
        $query=$e->createQuery("SELECT c FROM App\Entity\Client c WHERE c.gmail=:gmail")->setParameter("gmail",$gmail);
        if(count($query->getResult())!=0)
        return new Response("SVP cet email exist deja !!");
        $query=$e->createQuery("SELECT c FROM App\Entity\Client c WHERE c.tel=:tel")->setParameter("tel",$tel);
        if(count($query->getResult())!=0)
        return new Response("SVP cet telephone exist deja !!");
    
        $uploadeFile=$r->files->get("photo");
        $photo=$uploadeFile->getClientOriginalName();
        $img=explode(".",$photo);
        $uploadeFile->move("clients",$cni.'.'.$img[1]);
        $c=new Client();
        $c->setNom(strval($nom));
        $c->setPrenom(strval($prenom));
        $c->setGmail(strval($gmail));
        $c->setTel(strval($tel)); 
        $c->setCni(strval($cni));
        $c->setAdresse(strval($adresse));
        $c->setPhoto("/clients/".$cni.'.'.$img[1]);
        $e->persist($c);
        $e->flush();
        return new Response("Valide");
    }
    /**
     * @Route("/getDataClient/{offsete}")
     * @Method({"GET"})
     */
    public function getDataClient(Request $r,PersistenceManagerRegistry $doctrine,$offsete){
        $e=$doctrine->getManager();
        $query=$e->createQuery("SELECT c FROM App\Entity\Client c")
        ->setMaxResults(5)
        ->setFirstResult($offsete);
        $s=$query->getResult();
        $r="";
        foreach ($s as $c){
             $r.= "<tr><td scope='row'><img src=".$c->getPhoto()." class='photoMateriel'></td><td>".$c->getCni()."</td><td>".$c->getNom()."</td><td>".$c->getPrenom()."</td><td>".$c->getTele()."</td><td><ion-icon onClick=deleteClient(".$c->getId().") class='Icon Icon_delete' name='trash-outline'></ion-icon></td><td><ion-icon onClick=updateClient(".$c->getId().")  class='Icon Icon_update' name='pencil-outline'></ion-icon> </td><td><ion-icon onClick=showDetailClient(".$c->getId().") class='Icon Icon_details' name='information-circle-outline'></ion-icon>  </td></tr>";
        }
        $response=new Response($r);
        return $response;
  
    }
    /**
     * @Route("/deleteClient/{id}")
     * @Method({"POST"})
     */
   public function deleteClient(Request $r,PersistenceManagerRegistry $doctrine,$id){
    $c=$doctrine->getRepository(Client::class)->find($id);
    $e=$doctrine->getManager();
    if(file_exists('.'.$c->getPhoto()))
        unlink('.'.$c->getPhoto());
        $e->remove($c);
        $e->flush();
     return new Response("valide");
   }     
   /**
    * @Method({"POST"})
    * @Route("/getClientById/{id}")
    */
   public function getClientById(Request $r,PersistenceManagerRegistry $doctrine,$id){
     $c=$doctrine->getRepository(Client::class)->find($id);
    $data=array(
     "cni"=>$c->getCni(),
     "nom"=>$c->getNom(),
     "prenom"=>$c->getPrenom(),
     "email"=>$c->getGmail(),
     "tel"=>$c->getTele(),
     "adresse"=>$c->getAdresse(),
     "photo"=>$c->getPhoto()
    );
    return new Response(json_encode($data));
    }
    /**
     * @Method({"POST"})
     * @Route("/updateClient")
     */
    public function updateClient(Request $r,PersistenceManagerRegistry $doctrine){

        $id=$r->request->get("id");
        $nom=$r->request->get("nom");
        $prenom=$r->request->get("prenom");
        $gmail=$r->request->get("email");
        $tel=$r->request->get("tel");
        $cni=$r->request->get("cni");
        $adresse=$r->request->get("adresse");
        $e=$doctrine->getManager();
        

        $query=$e->createQuery("SELECT c FROM App\Entity\Client c WHERE c.cni=:cni AND c.id!=:id")
        ->setParameter("cni",$cni)
        ->setParameter("id",$id);
        if(count($query->getResult())!=0) 
        return new Response("SVP cet cni exist deja !!");

        $query=$e->createQuery("SELECT c FROM App\Entity\Client c WHERE c.nom=:nom AND c.prenom=:prenom AND c.id!=:id")
                ->setParameter("nom",$nom)
                ->setParameter("prenom",$prenom)
                ->setParameter("id",$id);
        if(count($query->getResult())!=0)
         return new Response("SVP le nom et le prenom exist deja !!");

        $query=$e->createQuery("SELECT c FROM App\Entity\Client c WHERE c.gmail=:gmail AND c.id!=:id")
        ->setParameter("gmail",$gmail)
        ->setParameter("id",$id);
        if(count($query->getResult())!=0)
         return new Response("SVP cet email exist deja !!");

         $query=$e->createQuery("SELECT c FROM App\Entity\Client c WHERE c.tel=:tel AND c.id!=:id")
         ->setParameter("tel",$tel)
         ->setParameter("id",$id);
         if(count($query->getResult())!=0)
          return new Response("SVP cet telephone exist deja !!");
      
       $c=$doctrine->getRepository(Client::class)->find(intval($id));
   
       $c->setNom(strval($nom));
       $c->setPrenom(strval($prenom));
       $c->setGmail(strval($gmail));
       $c->setTel(strval($tel)); 
       $c->setCni(strval($cni));
       $c->setAdresse(strval($adresse));
       if($r->files->get("photo")){
           unlink('.'.$c->getPhoto());
           $uploadFile=$r->files->get("photo");
           $photo=$uploadFile->getClientOriginalName();
           $img=explode(".",$photo);
           $uploadFile->move("clients",$cni.'.'.$img[1]);
           $c->setPhoto("/clients/".$cni.'.'.$img[1]);

        }
        $e->flush();
        return new Response("Valide");
        }
        /**
         * @Route("/searchClient")
         * @Method({"POST"})
         */
     public function searchClient(Request $r,PersistenceManagerRegistry $doctrine){
        $nom=$r->request->get("nom");
        $prenom=$r->request->get("prenom");
        $cni=$r->request->get("cni");
        $email=$r->request->get("email");
        $adresse=$r->request->get("adresse");
        $tel=$r->request->get("tel");
        $e=$doctrine->getManager();
        $query=$e->createQuery("
        SELECT m FROM App\Entity\Client m 
        INNER JOIN App\Entity\Client m1 WITH m1.id=m.id 
        INNER JOIN App\Entity\Client m2 WITH m2.id=m.id 
        INNER JOIN App\Entity\Client m3 WITH m3.id=m.id 
        INNER JOIN App\Entity\Client m4 WITH m4.id=m.id 
        INNER JOIN App\Entity\Client m5 WITH m5.id=m.id 
        INNER JOIN App\Entity\Client m6 WITH m6.id=m.id 
        WHERE 
        m1.nom like :nom AND 
        m2.prenom like :prenom AND 
        m3.gmail like :email AND 
        m4.tel like :tel AND 
        m5.cni like :cni AND 
        m6.adresse like :adresse ")
        ->setParameter("nom",'%'.$nom.'%')
        ->setParameter("prenom",'%'.$prenom.'%')
        ->setParameter("email",'%'.$email.'%')
        ->setParameter("tel",'%'.$tel.'%')
        ->setParameter("cni",'%'.$cni.'%')
        ->setParameter("adresse",'%'.$adresse.'%');
        $s=$query->getResult();
        $r="";
        foreach ($s as $c){
             $r.= "<tr><td scope='row'><img src=".$c->getPhoto()." class='photoMateriel'></td><td>".$c->getCni()."</td><td>".$c->getNom()."</td><td>".$c->getPrenom()."</td><td>".$c->getTele()."</td><td><ion-icon onClick=deleteClient(".$c->getId().") class='Icon Icon_delete' name='trash-outline'></ion-icon></td><td><ion-icon onClick=updateClient(".$c->getId().")  class='Icon Icon_update' name='pencil-outline'></ion-icon> </td><td><ion-icon onClick=showDetailClient(".$c->getId().") class='Icon Icon_details' name='information-circle-outline'></ion-icon>  </td></tr>";
        }
        $response=new Response($r);
        return $response;
     }
     /**
      * @Method({"GET"})
      * @Route("/getCountOfAll")
      */
      public function getCountOfAll(Request $r,PersistenceManagerRegistry $doctrine){
        $e=$doctrine->getManager();
        $query=$e->createQuery("SELECT c FROM App\Entity\Client c");
        return new Response(count($query->getResult()));
      }
           /**
      * @Method({"GET"})
      * @Route("/getLatestAddedClient")
      */

      public function getLatestAddedClient(Request $r,PersistenceManagerRegistry $doctrine){
        $e=$doctrine->getManager();
        $query=$e->createQuery("SELECT c FROM App\Entity\Client c ORDER BY c.id DESC")
        ->setMaxResults(8)
        ->setFirstResult(0);
        $s=$query->getResult();
        $r="";
        foreach ($s as $c){
             $r.= "<div class='itemLatestAdded'><img src=".$c->getPhoto()." class='photoMateriel'> &nbsp;<div>".$c->getNom()." ".$c->getPrenom()."</div></div>";
        }
        $response=new Response($r);
        return $response;

      }
}