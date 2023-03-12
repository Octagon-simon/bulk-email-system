<?php

/**
 * Please close this file and go back if you don't know what to do here!
 * 
 * This cron job runs the scripts created by the user
 * 
 * Run the cron script and ignore if the script is already running.. Script runs every minute
 * 
 *    ***** /usr/bin/pgrep path_to_script.php || /usr/bin/php path_to_script.php
 * 
 **/

require('../core/functions.php');

$script = $db->SelectOne("SELECT *, scripts.id AS script_id FROM scripts INNER JOIN templates INNER JOIN lists INNER JOIN progress ON progress.track_id = scripts.track_id AND lists.id = scripts.list_id AND templates.id = scripts.temp_id WHERE scripts.has_started = :started AND scripts.is_completed = :completed", [
    'started' => "Yes",
    'completed' => "No"
]);

if (!empty($script)) {
    //template
    $template = (file_exists("../uploads/templates/" . $script['temp_file'])) ? file_get_contents("../uploads/templates/" . $script['temp_file']) : null;
    //list
    $theCSV = (file_exists("../uploads/lists/" . $script['list_file'])) ? array_map('str_getcsv', file("../uploads/lists/" . $script['list_file'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) : null;

    //proceed if both files exist
    if (!empty($template) && !empty($theCSV)) {
        //also do names (next update)
        $interval = intval($script['scr_interval']);
        $completed = false;
        //the email addresses
        $emails = [];
        //get each string from the csv file and store in array
        foreach ($theCSV as $t => $v) {
            //check if the string from the csv file is an email address and store
            //email from list array
            $eFLAry = array_unique(array_map('checkIfStringIsEmail', $v));
            //loop through results from the emails array
            foreach ($eFLAry as $ee => $ss) {
                //check if element is a valid email then store it
                if (filter_var($ss, FILTER_VALIDATE_EMAIL)) {
                    $emails[] = $ss;
                }
            }
        }

        //make the email array elements unique and then loop through
        if (count($emails)) {
            //make sure email addresses are unique
            foreach (array_unique($emails) as $e) {
                //update progress
                $db->Update("UPDATE progress SET total_sent = :s, total_left = :l, current_email = :e WHERE track_id = :t", [
                    's' => (intval($script['total_sent'])) ? intval($script['total_sent']) + 1 : 1,
                    'l' => (intval($script['total_emails'])) ? intval($script['total_emails']) - intval($script['total_sent']) : intval($script['total_emails']),
                    'e' => $e,
                    't' => $script['track_id']
                ]);
                // sendMail($e, '', $script['temp_name'], $template);
                //update completed
                $completed = true;
                //rest
                //sleep($interval);
                //reset counter -- max execution time...
                set_time_limit(30);
            }
            //script is done
            if ($completed) {
                $db->Update("UPDATE scripts SET has_started = :h, is_completed = :c, time_completed = :t WHERE id = :i", [
                    'h' => "Done",
                    'c' => "Yes",
                    't' => time(),
                    'i' => $script['script_id']
                ]);
            }
        }
    }
}
//kill script
exit();
?>