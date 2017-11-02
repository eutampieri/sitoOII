var centroNotifiche = null;
function getFragment(){
	var tmp=location.hash;
	return tmp.replace('#','');
}
function padding(n, nZeros)
{
    var res = "";
    n = n.toString();
    var digit = n.length;
    for (var i = 0; i < nZeros - digit; i++)
        res += "0";
    res += n;
    return res;
}
function isDateSupported() {
    var input = document.createElement('input');
    input.setAttribute('type','date');

    var notADateValue = 'not-a-date';
    input.setAttribute('value', notADateValue); 

    return (input.value !== notADateValue);
}
function getUrlPromise(url) {
    return new Promise(function(resolve,reject){
    var webrequest = new XMLHttpRequest();
    webrequest.open('GET', url, true);
    webrequest.onload=function(){
        resolve(webrequest.responseText);
    };
    webrequest.send(null);});
}
function postUrlPromise(url,qry) {
    return new Promise(function(resolve,reject){
    var webrequest = new XMLHttpRequest();
    webrequest.open('POST', url, true);
    webrequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");    
    webrequest.onload=function(){
        resolve(webrequest.responseText);
    };
    webrequest.send(qry);});
}

function getUrl(url,func){
    var webrequest = new XMLHttpRequest();
    webrequest.open('GET', url, true);
    webrequest.onload=func;
    webrequest.send(null);
}
function getUrlSync(url){
    var webrequest = new XMLHttpRequest();
    webrequest.open('GET', url, false);
    webrequest.send(null);
    return webrequest.responseText;
}
function urlBN(){
    var tmp = location.pathname.split("/");
    var bn=location.protocol+"//"+window.location.hostname+document.location.pathname.replace(tmp[tmp.length-1],"");
    if (tmp[tmp.length - 2] == "admin" || tmp[tmp.length - 2] == "res") bn=bn.replace("/"+tmp[tmp.length-2],"");
    return bn;
}
function loadSideBar() {
    document.getElementsByTagName("title")[0].innerHTML = "OII - " + document.getElementsByTagName("h1")[0].innerHTML;
    if (typeof Notyf != "undefined") {
        centroNotifiche = new Notyf();
    }
    if (centroNotifiche !== null && getFragment()!="") {
        var msg = getFragment().split("&");
        if (msg[0] == "ok") {
            centroNotifiche.confirm(decodeURIComponent(msg[1]));
        }
        else {
            centroNotifiche.alert(decodeURIComponent(msg[1]));
        }
    }
    getUrlPromise(urlBN() + "res/menu.html").then(function (r) {
        document.getElementsByClassName("leftPart")[0].innerHTML = r.replace(/href="/g,'href="'+urlBN());
        
    }).then(function () {
        return getUrlPromise(urlBN() + "res/api.php?action=isTutor");
    }).then(function (r) {
        if (r == "1") {
            return getUrlPromise(urlBN() + "res/adminMenu.html");
        }
        return new Promise(function (resolve, reject) {
            resolve("");
        });
    }).then(function (r) {
        document.getElementsByClassName("menuBar")[0].innerHTML = document.getElementsByClassName("menuBar")[0].innerHTML + r.replace(/href="/g,'href="'+urlBN());
    });
}
function load(){
    loadSideBar();
}
function postUrl(url, func, data)
{
    var webrequest = new XMLHttpRequest();
    webrequest.open('POST', url, true);
    webrequest.onload=func;
    webrequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    webrequest.send(data);
}
