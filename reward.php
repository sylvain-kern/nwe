<?php
    $url = file("data/url.txt");
    $score_file = 'data/scores.json';
    $temp_file = 'data/selected_task.txt';
    // reward display
    $dir = $url[array_rand($url)];
    echo "<div id=pane> <center> Thanks!<br> </center>";
    echo "<center> <img src=\"$dir\" class='responsive-image'> </center> </div>";
    file_put_contents($temp_file, ''); // erase the previous task reminder
?>

<head>
    <title>&#x1F6BF Reward</title>
    <link rel="stylesheet" type="text/css" href="style.css" media="screen"/>
</head>

<body>
    <div id='pane'>
        <p>
            <?php
                // display the scores
                include 'utils.php';
                update_and_display_scores($score_file);
            ?>
        </p>

        <footer>
            <nav>
                <ul>
                    <li><a href="index.php"> Return to homepage </a></li>
                </ul>
            </nav>
        </footer>
    </div>
</body>