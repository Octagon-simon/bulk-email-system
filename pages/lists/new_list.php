<?php
//import functions file
require('../../core/functions.php');
$status = [];

if($_SERVER['REQUEST_METHOD'] == "POST"){
    //check if template exists in db
    $t = $db->SelectOne("SELECT * FROM lists WHERE list_name = :n", [
        'n' => $_POST['list_name']
    ]);

    if(!empty($t)){
        $status = [
            "success" => false,
            "msg" => "Mailing list already exists"
        ];
    }
    //store in db
    if(empty($status)){
        //move the template file
        $moveTo = "../../uploads/lists/".$_FILES['list_file']['name'];
        //get csv data into an array and then count
        //https://stackoverflow.com/questions/9139202/how-to-parse-a-csv-file-using-php
        $emails = array_map('str_getcsv', file($_FILES['list_file']['tmp_name'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        if(move_uploaded_file($_FILES['list_file']['tmp_name'], $moveTo)){
            $db->Insert("INSERT INTO lists (list_name, list_file, total_emails, date_created) VALUES (:n, :f, :e, :dt)", [
                'n' => $_POST['list_name'],
                'f' => $_FILES['list_file']['name'],
                'e' => count($emails),
                'dt' => time()
            ]);
            $status = [
                "success" => true,
                "msg" => "Mailing list was saved successfully"
            ];
        }else{
            $status = [
                "success" => false,
                "msg" => "Sorry... We couldn't save this list"
            ];
        }
    }
}
?>

<!Doctype html>
<html>

<head>
    <title>Upload A New Mailing List</title>
    <?php include('../../includes/head.php'); ?>
</head>

<body>
    <section>
        <div class="container">
            <?php include('../../includes/alert.php'); ?>
            <form method="post" id="form_new_list" enctype="multipart/form-data">
            <div class="mb-2">
                    <label>Select File <span class="text-danger">*</span></label>
                    <input type="file" octavalidate="R" accept=".csv" id="inp_file" name="list_file" class="form-control" value="">
                </div>
                <div class="mb-2">
                    <label>Name <span class="text-danger">*</span></label>
                    <input octavalidate="R,USERNAME" id="inp_name" name="list_name" class="form-control" placeholder="My new customers...">
                </div>
                <div class="">
                    <button class="btn btn-primary">Upload List</button>
                </div>
            </form>
            <script>
                $('#form_new_list').on('submit', (e) => {
                    const mf = new octaValidate("form_new_list", {
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