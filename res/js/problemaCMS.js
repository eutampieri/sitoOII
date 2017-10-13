function getUrlPromise(url, func) {
    return new Promise(function(resolve,reject){
    var webrequest = new XMLHttpRequest();
    webrequest.open('GET', url, true);
    webrequest.onload=function(){
        resolve(webrequest.responseText);
    };
    webrequest.send(null);});
}
function getTaskUrl(task){
    return new Promise(function(resolve,reject){
        var url
        getUrlPromise(urlBN()+"res/cmsTaskApi.php?task="+encodeURIComponent(task)).then(function(tskJson){
            tskDta=JSON.parse(tskJson);
            url="https://cms.di.unipi.it/api/files/"+tskDta.statements.it+"/"+task+".pdf";
            resolve(url);
        });
    });
}