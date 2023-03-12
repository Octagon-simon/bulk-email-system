<?php

require('../../core/app.php');
$status = [];

//get all templates
$data = $db->SelectAll("SELECT *, scripts.id AS script_id FROM scripts INNER JOIN templates INNER JOIN lists INNER JOIN progress ON progress.track_id = scripts.track_id AND lists.id = scripts.list_id AND templates.id = scripts.temp_id ORDER BY scripts.date_created ASC", []);

//handle script
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $a = trim($_POST['action']);

    $script_id = (isset($_POST['script_id']) && !empty($_POST['script_id'])) ? intval($_POST['script_id']) : 0;

    //start script
    if ($a == "start_script" && $script_id) {
        $exists = false;
        $temp_file = "";
        $key = 0;
        $already_running = false;
        $already_completed = false;
        $track_id = "";
        //do some checks
        foreach ($data as $dt => $da) {
            //a particular
            if ($da['script_id'] == $script_id) {
                $exists = true;
                $key = $dt;
                $track_id = $da['track_id'];
                //if script has completed
                if($da['is_completed'] == "Yes"){
                    $already_completed = true;
                }
            }
            //a script is already running
            if($da['has_started'] == "Yes"){
                $already_running = true;
            }
        }

        //check if temp exists
        if (!$exists) {
            $status = [
                "success" => false,
                "msg" => "The script you're trying to start does not exist"
            ];
        }

        if(!empty($already_running)){
            $status = [
                "success" => false,
                "msg" => "A script is already running. Please wait for it to complete"
            ];
        }
        //check if script is completed already
        if(!empty($already_completed)){
            $status = [
                "success" => false,
                "msg" => "You cannot restart a script that has already completed"
            ];
        }
        if (empty($status)) {
            $tstarted = time();
            $db->Update("UPDATE scripts SET has_started = :h, time_started = :t WHERE id = :i", [
                'h' => "Yes",
                't' => $tstarted,
                'i' => $script_id
            ]);

            $status = [
                "success" => true,
                "msg" => "The script has started successfully. Please check back later to confirm the status."
            ];

            //update array
            if ($data[$key]['script_id'] == $script_id) {
                $data[$key]['time_started'] = $tstarted;
                $data[$key]['has_started'] = "Yes";
            }
        }

        // //proceed with deletion
        // if (empty($status)) {
        //     $removed = $db->Remove("DELETE FROM templates WHERE id = :i", [
        //         'i' => $script_id
        //     ]);
        //     //check if file exists and delete it too
        //     if (file_exists("../../uploads/templates/" . $temp_file)) {
        //         unlink("../../uploads/templates/" . $temp_file);
        //     }

        //     if (empty($removed)) {
        //         $status = [
        //             "success" => false,
        //             "msg" => "Nothing was deleted. Please try again"
        //         ];
        //     } else {
        //         $status = [
        //             "success" => true,
        //             "msg" => "Template has been deleted successfully"
        //         ];

        //         //unset the array
        //         unset($ts[$key][$script_id]);
        //     }
        // }
    }

    //stop script
    if ($a == "stop_script" && $script_id) {
        $exists = false;
        $temp_file = "";
        $key = 0;
        //check if template exists
        foreach ($data as $dt => $da) {
            if ($da['script_id'] = $script_id) {
                $exists = true;
                $key = $dt;
            }
        }

        //check if temp exists
        if (!$exists) {
            $status = [
                "success" => false,
                "msg" => "The script you're trying to stop does not exist"
            ];
        }

        if (empty($status)) {
            $tstopped = time();
            $db->Update("UPDATE scripts SET has_started = :h, time_stopped = :t WHERE id = :i", [
                'h' => "No",
                't' => $tstopped,
                'i' => $script_id
            ]);

            $status = [
                "success" => true,
                "msg" => "The script has been stopped successfully"
            ];

            //update array
            if ($data[$key]['script_id'] == $script_id) {
                $data[$key]['time_stopped'] = $tstopped;
                $data[$key]['has_started'] = "No";
            }
        }
    }

}
?>
<!Doctype html>

<html>

<head>
    <title>
        View All Scripts
    </title>
    <?php include('../../includes/head.php'); ?>
</head>

<body>
    <section class="container">
        <div class="table-responsive">
            <?php include('../../includes/alert.php'); ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="text-center">S/N</th>
                        <th>Tracking</th>
                        <th>List</th>
                        <th>Template</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                        <th class="text-center">Date Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data) && count($data)): ?>
                    <?php foreach ($data as $d => $a): ?>
                    <tr>
                        <td class="text-center">
                            <?php print(++$d); ?>
                        </td>
                        <td>
                            <?php print($a['track_id']); ?>
                        </td>
                        <td>
                            <?php print($a['list_name']); ?>
                        </td>
                        <td>
                            <?php print($a['temp_name']); ?>
                        </td>
                        <td class="text-center">
                            <?php if ($a['has_started'] == "Yes"): ?>
                            <span class="badge bg-warning">Running</span>
                            <?php elseif ($a['has_started'] == "No"): ?>
                            <span class="badge bg-danger">Not running</span>
                            <?php else: ?>
                            <span class="badge bg-success">Completed</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($a['has_started'] == "Yes"): ?>
                                <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to stop this script?\nIt will start afresh to send emails whenever you choose to start it!')">
                                    <input type="hidden" name="action" value="stop_script">
                                    <input type="hidden" name="script_id" value="<?php print($a['script_id']); ?>">
                                    <button class="btn btn-danger">Stop</button>
                                </form>
                            <button class="btn btn-info btn-view-script" data-sentemails="<?php print($a['total_sent']); ?>" data-timestarted="<?php print(gmdate('D, M d Y', $a['time_started'])); ?>">View</button>
                            <?php elseif ($a['has_started'] == "No"): ?>
                            <button data-scriptid="<?php print($a['script_id']); ?>"
                                data-interval="<?php print($a['scr_interval']); ?>"
                                data-emails="<?php print($a['total_emails']); ?>"
                                data-template="<?php print($a['temp_name']); ?>"
                                data-eta="<?php print(intval($a['scr_interval']) * intval($a['total_emails']) * 60); ?>"
                                class="btn btn-success btn-start-script">Start</button>
                            <?php else: ?>
                            <button class="btn btn-info btn-view-script" data-sentemails="<?php print($a['total_sent']); ?>" data-timestarted="<?php print(gmdate('D, M d Y', $a['time_started'])); ?>">View</button>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php print(gmdate('D, M d Y', $a['date_created'])); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td class="text-center" colspan="7">
                            No scripts found
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
    <div id="modal_start_script" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="temp_title">About this Script</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" id="form_start_script">
                        <input type="hidden" name="action" value="start_script" />
                        <input type="hidden" name="script_id" id="inp_script_id" value="">
                    </form>
                    <p class="p-3" style="background-color: #ddd;">This script will send your template <span id="template" class="text-danger"></span> to <span
                            id="total_emails" class="text-danger"></span> every <span id="interval"
                            class="text-danger"></span> until it is completed.<br/><br/>
                    <strong>Estimated time of completion is: <span class="text-danger" id="eta"></span></strong>    
                        </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" form="form_start_script">Run Script</button>
                </div>
            </div>
        </div>
    </div>
    <div id="modal_view_script" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="temp_title">About this Script</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="p-3" style="background-color: #ddd;">This script was started on <span id="time_started" class="text-danger"></span> and has sent up to <span id="sent_emails" class="text-danger"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            //start script
            Array.from($('.btn-start-script')).forEach(e => {
                $(e).on('click', () => {
                    $('#total_emails').html((Number(e.dataset['emails']) === 1) ? e.dataset['emails'] + " email address": e.dataset['emails'] + " email addresses")
                    $('#interval').html((Number(e.dataset['interval']) === 1) ? e.dataset['interval'] + " sec" : e.dataset['interval'] + " secs")
                    $('#eta').html((Number(e.dataset['eta']) === 1) ? e.dataset['eta'] + " sec" : e.dataset['eta'] + " secs")
                    $('#template').html('('+e.dataset['template']+')')
                    $('#inp_script_id').val(e.dataset['scriptid'])
                    new bootstrap.Modal($("#modal_start_script")[0]).show()
                })
            });
            //view script info
            Array.from($('.btn-view-script')).forEach(e => {
                $(e).on('click', () => {
                    $('#time_started').html(e.dataset['timestarted'])
                    $('#sent_emails').html((Number(e.dataset['sentemails']) === 1) ? e.dataset['sentemails'] + " email" : e.dataset['sentemails'] + " emails")
                    new bootstrap.Modal($("#modal_view_script")[0]).show()
                })
            });
        })
    </script>
</body>

</html>