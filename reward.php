<?php
    $url = file("data/url.txt");
    // reward display
    $dir = $url[array_rand($url)];
    echo "<div id=pane> <center> Thanks!<br> </center>";
    echo "<center> <img src=\"$dir\" class='responsive-image'> </center> </div>";
    file_put_contents('data/selected_task.txt', '');
?>

<head>
    <title>&#x1F6BF Reward</title>
    <!-- <link rel="stylesheet" type="text/css" href="/artos/style.css" media="screen"/> -->
    <link rel="stylesheet" type="text/css" href="style.css" media="screen"/>
</head>

<body>
    <div id='pane'>
        <p>
            <?php
                // display the scores
                include 'utils.php';
                display_scores('data/scores.json');
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