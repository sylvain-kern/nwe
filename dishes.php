<?php

    $lines = file("scores.txt");
    $url = file("url.txt");
    if(isset($_POST["submit"])){
        // test if any information is missing
        if(!isset($_POST["person"]) || (!isset($_POST["dishes"]) && $_POST["person"]!="Guest")){
            echo "<div id='pane'> <center> Missing information, try again idiot ! <br> </center>";
            $dir = 'https://scontent-otp1-1.xx.fbcdn.net/v/t1.0-9/48211179_192761688347670_1675221920443793408_o.jpg?_nc_cat=105&ccb=1-3&_nc_sid=09cbfe&_nc_ohc=kDe9h78wNPkAX-h4wMf&_nc_ht=scontent-otp1-1.xx&oh=78271be66e745f3dd53965428a2ee572&oe=6081D757';
            echo "<center> <img src=\"$dir\" class='responsive-image'> </center> </div>";
        }
        else{
            if($_POST["person"]!="Guest"){
                // initialize variables
                $previous = array('Johan' => (int) $lines[2], 'Sylvain' => (int) $lines[4], 'Arthur' => (int) $lines[6]);
                $update = $previous;
                // update the score
                $update[$_POST["person"]] = $update[$_POST["person"]] + $_POST["dishes"];
                // balance the scores
                $min = min($update);
                foreach(array('Johan', 'Sylvain', 'Arthur') as $name){
                    $update[$name] = $update[$name] - $min;
                }
                // save the changes in the log file
                $lines[2] = $update['Johan'] . "\n";
                $lines[4] = $update['Sylvain'] . "\n";
                $lines[6] = $update['Arthur'] . "\n";
            }
            $lines[0] = $_POST["person"] . "\n";
            $lines[7] = date('Y/m/d H:i:s');
            file_put_contents("scores.txt", $lines);
            // reward display
            $dir = $url[array_rand($url)];
            if($_POST["person"]=="Guest"){
                echo "<div id=pane> <center> Thanks a lot, lovely guest !<br> </center>";
            }
            else{
                echo "<div id=pane> <center> Thanks " . $_POST['person'] . "!<br> </center>";
            }
            echo "<center> <img src=\"$dir\" class='responsive-image'> </center> </div>";
        }
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>&#x1F6BF Dish Dissipator</title>
    <!-- <link rel="stylesheet" type="text/css" href="/artos/style.css" media="screen"/> -->
    <link rel="stylesheet" type="text/css" href="style.css" media="screen"/>
</head>

<body>
    <div id="pane">
        <header>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <h1> <a href = "" > Dish equalizer </a> </h1>
        </header>
        <!-- <form method="POST" action="/artos/index.php"> -->
        <form method="POST" action="dishes.php">
            <p>
            <fieldset>
                <legend>Who washed the dishes ?</legend>
                <input type="radio" name='person' value='Johan' id='Johan'>
                <label for="Johan">Johan</label><br/>
                <input type="radio" name='person' value='Sylvain' id='Sylvain'>
                <label for="Sylvain">Sylvain</label><br/>
                <input type="radio" name='person' value='Arthur' id='Arthur'>
                <label for="Arthur">Arthur</label><br/>
                <input type="radio" name='person' value='Guest' id='Guest'>
                <label for="Guest">Guest</label><br/>
            </fieldset>
            </p>
            <p>
            <fieldset>
                <legend>What type of dishes ?</legend>
                <input type="radio" name='dishes' value='1' id="Breakfast">
                <label for="Breakfast">Breakfast</label><br/>
                <input type="radio" name='dishes' value='2' id="Meal">
                <label for="Meal">Meal</label><br/>
                <input type="radio" name='dishes' value='4' id="Double meal">
                <label for="Double meal">Double meal</label><br/>
            </fieldset>
            </p>
            <p>
            <input class='button' type='submit' name='submit' value="&#x1F6BF Washed"/>
            </p>
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