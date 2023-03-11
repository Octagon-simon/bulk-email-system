<?php

/**
 * Please close this file and go back if you don't know what to do here!
 * This cron job runs the scripts created by the user
 **/

 //handle max execution time...
 //make sure email addresses are unique

require('../core/functions.php');

$script = $db->SelectOne("SELECT *, scripts.id AS script_id FROM scripts INNER JOIN templates INNER JOIN lists INNER JOIN progress ON progress.track_id = scripts.track_id AND lists.id = scripts.list_id AND templates.id = scripts.temp_id WHERE scripts.has_started = :started AND scripts.is_completed = :completed", [
    'started' => "Yes",
    'completed' => "No"
]);

if (!empty($script)) {
    //templates
    $template = (file_exists("../uploads/templates/" . $script['temp_file'])) ? file_get_contents("../uploads/templates/" . $script['temp_file']) : null;
    //lists
    $list = (file_exists("../uploads/lists/" . $script['list_file'])) ? array_map('str_getcsv', file("../uploads/lists/" . $script['list_file'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) : null;

    //proceed if both files exist
    if (!empty($template) && !empty($list)) {
        //get emails
        //also do names (next update)
        $emails = [];
        $interval = intval($script['frequency']);
        $completed = false;
        foreach ($list as $ll => $tt) {
            $list_res = array_map('checkIfStringIsEmail', $tt);
            //loop through results from the list
            foreach ($list_res as $lll => $sss) {
                if (filter_var($sss, FILTER_VALIDATE_EMAIL)) {
                    //update progress
                    $db->Update("UPDATE progress SET total_sent = :s, total_left = :l, current_email = :e WHERE track_id = :t", [
                        's' => (intval($script['total_sent'])) ?  intval($script['total_sent']) + 1 : 1,
                        'l' => (intval($script['total_emails'])) ?  intval($script['total_emails']) - intval($script['total_sent']) : intval($script['total_emails']),
                        'e' => $sss,
                        't' => $script['track_id']
                    ]);
                    // $emails[] = $sss;
                    // sendMail($sss['address'], '', $script['temp_name'], $template);
                    // var_dump("sending to " . $sss . ' ' . $script['temp_name'] . ' ' . $template);
                    //update completed
                    $completed = true;
                    //rest
                    // sleep($interval);
                }
            }
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


?>