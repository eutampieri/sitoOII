function loadCalendario() {
    var dow=["Domenica", "Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato"];
    load();
    getUrlPromise(urlBN()+"res/api.php?action=lezioni").then(function(res){
        document.getElementById("lezioni").innerHTML="";
        var dati=JSON.parse(res);
        for (var i = 0; i < dati.length; i++) {
            var evento = dati[i];
            var n=document.createElement("li");
            var inizio=new Date(evento.Inizio*1000);
            var fine=new Date(evento.Fine*1000);
            n.innerHTML = dow[inizio.getDay()]+" "+padding(inizio.getDate(), 2)+'/'+padding(inizio.getMonth(), 2)+'/'+padding(inizio.getFullYear(), 4)+" dalle "+padding(inizio.getHours(), 2)+':'+padding(inizio.getMinutes(), 2)+" alle "+padding(fine.getHours(), 2)+':'+padding(fine.getMinutes(), 2)+" &#8658; "+evento.Descrizione;
            document.getElementById("lezioni").appendChild(n);
        }
    });
    getUrlPromise(urlBN()+"res/api.php?action=gare").then(function(res){
        document.getElementById("gare").innerHTML="";
        var dati=JSON.parse(res);
        for (var i = 0; i < dati.length; i++) {
            var evento = dati[i];
            var n=document.createElement("li");
            var inizio=new Date(evento.Inizio*1000);
            var fine=new Date(evento.Fine*1000);
            n.innerHTML = dow[inizio.getDay()]+" "+padding(inizio.getDate(), 2)+'/'+padding(inizio.getMonth(), 2)+'/'+padding(inizio.getFullYear(), 4)+" dalle "+padding(inizio.getHours(), 2)+':'+padding(inizio.getMinutes(), 2)+" alle "+padding(fine.getHours(), 2)+':'+padding(fine.getMinutes(), 2)+" &#8658; "+evento.Descrizione;
            document.getElementById("gare").appendChild(n);
        }
    });
}