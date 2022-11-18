<?php

//--------------------------Настройки подключения к БД-----------------------
$db_host = 'mihey83.ru';
$db_user = 'kis'; //имя пользователя совпадает с именем БД
$db_password = 'kis211-362kis211-362'; //пароль, указаный при создании БД
$database = 'iot'; //имя БД, которое было указано при создании
$link = pg_connect("host=$db_host port=5432 user=$db_user password=$db_password dbname=$database");
if ($link == False) {
    die("Cannot connect DB");
}

echo '
    <!DOCTYPE HTML>
    <html id="App_interface">
    <head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>MyApp</title>
    <script src=UpdateScript.js> </script>
    </head>
    <body>';
//----------------------------------------------------------------------------------------
$id = 1;
$queryCount = "SELECT device_id from device_table;";
$resultCount = pg_query($link, $queryCount);
while($rowName = pg_fetch_assoc($resultCount)){
    //-----------------Получаем из БД все данные об устройстве-------------------
    $query = "SELECT * from device_table WHERE device_id = $id";
    $result = pg_query($link, $query);
    if (pg_num_rows($result) == 1) { //Если в БД есть данные о имени для этого устройства
        $Arr = pg_fetch_array($result);
        $device_name = $Arr['name'];
    } else { //Если в БД нет данных о имени для этого устройства
        $device_name = '?';
    }

    $query = "SELECT * FROM temperature_table WHERE device_id = $id";
    $result = pg_query($link, $query);
    if (pg_num_rows($result) == 1) { //Если в БД есть данные о температуре для этого устройства
        $Arr = pg_fetch_array($result);
        $temperature = $Arr['temperature'];
        $temperature_dt = $Arr['date_time'];
    } else { //Если в БД нет данных о температуре для этого устройства
        $temperature = '?';
        $temperature_dt = '?';
    }

    $query = "SELECT * FROM out_state_table WHERE device_id = $id";
    $result = pg_query($link, $query);
    if (pg_num_rows($result) == 1) { //Если в БД есть данные о реле для этого устройства
        $Arr = pg_fetch_array($result);
        $out_state = $Arr['out_state'];
        $out_state_dt = $Arr['date_time'];
    } else { //Если в БД нет данных о реле для этого устройства
        $out_state = '?';
        $out_state_dt = '?';
    }
    //----------------------------------------------------------------------------------------

    //------Проверяем данные, полученные от пользователя---------------------

    if (isset($_POST['button_on'])) {
        $date_today = date("Y-m-d H:i:s");
        $query = "UPDATE command_table SET command='1', date_time='$date_today' where device_id = $id";
        $result = pg_query($link, $query);
        if (pg_affected_rows($link) != 1) //Если не смогли обновить - значит в таблице просто нет данных о команде для этого устройства
        { //вставляем в таблицу строчку с данными о команде для устройства
            $query = "insert command_table set device_id=$id, command='1', date_time='$date_today'";
            $result = pg_query($link, $query);
        }
    }

    if (isset($_POST['button_off'])) {
        $date_today = date("Y-m-d H:i:s");
        $query = "UPDATE command_table SET command='0', date_time='$date_today' WHERE device_id = $id";
        $result = pg_query($link, $query);
        if (pg_affected_rows($link) != 1) //Если не смогли обновить - значит в таблице просто нет данных о команде для этого устройства
        { //вставляем в таблицу строчку с данными о команде для устройства
            $query = "INSERT command_table SET device_id=$id, command='0', date_time='$date_today'";
            $result = pg_query($link, $query);
        }
    }
    
    //-----------------------------------------------------------------------

    //-------Формируем интерфейс приложения для браузера---------------------
    echo '
        <table>
            <tr>
                <td width=100px> Устройство:
                </td>
                <td width=40px>' . $device_name . '
                </td>
            </tr>
        </table>

        <table border=1>
            <tr>
                <td width=100px> Tемпература
                </td>
                <td width=40px>' . $temperature . '
                </td>
                <td width=150px>' . $temperature_dt . '
                </td>
            </tr>

            <tr>
                <td width=100px> Реле
                </td>
                <td width=40px>' . $out_state . '
                </td>
                <td width=150px> ' . $out_state_dt . '
                </td>
            </tr>
        </table>

        <form>
            <button formmethod=POST name=button_on value=1>Включить реле</button>
                </form>
                <form>
            <button formmethod=POST name=button_off value=1>Выключить реле</button>
        </form>

   '; 
    $id = $id + 1;
}
echo '</body>
    </html>';
?>
