<?php
    // read the score sheet
    $log = fopen("scores.csv", "r");
    while (($row = fgetcsv($log, 0, ",")) !== FALSE){
        switch($row[0]) {
            case "johan":
                    $johan_pts = $row[2];
                    $jsave = $row[1];
                break;
            case "arthur":
                    $arthur_pts = $row[2];
                    $asave = $row[1];
                break;
            case "sylvain":
                    $sylvain_pts = $row[2];
                    $ssave = $row[1];
                break;
        }
    }
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
                $previous = array('Johan' => (int) $johan_pts, 'Sylvain' => (int) $sylvain_pts, 'Arthur' => (int) $arthur_pts);
                $update = $previous;
                // update the score
                $update[$_POST["person"]] = $update[$_POST["person"]] + $_POST["dishes"];
                // balance the scores
                $min = min($update);
                foreach(array('Johan', 'Sylvain', 'Arthur') as $name){
                    $update[$name] = $update[$name] - $min;
                }
                // save the changes in the log file
                $data = array(
                    "person,dish,other",
                    "arthur,".$asave.",".$update["Arthur"],
                    "johan,".$jsave.",".$update["Johan"],
                    "sylvain,".$ssave.",".$update["Sylvain"],
                );
                $write_log = fopen('scores.csv', 'w');
                foreach ( $data as $line ) {
                    $cols = explode(",", $line);
                    fputcsv($write_log, $cols);
                }
                fclose($write_log);
            }
            // reward display
            header('location: reward.php');
        }
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>&#x1F6BF Other</title>
    <!-- <link rel="stylesheet" type="text/css" href="/artos/style.css" media="screen"/> -->
    <link rel="stylesheet" type="text/css" href="style.css" media="screen"/>
</head>

<body>
    <div id="pane">
        <header>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <h1> <a href = "" > Other </a> </h1>
        </header>
        <!-- <form method="POST" action="/artos/index.php"> -->
        <form method="POST" action="other.php">
            <p>
            <fieldset>
                <legend>Who did other ?</legend>
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
                <legend>What type of other ?</legend>
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