<?php

//import functions file
require('../../core/app.php');
$status = [];
$tmp = 0;

if(isset($_SESSION['tmp']) && !empty($_SESSION['tmp'])){
    $tmp = $_SESSION['tmp'];
}else if(isset($_GET) && !empty($_GET['template'])){
    $tmp = intval($_GET['template']);
    $_SESSION['tmp'] = $tmp;
}else{
    die("Invalid parameter passed");
}

//get template from db
$template = $db->SelectOne("SELECT * FROM templates WHERE id = :i", ['i' => $tmp]);

if(empty($tmp) || empty($template)) die("Invalid parameter passed");

//update template
if($_SERVER['REQUEST_METHOD'] == "POST"){
    //check if template name exists in db
    $t = $db->SelectOne("SELECT * FROM templates WHERE temp_name = :n", [
        'n' => $_POST['temp_name']
    ]);

    if(!empty($t)){
        $status = [
            "success" => false,
            "msg" => "Template name already exists"
        ];
    }
    //store in db
    if(empty($status)){
        $fe = false;
        //check if user selected a file
        if(!empty($_FILES) && isset($_FILES['temp_file']) && !empty($_FILES['temp_file']['name'])){
            //delete previous file if it exists
            if(file_exists("../../uploads/templates/".$template['temp_file'])){
                unlink("../../uploads/templates/".$template['temp_file']);
            }
            $moveTo = "../../uploads/templates/".$_FILES['temp_file']['name'];
            move_uploaded_file($_FILES['temp_file']['tmp_name'], $moveTo);
            $fe = true;
        }
        //update record
        $db->Update("UPDATE templates SET temp_name = :n, temp_desc = :d, temp_file = :f WHERE id = :i", [
            'n' => (!empty($_POST['temp_name'])) ? $_POST['temp_name'] : $template['temp_name'],
            'd' => (!empty($_POST['temp_desc'])) ? $_POST['temp_desc'] : $template['temp_desc'],
            'f' => ($fe)? $_FILES['temp_file']['name'] : $template['temp_file'],
            'i' => $tmp
        ]);
        $status = [
            "success" => true,
            "msg" => "Template has been updated successfully"
        ];
        //delete id from session
        unset($_SESSION['tmp']);
    }
}
?>

<!Doctype html>
<html>

<head>
    <title>Create a New Email Template</title>
    <?php include('../../includes/head.php'); ?>
</head>

<body>
    <section>
        <div class="container">
            <?php include('../../includes/alert.php'); ?>
            <form method="post" id="form_upd_temp" enctype="multipart/form-data">
            <div class="mb-2">
                    <label>Select File <span class="text-danger">*</span></label>
                    <input type="file" octavalidate="R" accept-mime="text/plain,text/html,text/htm" id="inp_file" name="temp_file" class="form-control">
                </div>
                <div class="mb-2">
                    <label>Name <span class="text-danger">*</span></label>
                    <input octavalidate="R,TEXT" id="inp_name" name="temp_name" class="form-control" placeholder="Pricing Template..." value="<?php print($template['temp_name']); ?>">
                </div>
                <div class="mb-3">
                    <label>Description <span class="text-danger">*</span></label>
                    <textarea type="text" octavalidate="R,TEXT" id="inp_desc" name="temp_desc" class="form-control" placeholder="How to price products..."><?php print($template['temp_desc']); ?></textarea>
                </div>
                <div class="">
                    <button class="btn btn-primary">Update Template</button>
                </div>
            </form>
            <script>
                $('#form_upd_temp').on('submit', (e) => {
                    const mf = new octaValidate("form_upd_temp", {
                        strictMode: true
                    });
                    if (mf.validate()) {
                        e.currentTarget.submit();
                    } else {
                        e.preventDefault();
                    }
                })
            </script>
        </div>
    </section>
</body>

</html>