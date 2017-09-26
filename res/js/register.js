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
    document.getElementById("userCMS");
    document.getElementById("passwordCMS");
    
}
