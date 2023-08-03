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
$all = mysqli_fetch_all($result);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Tankistan</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <div id="top">
        <div class="title"><h1 id="text_title">Tankistan</h1></div>
        <a class="logout" href="logout.php">Log out</a>
        <div id="button_wrapper">
            <div class="row_buttons">
                <div id="top_button" class="ctrl_button"><img src="static/arrow_up.png" alt="arrow_up" class="button_img" onclick="button_click(0, -1)"></img></div>
            </div>
            <div class="row_buttons">
               <div id="left_button" class="ctrl_button"><img src="static/arrow_left.png" alt="arrow_left" class="button_img" onclick="button_click(-1, 0)"></img></div>
               <div id="center_button" class="ctrl_button"></div>
               <div id="right_button" class="ctrl_button"><img src="static/arrow_right.png" alt="arrow_right" class="button_img" onclick="button_click(1, 0)"></img></div>
            </div>
            <div class="row_buttons">
                <div id="bottom_button" class="ctrl_button"><img src="static/arrow_down.png" alt="arrow_down" class="button_img" onclick="button_click(0, 1)"></img></div>
            </div>
        </div>
    </div>
    <div id="battlefield">
        <div id="grid"></div>
    </div>
    
    
</body>
<script src="textFit.js"></script>
<!--
<script type="text/javascript">

    let top_div = document.getElementById("top");
    let battlefield = document.getElementById("battlefield");

    console.log((top_div.clientHeight + 10).toString() + "px")
    battlefield.style.marginTop = (top_div.clientHeight + 10).toString() + "px";

</script>
-->
<script type="text/javascript">
    var username = "<?php echo $_SESSION["username"]; ?>"

    class Tank {
        constructor(tank_name, x, y, actions, health) {
        this.x = x;
        this.y = y;
        this.tank_name = tank_name;
        this.actions = actions;
        this.health = health;
        this.rgb_color = null;
    }
    }    

    function textCoords(x , y){
        return "x" + x.toString() + "y" + y.toString();
    }


    console.log("test");

    var columns = 10;
    var rows = 30;

    var all_tanks_data = <?php echo json_encode($all); ?>;
    var tanks = {};
    var user_tank = null;

    for (let i = 0; i < all_tanks_data.length; i++) {
        let tank_data = all_tanks_data[i]
        let tank = new Tank(tank_data[1], parseInt(tank_data[2]), parseInt(tank_data[3]), parseInt(tank_data[4]), parseInt(tank_data[5]));
        tanks[textCoords(tank.x, tank.y)] = tank;
        if (tank.tank_name == username){
            user_tank = tank;
        };
    };




    console.log(all_tanks_data)

    grid = document.getElementById("grid")
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
                cell.onclick= function(){attack(x, y)};
                if (tank.tank_name == user_tank.tank_name){
                    cell.style.backgroundColor = "rgba(50, 168, 105, 1)";
                }  else {
                    cell.style.backgroundColor = "rgba(168, 50, 80, 1)";
                };
            } else if (Math.abs(x-user_tank.x) <= 1 && Math.abs(y-user_tank.y) <= 1){
                cell.style.backgroundColor = "rgba(38, 153, 181, 1)";
            } else if (Math.abs(x-user_tank.x) <= 2 && Math.abs(y-user_tank.y) <= 2){
                cell.style.backgroundColor = "rgba(43, 93, 105, 1)";

            } 
            

            grid.appendChild(cell);
        }
    };

    grid.style.gridTemplateColumns = "repeat("+columns.toString()+", minmax(0, 1fr))"
    grid.style.gridTemplateRows = "repeat("+rows.toString()+", auto)"

    function sendData(user, new_x, new_y, new_actions, new_health){
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
              location.reload();
            }
        };


        var data = "user=" + user + "&new_x=" + new_x + "&new_y=" + new_y + "&new_actions=" + new_actions + "&new_health=" + new_health;
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
    }

    function attack(x, y){
        console.log("flag")
        if (user_tank.actions == 0){
            return;
        }

        if (!tanks[textCoords(x, y)]){
            return;
        }

        enemy_shot = tanks[textCoords(x, y)]

        if (enemy_shot.tank_name == user_tank.tank_name){
            return;
        }


        user_tank.actions -= 1;
        enemy_shot = tanks[textCoords(x, y)]
        enemy_shot.health -= 1;
        if (enemy_shot.health==0){
            enemy_shot.x = -1;
            enemy_shot.y = -1;

        }
        update_tank(user_tank);
        update_tank(enemy_shot);

    }




</script>

</html>
