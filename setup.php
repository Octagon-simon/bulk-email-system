<?php
if (session_status() === PHP_SESSION_NONE) session_start();
//import functions file
require('core/functions.php');
$status = [];
$an = null;
if(isset($_SESSION['bes_user']) && !empty($_SESSION['bes_user'])){
    header("Location: index.php");
    exit();
}

if(isset($_SESSION['an']) && !empty($_SESSION['an'])){
    $an = $_SESSION['an'];
}elseif($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_GET['an'])){
    $an = base64_decode(htmlspecialchars($_GET['an']));
    //check value of $an
    if($an == "reg"){
        $_SESSION['an'] = "reg";
    }elseif($an == "log"){
        //login
        $_SESSION['an'] = "log";
    }
}else{
    $an = null;
}

$title = ($an == "reg") ? "Create An Account" : "Login to your Account";

//handle form
if($_SERVER['REQUEST_METHOD'] == "POST"){
    if($an === "reg"){
        //CREATE USER
        $db->Insert("INSERT INTO user (username, secret) VALUES (:u, :s)", [
            'u' => $_POST['uname'],
            's' => password_hash($_POST['pass'], PASSWORD_BCRYPT)
        ]);
        //create session
        $_SESSION['bes_user'] = time();
        //set status
        $status = [
            "success" => true,
            "msg" => "Account created successfully"
        ];
    }elseif($an === "log"){
        $user = $db->SelectOne("SELECT * FROM user LIMIT 1", []);
        if($user['username'] == $_POST['uname'] && password_verify($_POST['pass'], $user['secret'])){
            //create session
            $_SESSION['bes_user'] = time();
            //set status
            $status = [
                "success" => true,
                "msg" => "Login successful"
            ];
        }else{
            //set status
            $status = [
                "success" => false,
                "msg" => "Invalid username or password"
            ];
        }
    }
}


?>

<!Doctype html>
<html>
<head>
    <title><?php print($title); ?></title>
    <?php include('includes/head.php'); ?>
</head>

<body>
    <section>
        <div class="container">
        <?php 
        if (
            isset($status['success']) && is_bool($status['success'])
            && isset($status['msg']) && !empty($status['msg'])
        ) {
            if ($status['success'] === true):
        ?>
        <p class="alert alert-success mb-3 radius-0 text-center">
            <?php print($status['msg']); ?>
        </p>
        <script>
            setTimeout( () => {
                window.location.href = "index.php";
            }, 3000)
        </script>
        <?php elseif ($status['success'] === false):

        ?>
        <p class="alert alert-danger mb-3 radius-0 text-center">
            <?php print($status['msg']); ?>
        </p>
        <?php endif;
        }
        ?>
            <?php if($an === "reg") : ?>
                <form method="post" id="form_reg">
                    <div class="mb-2">
                        <label>Username</label>
                        <input octavalidate="R,USERNAME" id="inp_uname" name="uname" class="form-control" value="">
                    </div>
                    <div class="mb-2">
                        <label>Password</label>
                        <input type="password" octavalidate="R,PWD" id="inp_pass" name="pass" class="form-control" value="">
                    </div>
                    <div class="">
                        <button class="btn btn-primary">Create Account</button>
                    </div>
                </form>
                <script>
                    $('#form_reg').on('submit', (e) => {
                        const mf = new octaValidate("form_reg");
                        if(mf.validate()){
                            e.currentTarget.submit();
                        }else{
                            e.preventDefault();
                        }
                    })
                </script>
            <?php elseif($an === "log") : ?>
                <form method="post" id="form_log">
                    <div class="mb-2">
                        <label>Username</label>
                        <input octavalidate="R,USERNAME" id="inp_uname" name="uname" class="form-control" value="">
                    </div>
                    <div class="mb-2">
                        <label>Password</label>
                        <input type="password" octavalidate="R,PWD" id="inp_pass" name="pass" class="form-control" value="">
                    </div>
                    <div class="">
                        <button class="btn btn-primary">Login</button>
                    </div>
                </form>
                <script>
                    $('#form_log').on('submit', (e) => {
                        const mf = new octaValidate("form_log");
                        if(mf.validate()){
                            e.currentTarget.submit();
                        }else{
                            e.preventDefault();
                        }
                    })
                </script>
            <?php else : ?>
                <p>We are sorry... This page is restricted</p>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>