<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<?php
require_once "config.php";

// Checking connection
if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

if($_SERVER["REQUEST_METHOD"] == "POST"){


}

$result = mysqli_query($link, "SELECT * FROM tanks");
$all = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Tankistan</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="speech-bubbles.css">
    <script src="fittext.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
</head>
<body>
    <div id="top">
        <div class="title"><h1 id="text_title">Tankistan</h1></div>
        <a class="logout" href="logout.php">Log out</a>
        <div id="button_wrapper">
            <div class="row_buttons">
                <div id="top_button" class="ctrl_button"><img src="static/arrow_up.png" alt="arrow_up" class="button_img" onclick="sendData(1)"></img></div>
            </div>
            <div class="row_buttons">
               <div id="left_button" class="ctrl_button"><img src="static/arrow_left.png" alt="arrow_left" class="button_img" onclick="sendData(3)"></img></div>
               <div id="center_button" class="ctrl_button"></div>
               <div id="right_button" class="ctrl_button"><img src="static/arrow_right.png" alt="arrow_right" class="button_img" onclick="sendData(4)"></img></div>
            </div>
            <div class="row_buttons">
                <div id="bottom_button" class="ctrl_button"><img src="static/arrow_down.png" alt="arrow_down" class="button_img" onclick="sendData(2)"></img></div>
            </div>
        </div>
    </div>
    <div id="battlefield" class="zoom">
        <div id="grid"></div>
    </div>
    <div id="popup-bubble" class="speech bottom" style="visibility: hidden;">
        <button onclick="attack()">Attack</button>
        <button onclick="aid()">Give action</button>
    </div>

    <div id="help_popout"><embed type="text/html" src="help_menu.html"  width="500" height="200"></div>
    <div id="help" onclick="showHelp()">?</div>
    
    
</body>
<!--<script src="textFit.js"></script>-->
<!--
<script type="text/javascript">

    let top_div = document.getElementById("top");
    let battlefield = document.getElementById("battlefield");

    console.log((top_div.clientHeight + 10).toString() + "px")
    battlefield.style.marginTop = (top_div.clientHeight + 10).toString() + "px";

</script>
-->
<script type="text/javascript">
    document.addEventListener("keydown", (e) => {
        console.log("tes")
        if (e.key == "ArrowUp"){sendData(1)}
        else if (e.key == "ArrowLeft"){sendData(3)}
        else if (e.key == "ArrowRight"){sendData(4)}
        else if (e.key == "ArrowDown"){sendData(2)};
            
    }, false);


    function showHelp(){
        let help_menu = document.getElementById("help_popout");
        console.log(help_menu.style.visibility)
        if (!help_menu.style.visibility || help_menu.style.visibility == "hidden"){
            help_menu.style.visibility = "visible";
        } else {
            help_menu.style.visibility = "hidden";
        }
    };


    var username = "<?php echo $_SESSION["username"]; ?>"

    class Tank {
        constructor(tank_name, x, y, actions, health, bullet_range) {
        this.x = x;
        this.y = y;
        this.tank_name = tank_name;
        this.actions = actions;
        this.health = health;
        this.rgb_color = null;
        this.bullet_range = bullet_range;
    }
    }    

    function textCoords(x , y){
        return "x" + x.toString() + "y" + y.toString();
    }


    var all_tanks_data = <?php echo json_encode($all); ?>;

    //var columns = 20;
    //var rows = 20;
    var game_data = <?php echo file_get_contents("game-data.txt"); ?>;
    var columns = rows = game_data["size"];

    var tanks = {};
    var user_tank = null;

    var cell_popup_data = null;

    for (let i = 0; i < all_tanks_data.length; i++) {
        let tank_data = all_tanks_data[i]
        
        let tank = new Tank(tank_data["name"], parseInt(tank_data["x"]), parseInt(tank_data["y"]), parseInt(tank_data["actions"]), parseInt(tank_data["health"]), parseInt(tank_data["bullet_range"]));
        tanks[textCoords(tank.x, tank.y)] = tank;
        if (tank.tank_name == username){
            user_tank = tank;
        };
    };



    const grid = document.getElementById("grid")
    for (let y = 0; y < rows; y++) {
        for (let x = 0; x < columns; x++) {
            let cell = document.createElement("div");
            cell.className = "cell";
            cell.id = textCoords(x, y);
            let tank = tanks[cell.id]
            if (tank){
                //let text = document.createElement("p");
                //text.innerHTML = tank.tank_name + "<br>" + String.fromCodePoint(0x27BC).repeat(tank.actions);
                //cell.appendChild(text)
                //textFit(text);
                cell.innerHTML = String.fromCodePoint(0x2764).repeat(tank.health) + "<br>" + tank.tank_name + "<br>" + tank.actions.toString() + "x" + String.fromCodePoint(0x27BC);
                
                //
                //cell.onclick= function(){console.log("test")};
                //cell.onclick= function(){attack(tank)};
                if (tank.tank_name == user_tank.tank_name){
                    cell.style.backgroundColor = "rgba(50, 168, 105, 1)";
                }  else {
                    cell.onclick = function(){update_popup(tank, cell)}
                    cell.style.backgroundColor = "rgba(168, 50, 80, 1)";
                };
            } else if (user_tank.x>=0 && Math.abs(x-user_tank.x) <= user_tank.bullet_range && Math.abs(y-user_tank.y) <= user_tank.bullet_range){
                cell.style.backgroundColor = "rgba(88, 203, 231, 1)";
            } /*else if (user_tank.x>=0 && Math.abs(x-user_tank.x) <= 2 && Math.abs(y-user_tank.y) <= 2){
                cell.style.backgroundColor = "rgba(43, 93, 105, 1)";

            } */
            

            grid.appendChild(cell);
        }
    };

    grid.style.gridTemplateColumns = "repeat("+columns.toString()+", minmax(0, 1fr))";
    grid.style.gridTemplateRows = "repeat("+rows.to
    

    document.addEventListener("DOMContentLoaded", (event) => {
        for (var cell of grid.children) {
        let tank = tanks[cell.id];
        if (tank){

            
        cell.style.fontSize = (cell.clientWidth/tank.tank_name.length).toString()+"px";
        }
        }
    });


    function sendData(id, other=null){
        if (window.XMLHttpRequest)    //  Objet standard
        { 
            xhr = new XMLHttpRequest();     //  Firefox, Safari, ...
        } 
        else  if (window.ActiveXObject)      //  Internet Explorer
        {
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
        };
        
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                //console.log(this.responseText);
                if (this.responseText != "Not allowed"){
                    location.reload();
                }
            }
        };


        //var data = "user=" + user + "&new_x=" + new_x + "&new_y=" + new_y + "&new_actions=" + new_actions + "&new_health=" + new_health;
        var data = "id=" + id;
        if (other){data += "&other="+other}
        xhr.withCredentials = true;
        xhr.open("POST", "send-data.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  
        xhr.send(data);

    };

    function update_tank(tank){
        sendData(tank.tank_name, tank.x, tank.y, tank.actions, tank.health);
    }

    function button_click(x_add, y_add){
        console.log(user_tank.actions, user_tank.x + x_add, user_tank.y + y_add)
        
        if (user_tank.actions == 0){
            return;
        }

        if (tanks[textCoords(user_tank.x + x_add, user_tank.y + y_add)]){
            return;
        }


        if (user_tank.x + x_add < 0 || user_tank.x + x_add >= columns || user_tank.y + y_add < 0 || user_tank.y + y_add >= rows){
            return;
        }
        user_tank.x += x_add;
        user_tank.y += y_add;
        user_tank.actions -= 1;

        update_tank(user_tank);
    };

    function update_popup(tank, cell){
        if (cell_popup_data){
        console.log(cell_popup_data[0].tank_name, tank.tank_name)}
        if (cell_popup_data && cell_popup_data[0].tank_name == tank.tank_name){
            let popup = document.getElementById("popup-bubble");
            popup.style.visibility = "hidden";
            cell_popup_data = null;
        } else {
            cell_popup_data = [tank, cell];
            update_popup_pos();
        }
    };

    function update_popup_pos(){
        let popup = document.getElementById("popup-bubble");
        if (!cell_popup_data){
            return;
        } else {
            let [target_tank, cell] = cell_popup_data;
            let popupRect = popup.getBoundingClientRect();
            let cellRect = cell.getBoundingClientRect();
            popup.style.visibility = "visible";
            let trueHeight = popupRect.bottom - popupRect.top;
            //let trueHeight = popupRect.height
            popup.style.top = (cellRect.top - trueHeight*1.1 - popupRect.height).toString()+"px";
            popup.style.left = (cellRect.left-popupRect.width/2+cellRect.width/2).toString()+"px";
        }
        //popup.classList.add("my-class");
    };

    window.addEventListener('resize', update_popup_pos);

    function aid(){
        if (user_tank.actions == 0){
            return;
        }
        other_tank = cell_popup_data[0];

        sendData(6, other_tank.tank_name);

        /*user_tank.actions -= 1;
        other_tank.actions += 1;
        update_tank(user_tank);
        update_tank(other_tank);*/
    };

    function attack(){
        console.log("flag");
        if (user_tank.actions == 0){
            return;
        }

        /*if (!tanks[textCoords(x, y)]){
            return;
        }

        other_tank = tanks[textCoords(x, y)]

        if (enemy_shot.tank_name == user_tank.tank_name){
            return;
        }*/
        other_tank = cell_popup_data[0];

        /*user_tank.actions -= 1;
        other_tank.health -= 1;
        if (other_tank.health==0){
            other_tank.x = -1;
            other_tank.y = -1;

        }*/
        sendData(5, other_tank.tank_name);

    }




</script>
<script>
    const body = document.body;
    const battlefield = document.getElementById("battlefield");
    const top_div = document.getElementById("top");

    battlefield.style.top = top_div.getBoundingClientRect().height+"px";
    //body.style.cursor = 'grab';

    /*let pos = { top: 0, left: 0, x: 0, y: 0 };

    const mouseDownHandler = function (e) {
        body.style.cursor = 'grabbing';
        body.style.userSelect = 'none';

        pos = {
            left: window.scrollX,
            top: window.scrollY,
            // Get the current mouse position
            x: e.clientX,
            y: e.clientY,
        }; 


        document.addEventListener('mousemove', mouseMoveHandler);
        document.addEventListener('mouseup', mouseUpHandler);
    };

    const mouseMoveHandler = function (e) {
        // How far the mouse has been moved
        let dx = e.clientX - pos.x;
        let dy = e.clientY - pos.y;

        // Scroll the element
        //window.scrollTo(pos.left - dx, pos.top - dy)

    };

    const mouseUpHandler = function () {
        body.style.cursor = 'grab';
        body.style.removeProperty('user-select');

        document.removeEventListener('mousemove', mouseMoveHandler);
        document.removeEventListener('mouseup', mouseUpHandler);
    };

    body.addEventListener('mousedown', mouseDownHandler); */

    function dragElement(elmnt) {
        var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
        if (document.getElementById(elmnt.id + "header")) {
            // if present, the header is where you move the DIV from:
            document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
        } else {
            // otherwise, move the DIV from anywhere inside the DIV:
            elmnt.onmousedown = dragMouseDown;
        }

      function dragMouseDown(e) {
        e = e || window.event;
        e.preventDefault();
        // get the mouse cursor position at startup:
        pos3 = e.clientX;
        pos4 = e.clientY;
        document.onmouseup = closeDragElement;
        // call a function whenever the cursor moves:
        document.onmousemove = elementDrag;
      }

      function elementDrag(e) {
        e = e || window.event;
        e.preventDefault();
        // calculate the new cursor position:
        pos1 = pos3 - e.clientX;
        pos2 = pos4 - e.clientY;
        pos3 = e.clientX;
        pos4 = e.clientY;
        // set the element's new position:
        elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
        elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";

        update_popup_pos();
      }

        function closeDragElement() {
            // stop moving when mouse button is released:
            document.onmouseup = null;
            document.onmousemove = null;
        }
    }

    dragElement(battlefield)

    const zoomElement = document.querySelector(".zoom");
    let zoom = 1;
    const ZOOM_SPEED = 0.1;

    document.addEventListener("wheel", function(e) {  
        if(event.ctrlKey == true)
        {
            e.preventDefault();
            if(e.deltaY > 0){    
                zoomElement.style.transform = `scale(${zoom -= ZOOM_SPEED})`;

            }else{    
                zoomElement.style.transform = `scale(${zoom += ZOOM_SPEED})`;
            }
            update_popup_pos();
        }
    }, {passive: false});
</script>

</html>
