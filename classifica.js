function loadClassifica(){
    load();
    var pr=[];
    for(var i = 100; i<801;i+=100){
        pr.push(getUrlP(urlBN()+"res/classificaCms.php?first="+(i-20).toString()+"&last="+i.toString(), 
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