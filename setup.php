<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
//import functions file
require('core/functions.php');
$status = [];
$an = null;
if (isset($_SESSION['bes_user']) && !empty($_SESSION['bes_user']) && (time() < intval($_SESSION['bes_user']))) {
    header("Location: index.php");
    exit();
}

if (isset($_SESSION['an']) && !empty($_SESSION['an'])) {
    $an = $_SESSION['an'];
} elseif (isset($_GET) && !empty($_GET['an'])) {
    $an = base64_decode(htmlspecialchars($_GET['an']));
    //check value of $an
    if ($an == "reg") {
        $_SESSION['an'] = "reg";
    } elseif ($an == "log") {
        //login
        $_SESSION['an'] = "log";
    }
} else {
    $an = null;
}

$title = ($an == "reg") ? "Create An Account" : "Login to your Account";

//handle form
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if ($an === "reg") {
        if (isset($_POST['uname']) && !empty($_POST['uname']) && isset($_POST['pass']) && !empty($_POST['pass'])) {
            //CREATE USER
            $db->Insert("INSERT INTO user (username, secret) VALUES (:u, :s)", [
                'u' => $_POST['uname'],
                's' => password_hash($_POST['pass'], PASSWORD_BCRYPT)
            ]);
            //create session
            $_SESSION['bes_user'] = strtotime("+7 hours", time());
            //set status
            $status = [
                "success" => true,
                "msg" => "Account created successfully"
            ];
        } else {
            $status = [
                "success" => false,
                "msg" => "You must provide a Username and a password"
            ];
        }
    } elseif ($an === "log") {
        $user = $db->SelectOne("SELECT * FROM user LIMIT 1", []);

        if (isset($_POST['uname']) && !empty($_POST['uname']) && isset($_POST['pass']) && !empty($_POST['pass'])) {
            if ($user['username'] == $_POST['uname'] && password_verify($_POST['pass'], $user['secret'])) {
                //create session
                $_SESSION['bes_user'] = strtotime("+7 hours", time());
                //set status
                $status = [
                    "success" => true,
                    "msg" => "Login successful. Please wait..."
                ];
            } else {
                //set status
                $status = [
                    "success" => false,
                    "msg" => "Invalid username or password"
                ];
            }
        } else {
            $status = [
                "success" => false,
                "msg" => "You must provide a Username and a password"
            ];
        }
    }
    unset($_POST);
}

?>

<!Doctype html>
<html>

<head>
    <title>
        <?php print($title); ?>
    </title>
    <?php include('includes/head.php'); ?>
    <style>
        .auth-wrapper {
            width: calc(100% - 20px);
            box-shadow: 0px 0px 8px #ddd;
            padding: 20px;
            border-radius: 10px;
            transform: translate(-50%, -50%);
            position: absolute;
            top: 50%;
            left: 50%;
            max-width: 400px;
            background-color: #fff !important;
        }

        html,
        body {
            background-color: #eee;
        }
    </style>
</head>

<body>
    <section>
        <div class="container">

            <?php if ($an === "reg"): ?>
            <div class="auth-wrapper">
                <?php if (!empty($status)): ?>
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
                    //disable btn
                    Array.from($('.btn')).forEach(e => {
                        e.classList.toggle('disabled');
                        $(e).attr('disabled', 'disabled');
                    })
                    //redirect
                    setTimeout(() => {
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
                <?php else: ?>
                <div class="alert alert-info">
                    <p class="mb-0">You are solely responsible for any emails sent with this software.</p>
                </div>
                <?php endif; ?>
                <h1>Register</h1>
                <p class="text-medium-emphasis">Create your account</p>
                <form method="post" id="form_reg">
                    <div class="mb-3">
                        <label class="form-label">Enter username</label>
                        <div class="input-group" id="inp_uname_wrapper">
                            <span class="input-group-text">
                                <svg class="icon">
                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-user"></use>

                                </svg></span>
                            <input placeholder="firefighter" octavalidate="R,USERNAME" id="inp_uname" name="uname"
                                class="form-control" value="">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Enter password</label>
                        <div class="input-group" id="inp_pwd_wrapper"><span class="input-group-text">
                                <svg class="icon">
                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-lock-locked">
                                    </use>
                                </svg></span>
                            <input placeholder="********" type="password" octavalidate="R,PWD" id="inp_pass" name="pass"
                                class="form-control" value="">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Repeat password</label>
                        <div class="input-group" id="inp_rpwd_wrapper"><span class="input-group-text">
                                <svg class="icon">
                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-lock-locked">
                                    </use>
                                </svg></span>
                            <input id="inp_con_pass" class="form-control" type="password" placeholder="Repeat password"
                                equalto="inp_pass">
                        </div>
                    </div>
                    <button class="btn btn-block btn-primary">Create Account</button>
                </form>

            </div>

            <script>
                $('#form_reg').on('submit', (e) => {
                    console.log(e)
                    const mf = new octaValidate("form_reg", {
                        strictMode: true,
                        errorElem: {
                            "inp_uname": "inp_uname_wrapper",
                            "inp_pass": "inp_pwd_wrapper",
                            "inp_con_pass": "inp_rpwd_wrapper"
                        }
                    });
                    if (mf.validate()) {
                        e.currentTarget.submit();
                    } else {
                        e.preventDefault();
                    }
                })
            </script>
            <?php elseif ($an === "log"): ?>
            <div class="auth-wrapper">
                <?php if (!empty($status)): ?>
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
                    //disable btn
                    Array.from($('.btn')).forEach(e => {
                        e.classList.toggle('disabled');
                        $(e).attr('disabled', 'disabled');
                    })
                    //redirect
                    setTimeout(() => {
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
                <?php else: ?>
                <div class="alert alert-info">
                    <p class="mb-0">You are solely responsible for any emails sent with this software.</p>
                </div>
                <?php endif; ?>
                <h1>Login</h1>
                <p class="text-medium-emphasis">Sign In to your account</p>
                <form method="post" id="form_log">
                    <div class="mb-3">
                        <label class="form-label">Enter username</label>
                        <div class="input-group" id="inp_uname_wrapper"><span class="input-group-text">
                                <svg class="icon">
                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-user"></use>
                                </svg></span>
                            <input placeholder="firefighter" octavalidate="R,USERNAME" id="inp_uname" name="uname"
                                class="form-control" value="">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Enter password</label>
                        <div class="input-group" id="inp_pwd_wrapper"><span class="input-group-text">
                                <svg class="icon">
                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-lock-locked"></use>
                                </svg></span>
                            <input placeholder="********" type="password" octavalidate="R,PWD" id="inp_pass" name="pass"
                                class="form-control" value="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <button class="btn btn-primary px-4">Login</button>
                        </div>
                        <div class="col-6 text-end">
                        </div>
                    </div>
                </form>
            </div>
            <script>
                $('#form_log').on('submit', (e) => {
                    const mf = new octaValidate("form_log", {
                        strictMode: true,
                        errorElem: {
                            "inp_uname": "inp_uname_wrapper",
                            "inp_pass": "inp_pwd_wrapper"
                        }
                    });
                    if (mf.validate()) {
                        e.currentTarget.submit();
                    } else {
                        e.preventDefault();
                    }
                })
            </script>
            <?php else: ?>
            <p>We are sorry... This page is restricted</p>
            <?php endif; ?>
        </div>
    </section>
    <?php include('includes/foot.php'); ?>
</body>

</html>