<?php

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $action = trim($_POST['action']);

  if ($action === "getout") {
    session_destroy();
    print("<script>window.location.href='setup.php?an=".base64_encode("log")."';</script>");
  }
}

?>

<header class="header header-sticky mb-4">
  <div class="container-fluid">
    <button class="header-toggler px-md-0 me-md-3" type="button"
      onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
      <svg class="icon icon-lg">
        <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-menu"></use>
      </svg>
    </button><a class="header-brand d-md-none" href="#">
      <img src="assets/brand/bes.png" width="50px" class="img-fluid" />
    </a>
    <ul class="header-nav ms-3">
      <li class="nav-item dropdown"><a class="nav-link py-0" data-coreui-toggle="dropdown" href="#" role="button"
          aria-haspopup="true" aria-expanded="false">
          <div class="avatar avatar-md"><img class="avatar-img" src="assets/img/avatars/4.jpg" alt="BES User"></div>
        </a>
        <div class="dropdown-menu dropdown-menu-end pt-0">
          <div class="dropdown-header bg-light py-2">
            <div class="fw-semibold">Account</div>
          </div>
          <form class="dropdown-item" method="post">
            <input type="hidden" name="action" value="getout" />
            <button style="all:unset;cursor:pointer" type="submit"><svg class="icon me-2">
                <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-account-logout"></use>
              </svg>Logout</button>
          </form>
        </div>
      </li>
    </ul>
  </div>
</header>