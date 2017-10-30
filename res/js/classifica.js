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
function loadClassifica() {
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
                            n.innerHTML += dati[j].first_name + " " + dati[j].last_name + ", " + dati[j].score.toString() + " punti"
                            ar.push(n);
                        }
                    }
                    return (ar);
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
}