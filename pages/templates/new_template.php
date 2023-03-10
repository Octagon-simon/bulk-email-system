<?php
//import functions file
require('../../core/functions.php');
$status = [];

if($_SERVER['REQUEST_METHOD'] == "POST"){
    //check if template exists in db
    $t = $db->SelectOne("SELECT * FROM templates WHERE temp_name = :n", [
        'n' => $_POST['temp_name']
    ]);

    if(!empty($t)){
        $status = [
            "success" => false,
            "msg" => "Template already exists"
        ];
    }
    //store in db
    if(empty($status)){
        //move the template file
        $moveTo = "../../uploads/templates/".$_FILES['temp_file']['name'];
        if(move_uploaded_file($_FILES['temp_file']['tmp_name'], $moveTo)){
            $db->Insert("INSERT INTO templates (temp_name, temp_desc, temp_file, date_created) VALUES (:n, :d, :f, :dt)", [
                'n' => $_POST['temp_name'],
                'd' => $_POST['temp_desc'],
                'f' => $_FILES['temp_file']['name'],
                'dt' => time()
            ]);
            $status = [
                "success" => true,
                "msg" => "Template has been uploaded successfully"
            ];
        }else{
            $status = [
                "success" => false,
                "msg" => "Sorry... We couldn't upload the template file"
            ];
        }
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
            <form method="post" id="form_new_temp" enctype="multipart/form-data">
            <div class="mb-2">
                    <label>Select File <span class="text-danger">*</span></label>
                    <input type="file" octavalidate="R" accept-mime="text/plain,text/html,text/htm" id="inp_file" name="temp_file" class="form-control" value="">
                </div>
                <div class="mb-2">
                    <label>Name <span class="text-danger">*</span></label>
                    <input octavalidate="R,USERNAME" id="inp_name" name="temp_name" class="form-control" placeholder="Pricing Template...">
                </div>
                <div class="mb-3">
                    <label>Description <span class="text-danger">*</span></label>
                    <textarea type="text" octavalidate="R,TEXT" id="inp_desc" name="temp_desc" class="form-control" placeholder="How to price products..."></textarea>
                </div>
                <div class="">
                    <button class="btn btn-primary">Upload Template</button>
                </div>
            </form>
            <script>
                $('#form_new_temp').on('submit', (e) => {
                    const mf = new octaValidate("form_new_temp", {
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