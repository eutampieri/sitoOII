function getUrl(url,func){
    var webrequest = new XMLHttpRequest();
    webrequest.open('GET', url, true);
    webrequest.onload=func;
    webrequest.send(null);
}
function getUrlP(url,func){
    return new Promise(function(resolve,reject){
    var webrequest = new XMLHttpRequest();
    webrequest.open('GET', url, true);
    webrequest.onload=function(){
        resolve(func(webrequest));
    };
    webrequest.send(null);});
}
function getUrlSync(url,func){
    var webrequest = new XMLHttpRequest();
    webrequest.open('GET', url, false);
    webrequest.onload=func;
    webrequest.send(null);
}
function urlBN(){
    var tmp=location.pathname.split("/");
    return location.protocol+"//"+window.location.hostname+document.location.pathname.replace(tmp[tmp.length-1],"");
}
function postUrl(url,data,func){
    console.log(JSON.stringify(data));
    var webrequest = new XMLHttpRequest();
    webrequest.open('POST', url, true);
    webrequest.setRequestHeader("Content-type", "application/json");
    webrequest.onload=func;
    webrequest.send(JSON.stringify(data));
}
function loadClassifica(){
    var pr=[];
    for(var i = 20; i<801;i+=20){
        pr.push(getUrlP(urlBN()+"classificaCms.php?first="+(i-20).toString()+"&last="+i.toString(), 
            function(wr){
                var ar=[];
                var dati=JSON.parse(wr.responseText).users;
                for(var j=0;j<dati.length;j++){
                    if(dati[j].institute.id==5155){
                        var n=document.createElement("li");
                        var ui=document.createElement("img");
                        ui.src="https://www.gravatar.com/avatar/"+dati[j].mail_hash+"?d=identicon&s=100";
                        ui.classList.add("classAvatar");
                        n.appendChild(ui);
                        n.innerHTML+=dati[j].first_name+" "+dati[j].last_name+", "+dati[j].score.toString()+" punti"
                        ar.push(n);
                    }
                }
                return(ar);
            }
           ));
    }
    Promise.all(pr).then(function(values){
        document.getElementById("classifica").innerHTML="";
        for(var i=0;i<values.length;i++){
            for(var j=0;j<values[i].length;j++){
                document.getElementById("classifica").appendChild(values[i][j]);
            }
        }
    });
}