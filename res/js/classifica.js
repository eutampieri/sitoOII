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
        for (var i = 100; i < 801; i += 100) {
            pr.push(getUrlP(urlBN() + "res/api.php?action=classifica&first=" + (i - 100).toString() + "&last=" + i.toString(),
                function (wr) {
                    var ar = [];
                    var dati = JSON.parse(wr.responseText).users;
                    for (var j = 0; j < dati.length; j++) {
                        if (lista.indexOf(dati[j].username) !== -1) {
                            aggiungiaClassifica(dati[j]).then(function (node) {
                                resClassifica.push(node);
                            });
                        }
                    }
                }
            ));
        }
        Promise.all(pr).then(function (values) {
            document.getElementById("classifica").innerHTML = "";
            resClassifica.sort(function(a,b){return b[1]-a[1]});
            for (var i = 0; i < resClassifica.length; i++) {
                document.getElementById("classifica").appendChild(resClassifica[i][0]);
            }
        });
    });
}