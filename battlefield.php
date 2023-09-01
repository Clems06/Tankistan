<?php 

if (!(defined('index_check')) And index_check){
    exit();
}

require "get-logs.php";
$splitted = explode("<br>", $string_logs);
$last_update = sizeof($splitted)-2;

require "get-map.php";
?>



<div id="battlefield">
        <div id="grid"></div>
</div>
<div id="popout-bubble" class="speech bottom" style="visibility: hidden;">
    <h3 id="selected-name">None</h3>
    <button onclick="attack()">Attack</button>
    <button onclick="aid()">Give action</button>
</div>

<div id="help_popout"><embed type="text/html" src="static/help_menu.html"  width="500" height="200"></div>
<div id="help" onclick="showHelp()">?</div>

<div id="bulletbox">
    <div id="bullet" style="visibility: hidden;"></div>
</div>

<script type="text/javascript">
    document.addEventListener("keydown", (e) => {
        if (e.key == "ArrowUp"){sendData(1)}
        else if (e.key == "ArrowLeft"){sendData(3)}
        else if (e.key == "ArrowRight"){sendData(4)}
        else if (e.key == "ArrowDown"){sendData(2)};
            
    }, false);


    function showHelp(){
        let help_menu = document.getElementById("help_popout");
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


    var all_tanks_data = <?php echo json_encode($visible); ?>;

    //var columns = 20;
    //var rows = 20;
    var columns = rows = <?php echo $game["size"]; ?>;
    var map = "<?php echo $game["map"]; ?>";

    var tanks = {};
    var user_tank = null;

    var cell_popup_data = null;


    function process_tanks(){
        tanks = {};
        for (let i = 0; i < all_tanks_data.length; i++) {
            let tank_data = all_tanks_data[i];
            
            let tank = new Tank(tank_data["name"], parseInt(tank_data["x"]), parseInt(tank_data["y"]), parseInt(tank_data["actions"]), parseInt(tank_data["health"]), parseInt(tank_data["bullet_range"]));
            tanks[textCoords(tank.x, tank.y)] = tank;
            if (tank.tank_name == username){
                user_tank = tank;
            };
        };
    }
    process_tanks();



    const grid = document.getElementById("grid");

    function map_cell(x, y){
        return map[x + y*columns];
    }

    function check_if_visible(target_x, target_y, player_x, player_y)
    {
        let x0 = player_x;
        let y0 = player_y;
        let x1 = target_x;
        let y1 = target_y;

        let dx = Math.abs(x1 - x0);
        let sx;
        if (x0 < x1){
            sx = 1;
        } else{
            sx = -1;
        }

        let dy = -Math.abs(y1 - y0);
        let sy;
        if (y0 < y1){
            sy = 1;
        } else{
            sy = -1;
        }

        let e = dx + dy;
        let e2;

        while (x0 != x1 || y0 != y1) {
            e2 = 2*e;
            if (e2 >= dy){
                //echo "test1";
                if (x0 == x1){
                    //echo "broke1";
                    break;
                }
                e = e + dy;
                x0 = x0 + sx;
            }
            if (e2 <= dx){
                //echo "test2";
                if (y0 == y1){
                    //echo "broke2";
                    break;
                }
                e = e + dx;
                y0 = y0 + sy;
            }

            if (map_cell(x0, y0)=="W"){
                return false;
            }
        }
        return true;
    }

    function paint_cell_tank(cell, tank){
        cell.innerHTML =String.fromCodePoint(0x2764).repeat(tank.health) + "<br>" +tank.actions.toString() + "x" + String.fromCodePoint(0x27BC);
                    
        if (tank.tank_name == user_tank.tank_name){
            cell.style.backgroundColor = "rgba(50, 168, 105, 1)";
            //cell.style.backgroundImage = 'url("../static/good.png")';
        }  else {
            cell.onclick = function(){update_popup(tank, cell)}
            cell.style.backgroundColor = "rgba(168, 50, 80, 1)";
        };
    };

    function paint_grid(){
        grid.innerHTML = "";

        for (let y = 0; y < rows; y++) {
            for (let x = 0; x < columns; x++) {

                let cell = document.createElement("div");
                cell.className = "cell";
                cell.id = textCoords(x, y);
                let tank = tanks[cell.id];
                let obstacle = map_cell(x, y);
                let visible = check_if_visible(x, y, user_tank.x, user_tank.y);
                if (tank && tank.health){
                    paint_cell_tank(cell, tank);
                } else if (obstacle == "W"){
                    cell.style.backgroundColor = "rgba(79, 79, 79, 1)";
                } else if (obstacle == "R"){
                    cell.style.backgroundColor = "rgba(3, 111, 252, 1)";
                } else if (visible && user_tank.x>=0 && Math.abs(x-user_tank.x) <= user_tank.bullet_range && Math.abs(y-user_tank.y) <= user_tank.bullet_range){
                    cell.style.backgroundColor = "rgba(88, 203, 231, 1)";
                } 

                if (!visible && obstacle != "W"){
                    cell.style.opacity = "0.1";
                }     

                grid.appendChild(cell);
            }
        };

    };
    paint_grid();

    grid.style.gridTemplateColumns = "repeat("+columns.toString()+", minmax(0, 1fr))";
    grid.style.gridTemplateRows = "repeat("+rows.toString()+", minmax(0, 1fr))";
    
    function adjust_grid(grid){
        grid.style.fontSize = (grid.firstChild.clientHeight/5).toString()+"px";
    }
    document.addEventListener("DOMContentLoaded", (event) => {
        adjust_grid(grid);
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
                    //location.reload();
                    make_action(user_tank.tank_name, id, other);
                    
                    if (id<=4){
                        let new_tanks_data = JSON.parse(this.responseText);
                        /*for (let i in new_tanks_data) {
                            let tank = new_tanks_data[i];

                            if (tank["name"] == username){
                                continue;
                            }

                            console.log(tank);
                            let object = new Tank(tank["name"], parseInt(tank["x"]), parseInt(tank["y"]), parseInt(tank["actions"]), parseInt(tank["health"]), parseInt(tank["bullet_range"]));
                            let coords = textCoords(object.x, object.y);
                            console.log(coords);
                            if (!all_tanks_data[coords]){
                                let cell = document.getElementById(coords);
                                paint_cell_tank(cell, object);
                            }
                        }*/
                        globalThis.all_tanks_data = new_tanks_data;
                        console.log(all_tanks_data);
                        process_tanks();
                    }
                }
            }
        };


        //var data = "user=" + user + "&new_x=" + new_x + "&new_y=" + new_y + "&new_actions=" + new_actions + "&new_health=" + new_health;
        var data = "id=" + id + "&game=" + "<?php echo $game['name'] ?>";
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
        if (cell_popup_data && cell_popup_data[0].tank_name == tank.tank_name){
            let popout = document.getElementById("popout-bubble");
            popout.style.visibility = "hidden";
            cell_popup_data = null;
        } else {
            cell_popup_data = [tank, cell];
            update_popup_pos();
        }
    };

    function update_popup_pos(){
        let popout = document.getElementById("popout-bubble");
        if (!cell_popup_data){
            return;
        } else {
            let [target_tank, cell] = cell_popup_data;
            popout.style.visibility = "visible";
            let popupRect = popout.getBoundingClientRect();
            let cellRect = cell.getBoundingClientRect();
            let namediv = document.getElementById("selected-name");
            namediv.innerHTML = target_tank.tank_name;
            
            let trueHeight = popupRect.height;
            popout.style.top = (cellRect.top - popout.offsetHeight - 70).toString()+"px";
            popout.style.left = (cellRect.left-popupRect.width/2+cellRect.width/2).toString()+"px";
        }
    };

    window.addEventListener('resize', update_popup_pos);

    function aid(){
        if (user_tank.actions == 0){
            return;
        }
        other_tank = cell_popup_data[0];

        sendData(6, other_tank.tank_name);
    };

    function attack(){
        if (user_tank.actions == 0){
            return;
        }

        other_tank = cell_popup_data[0];

        sendData(5, other_tank.tank_name);

    }




</script>
<script>
    const body = document.body;
    const battlefield = document.getElementById("battlefield");
    const top_div = document.getElementById("top");


    battlefield.style.top = top_div.getBoundingClientRect().height+"px";

    function dragElement(elmnt) {
        var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
        var scaling = false;
        var last_dist = 0;
        if (document.getElementById(elmnt.id + "header")) {
            // if present, the header is where you move the DIV from:
            document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
        } else {
            // otherwise, move the DIV from anywhere inside the DIV:
            elmnt.onmousedown = dragMouseDown;
            elmnt.ontouchstart = mobileSart;
        }

        function mobileSart(e){
            pos3 = e.clientX;
            pos4 = e.clientY;

            if (e.touches.length === 2) {
                scaling = true;
                last_dist = Math.hypot(
                    e.touches[0].pageX - e.touches[1].pageX,
                    e.touches[0].pageY - e.touches[1].pageY);
            }

            document.addEventListener("touchmove", mobileDrag, {passive: false});
            document.ontouchend = mobileStop;
        }

        function mobileDrag(e){
            e = e || window.event;

            e.preventDefault();
            //e.preventDefault();

            if (scaling) {

                let touchLocation1 = e.touches[0];
                let touchLocation2 = e.touches[1];

                let dist = Math.hypot(
                    touchLocation1.pageX - touchLocation2.pageX,
                    touchLocation1.pageY - touchLocation2.pageY);

                let midx = (touchLocation1.pageX + touchLocation2.pageX)/2;
                let midy = (touchLocation1.pageY + touchLocation2.pageY)/2;

                zoom_on_point(midx, midy, dist/last_dist);

                adjust_grid(grid);
                update_popup_pos();

                last_dist = dist;



            } else {
                let touchLocation = e.targetTouches[0];
                // calculate the new cursor position:
                pos1 = pos3 - touchLocation.pageX;
                pos2 = pos4 - touchLocation.pageY;
                pos3 = touchLocation.pageX;
                pos4 = touchLocation.pageY;
                // set the element's new position:
                elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
                elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";

                update_popup_pos();
            }


            
        }

        function mobileStop(e){
            if (scaling) {
                scaling = false;
            }
            document.touchmove = null;
            document.touchstop = null;
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
    

    dragElement(battlefield);


    //const zoomElement = document.querySelector(".zoom");
    let zoom = 1;
    const ZOOM_SPEED = 10;
    const ZOOM_SPEED2 = 0.9;

    function zoom_on_point(pointx, pointy, zoom_scale){

        let dx = pointx - battlefield.getBoundingClientRect().left;
        let dy = pointy - battlefield.getBoundingClientRect().top;

        let width = parseFloat(battlefield.style.width.slice(0, -2) || battlefield.offsetWidth);

        battlefield.style.width = (width*zoom_scale).toString()+"px";

        battlefield.style.left = pointx - dx * zoom_scale + "px";
        battlefield.style.top = pointy - dy * zoom_scale + "px";

    }

    document.addEventListener("wheel", function(e) {  
        //if(event.ctrlKey == true)
        
        e.preventDefault();
        let mouse_x = e.clientX;
        let mouse_y = e.clientY;
        
        if(e.deltaY > 0){    
            zoom_on_point(mouse_x, mouse_y, ZOOM_SPEED2);
            //zoomElement.style.top = (zoomElement.getBoundingClientRect().top/ZOOM_SPEED2).toString()+"px";

        }else{    
            zoom_on_point(mouse_x, mouse_y, 1/ZOOM_SPEED2);
            //zoomElement.style.top = (zoomElement.getBoundingClientRect().top*ZOOM_SPEED2).toString()+"px";
        }


        
        adjust_grid(grid);
        update_popup_pos();
    }, {passive: false});
</script>
<script>
    var last_update = <?php echo $last_update?>;

    function tank_by_name(name){
        let tank_values = Object.values(tanks);
        for (var i = 0; i < tank_values.length; i++) {
            if (tank_values[i].tank_name == name){
                return tank_values[i];
            }
        }
    }

    const sleep = (delay) => new Promise((resolve) => setTimeout(resolve, delay))

    function make_action(tank_name, id, other= null){
        let tank = tank_by_name(tank_name);

        tank.actions -= 1;
        
        if (id <= 4){
            let new_x = tank.x;
            let new_y = tank.y;
            if (id == 1){
                new_y = tank.y - 1;
            } else if (id == 2) {
                new_y = tank.y + 1;
            } else if (id == 3) {
                new_x = tank.x - 1;
            } else if (id == 4) {
                new_x = tank.x + 1;
            }
            
            let old_coords = textCoords(tank.x, tank.y);
            let new_coords = textCoords(new_x, new_y);

            tanks[textCoords(new_x, new_y)] = tank;
            delete tanks[textCoords(tank.x, tank.y)];

            tank.x = new_x;
            tank.y = new_y;

            let old_cell = document.getElementById(old_coords);
            let new_cell = document.getElementById(new_coords);


            const x0 = old_cell.getBoundingClientRect().left;
            const y0 = old_cell.getBoundingClientRect().top;

            const x1 = new_cell.getBoundingClientRect().left;
            const y1 = new_cell.getBoundingClientRect().top;
            
            old_cell.style.setProperty('--dx', (x1 - x0) + 'px');
            old_cell.style.setProperty('--dy', (y1 - y0) + 'px');


            old_cell.addEventListener('animationend', function() {
                paint_grid();
            });
            old_cell.classList.add('move');

            

        } else if (id == 5 || id == 6) {
            let other_tank = tank_by_name(other);
            let bullet = document.getElementById("bullet");
            let bulletbox = document.getElementById("bulletbox");
            let bullet_rect = bullet.getBoundingClientRect();

            let origin_cell_rect = document.getElementById(textCoords(tank.x, tank.y)).getBoundingClientRect();
            //let origin_x = origin_cell_rect.left + origin_cell_rect.width/2 - bullet_rect.width/2;
            let origin_x = origin_cell_rect.left + origin_cell_rect.width/2 - origin_cell_rect.width/2;
            let origin_y = origin_cell_rect.top + origin_cell_rect.width/2 - origin_cell_rect.width/2;
            //let origin_y = origin_cell_rect.top + origin_cell_rect.height/2 - bullet_rect.height/2;

            let target_cell_rect = document.getElementById(textCoords(other_tank.x, other_tank.y)).getBoundingClientRect();
            let target_x = target_cell_rect.left + target_cell_rect.width/2 - bullet_rect.width/2;
            let target_y = target_cell_rect.top + target_cell_rect.height/2 - bullet_rect.height/2;


            bulletbox.style.top = origin_y.toString()+"px";
            bulletbox.style.left = origin_x.toString()+"px";

            bulletbox.style.setProperty('--dx', (target_x - origin_x) + 'px');
            bullet.style.setProperty('--dy', (target_y - origin_y) + 'px');

            if (id == 5){
                bullet.style.backgroundImage = 'url("../static/bullet.png")';
            } else if (id==6){
                bullet.style.backgroundImage = 'url("../static/star.png")';
            }

            bullet.style.visibility = "visible";

            var animationend = function() {

                bullet.classList.remove('animated');
                bulletbox.classList.remove('animatedbox');

                bullet.style.visibility = "hidden";

                if (id==5){
                    other_tank.health -= 1;
                } else if (id==6){
                    other_tank.actions += 1;
                };


                paint_grid();

                bullet.removeEventListener('animationend', animationend);
            }

            bullet.addEventListener('animationend', animationend);

            bullet.classList.add('animated');
            bulletbox.classList.add('animatedbox');




        };
    };

    function getLogs(){
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
                let splitted = this.responseText.split("<br>");
                let missed = splitted.slice(last_update+1, -1);
                for (var i = 0; i < missed.length; i++) {
                    if (missed[i].split(" ")[0]==user_tank.tank_name){continue;}
                    make_action(...missed[i].split(" "));
                    //await new Promise(r => setTimeout(r, 2000));
                }
                last_update += missed.length;
                
            }


            
        };


        //var data = "user=" + user + "&new_x=" + new_x + "&new_y=" + new_y + "&new_actions=" + new_actions + "&new_health=" + new_health;
        var data = "game=" + "<?php echo $game['name'] ?>";
        xhr.withCredentials = true;
        xhr.open("POST", "get-logs.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  
        xhr.send(data);

    };

    async function check_updates(){
        let response = await fetch("game-logs.txt");
        var text = await response.text();
        if (text){
            let splitted = text.split("\n");
            let missed = splitted.slice(last_update+1);
            for (var i = 0; i < missed.length; i++) {
                if (missed[i].split(" ")[0]==user_tank.tank_name){continue;}
                make_action(...missed[i].split(" "));
                await new Promise(r => setTimeout(r, 2000));
            }
            last_update = splitted.length - 1;
        }
    }

    const updates_interval = setInterval(getLogs, 10000);

</script>
</html>
