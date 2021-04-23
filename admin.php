<?php
    $file = 'scores.json';
    $password = 'caonut';
    if(isset($_POST["submit"])){
        // if there is a password and it is the good one (as a confirmation process)
        if(isset($_POST["password"]) && $_POST["password"] == $password){
            echo "<div id='pane'> <center> Password OK: <br>";
            if(strcmp($_POST["new_name"],'') != 0){
                $user = $_POST["new_name"];
                $data = json_decode(file_get_contents($file), true);
                $data[$user] = $data["default"];
                file_put_contents($file, json_encode($data));
                echo "New user ".$user." added ! <br>";
            }
            if(strcmp($_POST["new_task"],'') != 0){
                $task = $_POST["new_task"];
                $data = json_decode(file_get_contents($file), true);
                $new_data = array();
                foreach($data as $mate => $val){
                    $val[$task] = 0;
                    $new_data[$mate] = $val;
                }
                file_put_contents($file, json_encode($new_data));
                echo "New task ".$task." added for all users ! <br>";
            }
            echo "</center> </div>";
        }
        else{
            echo "<div id='pane'> <center> Wrong password, try again. </center> </div>";
        }
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>&#x1F6BF Admin page </title>
    <!-- <link rel="stylesheet" type="text/css" href="/artos/style.css" media="screen"/> -->
    <link rel="stylesheet" type="text/css" href="style.css" media="screen"/>
</head>

<body>
    <div id="pane">
        <header>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <h1> <a href = "" > Admin page </a> </h1>
        </header>

        <form method='POST' action='admin.php'>
            <p> Admin password :<input type='text' name='password' /></p>
            <p> Add a new roommate :<input type='text' name='new_name' /></p>
            <p> Add a new task :<input type='text' name='new_task' /></p>
            <p><input class='buttont' type='submit' name='submit' value='Validate' /></p>
        </form>

    </div>
</body>
</html>