<!DOCTYPE html>
<html>

<head>
    <title>&#x1F6BF Roomate equalizer</title>
    <!-- <link rel="stylesheet" type="text/css" href="/artos/style.css" media="screen"/> -->
    <link rel="stylesheet" type="text/css" href="style.css" media="screen"/>
</head>

<body>
    <div id="pane">
        <header>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <h1> <a href = "" > Roomate equalizer </a> </h1>
        </header>
        <p>
        <!-- first display the scores -->
        <?php
            $log = fopen("scores.csv", "r");
            while (($row = fgetcsv($log, 0, ",")) !== FALSE){
                switch($row[0]) {
                    case "johan":
                            $johan_pts = $row[1];
                        break;
                    case "arthur":
                            $arthur_pts = $row[1];
                        break;
                    case "sylvain":
                            $sylvain_pts = $row[1];
                        break;
                }
            }
        ?>
        </p>
        <p>
        <?php
            echo "Scores: <br>";
            // last scores of each
            $scores = array("johan" => $johan_pts, "sylvain" => $sylvain_pts, "arthur" => $arthur_pts);
            // by default, cloud
            $smiles = array("johan" => "&#x26C5", "sylvain" => "&#x26C5", "arthur" => "&#x26C5");
            // if the scores are balanced as everything should be, put sun emoji
            if(count(array_unique($scores)) === 1){
                foreach($smiles as $smile){
                    $smile = "&#x1F31E";
                }
            }
            else{
                $max = max($scores);
                $min = min($scores);
                // best score
                foreach(array_keys($scores, $max, true) as $key){
                    $smiles[$key] = "&#x1F31E";
                } // worst score
                foreach(array_keys($scores, $min, true) as $key){
                    $smiles[$key] = "&#x1F4A9";
                }
            }
            // display the scores
            foreach(array('johan', 'sylvain', 'arthur') as $name){
                echo "$smiles[$name] " . ucfirst($name) . " $scores[$name] <br>";
            }
        ?>
        </p>
        <!-- the select the task -->
        <p> Select your task: </p>
        <nav>
            <ul>
                <li><a href="dishes.php"> Dish washing </a></li>
            </ul>
        </nav>
    </div>
</body>
</html>