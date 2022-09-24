function switchLoginBtn(d){
    document.querySelectorAll(".containerLoginForm")[0].style.left=d;
    // document.getElementById("containerLoginForm").style.left="100%";
}
function Inscrire(){
    var photo=document.getElementById("photo").files[0];
    var prenom=document.getElementById("prenom").value;
    var email=document.getElementById("email").value;
    var pw=document.getElementById("pw").value;
    var nom=document.getElementById("nom").value;
    var f=new FormData();
    f.append("photo",photo);    
    f.append("prenom",prenom);    
    f.append("email",email);    
    f.append("pw",pw);    
    f.append("nom",nom);    
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
         if(this.status==200 && this.readyState==4){

             if(this.responseText=="Inscription avec success !!"){
                 document.querySelectorAll(".containerLoginForm")[0].style.left='0';
                 document.getElementById("prenom").value="";
                 document.getElementById("email").value="";
                 document.getElementById("pw").value="";
                 document.getElementById("nom").value="";
                }
                alert(this.responseText);
            }
    }
    xhr.open("POST","/inscription",false);
    xhr.send(f);
}
function connexion(){
    var email=document.getElementById('emailCnx').value;
    var pw=document.getElementById('pwCnx').value;
    var f=new FormData();
    f.append("email",email);
    f.append("pw",pw);
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){

            alert(this.responseText);
            if(this.responseText=="Connxion avec succes")
             window.location.href="/materiel";
        }
          
    }
    xhr.open("POST","/connxion",false);
    xhr.send(f);
}
var menu=false;
function showHideMenu(){
    menu=!menu;
    menu==false?document.querySelectorAll(".itemBodyMenu")[0].style.left="-100%":document.querySelectorAll(".itemBodyMenu")[0].style.left="0";
}
function HideMenu(url){
    menu=!menu;
    document.querySelectorAll(".itemBodyMenu")[0].style.left="-100%";
    window.location.href=url;
}
function Quitter(){
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4)
         alert(this.responseText);
         window.location.href="/";
    }
    xhr.open("POST","/quite",false);
    xhr.send(null);
}
function hideForm(){
    // hideFormulaire
    //formulaire
    // formS
    id=-1;
        // formS
document.getElementById("matrecule_search").value="";
document.getElementById("designation_search").value="";
document.getElementById("prix_search").value="";
document.getElementById("date_search").value="";
document.getElementById("qte_search").value="";
document.getElementById("category_search").value="";
    document.getElementById("matrecule").value="";
    document.getElementById("designation").value="";
    document.getElementById("prix").value="";
    document.getElementById("date_peremption").value="";
    document.getElementById("qte").value="";
    document.getElementById("category").value="";
    document.getElementById("submit").value="Ajouter";
    
    getDataMateriel();
    document.getElementById("formS").classList.remove("formulaire");
    document.getElementById("formS").classList.add("hideFormulaire");

    // if(document.getElementById("formS").classList[0]=="formulaire"){
    //     document.getElementById("formS").classList.remove("formulaire");
    //     document.getElementById("formS").classList.add("hideFormulaire");
    // }else{
    //     document.getElementById("formS").classList.remove("hideFormulaire");
    //     document.getElementById("formS").classList.add("formulaire");
    // }
    
}
function showFormulaire(){
    document.getElementById("formS").classList.remove("hideFormulaire");
    document.getElementById("formS").classList.add("formulaire");
}

document.getElementById("submit").addEventListener('click',(e)=>{
if(e.target.value=="Moddifier"){
    var matrecule=document.getElementById("matrecule").value;
    var designation=document.getElementById("designation").value;
    var prix=document.getElementById("prix").value;
    var date_peremption=document.getElementById("date_peremption").value;
    var qte=document.getElementById("qte").value;
    var category=document.getElementById("category").value;
    var photo=document.getElementById("photo").files.length==1?document.getElementById("photo").files[0]:null;
    var r_date=RegExp('((?:19|20)\\d\\d)-(0?[1-9]|1[012])-([12][0-9]|3[01]|0?[1-9])');
    if(matrecule!="" &&  designation!="" && category!=""){
        if(r_date.test(date_peremption)){
         if(isFinite(prix)){
           if(isFinite(qte)){
            var f=new FormData();
            f.append("designation",designation);
            f.append("prix",prix);
            f.append("date_peremption",date_peremption);
            f.append("qte",qte);
            f.append("category",category);
            f.append("photo",photo);
            f.append("matrecule",matrecule);
            f.append("id",id)
           var xhr =new XMLHttpRequest();
           xhr.onreadystatechange=function(){
            if(this.readyState==4 && this.status==200){
                if(this.responseText=="Materiel Modffier avec success !!")
                hideForm();
                alert(this.responseText);
                window.location.reload();   
            }
           }
           xhr.open("POST","/UpdateMateriel",false);
           xhr.send(f);       
        }else
        alert("SVP la qentiter doit etre un ciffre !!");
          
      }else 
      alert("SVP le prix doit etre un chiffre !!");
     }
     else
     alert("date de peremption est invalide !!");
     }else 
     alert("SVP tout les champs sont obligatoire !! 22")
     }else if(e.target.value=="Ajouter"){
var matrecule=document.getElementById("matrecule").value;
var designation=document.getElementById("designation").value;
var prix=document.getElementById("prix").value;
var date_peremption=document.getElementById("date_peremption").value;
var qte=document.getElementById("qte").value;
var category=document.getElementById("category").value;
var photo=document.getElementById("photo").files[0];
var r_date=RegExp('((?:19|20)\\d\\d)-(0?[1-9]|1[012])-([12][0-9]|3[01]|0?[1-9])');
if(matrecule!="" &&  designation!="" && category!="" && document.getElementById("photo").files.length!=0){
if(r_date.test(date_peremption)){
 if(isFinite(prix)){
   if(isFinite(qte)){
    var f=new FormData();
    f.append("designation",designation);
    f.append("prix",prix);
    f.append("date_peremption",date_peremption);
    f.append("qte",qte);
    f.append("category",category);
    f.append("photo",photo);
    f.append("matrecule",matrecule);
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            if(this.responseText=="Mteriel ajouter avec success !!")
            hideForm();
            alert(this.responseText);
        }
    }
    xhr.open("POST","/AddMatrecule",false);
    xhr.send(f);
   }else
   alert("SVP la qentiter doit etre un ciffre !!");
     
 }else 
 alert("SVP le prix doit etre un chiffre !!");
}
else
alert("date de peremption est invalide !!");
}else 
alert("SVP tout les champs sont obligatoire !!")

}    
})
var offsete=0;
function getDataMateriel(){
    getChart1();
    getChart2();
    getLatestMaterielAdd();
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.readyState==4 && this.status==200)
        if(this.responseText!=""){
            document.getElementById("listMateriel").innerHTML=this.responseText;
        }else
        offsete=offsete-5;
        //  alert(this.responseText);
    }
    xhr.open("POST","/getData/"+offsete,false);
    xhr.send(null);
}
function Previos(){
    if(offsete!=0){
        offsete=offsete-5;
        getDataMateriel();
    }
}
function Next(){
    offsete=offsete+5;
    getDataMateriel();
}
function deleteMateriel(id){
    if(confirm("Voulez vous vraiment supprimer cet materiel ?")){
        var xhr=new XMLHttpRequest();
        xhr.onreadystatechange=function(){
            if(this.readyState==4 && this.status==200){
            if(this.responseText=="valide")
             getDataMateriel();
             else
             alert(this.responseText);
            }
        }
        xhr.open("POST","/deleteMateriel/"+id,false);
        xhr.send(null);
    }
}
var id=-1;
function updateMateriel(idd){
   var xhr=new XMLHttpRequest();
   xhr.onreadystatechange=function(){
    if(this.readyState==4 && this.status==200){
        var r=JSON.parse(this.responseText);
        document.getElementById("matrecule").value=r.matrecule;
        document.getElementById("designation").value=r.designation;
        document.getElementById("prix").value=r.prix;
        document.getElementById("date_peremption").value=r.datePeremption;
        document.getElementById("qte").value=r.qte;
        document.getElementById("category").value=r.category;
        document.getElementById("submit").value="Moddifier";
        document.getElementById("formS").classList.remove("hideFormulaire");
        document.getElementById("formS").classList.add("formulaire");
    }
   }
   xhr.open("POST","/getMateriel/"+idd,false);
   xhr.send(null);
   id=idd;
}
function search_materiel(){ 
var matrecule_search=document.getElementById("matrecule_search").value;
var designation_search=document.getElementById("designation_search").value;
var prix_search=document.getElementById("prix_search").value;
var date_search=document.getElementById("date_search").value;
var qte_search=document.getElementById("qte_search").value;
var category_search=document.getElementById("category_search").value;
var f=new FormData();
f.append("matrecule",matrecule_search);
f.append("designation",designation_search);
f.append("prix",prix_search);
f.append("date",date_search);
f.append("qte",qte_search);
f.append("category",category_search);
var xhr=new XMLHttpRequest();
xhr.onreadystatechange=function(){
    if(this.readyState==4 && this.status==200)
    document.getElementById("listMateriel").innerHTML=this.responseText;

}
xhr.open("POST","/searchMateriel",false);
xhr.send(f);
}
function showDetailMateriel(id){
var xhr=new XMLHttpRequest();
xhr.onreadystatechange=function(){
 if(this.readyState==4 && this.status==200){
     var r=JSON.parse(this.responseText);
     document.getElementById("matrecule_detail").innerHTML=r.matrecule;
     document.getElementById("designation_detail").innerHTML=r.designation;
     document.getElementById("prix_detail").innerHTML=r.prix;
     document.getElementById("date_peremption_detail").innerHTML=r.datePeremption;
     document.getElementById("qte_detail").innerHTML=r.qte;
     document.getElementById("category_detail").innerHTML=r.category;
     document.getElementById("imgDetail").src=r.photo;
     document.getElementById("detailMaterile").classList.remove("HidedetailItem");
     document.getElementById("detailMaterile").classList.add("ShoWdetailItem"); 
 }
}
xhr.open("POST","/getMateriel/"+id,false);
xhr.send(null);

}
function hideDetailForm(){
    document.getElementById("detailMaterile").classList.add("HidedetailItem");
    document.getElementById("detailMaterile").classList.remove("ShoWdetailItem");
    
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

var myChart = null;
var myChart2 = null;          

function getChart1(){
var xhr=new XMLHttpRequest();
xhr.onreadystatechange=function(){
    if(this.status==200 && this.readyState==4){
        var r=JSON.parse(this.responseText);
        // alert(r[0].category);
        var data1=new Array();
        var labels1=new Array();
        for(i=0;i<r.length;i++){
            data1.push(r[i].data);
            labels1.push(r[i].category);
        }
        data = {
            labels: labels1,
            datasets: [{
              label: 'Nombre des materiele par categorie',
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
          if(myChart!=null){
            myChart.destroy();
        }
        myChart = new Chart(document.getElementById('myChart1'),config);    
    }
}
xhr.open("GET","/nbrMaterilParCategory",false);
xhr.send(null);
}
function getChart2(){
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.readyState==4 && this.status==200){
           var r=JSON.parse(this.responseText);
            var data1=new Array();
            var labels1=new Array();
            for(i=0;i<r.length;i++){
                data1.push(r[i].dataMois);
                labels1.push(r[i].mois);
            }
            
            data = {
                labels: labels1,
                datasets: [{
                  label: 'Nombre des materiel ex',
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
              if(myChart2!=null){
                myChart2.destroy();
            }
            myChart2 = new Chart(document.getElementById('myChart2'),config);
            
        }
    }
    xhr.open("GET","/nbrMaterielExpirer",false);
    xhr.send(null);

}
function getLatestMaterielAdd(){
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
        if(this.status==200 && this.readyState==4){
            document.getElementById('itemLatestAdded').innerHTML=this.responseText;
        }
    }
    xhr.open("GET","/getLatestMaterielAdd",false);
    xhr.send(null);
}

function getPassword(){
  var email=document.getElementById("emailCnx").value;
  if(email!=""){
    var f=new FormData();
    f.append("email",email);
    var xhr=new XMLHttpRequest();
    xhr.onreadystatechange=function(){
      if(this.status==200 && this.readyState==4){
          alert(this.responseText);
      }
    }
    xhr.open("POST","/passwordForEmail",false);
    xhr.send(f);
  
  }else
   alert("SVP saisi votre email ?");
}