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
use App\Entity\Materiel;

class MaterielController extends AbstractController{
        /**
     * @Route("/materiel")
     * @Method({"GET"})
     */
    public function index(RequestStack $rs){
      $session=$rs->getSession();
      $nom=$session->get("nom","none");
      $prenom=$session->get("prenom","none");
      $path=$session->get("path","none");
      if($nom=="none")      
         return $this->render("login.html.twig");
        return $this->render("materiel.html.twig",
                        array('nom'=>$nom,
                              'prenom'=>$prenom,
                              'path'=>$path
                        ));
        
      }
    /**
     * @Route("/AddMatrecule")
     * @Method({"POST"})
     */
    public function AddMatrecule(Request $r,PersistenceManagerRegistry $doctrine){
      $designation=$r->request->get("designation");
      $prix=$r->request->get("prix");
      $date_peremption=$r->request->get("date_peremption");
      $qte=$r->request->get("qte");
      $category=$r->request->get("category");
      $matrecule=$r->request->get("matrecule");
      $e=$doctrine->getManager();
      $query=$e->createQuery("SELECT m FROM App\Entity\Materiel m WHERE m.matrecule=:matrecule")->setParameter("matrecule",$matrecule);
      if(count($query->getResult())!=0)
        return new Response("SVP cet matrecule exist deja !!");
      $query=$e->createQuery("SELECT m FROM App\Entity\Materiel m WHERE m.designation=:designation")->setParameter("designation",$designation);
      if(count($query->getResult())!=0)
       return new Response("SVP la designation exist deja !!");

       $uplodeFile=$r->files->get("photo");
       $photo=$uplodeFile->getClientOriginalName();
       $img=explode(".",$photo);
       $uplodeFile->move("materieles",$matrecule.'.'.$img[1]);
       $m=new Materiel();
       $m->setCategory(strval($category));
       $m->setPhoto("/materieles/".$matrecule.'.'.$img[1]);
       $m->setDesignation(strval($designation));
       $m->setPrix(intval($prix));
       $m->setDatePeremption(strval($date_peremption));
       $m->setQte(intval($qte));
       $m->setMatrecule(strval($matrecule));
       $e->persist($m);
       $e->flush();
       return new Response("Mteriel ajouter avec success !!");
}
/**
 * @Route("/getData/{ofsset}")
 * @Method({"POST"})
 */
public function getData(Request $r,PersistenceManagerRegistry $doctrine,$ofsset){
    
      $e=$doctrine->getManager();
      $query=$e->createQuery("SELECT m FROM App\Entity\Materiel m ORDER BY m.id desc")
      ->setMaxResults(5)
      ->setFirstResult($ofsset);
      $s=$query->getResult();
      $r="";
      foreach ($s as $v){
            //  $data=json_encode(array("photo"=>$v->getPhoto(),"datePeremption"=>$v->getdatePeremption(),"matrecule"=>$v->getMatrecule(),"prix"=>$v->getPrix(),"id"=>$v->getId(),"category"=>$v->getCategory(),"designation"=>$v->getDesignation()));
            // $designation=strval($v->getDesignation());      
           $r.= "<tr><td scope='row'><img src=".$v->getPhoto()." class='photoMateriel'></td><td>".$v->getMatrecule()."</td><td>".$v->getDesignation()."</td><td>".$v->getPrix()."</td><td>".$v->getPrix()."</td><td><ion-icon onClick=deleteMateriel(".$v->getId().") class='Icon Icon_delete' name='trash-outline'></ion-icon></td><td><ion-icon onClick=updateMateriel(".$v->getId().")  class='Icon Icon_update' name='pencil-outline'></ion-icon> </td><td><ion-icon onClick=showDetailMateriel(".$v->getId().") class='Icon Icon_details' name='information-circle-outline'></ion-icon>  </td></tr>";
      }
      $response=new Response($r);
      return $response;
}
/**
 * @Route("/deleteMateriel/{id}")
 * @Method({"POST"})
 */
 public function deleteMateriel(Request $r,PersistenceManagerRegistry $doctrine,$id){
   $m=$doctrine->getRepository(Materiel::class)->find($id);
   $e=$doctrine->getManager();
   if(file_exists('.'.$m->getPhoto()))
    unlink('.'.$m->getPhoto());
    $e->remove($m);
    $e->flush();
 return new Response("valide");     
}
/**
 * @Route("/getMateriel/{id}")
 * @Method({"POST"})
 */
   public function getMateriel(Request $r,PersistenceManagerRegistry $doctrine,$id){
    $m=$doctrine->getRepository(Materiel::class)->find($id);
    $data=json_encode(array(
      "photo"=>$m->getPhoto(),
      "datePeremption"=>$m->getdatePeremption(),
      "matrecule"=>$m->getMatrecule(),
      "prix"=>$m->getPrix(),
      "id"=>$m->getId(),
      "category"=>$m->getCategory(),
      "qte"=>$m->getQte(),
      "designation"=>$m->getDesignation()));
      return new Response($data);
   }
   /**
    * @Route("/UpdateMateriel")
    * @Method({"POST"})
    */
   public function UpdateMateriel(Request $r,PersistenceManagerRegistry $doctrine){
    $designation=$r->request->get("designation");
    $prix=$r->request->get("prix");
    $date_peremption=$r->request->get("date_peremption");
    $qte=$r->request->get("qte");
    $category=$r->request->get("category");
    $matrecule=$r->request->get("matrecule");
    $id=$r->request->get("id");
    $e=$doctrine->getManager();
    $query=$e->createQuery("SELECT m FROM App\Entity\Materiel m WHERE m.matrecule=:matrecule AND m.id!=:id")
    ->setParameter("matrecule",$matrecule)
    ->setParameter("id",$id);
    if(count($query->getResult())!=0)
    return new Response("SVP cet materiele exist deja !!");
    $query=$e->createQuery("SELECT m FROM App\Entity\Materiel m WHERE m.designation=:designation AND m.id!=:id")->setParameter("designation",$designation)->setParameter("id",$id);
    if(count($query->getResult())!=0)
    return new Response("SVP cet designation exist deja !!");
    $m=$doctrine->getRepository(Materiel::class)->find($id);
    if($r->files->get("photo")){

      $uplodeFile=$r->files->get("photo");

      if(file_exists('.'.$m->getPhoto()))
      unlink('.'.$m->getPhoto());  

      $uplodeFile=$r->files->get("photo");
      $photo=$uplodeFile->getClientOriginalName();
      $img=explode(".",$photo);
      $uplodeFile->move("materieles",$matrecule.'.'.$img[1]);
      $m->setPhoto("/materieles/".$matrecule.'.'.$img[1]);

    }
    $m->setCategory(strval($category));
    $m->setDesignation(strval($designation));
    $m->setPrix(intval($prix));
    $m->setDatePeremption(strval($date_peremption));
    $m->setQte(intval($qte));
    $m->setMatrecule(strval($matrecule));
    $e->flush();
     return new Response("Materiel Modffier avec success !!");
   }
   /**
    * @Route("/searchMateriel")
    * @Method({"POST"})
    */
    public function searchMateriel(Request $r,PersistenceManagerRegistry $doctrine){
      $matrecule=$r->request->get("matrecule");
      $designation=$r->request->get("designation");
      $prix=$r->request->get("prix");
      $date=$r->request->get("date");
      $qte=$r->request->get("qte");
      $category=$r->request->get("category");
      $e=$doctrine->getManager();
      $query=$e->createQuery("
    SELECT m FROM App\Entity\Materiel m  
    INNER JOIN App\Entity\Materiel m1 WITH m.id=m1.id
    INNER JOIN App\Entity\Materiel m2 WITH m.id=m2.id
    INNER JOIN App\Entity\Materiel m3 WITH m.id=m3.id
    INNER JOIN App\Entity\Materiel m4 WITH m.id=m4.id
    INNER JOIN App\Entity\Materiel m5 WITH m.id=m5.id
    INNER JOIN App\Entity\Materiel m6 WITH m.id=m6.id
    WHERE 
    m1.matrecule LIKE :matrecule AND
    m2.designation LIKE :designation AND
    m3.prix LIKE :prix AND
    m4.datePeremption LIKE :date AND
    m5.qte LIKE :qte AND
    m6.category LIKE :category
      ")->setParameter("matrecule",'%'.$matrecule.'%')
      ->setParameter("designation",'%'.$designation.'%')
      ->setParameter("prix",'%'.$prix.'%')
      ->setParameter("date",'%'.$date.'%')
      ->setParameter("qte",'%'.$qte.'%')
      ->setParameter("category",'%'.$category.'%');
      $s=$query->getResult();
      $r="";
      foreach ($s as $v){
            //  $data=json_encode(array("photo"=>$v->getPhoto(),"datePeremption"=>$v->getdatePeremption(),"matrecule"=>$v->getMatrecule(),"prix"=>$v->getPrix(),"id"=>$v->getId(),"category"=>$v->getCategory(),"designation"=>$v->getDesignation()));
            // $designation=strval($v->getDesignation());      
           $r.= "<tr><td scope='row'><img src=".$v->getPhoto()." class='photoMateriel'></td><td>".$v->getMatrecule()."</td><td>".$v->getDesignation()."</td><td>".$v->getPrix()."</td><td>".$v->getPrix()."</td><td><ion-icon onClick=deleteMateriel(".$v->getId().") class='Icon Icon_delete' name='trash-outline'></ion-icon></td><td><ion-icon onClick=updateMateriel(".$v->getId().")  class='Icon Icon_update' name='pencil-outline'></ion-icon> </td><td><ion-icon onClick=showDetailMateriel(".$v->getId().") class='Icon Icon_details' name='information-circle-outline'></ion-icon>  </td></tr>";
      }
      $response=new Response($r);
      return $response;
    }
    /**
     * @Route({"/nbrMaterilParCategory"})
     * @Method({"GET"})
     */
    public function nbrMaterilParCategory(Request $r,PersistenceManagerRegistry $doctrine){
      $e=$doctrine->getManager();
      $query=$e->createQuery("SELECT m.category,COUNT(m.id) AS data FROM App\Entity\Materiel m GROUP BY m.category");
      $data=array();
      $labels=array();
      $r=$query->getResult();
      return new Response(json_encode($r));

    }
    /**
     * @Route("/nbrMaterielExpirer")
     * @Method({"GET"})
     */
    public function nbrMaterielExpirer(Request $r,PersistenceManagerRegistry $doctrine){
      $e=$doctrine->getManager();
       $query=$e->createQuery("SELECT month(m.datePeremption) AS mois ,count(m.id) AS dataMois  FROM App\Entity\Materiel m GROUP BY mois");
       return new Response(json_encode($query->getResult()));
    }
    /**
     * @Method({"GET"})
     * @Route("/getLatestMaterielAdd")
     */
    public function getLatestMaterielAdd(Request $r,PersistenceManagerRegistry $doctrine){
      $e=$doctrine->getManager();
      $query=$e->createQuery("SELECT m FROM App\Entity\Materiel m ORDER BY m.id DESC")->setMaxResults(5);
      $s=$query->getResult();
      $data="";
      foreach($s as $m){
        $data.="<div class='itemLatestAdded'><img  src=".$m->getPhoto()." class='photoMateriel'> &nbsp;<div>".$m->getDesignation()."</div></div>";
      }
      return new Response($data);
    }

    // 
    // ->setFirstResult($ofsset);
    // $s=$query->getResult();
    // $r="";
    // foreach ($s as $v){
    //       //  $data=json_encode(array("photo"=>$v->getPhoto(),"datePeremption"=>$v->getdatePeremption(),"matrecule"=>$v->getMatrecule(),"prix"=>$v->getPrix(),"id"=>$v->getId(),"category"=>$v->getCategory(),"designation"=>$v->getDesignation()));
    //       // $designation=strval($v->getDesignation());      
    //      $r.= "<tr><td scope='row'><img src=".$v->getPhoto()." class='photoMateriel'></td><td>".$v->getMatrecule()."</td><td>".$v->getDesignation()."</td><td>".$v->getPrix()."</td><td>".$v->getPrix()."</td><td><ion-icon onClick=deleteMateriel(".$v->getId().") class='Icon Icon_delete' name='trash-outline'></ion-icon></td><td><ion-icon onClick=updateMateriel(".$v->getId().")  class='Icon Icon_update' name='pencil-outline'></ion-icon> </td><td><ion-icon onClick=showDetailMateriel(".$v->getId().") class='Icon Icon_details' name='information-circle-outline'></ion-icon>  </td></tr>";
    // }
    // $response=new Response($r);

}