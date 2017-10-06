var inizio = new Date;
var fine = new Date;
let cfields = ["inizio", "fine", "descrizione"];
let qfields = ["descrizione", "tipo"];
var centroNotifiche;
function clearEvents() {
    for (var f in cfields) {
        document.getElementById(cfields[f]).value = "";
    }
}
function loadEv() {
    centroNotifiche = new Notyf();
    loadSideBar();
    clearEvents();
    flatpickr(document.getElementById("inizio"), { time_24hr: true, enableTime: true, dateFormat: "d/m/Y H:i", onChange: function(a,b,c){inizio=a[0];} });
    flatpickr(document.getElementById("fine"), { time_24hr: true, enableTime: true, dateFormat: "d/m/Y H:i", onChange: function(a,b,c){fine=a[0];} });
}
function aggiungiEvento() {
    var qry = "action=addEvent";
    for (var f in qfields) {
        qry += '&' + qfields[f] + '=' + encodeURIComponent(document.getElementById(qfields[f]).value);
    }
    qry += "&inizio=" + (inizio.getTime() / 1000).toString();
    qry += "&fine=" + (fine.getTime() / 1000).toString();
    postUrlPromise(urlBN() + "res/api.php", qry).then(function (r) {
        if (r == "OK") centroNotifiche.confirm("Evento aggiunto");
        else centroNotifiche.alert("Errore: " + r);
    });
    clearEvents();
}