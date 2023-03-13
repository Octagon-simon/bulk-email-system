<?php

//import app
require('../core/app.php');

$status = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //check if template exists in db
    $t = $db->SelectOne("SELECT * FROM templates WHERE temp_name = :n", [
        'n' => $_POST['temp_name']
    ]);

    if (!empty($t)) {
        $status = [
            "success" => false,
            "msg" => "Template already exists"
        ];
    }
    //store in db
    if (empty($status)) {
        //move the template file
        $moveTo = "../uploads/templates/" . $_FILES['temp_file']['name'];
        if (move_uploaded_file($_FILES['temp_file']['tmp_name'], $moveTo)) {
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
        } else {
            $status = [
                "success" => false,
                "msg" => "Sorry... We couldn't upload the template file"
            ];
        }
    }
}
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <title>Create a new template</title>
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
                            <div class="card-header"><strong>Upload a new template</strong>
                            </div>
                            <div class="card-body">
                                <?php if (empty($status)): ?>
                                <div class="alert alert-info mb-3">
                                    <p class="mb-0"><strong>Note:</strong> The name of your template is the subject of
                                        the email that will be sent to your customers</p>
                                </div>
                                <?php else: ?>
                                <?php include('../includes/alert.php'); ?>
                                <?php endif; ?>
                                <form method="post" id="form_new_temp" enctype="multipart/form-data">
                                    <div class="mb-2">
                                        <label class="form-label">Select File <span class="text-danger">*</span></label>
                                        <input type="file" octavalidate="R" accept-mime="text/plain,text/html,text/htm"
                                            id="inp_file" name="temp_file" class="form-control" value="">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Name <span class="text-danger">*</span></label>
                                        <input octavalidate="R,TEXT" id="inp_name" name="temp_name" class="form-control"
                                            placeholder="Pricing Template...">
                                        <small>Name should be the subject of the Email</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description <span class="text-danger">*</span></label>
                                        <textarea type="text" octavalidate="R,TEXT" id="inp_desc" name="temp_desc"
                                            class="form-control" placeholder="How to price products..."></textarea>
                                    </div>
                                    <div class="">
                                        <button class="btn btn-primary">Upload Template</button>
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
</body>

</html>