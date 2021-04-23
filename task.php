<?php
    include 'utils.php';
    $currentTask = file_get_contents('data/selected_task.txt');
    $default_type = true;
    if($currentTask != ''){
        $users = get_users('data/scores.json');
        $data = json_decode(file_get_contents('data/scores.json'), true);
        $global_conf = json_decode(file_get_contents('data/config.json'), true);  
        $conf = $global_conf[$currentTask];
        // show data/configuration mode
        if(isset($_POST["conf_mod"])){
            $class = 'visible';
        }
        else{
            $class = 'hidden';
        }
        // process task done
        if(isset($_POST['submit_task'])){
            if(!isset($_POST["person"]) || (!isset($_POST["type"]) && $_POST["person"]!="Guest" && !$default_type)){
                echo "Missing information";
            }
            else{
                if($_POST["person"] != 'Guest'){
                    // get previous scores
                    $new_scores = array();
                    if($default_type){
                        $gain = $conf['default'];
                    }
                    else{
                        $gain = $_POST["type"];
                    }
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
                    var_dump($data);
                    file_put_contents('data/scores.json', json_encode($data));
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
            if(is_numeric($score)){
                $conf[$modify] = (int) $score;
                $global_conf[$currentTask] = $conf;
                file_put_contents('data/config.json', json_encode($global_conf));
            }
            else{
                echo "Incorrect entry, please retry...";
            }
        }
        // add new type
        if(isset($_POST['submit_add_type'])){
            $new_type_name = $_POST['add_type_name'];
            $new_type_score = $_POST['add_type_score'];
            if(is_numeric($new_type_score)){
                $conf[$new_type_name] = (int) $new_type_score;
                $global_conf[$currentTask] = $conf;
                file_put_contents('data/config.json', json_encode($global_conf));
            }
            else{
                echo "Incorrect entry, please retry...";
            }
        }
        // remove type
        if(isset($_POST['submit_remove'])){
            $remove = $_POST['type_to_remove'];
            unset($conf[$remove]);
            $global_conf[$currentTask] = $conf;
            file_put_contents('data/config.json', json_encode($global_conf));
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
            if(count($conf)>0){
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
                if(count($conf) > 1){
                    echo "<p> <fieldset> <legend> What type of task ?</legend>";
                    foreach($conf as $type => $val){
                        echo "<input type='radio' name='type' value='".$val."' id='".$type."'>";
                        echo "<label for='".$type."'> ".$type." </label><br/>";
                    }
                    echo "</fieldset> </p>";
                    $default_type = false;
                }
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
                if(count($conf)>0){
                    echo "<form method='POST' action='task.php'>";
                    echo "<label for='type_to_modify'> Modify a type :</label>";
                    echo "<select name='type_to_modify' id='type_to_modify'>";
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
                if(count($conf)>0){
                    echo "<form method='POST' action='task.php'>";
                    echo "<label for='type_to_remove'> Remove a type :</label>";
                    echo "<select name='type_to_remove' id='type_to_remove'>";
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