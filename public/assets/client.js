function submitClient(){
    // if(document.getElementById('submitClient').value=="Moddifier"){

    // }
    // if(document.getElementById('submitClient').value=="Ajouter"){
    // }

    var photo=document.getElementById("photoClient").files[0]; 
    var cni=document.getElementById("cni_client").value;
    var nom=document.getElementById("nom_client").value;
    var prenom=document.getElementById("prenom_client").value;
    var email=document.getElementById("email_client").value;
    var tel=document.getElementById("tel_client").value;
    var adresse=document.getElementById("adresse_client").value;
    var r_cni=RegExp('[A-Z]{1}[0-9]{7}|[A-Z]{2}[0-9]{6}');
    var r_tel=RegExp('0[0-9]{9}');
    var r_email=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    
     if(cni !="" && nom!="" && prenom!="" && email!="" && tel!="" && adresse!=""){
      if((document.getElementById("photoClient").files.length!=0 && document.getElementById('submitClient').value=="Ajouter") || document.getElementById('submitClient').value=="Moddifier"){

        if(r_cni.test(cni)){
   
        if(email.match(r_email)){
         if(r_tel.test(tel)){
   
        var f=new FormData();
        f.append("photo",photo);
        f.append("cni",cni);
        f.append("nom",nom);
        f.append("prenom",prenom);
        f.append("email",email);
        f.append("tel",tel);
        f.append("adresse",adresse);
        f.append("id",idClient);
        var xhr=new XMLHttpRequest();
        xhr.onreadystatechange=function(){
            if(this.status==200 && this.readyState==4){
                if(this.responseText=="Valide")
                hideFormulaire();
                else
                alert(this.responseText);
            }
        }
        if(document.getElementById('submitClient').value=="Ajouter")
        xhr.open("POST","/addClient",false);
        else
        xhr.open("POST","/updateClient",false);
        xhr.send(f);

        }else
          alert("SVP le format de telephone est invalide !!")
        }else
          alert("SVP cet email est invalide !!");
     }else
     alert("le format de cni et invalide !!");
    }else
    alert("SVP choisire une photo !!");

    }else
    alert("SVP tout les champs sont obligatoire !!");  

}
var limiteClient=0;
function getDataClient(){
    getCountOfAll();
    getLatestAddedClient();
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            if(this.responseText=="")
            limiteClient=limiteClient-5;
            else
            document.getElementById("listClient").innerHTML=this.responseText;
        }
    }
    xhr.open("GET","/getDataClient/"+limiteClient,false);
    xhr.send(null);
}
function PreviosClient(){
 if(limiteClient!=0){
    limiteClient=limiteClient-5;
    getDataClient();
 }
}
function NextClient(){
    limiteClient=limiteClient+5;
    getDataClient();

}
function hideFormulaire(){
    getDataClient();
    document.getElementById('submitClient').value="Ajouter";
    document.getElementById("formS").classList.remove("formulaire");
    document.getElementById("formS").classList.add("hideFormulaire");
    document.getElementById("cni_client").value="";
    document.getElementById("nom_client").value="";
    document.getElementById("prenom_client").value="";
    document.getElementById("email_client").value="";
    document.getElementById("tel_client").value="";
    document.getElementById("adresse_client").value="";

}
function deleteClient(id){
   if(window.confirm("Voulez vous vraiment supprimer cet client")){
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            if(this.responseText=="valide")
            hideFormulaire()
            else
            alert(this.responseText);
        }
    }
    xhr.open("POST","/deleteClient/"+id,false);
    xhr.send(null);
   }
}
var idClient=-1;
function updateClient(id){
    idClient=id;
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            var r=JSON.parse(this.responseText);
            document.getElementById("formS").classList.add("formulaire");
            document.getElementById("formS").classList.remove("hideFormulaire");
            document.getElementById("cni_client").value=r.cni;
            document.getElementById("nom_client").value=r.nom;
            document.getElementById("prenom_client").value=r.prenom;
            document.getElementById("email_client").value=r.email;
            document.getElementById("tel_client").value=r.tel;
            document.getElementById("adresse_client").value=r.adresse;
            document.getElementById('submitClient').value="Moddifier";
        }
    }
    xhr.open("POST","/getClientById/"+id,false);
    xhr.send();
    
}
function showDetailClient(id){    
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
     if(this.status==200 && this.readyState==4){
        var r=JSON.parse(this.responseText);
        document.getElementById("imgClient").src=r.photo;
        document.getElementById("nom_detail").innerHTML=r.nom;
        document.getElementById("prenom_detail").innerHTML=r.prenom;
        document.getElementById("email_detail").innerHTML=r.email;
        document.getElementById("tel_detail").innerHTML=r.tel;
        document.getElementById("adresse_detail").innerHTML=r.adresse;
        document.getElementById("CNI_detail").innerHTML=r.cni;
        document.getElementById("detailMaterile").classList.remove("HidedetailItem");
        document.getElementById("detailMaterile").classList.add("ShoWdetailItem");     
     }   
    }
    xhr.open("POST","/getClientById/"+id,false);
    xhr.send(null);
}

function search_client(){
var cni=document.getElementById("cni_search").value;
var nom=document.getElementById("nom_search").value;
var prenom=document.getElementById("prenom_search").value;
var email=document.getElementById("email_search").value;
var tel=document.getElementById("tel_search").value;
var adresse=document.getElementById("adresse_search").value;
var f=new FormData();
f.append("nom",nom);
f.append("prenom",prenom);
f.append("email",email);
f.append("tel",tel);
f.append("adresse",adresse);
f.append("cni",cni);
var xhr=new XMLHttpRequest();
xhr.onreadystatechange=function(){
    if(this.status==200 && this.readyState==4){
        document.getElementById("listClient").innerHTML=this.responseText;
    }
}
xhr.open("POST","/searchClient",false);
xhr.send(f);
}
function getCountOfAll(){
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            document.getElementById("ticketCountAll").innerHTML=this.responseText;
        }
    }
    xhr.open("GET","/getCountOfAll",false);
    xhr.send(null);
}
function getLatestAddedClient(){
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            document.getElementById("itemLatestAddedClient").innerHTML=this.responseText;
        }
    }
    xhr.open("GET","/getLatestAddedClient",false);
    xhr.send(null);
}