function hideFormulaireDegat(){
    document.getElementById("formS").classList.remove("formulaire");
    document.getElementById("formS").classList.add("hideFormulaire");   
    document.getElementById("clientDegat").value="";
    document.getElementById("materielDegat").value="";
    document.getElementById("dateDegat").value="";
    document.getElementById("coutEstimerDegat").value="";
    document.getElementById("descriptionDegat").value="";
    document.getElementById("submitEmprunt").value="Ajouter";
}
var offsetDegat=0;
function getDataDegat(){
    nbrDegatParCleint();
    getClientForDegat();
    nbrMaterielDomager();
    latestAdded();
    nbrTotaleDegat();
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            document.getElementById("listDegat").innerHTML=this.responseText;
        }
    }
    xhr.open("GET","/getDataDegat/"+offsetDegat,false);
    xhr.send(null);
}
function getClientForDegat(){
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            document.getElementById("clientDegat").innerHTML=this.responseText;
        }
    }
    xhr.open("GET","/getClientForDegat",false);
    xhr.send(null);
}
function getMaterielForEmpruntClient(){
    var token=document.getElementsByName("token")[0].value;
    var f=new FormData();
    f.append("token",token);
    var codeClient=document.getElementById("clientDegat").value;
    if(codeClient!=""){

        var xhr=new XMLHttpRequest();
        xhr.onreadystatechange=function(){
            if(this.status==200 && this.readyState==4){
                 document.getElementById("materielDegat").innerHTML=this.responseText;
            }
        }
        xhr.open("POST","/getMaterielForEmpruntClient/"+codeClient,false);
        xhr.send(f);
    }
}
function submitDegat(){
    var client=document.getElementById("clientDegat").value;
    var materiel=document.getElementById("materielDegat").value;
    var date=document.getElementById("dateDegat").value;
    var coutEstimer=document.getElementById("coutEstimerDegat").value;
    var description=document.getElementById("descriptionDegat").value;
    var token=document.getElementsByName("token")[0].value;
    var r_date=RegExp('((?:19|20)\\d\\d)-(0?[1-9]|1[012])-([12][0-9]|3[01]|0?[1-9])');

   if(client!="" && materiel!="" && description!=""){
    if(r_date.test(date)){
      if(isFinite(coutEstimer)){
        var f=new FormData();
        f.append("client",client);
        f.append("materiel",materiel);
        f.append("date",date);
        f.append("coutEstimer",coutEstimer);
        f.append("description",description);
        f.append("token",token);
        f.append("idDegat",idDegat);
       var xhr=new XMLHttpRequest();
       xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            if(this.responseText=="valide"){
                getDataDegat();
                hideFormulaireDegat();
            }
            else
            console.log(this.responseText);
        }
       }
       if(document.getElementById("submitEmprunt").value=="Ajouter")
       xhr.open("POST","/addDegat",false);
       else{
           xhr.open("POST","/updateDegat",false);
        }
       xhr.send(f);
       
      }else
       alert("SVP le cout estimer doit etre un chiffre !!");
    }else
    alert("SVP la format de date est invalide !!");
   }else
   alert("SVP tout les champs sont obligatoire !!");
}
function deleteDegat(id){
    if(window.confirm("Voulez vous vraiment supprimer cet degat !!")){
        var xhr=new XMLHttpRequest();
        xhr.onreadystatechange=function(){
            if(this.status==200 && this.readyState==4){
                if(this.responseText=="valide")
                getDataDegat();
                else
                console.log(this.responseText);
                }
        }
        xhr.open("POST","/deleteDegat/"+id,false);
        xhr.send(null);
    }
}
var idDegat=-1;
function updateDegat(id){
    idDegat=id;
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            var data=JSON.parse(this.responseText);
            document.getElementById("clientDegat").innerHTML="<option value='"+data.client_id+"'>"+data.nom+" "+data.prenom+"</option>";
            document.getElementById("materielDegat").innerHTML="<option value='"+data.empruntId+"'>"+data.designation+"</option>";
            document.getElementById("dateDegat").value=data.dateDegat;
            document.getElementById("coutEstimerDegat").value=data.coutEstimer;
            document.getElementById("descriptionDegat").value=data.description;
            document.getElementById("formS").classList.add("formulaire");
            document.getElementById("formS").classList.remove("hideFormulaire");           
            document.getElementById("submitEmprunt").value="Moddifier";
        }
    }
    xhr.open("GET","/getDegatById/"+id,false);
    xhr.send(null);
}
function showDetailDegat(id){
    document.getElementById("detailMaterile").classList.remove("HidedetailItem");
    document.getElementById("detailMaterile").classList.add("ShoWdetailItem");     
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            var data=JSON.parse(this.responseText);
            // document.getElementById("clientDegat").innerHTML="<option value='"+data.client_id+"'>"+data.nom+" "+data.prenom+"</option>";
            // document.getElementById("materielDegat").innerHTML="<option value='"+data.empruntId+"'>"+data.designation+"</option>";
            // document.getElementById("dateDegat").value=data.dateDegat;
            // document.getElementById("coutEstimerDegat").value=data.coutEstimer;
            // document.getElementById("descriptionDegat").value=data.description;
            // document.getElementById("formS").classList.add("formulaire");
            // document.getElementById("formS").classList.remove("hideFormulaire");           
             document.getElementById("description_detail_Degat").innerHTML=data.description;
             document.getElementById("coutEstimer_detail_Degat").innerHTML=data.coutEstimer;
             document.getElementById("dateDegat_detail_Degat").innerHTML=data.dateDegat;
             document.getElementById("imgClient_Degat").src=data.clientPhoto;
             document.getElementById("CNI_detail_Degat").innerHTML=data.cni;
             document.getElementById("nom_detail_Degat").innerHTML=data.nom;
             document.getElementById("prenom_detail_Degat").innerHTML=data.prenom;
             document.getElementById("email_detail_Degat").innerHTML=data.gmail;
             document.getElementById("tel_detail_Degat").innerHTML=data.tel;
             document.getElementById("adresse_detail_Degat").innerHTML=data.adresse;
             document.getElementById("imgMateriel_Degat").src=data.materielPhoto;
             document.getElementById("matrecule_detail_Degat").innerHTML=data.matrecule;
             document.getElementById("designation_detail_Degat").innerHTML=data.designation;
             document.getElementById("prix_detail_Degat").innerHTML=data.prix;
             document.getElementById("category_detail_Degat").innerHTML=data.category;
             document.getElementById("qte_detail_Degat").innerHTML=data.qte;
             document.getElementById("date_peremption_detail_Degat").innerHTML=data.datePeremption;
             document.getElementById("montant_detail_Degat").innerHTML=data.montantTotale;
             document.getElementById("dateEmprunt_detail_Degat").innerHTML=data.dateEmprunt;
             document.getElementById("dateReteur_detail_Degat").innerHTML=data.dateReteur;
}
    }
    xhr.open("GET","/getDegatById/"+id,false);
    xhr.send(null);
}
function search_Emprunt(){
    var cni=document.getElementById("cni_search_degat").value;
    var nom=document.getElementById("nom_search_degat").value;
    var prenom=document.getElementById("prenom_search_degat").value;
    var email=document.getElementById("email_search_degat").value;
    var tel=document.getElementById("tel_search_degat").value;
    var adresse=document.getElementById("adresse_search_degat").value;
    var matrecule=document.getElementById("matrecule_search_degat").value;
    var designation=document.getElementById("designation_search_degat").value;
    var prix=document.getElementById("prix_search_degat").value;
    var date=document.getElementById("date_search_degat").value;
    var qte=document.getElementById("qte_search_degat").value;
    var category=document.getElementById("category_search_degat").value;
    var dateEmprunt=document.getElementById("dateEmprunt_search_degat").value;
    var dateReteur=document.getElementById("dateReteur_search_degat").value;
    var montantTotal=document.getElementById("montantTotal_search_degat").value;
    var dateDegat=document.getElementById("dateDegat_search_degat").value;
    var coutEsitmer=document.getElementById("coutEsitmer_search_degat").value;
    var description=document.getElementById("description_search_degat").value;
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
    f.append("dateDegat",dateDegat);
    f.append("coutEsitmer",coutEsitmer);
    f.append("description",description);
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            document.getElementById("listDegat").innerHTML=this.responseText;
        }
    }
    xhr.open("POST","/searchDegat",false);
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

var myChart5 = null;
var myChart6 = null;          

function nbrDegatParCleint(){
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.readyState==4 && this.status==200){
            var data=JSON.parse(this.responseText);
            var data1=new Array();
            var labels1=new Array();
            for(i=0;i<data.length;i++){
                data1.push(data[i].nbrDegat);
                labels1.push(data[i].nom+" "+data[i].prenom);   
            }
            data = {
                labels: labels1,
                datasets: [{
                  label: 'Nombre des degat par client',
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
              if(myChart5!=null){
                myChart5.destroy();
            }
            myChart5 = new Chart(document.getElementById('myChartDegatClient'),config);
   
        }
    }
    xhr.open("GET","/nbrDegatParClient",false);
    xhr.send();
}
function nbrMaterielDomager(){
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            var data=JSON.parse(this.responseText);
            var data1=new Array();
            var labels1=new Array();
            for(i=0;i<data.length;i++){
                data1.push(data[i].nbrDomager);
                labels1.push(data[i].designation);
            }
            data = {
                labels: labels1,
                datasets: [{
                  label: 'Nombre des Materiel Dommager',
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
              if(myChart6!=null){
                myChart6.destroy();
            }
            myChart6 = new Chart(document.getElementById('myChartMaterieleDomager'),config);

        }
    }
    xhr.open("GET","/nbrMaterielDomager",false);
    xhr.send();
}
function nbrTotaleDegat(){
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            document.getElementById("nbrTotalDegat").innerHTML=this.responseText;
        }
    }
    xhr.open("GET","/nbrTotaleDegat",false);
    xhr.send(null);
}
function latestAdded(){
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            // document.getElementById("nbrTotalDegat").innerHTML=this.responseText;
            document.getElementById("itemLatestAddedEmprunt").innerHTML=this.responseText;
        }
    }
    xhr.open("GET","/LatestAddedDegat",false);
    xhr.send(null);
    
}
function getAdmin(){
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            var data=JSON.parse(this.responseText);
            document.getElementById("nomAdminUpdate").value=data.nom;
            document.getElementById("prenomAdminCompt").value=data.prenom;
            document.getElementById("emailAdminCompt").value=data.email;
            document.getElementById("pwAdminCompt").value=data.pw;
        }
    }
    xhr.open("GET","/getAdmin",false);
    xhr.send();
}
function updateAdmin(){
    var nom=document.getElementById("nomAdminUpdate").value;
    var prenom=document.getElementById("prenomAdminCompt").value;
    var email=document.getElementById("emailAdminCompt").value;
    var pw=document.getElementById("pwAdminCompt").value;
    var r_email=/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    if(nom!="" && prenom!="" && email!="" && pw!=""){  
         if(email.match(r_email)){
          var f=new FormData();
          f.append("nom",nom);
          f.append("prenom",prenom);
          f.append("email",email);
          f.append("pw",pw);
          if(document.getElementById("photoAdminUpdate").files.length>0)
           f.append("photo",document.getElementById("photoAdminUpdate").files[0])
           var xhr=new XMLHttpRequest();
           xhr.onreadystatechange=function(){
            if(this.status==200 && this.readyState==4){
                alert(this.responseText);
                if(this.responseText=="Compt moddifier avec success !!"){
                    window.location.reload();
                }
            }
           }
           xhr.open("POST","/updateAdmin",false);
           xhr.send(f);
         }else 
             alert("SVP le format de gmail est invalide !!");
    }else
    alert("Pour moddifier votre compt , tout les champs sont obligatoire !!");
}