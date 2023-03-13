<?php

require('../core/app.php');
$status = [];

//get all templates
$ts = $db->SelectAll("SELECT * FROM templates", []);

//handle delete
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $a = trim($_POST['action']);

    $temp_id = (isset($_POST['template']) && !empty($_POST['template'])) ? intval($_POST['template']) : 0;

    //check action
    if ($a == "del_temp") {
        $exists = false;
        $temp_file = "";
        $key = 0;
        //check if template exists
        foreach ($ts as $tt => $ss) {
            if ($ss['id'] = $temp_id) {
                $exists = true;
                $temp_file = $ss['temp_file'];
                $key = $tt;
            }
        }

        //check if temp exists
        if (!$exists) {
            $status = [
                "success" => false,
                "msg" => "The Template you're trying to delete does not exist."
            ];
        }

        //proceed with deletion
        if (empty($status)) {
            $removed = $db->Remove("DELETE FROM templates WHERE id = :i", [
                'i' => $temp_id
            ]);
            //check if file exists and delete it too
            if (file_exists("../uploads/templates/" . $temp_file)) {
                unlink("../uploads/templates/" . $temp_file);
            }

            if (empty($removed)) {
                $status = [
                    "success" => false,
                    "msg" => "Nothing was deleted. Please try again"
                ];
            } else {
                $status = [
                    "success" => true,
                    "msg" => "Template has been deleted successfully"
                ];

                //unset the array
                unset($ts[$key][$temp_id]);
            }
        }
    }
}
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <title>View all templates</title>
    <base href="<?php print(ORIGIN); ?>">
    <?php include('../includes/head.php'); ?>
</head>

<body>
    <?php include('../includes/sidebar.php'); ?>
    <div class="wrapper d-flex flex-column min-vh-100 bg-light">
        <?php include('../includes/header.php'); ?>
        <div class="body flex-grow-1 px-3">
            <div class="container-lg">
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header"><strong>Manage templates</strong>
                            </div>
                            <div class="card-body">
                                <?php include('../includes/alert.php'); ?>
                                <div class="table-responsive">

                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="text-center">S/N</th>
                                                <th>Name</th>
                                                <th>Date Created</th>
                                                <th class="text-center">View</th>
                                                <th class="text-center">Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($ts) && count($ts)): ?>
                                            <?php foreach ($ts as $t => $s): ?>
                                            <tr>
                                                <td class="text-center">
                                                    <?php print(++$t); ?>
                                                </td>
                                                <td>
                                                    <?php print($s['temp_name']); ?>
                                                </td>
                                                <td>
                                                    <?php print(gmdate('D, M d Y', $s['date_created'])); ?>
                                                </td>
                                                <td class="text-center">
                                                    <button data-tempId="<?php print($s['id']); ?>"
                                                        data-title="<?php print($s['temp_name']); ?>"
                                                        data-desc="<?php print($s['temp_desc']); ?>"
                                                        data-file="<?php print('uploads/templates/' . $s['temp_file']); ?>"
                                                        type="button"
                                                        class="btn btn-info text-light btn-view-temp">View</button>
                                                </td>
                                                <td class="text-center">
                                                    <form method="post"
                                                        onsubmit="return confirm('Are you sure you want to delete this template?')">
                                                        <input type="hidden" name="action" value="del_temp">
                                                        <input type="hidden" name="template"
                                                            value="<?php print($s['id']); ?>">
                                                        <button class="btn btn-danger text-light">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php else: ?>
                                            <tr>
                                                <td class="text-center" colspan="5">
                                                    No templates found
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="view_temp_modal" class="modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="temp_title">Modal title</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="temp_desc" class="p-3" style="background-color: #ddd;">Modal body text goes here.</p>
                        <div class="mt-3">
                            <a href="" class="btn btn-success" id="temp_file">View file</a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a class="btn btn-primary" href="" id="temp_upd">Update template</a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php include('../includes/footer.php'); ?>
    </div>
    <!-- CoreUI and necessary plugins-->
    <?php include('../includes/foot.php'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Array.from($('.btn-view-temp')).forEach(e => {
                $(e).on('click', () => {
                    $('#temp_title').html(e.dataset['title'])
                    $('#temp_desc').html(e.dataset['desc'])
                    document.getElementById('temp_file').href = e.dataset['file']
                    document.getElementById('temp_upd').href = 'templates/update_template.php?template=' + e.dataset['tempid']
                    new bootstrap.Modal($("#view_temp_modal")[0]).show()
                })
            });
        })
    </script>
</body>

</html>