var pwdState = false;
var CMSState = false;

function checkPassword()
{
    pwdState = (document.getElementById("pwd1").value == document.getElementById("pwd2").value);
    document.getElementById("invia").disabled = !pwdState && CMSState;
    
    if(!pwdState) document.getElementById("pwdMisMatch").classList.remove("nascosto");
    else document.getElementById("pwdMisMatch").classList.add("nascosto");
}

function checkUser()
{
    var username = document.getElementById("userCMS");
    var password = document.getElementById("passwordCMS");
    var query = "username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password);
    
    document.getElementById("cmsStatus").src = "res/loading.gif";
    
    postUrl(urlBN() + "res/checkuser.php",
            function()
            {
                var response = JSON.parse(this.responseText);
                CMSState = response.success == 1;
                document.getElementById("invia").disabled = !pwdState && CMSState;
            
                if(CMSState) document.getElementById("cmsStatus").src = "res/icons/ok.svg";
                else document.getElementById("cmsStatus").src = "res/icons/error.svg";
            }, query);
}
