<?php

require('../../core/app.php');
$status = [];

//get all templates
$data = $db->SelectAll("SELECT *, scripts.id AS script_id FROM scripts INNER JOIN templates INNER JOIN lists ON lists.id = scripts.list_id AND templates.id = scripts.temp_id ORDER BY scripts.date_created ASC", []);

//handle script
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $a = trim($_POST['action']);

    $script_id = (isset($_POST['script_id']) && !empty($_POST['script_id'])) ? intval($_POST['script_id']) : 0;

    //start script
    if ($a == "start_script" && $script_id) {
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
                "msg" => "The script you're trying to start does not exist"
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
                "msg" => "The script has started successfully"
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
                        //start, stop, view progress
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
                        <td>
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
                            <button class="btn btn-info btn-view-script" data-sentemails="" data-timestarted="<?php print(gmdate('D, M d Y', $a['time_started'])); ?>">View</button>
                            <?php elseif ($a['has_started'] == "No"): ?>
                            <button data-scriptid="<?php print($a['script_id']); ?>"
                                data-freq="<?php print($a['frequency']); ?>"
                                data-emails="<?php print($a['total_emails']); ?>"
                                class="btn btn-success btn-start-script">Start</button>
                            <?php else: ?>
                            <button class="btn btn-info btn-view-script" data-timestarted="<?php print(gmdate('D, M d Y', $a['time_started'])); ?>">View</button>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php print(gmdate('D, M d Y', $a['date_created'])); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td class="text-center" colspan="5">
                            No schedules found
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
                    <p id="temp_desc" class="p-3" style="background-color: #ddd;">This script will send emails to <span
                            id="total_emails" class="text-danger"></span> every <span id="freq"
                            class="text-danger"></span> until it is completed</p>
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
                    $('#total_emails').html(e.dataset['emails'] + " addresses")
                    $('#freq').html(e.dataset['freq'] + " secs")
                    $('#inp_script_id').val(e.dataset['scriptid'])
                    new bootstrap.Modal($("#modal_start_script")[0]).show()
                })
            });
            //view script info
            Array.from($('.btn-view-script')).forEach(e => {
                $(e).on('click', () => {
                    $('#time_started').html(e.dataset['timestarted'])
                    $('#sent_emails').html(e.dataset['sentemails'] + " emails")
                    new bootstrap.Modal($("#modal_view_script")[0]).show()
                })
            });
        })
    </script>
</body>

</html>