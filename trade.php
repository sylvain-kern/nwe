<?php
    // initialize
    include 'utils.php';
    $score_file = 'data/scores.json';
    $config_file = 'data/config.json';
    $trading_file = 'data/trading_rates.json';
    // read the data
    $score = json_decode(file_get_contents($score_file), true);
    $trading_rates = json_decode(file_get_contents($trading_file), true);
    $tasks = get_tasks($config_file);
    $users = get_users($score_file);
    if(count($users) == 0){
        // if there are no users, return to index
        header('location: index.php');
    }
    // trading process
    if(isset($_POST['submit_trade'])){
        $points = $_POST['points_to_trade'];
        // check if format is correct
        if(is_numeric($points)){
            $task_given = $_POST['task_to_be_given'];
            $task_receive = $_POST['task_to_receive'];
            // check if selection is correct (prevent trading from one task to the same)
            if($task_given != $task_receive){
                $trader = $_POST['user_to_give'];
                $score_of_trader = $score[$trader][$task_given];
                // scale the score according to the trading rate
                $scaled_points = convert($points, $task_given, $task_receive, $trading_rates);
                // check if the user has enough points
                if($score_of_trader >= $points){
                    $score[$trader][$task_given] -= $points; // remove points
                    $score[$trader][$task_receive] += $scaled_points; // add scaled points
                    file_put_contents($score_file, json_encode($score));
                    echo "<div id='pane'> <p> Points successfuly traded. </div> </p>";
                }
                else{
                    echo "<div id='pane'> <p> Not enough points... </div> </p>";
                }
            }
            else{
                echo "<div id='pane'> <p> Useless trade... </div> </p>";
            }
        }
        else{
            echo "<div id='pane'> <p> Wrong format... </div> </p>";
        }
    }
    // show data/configuration mode
    if(isset($_POST["conf_mod"])){
        $class = 'visible';
    }
    else{
        $class = 'hidden';
    }
    // modify a rate
    if(isset($_POST['submit_modify_rate'])){
        $from_mod = $_POST['rate_from'];
        $to_mod = $_POST['rate_to'];
        // prevent from modifying a rate between a task and herself
        if($from_mod != $to_mod){
            $new_rate = (float) $_POST['new_rate'];
            // check if the format is correct and prevent from having a negative rate
            if(is_numeric($new_rate) &&  ($new_rate > 0)){
                $trading_rates[$from_mod][$to_mod] = $new_rate;
                $trading_rates[$to_mod][$from_mod] = round(1/$new_rate, 2, PHP_ROUND_HALF_DOWN); // inverse the symmetrical rate
                file_put_contents($trading_file, json_encode($trading_rates));
            }
            else{
                echo "<div id='pane'> <p> Wrong format... </div> </p>";
            }
        }
        else{
            echo "<div id='pane'> <p> Impossible change... </div> </p>";
        }
    }
?>

<head>
    <title> Trade page </title>
    <link rel="stylesheet" type="text/css" href="style.css" media="screen"/>
</head>

<body>
    <div id='pane'>
        <h1> Updated scores: </h1>
        <p>
            <?php
                // display the scores
                update_and_display_scores($score_file, $config_file);
            ?>
        </p>

        <?php // display the trading options
            // who gives the points
            echo "<form method='POST' action='trade.php'>";
            echo "<label for='user_to_give'> User:</label>";
            echo "<select name='user_to_give' id='user_to_give'>";
            foreach($users as $user){
                echo "<option value='".$user."'> ".$user." </option>";
            }
            echo "</select>";
            // from which task
            echo "<label for='task_to_be_given'> from task:</label>";
            echo "<select name='task_to_be_given' id='task_to_be_given'>";
            foreach($tasks as $task){
                echo "<option value='".$task."'> ".$task." </option>";
            }
            echo "</select>";
            // to which task
            echo "<label for='task_to_receive'> to task:</label>";
            echo "<select name='task_to_receive' id='task_to_receive'>";
            foreach($tasks as $task){
                echo "<option value='".$task."'> ".$task." </option>";
            }
            echo "</select> <br>";
            echo "<p> how many points:<input type='text' name='points_to_trade' /></p>";
            echo "<p><input type='submit' name='submit_trade' value='Confirm trade' /></p>";
            echo "</form>";
        ?>

        <h2> Trading rates : </h1>

        <?php
            // display as a table
            echo "<table style='width:100%'>";
            echo "<tr>"; // first row (headers)
            echo "<th> from - to </th>";
            foreach($trading_rates as $from => $to){
                echo "<th>".$from."</th>";
            }
            echo "</tr>"; // end of first row
            foreach($trading_rates as $from => $to){ // for each user
                echo "<tr>";
                echo "<td>".$from."</td>"; // write user's name
                foreach($to as $val){
                    echo "<td>".$val."</td>"; // and trading score in each corresponding column
                }
                echo "</tr>";
            }
            echo "</table>";
        ?>

        <form method='POST' action='trade.php'>
            <input class='button' type='submit' name='conf_mod' value='&#128296 configuration mode'/>
        </form>
        <?php
            // display configuration mode
            if($class == 'visible'){
                echo "<h2> Settings : </h2>";
                // modify rates from...
                echo "<form method='POST' action='trade.php'>";
                echo "<label for='rate_from'> Modify the rate from :</label>";
                echo "<select name='rate_from' id='rate_from'>";
                foreach($tasks as $task){
                    echo "<option value='".$task."'> ".$task." </option>";
                }
                echo "</select>";
                // ... to ...
                echo "<label for='rate_to'> to:</label>";
                echo "<select name='rate_to' id='rate_to'>";
                foreach($tasks as $task){
                    echo "<option value='".$task."'> ".$task." </option>";
                }
                echo "</select>";
                // new rate
                echo "<p> New rate <input type='text' name='new_rate' /></p>";
                echo "<p><input type='submit' name='submit_modify_rate' value='Modify' /></p>";
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