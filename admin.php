<?php
    // initialize variables
    include 'utils.php';
    $password = 'password';
    
    $score_file = 'data/scores.json';
    $config_file = 'data/config.json';
    $trading_file = 'data/trading_rates.json';
    // read the scores, tasks configurations and trading rates
    $data = json_decode(file_get_contents($score_file), true);
    $config = json_decode(file_get_contents($config_file), true);
    $rates = json_decode(file_get_contents($trading_file), true);
    $nbtasks = count($config);
    // when submit is clicked
    if(isset($_POST["submit"])){
        // check if there is a password and it is the good one (as a confirmation process)
        if(isset($_POST["password"]) && $_POST["password"] == $password){
            echo "<div id='pane'> <center> Password OK <br>";
            // if a new task is being defined
            if(strcmp($_POST["new_task"],'') != 0){
                $task = $_POST["new_task"];
                foreach($data as $mate => &$val){ // create a new score for each roommate with zero points
                    $val[$task] = 0;
                }
                $config[$task] = array('default' => 1); // also create a new configuration with one 'default' type
                // create the trading rates matrix
                if(count($rates) == 0){ // if empty, create a 1x1 matrix with just the task
                    $rates[$task] = array($task => 1);
                }
                else{ // if not empty, expend the matrix from NxN where N is the new count of taskss
                    foreach($rates as $from => &$to){
                        $to[$task] = 1; // rate of one by default
                    }
                    $rates[$task] = end($rates);
                }
                // write the data and confirm the creation
                file_put_contents($score_file, json_encode($data));
                file_put_contents($config_file, json_encode($config, JSON_FORCE_OBJECT));
                file_put_contents($trading_file, json_encode($rates));
                echo "New task ".$task." added for all users ! <br>";
            }
            // if a new roommate is being defined
            if(strcmp($_POST["new_name"],'') != 0){
                $user = $_POST["new_name"];
                $data[$user] = $data["default"]; // just modify the scores with the 'default' template
                file_put_contents($score_file, json_encode($data, JSON_FORCE_OBJECT));
                echo "New user ".$user." added ! <br>";
            }
            echo "</center> </div>";
        }
        else{
            echo "<div id='pane'> <center> Wrong password, try again. </center> </div>";
        }
    }
    // show data/configuration mode
    if(isset($_POST["reset_one"])){
        $class = 'visible';
    }
    else{
        $class = 'hidden';
    }
    // to reset all data
    if(isset($_POST['reset_two'])){
        $reset_config = array();
        // create the 'default' template for the scores
        $reset_scores = array('default' => array());
        $reset_trading_rates = array();
        // write the *empty* data 
        file_put_contents($config_file, json_encode($reset_config, JSON_FORCE_OBJECT));
        file_put_contents($score_file, json_encode($reset_scores, JSON_FORCE_OBJECT));
        file_put_contents($trading_file, json_encode($reset_trading_rates, JSON_FORCE_OBJECT));
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

        <form method='POST' action='admin.php'>
            <input class='button' type='submit' name='reset_one' value='&#9888; Reset all &#9888;'/>
        </form>
        <?php
            // display configuration mode if needed
            if($class == 'visible'){
                echo "<form method='POST' action='admin.php'>";
                echo "<p> <input class='button' type='submit' name='reset_two' value='Sure ?' /> </p>";
                echo "</form>";
            }
        ?>

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
    // in the end, always update the scores data given the tasks newly created
    update_tasks_in_scores($config_file, $score_file);
?>