<?php
    include 'utils.php';
    $password = 'caonut';
    $data = json_decode(file_get_contents('data/scores.json'), true);
    $config = json_decode(file_get_contents('data/config.json'), true);
    $count = 0;
    foreach($data as $user => $scores){
        $count += 1;
    }
    $nbtasks = count($config);
    if(isset($_POST["submit"])){
        // if there is a password and it is the good one (as a confirmation process)
        if(isset($_POST["password"]) && $_POST["password"] == $password){
            echo "<div id='pane'> <center> Password OK: <br>";
            if(strcmp($_POST["new_task"],'') != 0){
                $task = $_POST["new_task"];
                $new_data = array();
                foreach($data as $mate => $val){
                    // assign a score of zero for each roommate
                    $val[$task] = 0;
                    $new_data[$mate] = $val;
                }
                $config[$task] = array();
                file_put_contents('data/scores.json', json_encode($new_data));
                file_put_contents('data/config.json', json_encode($config, JSON_FORCE_OBJECT));
                echo "New task ".$task." added for all users ! <br>";
            }
            if(strcmp($_POST["new_name"],'') != 0){
                $user = $_POST["new_name"];
                $data[$user] = $data["default"];
                file_put_contents('data/scores.json', json_encode($data, JSON_FORCE_OBJECT));
                echo "New user ".$user." added ! <br>";
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
            <p><input class='button' type='submit' name='submit' value='Submit' /></p>
        </form>

        <footer>
            <nav>
                <ul>
                    <li><a href="index.php"> Return to homepage </a></li>
                </ul>
            </nav>
        </footer>
        
    </div>
</body>
</html>

<?php
    update_tasks_in_scores('data/config.json', 'data/scores.json');
?>