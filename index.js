var express = require("express");
var app = express();
var https = require("http").createServer(app).listen(process.env.PORT || 3000);

var fs = require("fs")
var bodyParser = require('body-parser');
var multer = require('multer');
var upload = multer();
const path = require('path')
const { v4: uuidV4 } = require("uuid");
var session = require('express-session')
var mysql = require('mysql')

var acc = require("./account");
// var room = require("./room");

var dbConfig = {
    host: "us-cdbr-east-03.cleardb.com",
    user: "b834813d1e3930",
    password: "486d8737",
    database: "heroku_9b13e198b68e574"
}

var connection;
function handleDisconnect() {
    connection = mysql.createConnection(dbConfig);
    connection.connect(function (err) {
        if (err) {
            console.log('error when connecting to db:', err);
            setTimeout(handleDisconnect, 2000);
        }
    });
    connection.on('error', function (err) {
        console.log('db error', err);
        if (err.code === 'PROTOCOL_CONNECTION_LOST') {
            handleDisconnect();
        } else {
            throw err;
        }
    });
}

var io = require("socket.io")(https,{cors: {
    origin: "https://gamvip88.net",
    methods: ["GET", "POST"]
  }});


app.use(express.urlencoded({
    extended: true
}))
app.use(express.json());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: false }))
app.set('view engine', 'ejs')
app.set('views', './views')
app.use(express.static("public"))
app.use(express.static('node_modules'))
app.use(session({
    secret: 'this-is-a-secret-token',
    resave: true,
    saveUninitialized: true,
    cookie: {
        maxAge: 60000,
    }
}));
app.use(function(req, res, next) {
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    next();
  });
app.use(function (req, res, next) {
    if (req.session.user != null) {
        res.locals.user = req.session.user;
    }

    next();
});
console.log("start server....");
app.get('/', (req, res) => {
    res.render("index")
})
app.post("/login", (req, res) => {
    var username = req.body.username;
    var pass = req.body.password;
    acc.login(connection, username, pass, (rs) => {
        if (rs.length != 0) {
            req.app.set('log', "")
            req.session.user = rs[0];
            //res.json(rs);
            console.log(rs)
            res.redirect("/");
        }
        else {
            req.app.set('log', "Sai tai khoan hoac mat khau")
            res.redirect("/");
        }

    });
})
managerPeer = {}
accountL = []
app.get("/home",(req,res)=>{
    res.render("home")
})
io.on("connection",(socket)=>{
    console.log("connect "+socket.id);
    socket.on("mlog",()=>{
        console.log("mlog");
        managerPeer[socket.id]=socket;
    })
    socket.on("getAllAcc",()=>{
        console.log("get All");
        socket.emit("returnAll",accountL)
    })
    socket.on("loginchange",(data)=>{
        console.log("login Change");
        console.log(data);
        data.socket = socket.id
        accountL.push(data)
        for(id in managerPeer){
            managerPeer[id].emit("loginchange",accountL)
        }
    })
    socket.on("logout",(skid)=>{
       io.to(skid).emit("logout");
       accountL = accountL.filter(item => item.socket !== value)
    })
    socket.on("disconnect",()=>{
        console.log("disconnect "+socket.id);
        delete managerPeer[socket.id]
        
    })
})
handleDisconnect();