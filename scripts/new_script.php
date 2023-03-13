<?php
//import functions file
require('../core/functions.php');
$status = [];

//get lists
$lists = $db->SelectAll("SELECT * FROM lists", []);
//get templates
$templates = $db->SelectAll("SELECT * FROM templates", []);

if (empty($lists) || empty($templates)) {
    die("You have to create at least one list and one template before accessing this page");
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    //store in db
    if (empty($status)) {
        $track_id = generateRandomString(7);
        //check if progress exists 
        $progress = $db->SelectOne("SELECT * FROM progress WHERE track_id = :t", [
            't' => $track_id
        ]);

        if (empty($progress)) {
            //create record
            $db->Insert("INSERT INTO progress (track_id) VALUES (:t)", [
                't' => $track_id
            ]);
        }

        $db->Insert("INSERT INTO scripts (track_id, list_id, temp_id, scr_interval, date_created) VALUES (:t, :l, :s, :f, :dt)", [
            't' => $track_id,
            'l' => $_POST['list_id'],
            's' => $_POST['temp_id'],
            'f' => (!(intval($_POST['interval']) > 5) && !(intval($_POST['interval']) < 1)) ? intval($_POST['interval']) : 2,
            'dt' => time()
        ]);
        $status = [
            "success" => true,
            "msg" => "Script has been created successfully"
        ];
    }
}
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <title>Create a new script</title>
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
                            <div class="card-header"><strong>Create a new script</strong>
                            </div>
                            <div class="card-body">
                                <?php include('../includes/alert.php'); ?>
                                <form method="post" id="form_new_script">
                                    <div class="mb-2">
                                        <label class="form-label">Select Mailing List <span class="text-danger">*</span></label>
                                        <select id="inp_list" name="list_id" class="form-control" octavalidate="R,TEXT">
                                            <option value="">Select One</option>
                                            <?php foreach ($lists as $l => $s): ?>
                                            <option value="<?php print($s['id']); ?>">
                                                <?php print($s['list_name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Select Template <span class="text-danger">*</span></label>
                                        <select id="inp_temp" name="temp_id" class="form-control" octavalidate="R,TEXT">
                                            <option value="">Select One</option>
                                            <?php foreach ($templates as $t => $s): ?>
                                            <option value="<?php print($s['id']); ?>">
                                                <?php print($s['temp_name']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Enter Interval <span class="text-danger">*</span></label>
                                        <input ov-range-msg="Interval must be within the range of 1-5s" type="number"
                                            class="form-control" id="inp_fr" octavalidate="R,DIGITS" name="interval" value="1">
                                        <small>Maximum interval is 5 secs. The lower the interval, the lower the time of
                                            completion</small>
                                    </div>
                                    <div class="">
                                        <button class="btn btn-primary">Create Script</button>
                                    </div>
                                </form>
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
    <script>
        $('#form_new_script').on('submit', (e) => {
            const mf = new octaValidate("form_new_script", {
                strictMode: true
            });
            if (mf.validate()) {
                // console.log( mf.status() )
                e.currentTarget.submit();
            } else {
                // console.log( mf.status() )
                e.preventDefault();
            }
        })
    </script>
</body>

</html>