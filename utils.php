<?php
    // some functions used in multiple .php files

    function get_tasks($conf_file){
        // used to extract all the tasks' names from a given configuration file
        $conf = json_decode(file_get_contents($conf_file), true);
        $tasks = array();
        $count = 0;
        foreach($conf as $task => $types){
            $tasks[$count] = $task;
            $count += 1;
        }
        return $tasks;
    }

    function get_users($score_file){
        // used to extract all the users' names from a given scores file
        $data = json_decode(file_get_contents($score_file), true);
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

    function modify_or_add_conf($name, $value, $task, $conf_file){
        // used to modify or add a type of task in the configuration file
        $global_conf = json_decode(file_get_contents($conf_file), true);  
        $conf = $global_conf[$task]; // configuration of the proper task
        if(is_numeric($value)){ // check if the change has a good format
            $conf[$name] = (int) $value; // modify if existing or add if not
            $global_conf[$task] = $conf;
            file_put_contents($conf_file, json_encode($global_conf));
        }
        else{
            echo "Incorrect entry, please retry...";
        }
    }

    function convert($number, $from, $to, $changes){
        // make a change between points from a task to another given the matrix of trading rates
        $rate = $changes[$from][$to];
        $scaled = $number * $rate;
        return $scaled;
    }

    function update_tasks_in_scores($task_file, $score_file){
        // when a new task is added, extend the score file with a score of 0 for the said task to each user
        $tasks = get_tasks($task_file);
        $scores = json_decode(file_get_contents($score_file), true);
        if(count($scores['default']) != count($tasks)){ // only if there are more tasks in the config than in the score file
            foreach($scores as $user => &$task){
                $task[end($tasks)] = 0;
            }
            file_put_contents($score_file, json_encode($scores));
        }
    }

    function update_and_display_scores($score_file){
        // used to update the scores and compute the right emoji before displaying them in a table, given the score file
        $data = json_decode(file_get_contents($score_file), true);
        $tasks = array();
        $smiles = array();
        $nbUsers = 0;
        $nbTasks = 0;
        // transpose the score matrix to obtain the _$tasks_ matrix and construct the default emoji matrix 
        foreach($data as $user => $scores){
            if($user == 'default'){ // skip the default user
                continue;
            }
            foreach($scores as $task => $val){
                $tasks[$task][$user] = $val; // to obtain the scores of each user per task (transpose of $data)
                $smiles[$task][$user] = "&#x26C5"; // by default, everybody got a cloud (emoji matrix)
                $nbTasks += 1;
            }
            $nbUsers += 1;
        }
        $nbTasks = $nbTasks/$nbUsers;
        // if there are more than one users and at least a task
        if($nbUsers>1 && $nbTasks>0){
            // compute the smiles for each task
            foreach($tasks as $task => $scores){
                $max = max($scores);
                $min = min($scores);
                // best score got a sun
                foreach(array_keys($scores, $max, true) as $user){
                    if($user == 'default'){
                        continue;
                    }
                    $smiles[$task][$user] = "&#x1F31E";
                } // worst score got a poop
                foreach(array_keys($scores, $min, true) as $user){
                    if($user == 'default'){
                        continue;
                    }
                    $smiles[$task][$user] = "&#x1F4A9";
                }
            }
            // display as a table
            echo "<table style='width:100%'>";
            echo "<tr>"; // first row (headers: tasks names)
            echo "<th> User </th>";
            foreach($tasks as $task => $scores){
                echo "<th>".$task."</th>";
            }
            echo "</tr>"; // end of first row
            foreach($data as $user => $scores){ // for each user
                if($user == 'default'){
                    continue; // skip default user
                }
                echo "<tr>";
                echo "<td>".$user."</td>"; // write user's name
                foreach($scores as $task => $val){
                    echo "<td>".$smiles[$task][$user]." ".$val."</td>"; // right score and smile for each task in the corresponding column
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