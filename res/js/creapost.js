var filepicker;
var mde;
function mdFile(fn) {
    return new Promise(function (resolve, reject) {
        var webrequest = new XMLHttpRequest();
        webrequest.open('GET', urlBN() + "res/api.php?idFile&q=" + encodeURIComponent(fn), false);
        webrequest.send(null);
        var t=`| [![](../res/api.php?action=iconaFile&id=`+webrequest.responseText+`)](../res/api.php?action=dlFile&id=`+webrequest.responseText+`) |
        | -- |
        | `+ fn + ` |`;
        resolve(t);
    });
}
function loadFileSearch(){
    filepicker = new Awesomplete(document.getElementById("file"));
}
function cerca() {
    getUrlPromise(urlBN() + "res/api.php?action=cercaFile&q="+encodeURIComponent(document.getElementById("file").value)).then(function (r) {
        filepicker.list = JSON.parse(r);
     });
}
function aggiungi() {
    var fn = document.getElementById("file").value;
    getUrlPromise(urlBN()+"res/api.php?action=idFile&q=" + encodeURIComponent(fn)).then(function (r) {
        var md=`\n| [![](res/api.php?action=iconaFile&id=`+r+`)](res/api.php?action=dlFile&id=`+r+`) |
| -- |
| <center>`+ fn + `</center> |\n`;
        mde.codemirror.setValue(mde.codemirror.getValue()+md);
    });
}
function postTitle() {
    var md = mde.value();
    for (var i = 4; i > 0; i--){
        md=md.replace(new RegExp('\n'+"#".repeat(i)+' ','g'), '\n'+'#'.repeat(i+2)+' ');
    }
    return md.substring(0, md.search("\n")).replace("# ",'');
}
function postBody() {
    var md = mde.value();
    for (var i = 4; i > 0; i--){
        md=md.replace(new RegExp('\n'+"#".repeat(i)+' ','g'), '\n'+'#'.repeat(i+2)+' ');
    }
    var tit;
    tit=(md.substring(0, md.search("\n")));
    return md.replace(tit+"\n",'');
}
function loadWrapper() {
    loadSideBar();
    loadFileSearch();
    mde = new SimpleMDE();
}
function savePost() {
    postUrl(urlBN() + "res/api.php", function () {
        if (this.responseText == "OK") centroNotifiche.confirm("Post salvato");
        else centroNotifiche.alert(this.responseText);
    },"action=creaPost&titolo="+encodeURIComponent(postTitle())+"&contenuto="+encodeURIComponent(postBody()));
}