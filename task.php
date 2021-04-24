<?php
    // initialization
    include 'utils.php';
    $score_file = 'data/scores.json';
    $config_file = 'data/config.json';
    $temp_file = 'data/selected_task.txt';
    $currentTask = file_get_contents($temp_file);
    // by default, the default type is activated
    $default_type = true;
    if($currentTask != ''){ // if a task was selected
        // get the data
        $users = get_users($score_file);
        $data = json_decode(file_get_contents($score_file), true);
        $global_conf = json_decode(file_get_contents($config_file), true);  
        $conf = $global_conf[$currentTask]; // configuration of the proper task
        $nb_types = count($conf);
        // show configuration mode
        if(isset($_POST["conf_mod"])){
            $class = 'visible';
        }
        else{
            $class = 'hidden';
        }
        // process task done
        if(isset($_POST['submit_task'])){
            // if nobody is assigned or there are more than one type and none is selected (unless it's the guest's work)
            if(!isset($_POST["person"]) || (!isset($_POST["type"]) && $_POST["person"]!="Guest" && !$default_type)){
                echo "Missing information";
            }
            else{
                // if the person is not the guest
                if($_POST["person"] != 'Guest'){
                    // write a new score sheet and get the points to give
                    $new_scores = array();
                    if($default_type){
                        $gain = $conf['default'];
                    }
                    else{
                        $gain = $_POST["type"];
                    }
                    // write the users' scores
                    foreach($data as $user => $scores){
                        if($user == 'default'){ // skip the default user
                            continue;
                        }
                        if($user == $_POST["person"]){ // if the user is the one who performed the task, give him the points
                            $new_scores[$user] = $scores[$currentTask] + $gain;
                        }
                        else{ // else no changes
                            $new_scores[$user] = $scores[$currentTask];
                        }
                    }
                    // balance the scores
                    $min = min($new_scores);
                    foreach($users as $user){
                        $new_scores[$user] = $new_scores[$user] - $min;
                    }
                    // save the changes in the file
                    foreach($data as $user => &$scores){
                        if($user == 'default'){
                            continue;
                        }
                        $data[$user][$currentTask] = $new_scores[$user];
                    }
                    // write the data
                    file_put_contents($score_file, json_encode($data));
                }
                // reward display
                header('location: reward.php');
            }
        }

        // configuration actions

        // modify existing type
        if(isset($_POST['submit_modify_type'])){
            $modify = $_POST['type_to_modify'];
            $score = $_POST['modify_type_score'];
            modify_or_add_conf($modify, $score, $currentTask, $config_file);
        }
        // add new type
        if(isset($_POST['submit_add_type'])){
            $new_type_name = $_POST['add_type_name'];
            $new_type_score = $_POST['add_type_score'];
            modify_or_add_conf($new_type_name, $new_type_score, $currentTask, $config_file);
        }
        // remove type
        if(isset($_POST['submit_remove'])){
            $remove = $_POST['type_to_remove'];
            unset($conf[$remove]); // remove from array
            // write the data
            $global_conf[$currentTask] = $conf;
            file_put_contents($config_file, json_encode($global_conf));
        }
    }
    else{
        header('location: index.php');
    }
?>

<!DOCTYPE html>
<html>

<head>
    <?php
    echo "<title> ".$currentTask." </title>";
    ?>
    <link rel="stylesheet" type="text/css" href="style.css" media="screen"/>
</head>

<body>
    <div id="pane">
        <header>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <?php
            echo "<h1> <a href = '' > ".$currentTask." equalizer </a> </h1>";
            ?>
        </header>
        <?php
            // display the users and the types of task if any
            if($nb_types>0){
                // who did the task
                echo "<form method='POST' action='task.php'>";
                echo "<p> <fieldset> <legend> Who did the task ?</legend>";
                foreach($users as $user){
                    echo "<input type='radio' name='person' value='".$user."' id='".$user."'>";
                    echo "<label for='".$user."'> ".$user." </label><br/>";
                }
                echo "<input type='radio' name='person' value='Guest' id='Guest'>";
                echo "<label for='Guest'> Guest </label><br/>";
                echo "</fieldset> </p>";
                // what type of task
                if($nb_types > 1){ // if more than one type, display them
                    echo "<p> <fieldset> <legend> What type of task ?</legend>";
                    foreach($conf as $type => $val){
                        echo "<input type='radio' name='type' value='".$val."' id='".$type."'>";
                        echo "<label for='".$type."'> ".$type." </label><br/>";
                    }
                    echo "</fieldset> </p>";
                    $default_type = false; // not default mode anymore, the user needs to make a choice
                }
                // default mode will be used
                echo "<input class='button' type='submit' name='submit_task' value='Task done'/>";
                echo "</form>";
            }
            else{
                echo "<p> Task undefined, please configure. </p>";
            }
        ?>

        <form method='POST' action='task.php'>
            <input class='button' type='submit' name='conf_mod' value='&#128296 configuration mode'/>
        </form>
        <?php
            // display configuration mode
            if($class == 'visible'){
                echo "<h1> Settings : </h1>";
                // modify existing types if any
                if($nb_types>0){
                    echo "<form method='POST' action='task.php'>";
                    echo "<label for='type_to_modify'> Modify a type :</label>";
                    echo "<select name='type_to_modify' id='type_to_modify'>";
                    // make each type selectable
                    foreach($conf as $type => $val){
                        echo "<option value='".$type."'> ".$type."(".$conf[$type]." points)</option>";
                    }
                    echo "</select>";
                    echo "<p> New number of points <input type='text' name='modify_type_score' /></p>";
                    echo "<p><input type='submit' name='submit_modify_type' value='Modify' /></p>";
                    echo "</form>";
                }
                // add type
                echo "<h2> Add a type : </h2>";
                echo "<form method='POST' action='task.php'>";
                echo "<p> Type name <input type='text' name='add_type_name' /></p>";
                echo "<p> Number of points <input type='text' name='add_type_score' /></p>";
                echo "<p><input type='submit' name='submit_add_type' value='Add' /></p>";
                echo "</form>";
                // remove type
                if($nb_types>0){
                    echo "<form method='POST' action='task.php'>";
                    echo "<label for='type_to_remove'> Remove a type :</label>";
                    echo "<select name='type_to_remove' id='type_to_remove'>";
                    // make each type selectable
                    foreach($conf as $type => $val){
                        echo "<option value='".$type."'> ".$type." </option>";
                    }
                    echo "</select>";
                    echo "<p><input type='submit' name='submit_remove' value='Remove' /></p>";
                    echo "</form>";
                }
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