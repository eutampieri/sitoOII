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
            for (var i = 0; i < lista.length; i++){
                pr.push(new Promise(function (resolve, reject) {
                    getUrlPromise(urlBN() + "res/api.php?action=userCMS&user=" + encodeURIComponent(lista[i])).then(function (r) {
                        var user = JSON.parse(r);
                        resClassifica.push([user.score, user.username]);
                        resolve();
                    });
                }));
            }
            Promise.all(pr).then(function () {
                username().then(function (u) {
                    u = u.cms;
                    resClassifica.sort(function (a, b) { return b[0] - a[0] });
                    console.log(resClassifica);
                    for (var i = 0; i < resClassifica.length; i++) {
                        if (resClassifica[i][1] == u) resolve([i + 1,lista.length]);
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
        document.getElementById("userPic").src = "https://www.gravatar.com/avatar/" + md5(d.email) + "?d=identicon&s=512";
        getUrlPromise("https://api.etsrv.tk/gender?name=" + encodeURIComponent(userData.nome)).then(function (g) {
            var suffisso;
            if (g == "male") suffisso = "o";
            else suffisso = "a";
            document.getElementById("welcome").innerHTML = document.getElementById("welcome").innerHTML.replace("#sex", suffisso).replace("#nome", userData.nome);
            posClassifica().then(function (p) {
                var soglia = p[1] / 2;
                if (p[0] <= soglia) {
                    document.getElementById("badgeClassifica").classList.remove("nullBadge");
                }
                else {
                    document.getElementById("bDC").innerHTML += ": devi essere almeno " + soglia.toString() + "<sup>" + suffisso + "</sup> per ottenere il riconoscimento (sei "+p[0].toString()+"<sup>"+suffisso+"</sup>)";
                }
                if (p[0] <= p[1]/16) {
                    document.getElementById("badgeClassifica").classList.add("platino");
                    document.getElementById("bDC").innerHTML += " Maestro: sei " + +p[0].toString()+"<sup>"+suffisso+"</sup> in classifica";
                }
                else if (p[0] <= p[1]/8) {
                    document.getElementById("badgeClassifica").classList.add("oro");
                    document.getElementById("bDC").innerHTML += " Esperto: sei " + +p[0].toString()+"<sup>"+suffisso+"</sup> in classifica";
                    
                }
                else if (p[0] <= p[1]/4) {
                    document.getElementById("badgeClassifica").classList.add("argento");
                    document.getElementById("bDC").innerHTML += " Apprendista: sei " + +p[0].toString()+"<sup>"+suffisso+"</sup> in classifica";
                    
                }
                else if (p[0] <= p[1]/2) {
                    document.getElementById("badgeClassifica").classList.add("bronzo");
                    document.getElementById("bDC").innerHTML += " Novizio: sei " + +p[0].toString()+"<sup>"+suffisso+"</sup> in classifica";
                    
                }
                document.getElementById("classPos").innerHTML = p.toString() + "<sup>" + suffisso + "</sup";
            });
        });
        getUrlPromise(urlBN() + "res/api.php?action=userCMS&user=" + encodeURIComponent(userData.cms)).then(function (r) {
            var promiseTagArray = [];
            var proRis = 0;
            r = JSON.parse(r).scores;
            for (var j = 0; j < r.length; j++) {
                if (r[j].score == 100) {
                    promiseTagArray.push(new Promise(function (resolve, reject) {
                        getUrlPromise(urlBN() + "res/api.php?action=task&task=" + encodeURIComponent(r[j].name)).then(function (tskJson) {
                            var tags = [];
                            tskDta = JSON.parse(tskJson);
                            for (var i = 0; i < tskDta.tags.length; i++) {
                                tags.push(tskDta.tags[i].name);
                            }
                            resolve(tags);
                        });
                    }));    
                    proRis++;
                    var li = document.createElement("li");
                    li.classList.add("problemaRisolti");
                    li.innerHTML = r[j].title + " <br><i><pre class=\"pre-inline\">" + r[j].name + "</pre></i>";
                    document.getElementById("listaRisolti").appendChild(li);
                }
            }
            Promise.all(promiseTagArray).then(function (risultato) {
                risoltiTags = {};
                for (var i = 0; i < risultato.length; i++){
                    for (var j = 0; j < risultato[i].length; j++) {
                        if (risoltiTags[risultato[i][j]] === undefined) risoltiTags[risultato[i][j]] = 0;
                        risoltiTags[risultato[i][j]]++;
                    }
                }
                console.log(risoltiTags);
            });
            if (proRis >= 10) {
                document.getElementById("badgeNProblemi").classList.remove("nullBadge");
            }
            else {
                document.getElementById("bDNP").innerHTML += ": devi risolvere ancora " + (10 - proRis).toString() + " per ottenere il riconoscimento";
            }
            if (proRis >= 500) {
                document.getElementById("badgeNProblemi").classList.add("platino");
                document.getElementById("bDNP").innerHTML += " Maestro: hai risolto " + proRis.toString() + " problemi";
            }
            else if (proRis >= 100) {
                document.getElementById("badgeNProblemi").classList.add("oro");
                document.getElementById("bDNP").innerHTML += " Esperto: hai risolto " + proRis.toString() + " problemi";
                
            }
            else if (proRis >= 50) {
                document.getElementById("badgeNProblemi").classList.add("argento");
                document.getElementById("bDNP").innerHTML += " Apprendista: hai risolto " + proRis.toString() + " problemi";
                
            }
            else if (proRis >= 10) {
                document.getElementById("badgeNProblemi").classList.add("bronzo");
                document.getElementById("bDNP").innerHTML += " Novizio: hai risolto " + proRis.toString() + " problemi";
                
            }
            //document.getElementById("nProb").innerHTML = proRis.toString();
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