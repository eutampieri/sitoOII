var utenti=[];
var nTutor=0;
function getUrlPromise(url) {
    return new Promise(function(resolve,reject){
    var webrequest = new XMLHttpRequest();
    webrequest.open('GET', url, true);
    webrequest.onload=function(){
        resolve(webrequest.responseText);
    };
    webrequest.send(null);});
}
function loadUserSearch(n){
    if(utenti.length==0){
        getUrlPromise(urlBN()+"classificaCms.php?first=0&last=200").then(function(r){
            dati=JSON.parse(r).users;
            for(var i=0;i<dati.length;i++){
                utenti.push([dati[i].first_name + " " + dati[i].last_name, dati[i].username]);
                utenti.push([dati[i].username, dati[i].username])
            }
            document.getElementById("tutor"+n.toString()).disabled=false;
        });
    }
    new Awesomplete(document.getElementById("tutor"+n.toString()),{list:utenti});
}
function loadWrapper(){
    getUrl(urlBN()+"menu.html",function(){
        document.getElementsByClassName("leftPart")[0].innerHTML=this.responseText.replace(/href="/g,'href="../');
    });
    loadUserSearch(nTutor);
}