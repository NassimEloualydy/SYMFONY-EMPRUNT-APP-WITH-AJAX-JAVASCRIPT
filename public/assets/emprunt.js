function hideFormulaireEmprunt(){
    document.getElementById('submitEmprunt').value="Ajouter";
    document.getElementById("formS").classList.remove("formulaire");
    document.getElementById("formS").classList.add("hideFormulaire");
    document.getElementById("clientEmprunt").value="";
    document.getElementById("materielEmprunt").value="";
    document.getElementById("dateEmprunt").value="";
    document.getElementById("dateReteur").value="";
    document.getElementById("montentTotale").value="";
}
 
 var offsete=0;
function getEmpruntData(){
getClientForEmprunt();
getMaterielForEmprunt();
nbrEmpruntParClient();
nbrMaterielClient();
var xhr=new XMLHttpRequest();
xhr.onreadystatechange=function(){
    if(this.status==200 && this.readyState==4){
        if(this.responseText!=""){
            document.getElementById("listEmprunt").innerHTML=this.responseText;
        }else{
            offsete=offsete-5;
        }
    }
}
xhr.open("POST","/getDataEmprunt/"+offsete,false);
xhr.send(null);
}
function getClientForEmprunt(){
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
document.getElementById("clientEmprunt").innerHTML=this.responseText;
document.getElementById("clientForEmpruntMateriel").innerHTML=this.responseText;
        }
    }
    xhr.open("POST","/getClientForEmprunt",false);
    xhr.send(null);
}
function getMaterielForEmprunt(){
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            document.getElementById("materielEmprunt").innerHTML=this.responseText;
        }
    }
    xhr.open("POS","/getMaterielForEmprunt",false);
    xhr.send(null);
}
function submitEmprunt(){
var client=document.getElementById("clientEmprunt").value;
var materiel=document.getElementById("materielEmprunt").value;
var dateEmprunt=document.getElementById("dateEmprunt").value;
var dateReteur=document.getElementById("dateReteur").value;
var montentTotale=document.getElementById("montentTotale").value;
var r_date=RegExp('((?:19|20)\\d\\d)-(0?[1-9]|1[012])-([12][0-9]|3[01]|0?[1-9])');

if(client!="" && materiel!=""){
  if(r_date.test(dateEmprunt)){
    if(r_date.test(dateReteur)){
       if(isFinite(montentTotale) && montentTotale!=""){
        var d1=new Date(dateEmprunt);
        var d2=new Date(dateReteur);
        if(d2>d1){
        var f=new FormData();
        f.append("client",parseInt(client));
        f.append("materiel",parseInt(materiel));
        f.append("dateEmprunt",dateEmprunt);
        f.append("dateReteur",dateReteur);
        f.append("montentTotale",montentTotale);
        f.append("id",idEmprunt);
        var xhr=new XMLHttpRequest();
        xhr.onreadystatechange=function(){
            if(this.status==200 && this.readyState==4){
                if(this.responseText=="valide"){
                    getEmpruntData();
                    hideFormulaireEmprunt();
                }
                else 
                alert(this.responseText);
            }
        }
        if(document.getElementById("submitEmprunt").value=="Moddifier")
        xhr.open("POST","/updateEmprunt",false);
        else
        xhr.open("POST","/addEmprunt",false);
        xhr.send(f);

        }else
        alert("SVP la date d'emprunt doit etre inferieur a la date de reteur !!")
       }else
       alert("SVP le montant totale doit etre un chiffre !!");
    } else
    alert("SVP le format de la date de reteur est invalide !!");  
  
  } else
  alert("SVP le format de la date d'emprunt est invalide !!");  
}else 
alert("SVP tout les champs est obligatoire !!")
}
function deleteMateriel(id){
    if(window.confirm("Voulez vous vraiment supprimer cet emprunt ?")){
        var xhr=new XMLHttpRequest();
        xhr.onreadystatechange=function(){
            if(this.status==200 && this.readyState==4){
                if(this.responseText=="valide")
                getEmpruntData();
            }
        }
        xhr.open("POST","/deleteMateriel/"+id,false);
        xhr.send(null);
    }
}
var idEmprunt=-1;
function updateEmprunt(id){
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            var data=JSON.parse(this.responseText);
            // alert(data.+data.);
            document.getElementById("formS").classList.add("formulaire");
            document.getElementById("formS").classList.remove("hideFormulaire");
            document.getElementById("clientEmprunt").value=data.client_id;
            document.getElementById("materielEmprunt").value=data.materiel_id;
            document.getElementById("dateEmprunt").value=data.dateEmprunt;
            document.getElementById("dateReteur").value=data.dateReteur;
            document.getElementById("montentTotale").value=data.montantTotale;
            document.getElementById("submitEmprunt").value="Moddifier";
            idEmprunt=id;
        }
    }
    xhr.open("GET","/GetEmprunt/"+id,false);
    xhr.send();
}
function showDetailEmprunt(id){
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            document.getElementById("detailMaterile").classList.remove("HidedetailItem");
            document.getElementById("detailMaterile").classList.add("ShoWdetailItem");            
            var data=JSON.parse(this.responseText);
            document.getElementById("imgClient_emprunt").src=data.clientPhoto;
            document.getElementById("CNI_detail_Emprunt").innerHTML=data.nom;
            document.getElementById("nom_detail_Emprunt").innerHTML=data.prenom;
            document.getElementById("prenom_detail_Emprunt").innerHTML=data.cni;
            document.getElementById("email_detail_Emprunt").innerHTML=data.tel;
            document.getElementById("tel_detail_Emprunt").innerHTML=data.gmail;
            document.getElementById("adresse_detail_Emprunt").innerHTML=data.adresse;
            document.getElementById("imgMateriel_Emprunt").src=data.materielPhoto;
            document.getElementById("matrecule_detail_Emprunt").innerHTML=data.matrecule;
            document.getElementById("designation_detail_Emprunt").innerHTML=data.designation;
            document.getElementById("prix_detail_Emprunt").innerHTML=data.prix;
            document.getElementById("date_peremption_detail_Emprunt").innerHTML=data.datePeremption;
            document.getElementById("qte_detail_Emprunt").innerHTML=data.qte;
            document.getElementById("category_detail_Emprunt").innerHTML=data.category;
            document.getElementById("dateEmprunt_detail_Emprunt").innerHTML=data.dateEmprunt;
            document.getElementById("dateReteur_detail_Emprunt").innerHTML=data.dateReteur;
            document.getElementById("montant_detail_Emprunt").innerHTML=data.montantTotale;
            document.getElementById("date_peremption_detail_Emprunt").innerHTML=data.datePeremption;            
            
        }
    } 
    xhr.open("GET","/GetEmprunt/"+id,false);
    xhr.send();
}
function PreviosEmprunt(){
    if(offsete>0){
    offsete=offsete-5;
        getEmpruntData();
    }

}
function NextEmprunt(){
    offsete=offsete+5;
    getEmpruntData();

}
function search_Emprunt(){
   var cni=document.getElementById("cni_search_emprunt").value;
   var nom=document.getElementById("nom_search_emprunt").value;
   var prenom=document.getElementById("prenom_search_emprunt").value;
   var email=document.getElementById("email_search_emprunt").value;
   var tel=document.getElementById("tel_search_emprunt").value;
   var adresse=document.getElementById("adresse_search_emprunt").value;
   var matrecule=document.getElementById("matrecule_search_emprunt").value;
   var designation=document.getElementById("designation_search_emprunt").value;
   var prix=document.getElementById("prix_search_emprunt").value;
   var date=document.getElementById("date_search_emprunt").value;
   var qte=document.getElementById("qte_search_emprunt").value;
   var category=document.getElementById("category_search_emprunt").value;
   var dateEmprunt=document.getElementById("dateEmprunt_search_emprunt").value;
   var dateReteur=document.getElementById("dateReteur_search_emprunt").value;
   var montantTotal=document.getElementById("montantTotal_search_emprunt").value;
   var f=new FormData();
   f.append("cni",cni);
   f.append("nom",nom);
   f.append("prenom",prenom);
   f.append("email",email);
   f.append("tel",tel);
   f.append("adresse",adresse);
   f.append("matrecule",matrecule);
   f.append("designation",designation);
   f.append("prix",prix);
   f.append("date",date);
   f.append("qte",qte);
   f.append("category",category);
   f.append("dateEmprunt",dateEmprunt);
   f.append("dateReteur",dateReteur);
   f.append("montantTotal",montantTotal);
   var xhr=new XMLHttpRequest();
   xhr.onreadystatechange=function(){
    if(this.status==200 && this.readyState==4){
        document.getElementById("listEmprunt").innerHTML=this.responseText;
    }
   }
   xhr.open("POST","/searchEmprunt",false);
   xhr.send(f);
}
var labels = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
  ];

  var data = {
    labels: labels,
    datasets: [{
      label: 'My First dataset',
      backgroundColor: [
        'rgba(255, 99, 132, 0.2)',
        'rgba(255, 159, 64, 0.2)',
        'rgba(255, 205, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(201, 203, 207, 0.2)'
      ],
      borderColor: [
        'rgb(255, 99, 132)',
        'rgb(255, 159, 64)',
        'rgb(255, 205, 86)',
        'rgb(75, 192, 192)',
        'rgb(54, 162, 235)',
        'rgb(153, 102, 255)',
        'rgb(201, 203, 207)'
      ],
      data: [17, 10, 5, 2, 20, 30, 45],
    }]
  };

  var config = {
    type: 'line',
    data: data,
    options: {}
  };

var myChart3 = null;
var myChart4 = null;          
function nbrEmpruntParClient(){
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            var r=JSON.parse(this.responseText);
            var data1=new Array();
            var labels1 =new Array();
            for(i=0;i<r.length;i++){
                data1.push(r[i].nbrEmprunt);
                labels1.push(r[i].nom+" "+r[i].prenom);
            }
            // console.log(data1);

            data = {
                labels: labels1,
                datasets: [{
                  label: 'Nombre des Emprunt par client',
                  backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 205, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(201, 203, 207, 0.2)'
                  ],
                  borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(255, 159, 64)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(54, 162, 235)',
                    'rgb(153, 102, 255)',
                    'rgb(201, 203, 207)'
                  ],
                  data: data1,
                }]
              };
              var config = {
                type: 'bar',
                data: data,
                options: {}
              };
              if(myChart3!=null){
                myChart3.destroy();
            }
            myChart3 = new Chart(document.getElementById('myChartEmpruntClient'),config);

        }
    }
    xhr.open("POST","/nbrEmpruntParClient",false);
    xhr.send(null);
}
function nbrMaterielClient(){
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            var r=JSON.parse(this.responseText);
            var data1=new Array();
            var labels1=new Array();
            for(i=0;i<r.length;i++){
                data1.push(r[i].nbrMaterielClient);
                labels1.push(r[i].designation);
            }
            // console.log(data1);

            data = {
                labels: labels1,
                datasets: [{
                  label: 'Nombre des Materiel emprunter',
                  backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 205, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(201, 203, 207, 0.2)'
                  ],
                  borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(255, 159, 64)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(54, 162, 235)',
                    'rgb(153, 102, 255)',
                    'rgb(201, 203, 207)'
                  ],
                  data: data1,
                }]
              };
              var config = {
                type: 'bar',
                data: data,
                options: {}
              };
              if(myChart4!=null){
                myChart4.destroy();
            }
            myChart4 = new Chart(document.getElementById('myChartEmpruntClientMateriel'),config);

        }
    }
    xhr.open("POST","/labnbrMaterielClientels",false);
    xhr.send(null);
}
function getMaterielForclinetByEmprunt(){
    var idClient=document.getElementById("clientForEmpruntMateriel").value;
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            this.responseText!=""?document.getElementById("itemLatestAddedEmprunt").innerHTML=this.responseText:document.getElementById("itemLatestAddedEmprunt").innerHTML="Aucun produit emprunter !!";
        }
    }
    xhr.open("POST","/getMaterielForclinetByEmprunt/"+idClient,false);
    xhr.send(null);
}