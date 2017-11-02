function getMobileOS() {
    var userAgent = navigator.userAgent || navigator.vendor || window.opera;
  
    if( userAgent.match( /iPad/i ) || userAgent.match( /iPhone/i ) || userAgent.match( /iPod/i ) || ("standalone" in window.navigator) &&!window.navigator.standalone )
    {
      return 'iOS';
  
    }
    else if( userAgent.match( /Android/i ) )
    {
  
      return 'Android';
    }
    else
    {
      return 'unknown';
    }
  }
  function getOS(){
      if(getMobileOS()=="unknown"){
           if(navigator.userAgent.indexOf("Chrome") != -1 ) 
          {
              return 'Chrome-'+navigator.platform.replace(/\s+/g, '');
          }
          else if(navigator.userAgent.indexOf("Opera") != -1 )
          {
           return 'Opera-'+navigator.platform.replace(/\s+/g, '');
          }
          else if(navigator.userAgent.toLowerCase().indexOf("opr") != -1 )
          {
           return 'Opera-'+navigator.platform.replace(/\s+/g, '');
          }
          else if(navigator.userAgent.toLowerCase().indexOf("trident") != -1 )
          {
           return 'IE-'+navigator.platform.replace(/\s+/g, '');
          }
          else if(navigator.userAgent.indexOf("Firefox") != -1 ) 
          {
               return 'Firefox-'+navigator.platform.replace(/\s+/g, '');
          }
          else if((navigator.userAgent.indexOf("MSIE") != -1 ) || (!!document.documentMode == true )) //IF IE > 10
          {
            return 'IE-'+navigator.platform.replace(/\s+/g, '');
          }  
          else 
          {
             return 'unknown-on-'+navigator.platform.replace(/\s+/g, '');
          }
      }
      else{
          return getMobileOS()
      }
  }
var affects;
function getTaskUrl(task) {
    return new Promise(function(resolve,reject){
        var url
        getUrlPromise(urlBN()+"res/api.php?action=task&task="+encodeURIComponent(task)).then(function(tskJson){
            tskDta=JSON.parse(tskJson);
            url="https://training.olinfo.it/api/files/"+tskDta.statements.it+"/"+task+".pdf";
            resolve(url);
        });
    });
}
function loadSearch() {
    affects = new Awesomplete(document.querySelector("#taskName"));
}
function sAffects() {
    var ajax = new XMLHttpRequest();
    ajax.open("GET", "res/ac.php?term=" + document.getElementById("taskName").value, true);
    ajax.onload = function () {
        var list = JSON.parse(ajax.responseText);
        affects.list = list;
    };
    ajax.send();
}
function loadW() {
    load();
    loadSearch();
}
function loadTask() {
    getTaskUrl(document.getElementById("taskName").value).then(function (pdf) {
        if (getOS() == "iOS") {
            var pdo = document.getElementById("taskPDFlnk");
            pdo.classList.remove("nascosto");
            pdo.href = pdf;
        }
        else {
            var pdo = document.getElementById("taskPDF");
            pdo.classList.remove("nascosto");
            pdo.src = pdf;
        }
    });
    solvedBy();
}
function add(currentUser,tsk) {
    getUrlPromise(urlBN() + "res/api.php?action=userCMS&user=" + encodeURIComponent(currentUser.CMSUser)).then(function (r) {
        r = JSON.parse(r).scores;
        for (var j = 0; j < r.length; j++){
            if (r[j].name == tsk && r[j].score == 100) {
                li = document.createElement("li");
                li.innerHTML = currentUser.Nome + ' ' + currentUser.Cognome + ', ' + currentUser.Classe;
                document.getElementById("listaRisolto").appendChild(li);
                break;
            }
        }
    });
}
function solvedBy() {
    document.getElementById("listaRisolto").innerHTML = "";
    document.getElementById("listaRisoltoW").classList.remove("nascosto");
    var tsk = document.getElementById("taskName").value;
    getUrlPromise(urlBN() + "res/api.php?action=listaUtentiCMS").then(function (dta) {
        dta = JSON.parse(dta);
        for (var i = 0; i < dta.length; i++){
            add(dta[i],tsk);
        }
    });
}