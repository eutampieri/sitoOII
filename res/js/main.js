function getUrlP(url, func) {
    return new Promise(function(resolve,reject){
    var webrequest = new XMLHttpRequest();
    webrequest.open('GET', url, true);
    webrequest.onload=function(){
        resolve(func(webrequest));
    };
    webrequest.send(null);});
}
function getUrl(url,func){
    var webrequest = new XMLHttpRequest();
    webrequest.open('GET', url, true);
    webrequest.onload=func;
    webrequest.send(null);
}
function urlBN(){
    var tmp=location.pathname.split("/");
    return location.protocol+"//"+window.location.hostname+document.location.pathname.replace(tmp[tmp.length-1],"");
}
function loadSideBar(){
    getUrl(urlBN()+"menu.html",function(){
        document.getElementsByClassName("leftPart")[0].innerHTML=this.responseText;
    });
}
function load(){
    loadSideBar();
}