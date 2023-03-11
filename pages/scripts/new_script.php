<?php
//import functions file
require('../../core/functions.php');
$status = [];

//get lists
$lists = $db->SelectAll("SELECT * FROM lists", []);
//get templates
$templates = $db->SelectAll("SELECT * FROM templates", []);

if(empty($lists) || empty($templates)){
    die("You have to create at least one list and one template before accessing this page");
}

if($_SERVER['REQUEST_METHOD'] == "POST"){

    //store in db
    if(empty($status)){
        $db->Insert("INSERT INTO schedules (track_id, list_id, temp_id, frequency, date_created) VALUES (:t, :l, :s, :f, :dt)", [
            't' => generateRandomString(7),
            'l' => $_POST['list_id'],
            's' => $_POST['temp_id'],
            'f' => intval($_POST['frequency']),
            'dt' => time()
        ]);
        $status = [
            "success" => true,
            "msg" => "Schedule has been created successfully"
        ];
    }
}
?>

<!Doctype html>
<html>

<head>
    <title>Create a New Schedule</title>
    <?php include('../../includes/head.php'); ?>
</head>

<body>
    <section>
        <div class="container">
            <?php include('../../includes/alert.php'); ?>
            <form method="post" id="form_new_sched">
            <div class="mb-2">
                    <label>Select Mailing List <span class="text-danger">*</span></label>
                    <select id="inp_list" name="list_id" class="form-control" octavalidate="R,TEXT">
                        <?php foreach($lists as $l => $s) : ?>
                            <option value="<?php print($s['id']); ?>">
                            <?php print($s['list_name']); ?>
                            </option>
                        <?php endforeach;?>
                    </select>
                </div>
                <div class="mb-2">
                <label>Select Template <span class="text-danger">*</span></label>
                    <select id="inp_temp" name="temp_id" class="form-control" octavalidate="R,TEXT">
                        <?php foreach($templates as $t => $s) : ?>
                            <option value="<?php print($s['id']); ?>">
                            <?php print($s['temp_name']); ?>
                            </option>
                        <?php endforeach;?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Enter Frequency <span class="text-danger">*</span></label>
                    <input ov-range-msg="Frequency must be within the range of 1-5s" type="number" class="form-control" id="inp_fr" octavalidate="R,DIGITS" name="frequency">
                    <small>Maximum frequency is 5 secs</small>
                </div>
                <div class="">
                    <button class="btn btn-danger">Create Schedule</button>
                </div>
            </form>
            <script>
                $('#form_new_sched').on('submit', (e) => {
                    const mf = new octaValidate("form_new_sched", {
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
        </div>
    </section>
</body>

</html>