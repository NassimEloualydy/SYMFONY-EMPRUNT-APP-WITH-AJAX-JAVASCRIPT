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
use App\Entity\Admin;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
    // composer require annotations (for routes)
    // composer require twig (for twig engine)

class AdminController extends AbstractController {
// private $RequestStack;
// public function __construct ( $RequestStack $rs ) {
//   $this->RequestStack=$rs;
// }
// public function __construct(ManagerRegestry $r){
//   parent::__construct($r,Admin::class);
// }
  /**
     * @Route("/")
     * @Method({"GET"})
     */
    public function login(){
      return  $this->render("login.html.twig");
    }
    /**
     * @Method({"GET"})
     * @Route("/compt")
     */
    public function compt(RequestStack $rs){
      $session=$rs->getSession();
      $nom=$session->get("nom","none");
      $prenom=$session->get("prenom","none");
      $path=$session->get("path","none");
      if($nom=="none")
      return $this->render("login.html.twig");
      return $this->render("compt.html.twig",array("nom"=>$nom,"prenom"=>$prenom,"path"=>$path));
    }
    /**
     * @Route("/inscription")
     * @Method({"POST"})
     */
    public function inscrire(Request $r,PersistenceManagerRegistry $doctrine){
      $prenom=$r->request->get("prenom");
      $email=$r->request->get("email");
      $pw=$r->request->get("pw");
      $nom=$r->request->get("nom");
      $rsm=new ResultSetMapping();
      $e=$doctrine->getManager();
      $query=$e->createQuery('SELECT a FROM App\Entity\Admin a WHERE a.nom = :nom AND a.prenom=:prenom')->setParameter('nom',$nom)->setParameter('prenom',$prenom);
      if(count($query->getResult())!=0)
      return new Response("SVP le nom et le prenom exist deja !!");
      $query=$e->createQuery("SELECT a FROM App\Entity\Admin a where a.email = :email")->setParameter("email",$email);
      if(count($query->getResult())!=0)
      return new Response("SVP le email exit deja !!");
      $query=$e->createQuery("SELECT a FROM App\Entity\Admin a WHERE a.pw= :pw ")->setParameter("pw",$pw);
      if(count($query->getResult())!=0)
      return new Response("SVP cet mot de passe exit deja !!");

      $uplodeFile=$r->files->get("photo");
      $photo=$r->files->get('photo')->getClientOriginalName();
      $img=explode(".",$photo);
      $uplodeFile->move("admins",$nom.'_'.$prenom.'.'.$img[1]);

      $a=new Admin();
      $a->setNom(strval($nom));
      $a->setPrenom(strval($prenom));
      $a->setEmail(strval($email));
      $a->setPw(strval($pw));
      $a->setPath("/admins/".$nom.'_'.$prenom.'.'.$img[1]);
      $e->persist($a);
      $e->flush();
       return new Response("Inscription avec success !!");
    }
  /**
   * @Route("/connxion")
   * @Method({"POST"})
   */
  public function connxion(Request $r,PersistenceManagerRegistry $doctrine,RequestStack $rs){
    $email=$r->request->get("email");
    $pw=$r->request->get("pw");
    $e=$doctrine->getManager();
    $query=$e->createQuery("SELECT a FROM App\Entity\Admin a WHERE a.email=:email AND a.pw=:pw")
    ->setParameter("email",$email)
    ->setParameter("pw",$pw);
    if(count($query->getResult())!=1)
       return new Response("SVP l'email au le mot de passe est introuvable !!");
    $r=$query->getResult();    
    $session=$rs->getSession();
    $session->set("nom",$r[0]->getNom());
    $session->set("prenom",$r[0]->getPrenom());
    $session->set("path",$r[0]->getPath());
    $session->set("id",$r[0]->getId());
    return new Response("Connxion avec succes");
  }
  /**
   * @Route("/quite")
   * @Method({"POST"})
   */
  public function quite(RequestStack $rs){
    $session=$rs->getSession();
    $session->clear();
    return  $this->render("login.html.twig"); 
  }
  /**
   * @Method({"GET"})
   * @Route("/getAdmin")
   */
  public function getAdmin(Request $r,PersistenceManagerRegistry $doctrine,RequestStack $rs){
   $session=$rs->getSession();
   $id=$session->get("id","null");
   $a=$doctrine->getRepository(Admin::class)->find($id);
   $data=Array("nom"=>$a->getNom(),"prenom"=>$a->getPrenom(),"email"=>$a->getEmail(),"pw"=>$a->getPw());
   return new Response(json_encode($data));
  }
  /**
   * @Method({"POST"})
   * @Route("/updateAdmin")
   */
  public function updateAdmin(Request $r,PersistenceManagerRegistry $doctrine,RequestStack $rs){
    $session=$rs->getSession();
    $id=$session->get("id");
    $a=$doctrine->getRepository(Admin::class)->find($id);
    $nom=$r->request->get("nom");
    $prenom=$r->request->get("prenom");
    $email=$r->request->get("email");
    $pw=$r->request->get("pw");
    $a->setNom(strval($nom));
    $a->setPrenom(strval($prenom));
    $a->setEmail(strval($email));
    $a->setPw(strval($pw));
      if($r->files->get("photo")){
      if(file_exists('.'.$a->getPath()))
      unlink('.'.$a->getPath());
      $uploadeFile=$r->files->get("photo");
      $photo=$uploadeFile->getClientOriginalName();
      $img=explode('.',$photo);
      $uploadeFile->move("admins",$nom.'_'.$prenom.'.'.$img[1]);
      $a->setPath("/admins/".$nom.'_'.$prenom.'.'.$img[1]);
       $session->set("path","/admins/".$nom.'_'.$prenom.'.'.$img[1]);
    }
    $session->set("nom",$nom);
    $session->set("prenom",$prenom);
    $e=$doctrine->getManager();
    $e->flush();
    
    return new Response("Compt moddifier avec success !!");
  }
  /**
   * @Route("/passwordForEmail")
   * @Method({"POST"})
   */
  public function sendPaswordForAdmin(MailerInterface $mailer,Request $r,PersistenceManagerRegistry $doctrine,RequestStack $rs){
      // $email=$r->request->get("email");
      // $e=$doctrine->getManager();
      // $query=$e->createQuery("SELECT a FROM App\Entity\Admin a WHERE a.email=:email")->setParameter("email",$email);
      // if(count($query->getResult())!=1)
      // return new Response("SVP cet email est introuvable !!");
      // $email = (new Email())
      // ->from('nassimesofian@gmail.com')
      // ->to('nassimesofian@gmail.com')
      // //->cc('cc@example.com')
      // //->bcc('bcc@example.com')
      // //->replyTo('fabien@example.com')
      // //->priority(Email::PRIORITY_HIGH)
      // ->subject('Time for Symfony Mailer!')
      // ->text('Sending emails is fun again!');
      // // ->html('<p>See Twig integration for better HTML integration!</p>');
      // // $data=$query->getResult();
      // $mailer->send($email);
      // // return new Response("Votre mot de passe est : ".$data[0]->getPw());
      $r=mail("nassimesofian@gmail.com","nn","nn","nassimaimadtahiri@gmail.com");
          return new Response($r);
        }
}