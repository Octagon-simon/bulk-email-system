<?php

require('../../core/app.php');
$status = [];

//get all lists
$ts = $db->SelectAll("SELECT * FROM lists", []);

//handle delete
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $a = trim($_POST['action']);

    $temp_id = (isset($_POST['list']) && !empty($_POST['list'])) ? intval($_POST['list']) : 0;

    //check action
    if ($a == "del_list") {
        $exists = false;
        $list_file = "";
        $key = 0;
        //check if list exists
        foreach ($ts as $tt => $ss) {
            if ($ss['id'] = $temp_id) {
                $exists = true;
                $list_file = $ss['list_file'];
                $key = $tt;
            }
        }

        //check if temp exists
        if (!$exists) {
            $status = [
                "success" => false,
                "msg" => "The list you're trying to delete does not exist."
            ];
        }

        //proceed with deletion
        if (empty($status)) {
            $removed = $db->Remove("DELETE FROM lists WHERE id = :i", [
                'i' => $temp_id
            ]);
            //check if file exists and delete it too
            if (file_exists("../../uploads/lists/" . $list_file)) {
                unlink("../../uploads/lists/" . $list_file);
            }

            if (empty($removed)) {
                $status = [
                    "success" => false,
                    "msg" => "Nothing was deleted. Please try again"
                ];
            } else {
                $status = [
                    "success" => true,
                    "msg" => "List has been deleted successfully"
                ];

                //unset the array
                unset($ts[$key][$temp_id]);
            }
        }
    }
}
?>
<!Doctype html>

<html>

<head>
    <title>
        View All Lists
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
                            <?php print($s['list_name']); ?>
                        </td>
                        <td>
                            <?php print(gmdate('D, M d Y', $s['date_created'])); ?>
                        </td>
                        <td class="text-center">
                            <button data-listId="<?php print($s['id']); ?>"
                                data-title="<?php print($s['list_name']); ?>"
                                data-file="<?php print('uploads/lists/' . $s['list_file']); ?>" type="button"
                                class="btn btn-info btn-view-list">View</button>
                        </td>
                        <td class="text-center">
                            <form method="post"
                                onsubmit="return confirm('Are you sure you want to delete this list?')">
                                <input type="hidden" name="action" value="del_list">
                                <input type="hidden" name="list" value="<?php print($s['id']); ?>">
                                <button class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td class="text-center" colspan="5">
                            No lists found
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
    <div id="view_list_modal" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="list_title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mt-3">
                        <a href="" class="btn btn-success" id="list_file">View file</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Array.from($('.btn-view-list')).forEach(e => {
                $(e).on('click', () => {
                    $('#list_title').html(e.dataset['title'])
                    document.getElementById('list_file').href = e.dataset['file']
                    new bootstrap.Modal($("#view_list_modal")[0]).show()
                })
            });
        })
    </script>
</body>

</html>