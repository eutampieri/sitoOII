function padding(n, nZeros)
{
    var res = "";
    n = n.toString();
    var digit = n.length;
    for (var i = 0; i < nZeros - digit; i++)
        res += "0";
    res += n;
    return res;
}

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
            n.innerHTML = dow[inizio.getDay()]+" "+padding(inizio.getDate(), 2)+'/'+padding(inizio.getMonth(), 2).toString()+'/'+padding(inizio.getFullYear(), 4).toString()+" dalle "+padding(inizio.getHours(), 2).toString()+':'+padding(inizio.getMinutes(), 2).toString()+" alle "+padding(fine.getHours(), 2).toString()+':'+padding(fine.getMinutes(), 2).toString()+" &#8658; "+evento.Descrizione;
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
            n.innerHTML=dow[inizio.getDay()]+" "+inizio.getDate().toString()+'/'+inizio.getMonth().toString()+'/'+inizio.getFullYear().toString()+" dalle "+inizio.getHours().toString()+':'+inizio.getMinutes().toString()+" alle "+fine.getHours().toString()+':'+fine.getMinutes().toString()+" &#8658; "+evento.Descrizione;
            document.getElementById("gare").appendChild(n);
        }
    });
}