function listaUtentiRegistrati() {
    return new Promise(function (resolve, reject) {
        var l = [];
        getUrlPromise(urlBN() + "res/api.php?action=listaUtentiCMS").then(function (o) {
            o = JSON.parse(o);
            for (var i = 0; i < o.length; i++){
                l.push(o[i].CMSUser);
            }
            return getUrlPromise(urlBN() + "res/api.php?action=rifClassifica");
        }).then(function (o) {
            o = JSON.parse(o);
            for (var i = 0; i < o.length; i++){
                l.push(o[i]);
            }
            resolve(l);
        })
    });
}
function aggiungiaClassifica(dati) {
    return new Promise(function (resolve, reject) {
        getUrlPromise(urlBN() + "res/api.php?action=userDetailByCMS&user=" + encodeURIComponent(dati.username)).then(function (r) {
            var n = document.createElement("li");
            var ui = document.createElement("img");
            ui.src = "https://www.gravatar.com/avatar/" + dati.mail_hash + "?d=identicon&s=100";
            ui.classList.add("classAvatar");
            n.appendChild(ui);
            var userData = JSON.parse(r);
            if (userData !== null) {
                n.innerHTML += userData.nome + " " + userData.cognome + " (" + userData.classe + "), " + dati.score.toString() + " punti"
            }
            else {
                n.innerHTML += dati.first_name + " " + dati.last_name + ", " + dati.score.toString() + " punti";
            }
            resolve([n,dati.score]);
        });
    });
}
function getUrlP(url, func) {
    return new Promise(function(resolve,reject){
    var webrequest = new XMLHttpRequest();
    webrequest.open('GET', url, true);
    webrequest.onload=function(){
        resolve(func(webrequest));
    };
    webrequest.send(null);});
}
function loadClassifica() {
    var resClassifica = [];
    load();
    listaUtentiRegistrati().then(function (lista) {
        var pr = [];
        for (var i = 0; i < lista.length; i++) {
            pr.push(new Promise(function (resolve, reject) {
                getUrlPromise(urlBN() + "res/api.php?action=userCMS&user=" + encodeURIComponent(lista[i])).then(function (r) {
                    var user = JSON.parse(r);
                    aggiungiaClassifica(user).then(function (n) { resClassifica.push(n); resolve();});
                });
            }));
        }
        Promise.all(pr).then(function (values) {
            document.getElementById("classifica").innerHTML = "";
            resClassifica.sort(function(a,b){return b[1]-a[1]});
            for (var i = 0; i < resClassifica.length; i++) {
                console.log(resClassifica[i]);
                document.getElementById("classifica").appendChild(resClassifica[i][0]);
            }
        });
    });
}