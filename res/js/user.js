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

function getUrlP(url, func) {
    return new Promise(function(resolve,reject){
    var webrequest = new XMLHttpRequest();
    webrequest.open('GET', url, true);
    webrequest.onload=function(){
        resolve(func(webrequest));
    };
    webrequest.send(null);});
}
function username() {
    return new Promise(function (resolve, reject) {
        getUrlPromise(urlBN() + "res/api.php?action=thisUserInfo").then(function (r) {
            r = JSON.parse(r);
            if (r == null) location = urlBN() + "login.html#err&Per%20vedere%20questa%20pagina%20devi%20entrare";
            resolve(r);
        })
    });
}
function posClassifica() {
    return new Promise(function (resolve, reject) {
        var resClassifica = [];
        listaUtentiRegistrati().then(function (lista) {
            var pr = [];
            for (var i = 100; i < 801; i += 100) {
                if (lista.length == resClassifica.length) break;
                pr.push(getUrlP(urlBN() + "res/api.php?action=classifica&first=" + (i - 100).toString() + "&last=" + i.toString(),
                    function (wr) {
                        if (lista.length == resClassifica.length) return [];
                        var ar = [];
                        var dati = JSON.parse(wr.responseText).users;
                        for (var j = 0; j < dati.length; j++) {
                            if (lista.indexOf(dati[j].username) !== -1) {
                                resClassifica.push([dati[j].score, dati[j].username]);
                            }
                        }
                    }
                ));
            }
            Promise.all(pr).then(function (values) {
                username().then(function (u) {
                    u = u.cms;
                    resClassifica.sort(function (a, b) { return b[0] - a[0] });
                    console.log(resClassifica);
                    for (var i = 0; i < resClassifica.length; i++) {
                        if (resClassifica[i][1] == u) resolve(i + 1);
                    }
                    resolve(null);
                });
            });
        });
    });    
}
function loadUserPage() {
    load();
    var userData;
    username().then(function (d) {
        userData = d;
        getUrlPromise("https://api.etsrv.tk/gender?name=" + encodeURIComponent(userData.nome)).then(function (g) {
            posClassifica().then(function (p) {
                var suffisso;
                if (g == "male") suffisso = "o";
                else suffisso = "a";
                document.getElementById("classPos").innerHTML = p.toString() + "<sup>" + suffisso + "</sup";
            });
        });
        getUrlPromise(urlBN() + "res/api.php?action=userCMS&user=" + encodeURIComponent(userData.cms)).then(function (r) {
            var proRis = 0;
            r = JSON.parse(r).scores;
            for (var j = 0; j < r.length; j++) {
                if (r[j].score == 100) {
                    proRis++;
                    var li = document.createElement("li");
                    li.classList.add("problemaRisolti");
                    li.innerHTML = r[j].title + " <br><i><pre class=\"pre-inline\">" + r[j].name + "</pre></i>";
                    document.getElementById("listaRisolti").appendChild(li);
                }
            }
            document.getElementById("nProb").innerHTML = proRis.toString();
        });
    });
}
function eliminaAccount() {
    postUrlPromise(urlBN() + "res/api.php?action=eliminaAccount", "password=" + encodeURIComponent(document.getElementById("del-password").value)).then(function (risposta) {
        if (risposta == "OK") {
            centroNotifiche.confirm("Account eliminato con successo!");
        }
        else {
            centroNotifiche.alert("Errore: "+risposta);
        }
    })
}