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
function classificaCMS(n) {
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
/*function loadClassifica() {
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
                            var n = document.createElement("li");
                            var ui = document.createElement("img");
                            ui.src = "https://www.gravatar.com/avatar/" + dati[j].mail_hash + "?d=identicon&s=100";
                            ui.classList.add("classAvatar");
                            n.appendChild(ui);
                            getUrlPromise(urlBN() + "res/api.php?action=userDetailByCMS&user=" + encodeURIComponent(dati[j].username)).then(function (r) {
                                var userData = JSON.parse(r);
                                n.innerHTML += userData.nome + " " + userData.cognome + "(" + userData.classe + "), " + dati[j].score.toString() + " punti"
                                ar.push(n);
                                return (ar);
                            });
                        }
                    }
                }
            ));
        }
        Promise.all(pr).then(function (values) {
            document.getElementById("classifica").innerHTML = "";
            for (var i = 0; i < values.length; i++) {
                for (var j = 0; j < values[i].length; j++) {
                    document.getElementById("classifica").appendChild(values[i][j]);
                }
            }
        });
    });
}*/
function loadClassifica() {
    load();
    var classArray = {};
    classArray[0] = "Ciao";
    var last = 0;
    listaUtentiRegistrati().then(function (lista) {
        new Promise(function (resolve, reject) {
            for (var i = 100; i < 801; i += 100) {
                getUrlPromise(urlBN() + "res/api.php?action=classifica&first=" + (i - 100).toString() + "&last=" + i.toString()).then(function (d) {
                    var dati = JSON.parse(d).users;
                    for (var j = 0; j < dati.length; j++) {
                        if (lista.indexOf(dati[j].username) !== -1) {
                            var n = document.createElement("li");
                            var ui = document.createElement("img");
                            ui.src = "https://www.gravatar.com/avatar/" + dati[j].mail_hash + "?d=identicon&s=100";
                            ui.classList.add("classAvatar");
                            n.appendChild(ui);
                            var CMSuserData = dati[j];
                            getUrlPromise(urlBN() + "res/api.php?action=userDetailByCMS&user=" + encodeURIComponent(dati[j].username)).then(function (r) {
                                console.log(CMSuserData);
                                var userData = JSON.parse(r);
                                if (userData !== null) {
                                    n.innerHTML += userData.nome + " " + userData.cognome + "(" + userData.classe + "), " + CMSuserData.score.toString() + " punti"
                                }
                                else {
                                    n.innerHTML += CMSuserData.first_name + " " + CMSuserData.last_name + ", " + CMSuserData.score.toString() + " punti";
                                }
                                classArray[last] = n;
                                last++;
                                //document.getElementById("classifica").appendChild(n);
                            });
                        }
                        if (i == 800 && j == dati.length - 1) {
                            resolve("");
                        }
                    }
                });
            }
        }).then(function () {
            console.log(classArray);
        });
    });
}