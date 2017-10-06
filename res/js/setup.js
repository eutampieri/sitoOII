var utenti=[];
var nTutor=0;
var nPDR=0;
function waitForUsers(){
    return new Promise(function(resolve,reject){
        while(utenti.length==0){
            //
        }
        resolve();
    });
}
function loadUserSearch(n){
    if(utenti.length==0){
        getUrlPromise(urlBN() + "api.php?action=classifica& first=0 & last=200").then(function(r){
            dati=JSON.parse(r).users;
            for(var i=0;i<dati.length;i++){
                utenti.push([dati[i].first_name + " " + dati[i].last_name, dati[i].username]);
                utenti.push([dati[i].username, dati[i].username])
            }
            document.getElementById("tutor"+n.toString()).disabled=false;
        });
    }
    else{
        document.getElementById("tutor"+n.toString()).disabled=false;
    }
    new Awesomplete(document.getElementById("tutor"+n.toString()),{list:utenti});
}
function loadPDRUserSearch(n){
    document.getElementById("PDR"+n.toString()).disabled=false;
    new Awesomplete(document.getElementById("PDR"+n.toString()),{list:utenti});
}
function loadWrapper() {
    laodSideBar();
    loadUserSearch(nTutor);
    loadPDRUserSearch(nPDR);
}
function serializeTutors(){
    var t=[];
    for(var i=0;i<=nTutor;i++){
        t.push(document.getElementById("tutor"+i.toString()).value);
    }
    document.getElementById("tutors").value=JSON.stringify(t);
}
function addTutor(){
    nTutor++;
    var aw=document.createElement("input");
    aw.disabled=true;
    aw.id="tutor"+nTutor.toString();
    aw.placeholder="Ricerca per nome o username";
    aw.onchange=serializeTutors;
    var li=document.createElement("li")
    li.appendChild(aw);
    document.getElementById("listaTutor").appendChild(li);
    loadUserSearch(nTutor);
}
function serializePDR(){
    var t=[];
    for(var i=0;i<=nPDR;i++){
        t.push(document.getElementById("PDR"+i.toString()).value);
    }
    document.getElementById("classpdr").value=JSON.stringify(t);
}
function addPDR(){
    nPDR++;
    var aw=document.createElement("input");
    aw.disabled=true;
    aw.id="PDR"+nPDR.toString();
    aw.placeholder="Ricerca per nome o username";
    aw.onchange=serializePDR;
    var li=document.createElement("li")
    li.appendChild(aw);
    document.getElementById("listaPDR").appendChild(li);
    loadPDRUserSearch(nPDR);
}