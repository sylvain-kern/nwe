<?php

    function get_tasks($file){
        $conf = json_decode(file_get_contents($file), true);
        $tasks = array();
        $count = 0;
        foreach($conf as $task => $types){
            $tasks[$count] = $task;
            $count += 1;
        }
        return $tasks;
    }

    function get_users($file){
        $data = json_decode(file_get_contents($file), true);
        $users = array();
        $count = 0;
        foreach($data as $user => $scores){
            if($user == 'default'){
                continue;
            }
            $users[$count] = $user;
            $count += 1;
        }
        return $users;
    }

    function convert($number, $from, $to, $changes){
        $rate = $changes[$from][$to];
        $scaled = $number * $rate;
        // to be implemented : given a matrix of changes from task1 to task2, return the scaled number
        return $scaled;
    }

    function update_tasks_in_scores($task_file, $score_file){
        $tasks = get_tasks($task_file);
        $scores = json_decode(file_get_contents($score_file), true);
        if(count($scores['default']) != count($tasks)){
            foreach($scores as $user => &$task){
                $task[end($tasks)] = 0;
            }
            file_put_contents($score_file, json_encode($scores));
        }
    }

    function display_scores($file){
        // display latest scores
        $data = json_decode(file_get_contents($file), true);
        $tasks = array();
        $smiles = array();
        $nbUsers = 0;
        foreach($data as $user => $scores){
            if($user == 'default'){
                continue;
            }
            foreach($scores as $task => $val){
                $tasks[$task][$user] = $val; // to obtain the scores of each user per task
                $smiles[$task][$user] = "&#x26C5"; // by default, everybody got a cloud
            }
            $nbUsers += 1;
        }
        if($nbUsers>1 && count(get_tasks('data/config.json'))>0){
            // compute the smiles for each task
            foreach($tasks as $task => $scores){
                // everybody got a sun if every score is balanced
                if(count(array_unique($scores)) === 1){
                    foreach($scores as $user){
                        if($user == 'default'){
                            continue;
                        }
                        $smiles[$task][$user] = "&#x1F31E";
                    }
                }
                else{
                    $max = max($scores);
                    $min = min($scores);
                    // best score got a sun
                    foreach(array_keys($scores, $max, true) as $key){
                        if($key == 'default'){
                            continue;
                        }
                        $smiles[$task][$key] = "&#x1F31E";
                    } // worst score got a poop
                    foreach(array_keys($scores, $min, true) as $key){
                        if($key == 'default'){
                            continue;
                        }
                        $smiles[$task][$key] = "&#x1F4A9";
                    }
                }
            }
            // display as a table
            echo "<table style='width:100%'>";
            echo "<tr>"; // first row (headers)
            echo "<th> User </th>";
            foreach($tasks as $task => $scores){
                echo "<th>".$task."</th>";
            }
            echo "</tr>"; // end of first row
            foreach($data as $user => $scores){ // for each user
                if($user == 'default'){
                    continue; // skip "default"
                }
                echo "<tr>";
                echo "<td>".$user."</td>"; // write user's name
                foreach($scores as $task => $val){
                    echo "<td>".$smiles[$task][$user]." ".$val."</td>"; // right score and smile for each task
                }
                echo "</tr>";
            }
            echo "</table>";
        }
        else{
            echo "Not enough users or no task, please add at least two users and one task in the admin page.";
        }
    }

?>