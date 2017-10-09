function loadPost() {
    loadSideBar();
    getUrlPromise(urlBN() + "res/api.php?action=post&id=" + encodeURIComponent(getFragment())).then(function (d) {
        var post = JSON.parse(d);
        var titolo, desc, testo, converter = new showdown.Converter({tables:true});
        titolo = document.createElement("h2");
        titolo.innerHTML = post.Titolo;
        document.getElementsByTagName("h1")[0].innerHTML = post.Titolo;
        document.getElementsByTagName("title")[0].innerHTML = "OII - Post: " + post.Titolo;
        desc = document.createElement("i");
        var d = new Date(parseInt(post.Data)*1000);
        desc.innerHTML = "Di " + post.Autore + ", " + d.toString();
        testo = document.createElement("div");
        testo.innerHTML = converter.makeHtml(post.Contenuto);
        var p = document.getElementById("p");
        p.appendChild(titolo);
        p.appendChild(desc);
        p.appendChild(testo);
    })
}
function loadPostList() {
    loadSideBar();
    getUrlPromise(urlBN() + "res/api.php?action=postList").then(function (d) {
        var lpost = JSON.parse(d);
        for (var i = 0; i < lpost.length; i++){
            var post = lpost[i], pw, titolo, desc, testo, converter = new showdown.Converter({tables:true});
            pw = document.createElement("div");
            pw.classList.add("post");
            titolo = document.createElement("h2");
            titolo.innerHTML = post.Titolo;
            desc = document.createElement("i");
            var d = new Date(parseInt(post.Data)*1000);
            desc.innerHTML = "Di " + post.Autore + ", " + d.toString();
            testo = document.createElement("div");
            testo.innerHTML = converter.makeHtml(post.Contenuto).substr(0,400)+"<br><a href=\"post.html#"+post.Data+'">Mostra tutto</a>';
            var p = document.getElementsByClassName("mainPart")[0];
            pw.appendChild(titolo);
            pw.appendChild(desc);
            pw.appendChild(testo);
            p.appendChild(pw);
        }
    })
}