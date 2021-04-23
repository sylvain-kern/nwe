<?php
    include 'utils.php';
    $tasks = get_tasks('data/config.json');
    if(file_get_contents('data/selected_task.txt') != ''){
        file_put_contents('data/selected_task.txt', '');
    }
    if(isset($_POST['go_to_task']) && isset($_POST['task_name'])){
        file_put_contents('data/selected_task.txt', $_POST['task_name']);
        header('location: task.php');
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>&#x1F6BF Roommate equalizer</title>
    <!-- <link rel="stylesheet" type="text/css" href="/artos/style.css" media="screen"/> -->
    <link rel="stylesheet" type="text/css" href="style.css" media="screen"/>
</head>

<body>
    <div id="pane">
        <header>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <h1> <a href = "" > Roomm   ate equalizer </a> </h1>
        </header>
        <p>
        <?php
            // display the scores
            display_scores('data/scores.json');
        ?>
        </p>
        <?php
            if(count($tasks)>0){
                // Task selection
                echo "<form method='POST' action='index.php'>";
                echo "<p> <fieldset> <legend> Choose your task:</legend>";
                foreach($tasks as $task){
                    echo "<input type='radio' name='task_name' value='".$task."' id='".$task."'>";
                    echo "<label for='".$task."'> ".$task." </label><br/>";
                }
                echo "</fieldset> </p>";
                echo "<input class='button' type='submit' name='go_to_task' value='Go!'/>";
                echo "</form>";
            }
            else{
                echo '<p> No task detected, add at least one in the admin page.';
            }
        ?>
    </div>
    <div id='pane'>
        <nav>
            <ul>
                <li><a href="admin.php"> Enter admin page </a></li>
            </ul>
        </nav>
    </div>
</body>
</html>