var pwdState = false;
var CMSState = false;
var UState = false;

function checkPassword()
{
    pwdState = (document.getElementById("pwd1").value == document.getElementById("pwd2").value);
    document.getElementById("invia").disabled = !(pwdState && CMSState && UState);
    var score = zxcvbn(document.getElementById("pwd1").value).score;
    switch(score){
        case 0:
        document.getElementById("meter").style.width="20%";
        document.getElementById("meter").style.backgroundColor="#C23235";
        break;
        case 1:
        document.getElementById("meter").style.width="40%";
        document.getElementById("meter").style.backgroundColor="#D97B32";
        break;
        case 2:
        document.getElementById("meter").style.width="60%";
        document.getElementById("meter").style.backgroundColor="#E3DE39";
        break;
        case 3:
        document.getElementById("meter").style.width="80%";
        document.getElementById("meter").style.backgroundColor="#91D95C";
        break;
        case 4:
        document.getElementById("meter").style.width="100%";
        document.getElementById("meter").style.backgroundColor="#287A32";
        break;
    }
    
    if(!pwdState) document.getElementById("pwdMisMatch").classList.remove("nascosto");
    else document.getElementById("pwdMisMatch").classList.add("nascosto");
}

function checkUser()
{
    var username = document.getElementById("userCMS").value;
    var password = document.getElementById("passwordCMS").value;
    var query = "username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password);
    
    document.getElementById("cmsStatus").src = "res/icons/loading.gif";
    
    postUrl(urlBN() + "res/checkUser.php",
            function()
            {
                var response = JSON.parse(this.responseText);
                CMSState = response.success == 1;
                document.getElementById("invia").disabled = !(pwdState && CMSState && UState);
            
                if(CMSState) document.getElementById("cmsStatus").src = "res/icons/ok.svg";
                else document.getElementById("cmsStatus").src = "res/icons/error.svg";
            }, query);
}
function deDupeUser()
{
    var username = document.getElementById("userName").value;
    var query = "username=" + encodeURIComponent(username);

    document.getElementById("userStatus").src = "res/icons/loading.gif";

    postUrl(urlBN() + "res/userExists.php",
            function()
            {
                UState = this.responseText === "0";
                document.getElementById("invia").disabled = !(pwdState && CMSState && UState);
                if(UState) document.getElementById("userStatus").src = "res/icons/ok.svg";
                else document.getElementById("userStatus").src = "res/icons/error.svg";
            }, query);
}
