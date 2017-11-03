function loadTutors() {
    var isDirty = true;
    load();
    getUrlPromise(urlBN() + "res/api.php?action=tutorMD5").then(function (jsonTutorHash) {
        var tutors = JSON.parse(jsonTutorHash);
        for (var i = 0; i < tutors.length; i++){
            getUrlPromise(urlBN() + "res/api.php?action=gravatarProfile&hash=" + tutors[i]).then(function (datiGravatarJson) {
                if (isDirty) {
                    isDirty = false;
                    document.getElementsByClassName("mainPart")[0].innerHTML = "";
                }
                var gravatar = JSON.parse(datiGravatarJson).entry[0];
                var maindiv = document.createElement("div");;
                maindiv.classList.add("tutor");
                var nome = document.createElement("h2");
                nome.classList.add("tutorName");
                nome.innerHTML = gravatar.name.formatted;
                maindiv.appendChild(nome);
                var foto = document.createElement("img");
                foto.classList.add("tutorImage");
                foto.src = "https://gravatar.com/avatar/"+gravatar.requestHash+"?d=identicon&s=200";
                maindiv.appendChild(foto);
                var bio = document.createElement("img");
                bio.classList.add("tutorDesc");
                bio.innerHTML = gravatar.aboutMe;
                maindiv.appendChild(bio);
                document.getElementsByClassName("mainPart")[0].appendChild(maindiv);
            });
        }
    });
}