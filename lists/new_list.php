<?php
//import functions file
require('../core/functions.php');
$status = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //check if template exists in db
    $t = $db->SelectOne("SELECT * FROM lists WHERE list_name = :n", [
        'n' => $_POST['list_name']
    ]);

    if (!empty($t)) {
        $status = [
            "success" => false,
            "msg" => "Mailing list already exists"
        ];
    }
    //store in db
    if (empty($status)) {
        //move the template file
        $moveTo = "../uploads/lists/" . $_FILES['list_file']['name'];
        //get csv data into an array and then count
        //https://stackoverflow.com/questions/9139202/how-to-parse-a-csv-file-using-php
        $theCSV = array_map('str_getcsv', file($_FILES['list_file']['tmp_name'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        //the email addresses
        $emails = [];
        //get each string from the csv file and store in array
        foreach ($theCSV as $t => $v) {
            //check if the string from the csv file is an email address and store
            //email from list array
            $eFLAry = array_unique(array_map('checkIfStringIsEmail', $v));
            //loop through results from the emails array
            foreach ($eFLAry as $ee => $ss) {
                //check if array value is an email then store it
                if (filter_var($ss, FILTER_VALIDATE_EMAIL)) {
                    $emails[] = $ss;
                }
            }
        }
        if (move_uploaded_file($_FILES['list_file']['tmp_name'], $moveTo)) {
            $db->Insert("INSERT INTO lists (list_name, list_file, total_emails, date_created) VALUES (:n, :f, :e, :dt)", [
                'n' => $_POST['list_name'],
                'f' => $_FILES['list_file']['name'],
                'e' => count(array_unique($emails)),
                'dt' => time()
            ]);
            $status = [
                "success" => true,
                "msg" => "Mailing list was saved successfully"
            ];
        } else {
            $status = [
                "success" => false,
                "msg" => "Sorry... We couldn't save this list"
            ];
        }
    }
}
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <title>Upload your mailing list</title>
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
                            <div class="card-header"><strong>Upload new mailing list</strong>
                            </div>
                            <div class="card-body">
                                <?php include('../includes/alert.php'); ?>
                                <form method="post" id="form_new_list" enctype="multipart/form-data">
                                    <div class="mb-2">
                                        <label class="form-label">Select File <span class="text-danger">*</span></label>
                                        <input type="file" octavalidate="R" accept=".csv" id="inp_file" name="list_file"
                                            class="form-control" value="">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Name <span class="text-danger">*</span></label>
                                        <input octavalidate="R,TEXT" id="inp_name" name="list_name" class="form-control"
                                            placeholder="My new customers...">
                                    </div>
                                    <div class="">
                                        <button class="btn btn-primary">Upload List</button>
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
</body>

</html>