<?php

require('../core/app.php');
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
            if (file_exists("../uploads/lists/" . $list_file)) {
                unlink("../uploads/lists/" . $list_file);
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

<!DOCTYPE html>

<html lang="en">

<head>
    <title>View all lists</title>
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
                            <div class="card-header"><strong>Manage mailing lists</strong>
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
                                                    <?php print($s['list_name']); ?>
                                                </td>
                                                <td>
                                                    <?php print(gmdate('D, M d Y', $s['date_created'])); ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?php print('uploads/lists/' . $s['list_file']); ?>" class="btn btn-info text-light">View file</a>
                                                </td>
                                                <td class="text-center">
                                                    <form method="post"
                                                        onsubmit="return confirm('Are you sure you want to delete this list?')">
                                                        <input type="hidden" name="action" value="del_list">
                                                        <input type="hidden" name="list"
                                                            value="<?php print($s['id']); ?>">
                                                        <button class="btn btn-danger text-light">Delete</button>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include('../includes/footer.php'); ?>
    </div>
    <!-- CoreUI and necessary plugins-->
    <?php include('../includes/foot.php'); ?>

</body>

</html>