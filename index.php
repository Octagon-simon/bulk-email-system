<?php
//import functions file
require('core/app.php');

$status = checkSystemStatus();

$total_sent = $total_temps = $total_scripts = $contacts = 0;

//get templates
$total_temps = $db->SelectOne("SELECT COUNT(*) AS total FROM templates", []);
//get scripts
$total_scripts = $db->SelectOne("SELECT COUNT(*) AS total FROM scripts", []);
//get lists
$all_lists = $db->SelectAll("SELECT total_emails FROM lists", []);

//get total contacts
foreach ($all_lists as $a => $s) {
  $contacts += intval($s['total_emails']);
}

//get all templates
$data = $db->SelectAll("SELECT * FROM scripts INNER JOIN templates INNER JOIN lists INNER JOIN progress ON progress.track_id = scripts.track_id AND lists.id = scripts.list_id AND templates.id = scripts.temp_id ORDER BY scripts.date_created ASC", []);

//total emails sent
if (!empty($data)) {
  //loop through data
  foreach ($data as $d => $a) {
    $total_sent += intval($a['total_sent']);
  }
}

?>

<!DOCTYPE html>
<!-- Breadcrumb-->
<html lang="en">

<head>
  <title>Bulk Email System</title>
  <?php include('includes/head.php'); ?>
</head>

<body>
  <!--Sidebar-->
  <?php include('includes/sidebar.php'); ?>
  <div class="wrapper d-flex flex-column min-vh-100 bg-light">
    <!--header -->
    <?php include('includes/header.php'); ?>
    <div class="body flex-grow-1 px-3">
      <div class="container-lg">
        <div class="row">
          <div class="col-sm-6 col-lg-3">
            <div class="card mb-4 text-white bg-primary">
              <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                <div>
                  <div class="fs-4 fw-semibold">
                    <?php print($total_temps['total']); ?>
                  </div>
                  <div><?php ($total_temps['total'] == 1) ? print("Template") : print("Templates"); ?></div>
                </div>
                <div class="dropdown">
                  <button class="btn btn-transparent text-white p-0" type="button" data-coreui-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <svg class="icon">
                      <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-options"></use>
                    </svg>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item"
                      href="templates/new_template.php">Upload new template</a><a class="dropdown-item"
                      href="templates/all_templates.php">Manage templates</a></div>
                </div>
              </div>
              <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                <div class="chart" id="card-chart1" height="70"></div>
              </div>
            </div>
          </div>
          <!-- /.col-->
          <div class="col-sm-6 col-lg-3">
            <div class="card mb-4 text-white bg-info">
              <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                <div>
                  <div class="fs-4 fw-semibold">
                    <?php print($contacts); ?>
                  </div>
                  <!--total emails -->
                  <div><?php ($contacts == 1) ? print("Contact") : "Contacts"; ?></div>
                </div>
                <div class="dropdown">
                  <button class="btn btn-transparent text-white p-0" type="button" data-coreui-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <svg class="icon">
                      <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-options"></use>
                    </svg>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="lists/new_list.php">Upload new list</a>
                    <a class="dropdown-item" href="lists/all_lists.php">Manage lists</a>
                  </div>
                </div>
              </div>
              <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                <div class="chart" id="card-chart2" height="70"></div>
              </div>
            </div>
          </div>
          <!-- /.col-->
          <div class="col-sm-6 col-lg-3">
            <div class="card mb-4 text-white bg-warning">
              <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                <div>
                  <div class="fs-4 fw-semibold">
                    <?php print($total_scripts['total']); ?>
                  </div>
                  <div><?php ($total_scripts['total'] == 1) ? print("Script") : "Scripts"; ?></div>
                </div>
                <div class="dropdown">
                  <button class="btn btn-transparent text-white p-0" type="button" data-coreui-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <svg class="icon">
                      <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-options"></use>
                    </svg>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end"><a class="dropdown-item"
                      href="scripts/new_script.php">Create new script</a><a class="dropdown-item"
                      href="scripts/all_scripts.php">Manage scripts</a>
                  </div>
                </div>
              </div>
              <div class="c-chart-wrapper mt-3" style="height:70px;">
                <div class="chart" id="card-chart3" height="70"></div>
              </div>
            </div>
          </div>
          <!-- /.col-->
          <!-- /.col-->
          <div class="col-sm-6 col-lg-3">
            <div class="card mb-4 text-white bg-danger">
              <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                <div>
                  <div class="fs-4 fw-semibold">
                    <?php print($total_sent); ?>
                  </div>
                  <div><?php ($total_sent == 1) ? print("Email sent") : print("Emails sent"); ?></div>
                </div>
              </div>
              <div class="c-chart-wrapper mt-3" style="height:70px;">
                <div class="chart" id="card-chart4" height="70"></div>
              </div>
            </div>
          </div>
          <!-- /.col-->
        </div>
        <!-- /.row-->
        <div class="row mb-4">
        <div class="col-md-6 col-sm-12 mb-4 mb-md-0">
            <div class="card">
              <div class="card-header">Stay in touch</div>
              <div class="card-body">
                <div class="alert alert-info mb-2">
                  <p class="mb-0">Follow me now on Twitter, Facebook and linkedIn</p>
                </div>
              <div class="btn-group justify-content-center d-flex" role="group" aria-label="Horizontal button group">
                  <a aria-label="Follow me on Twitter" href="https://twitter.com/ugorji_simon" class="btn btn-info text-white">
                <svg class="icon icon-2xl m-2">
                    <use xlink:href="vendors/@coreui/icons/svg/brand.svg#cib-twitter"></use>
                  </svg>  
                </a>
                <a href="https://facebook.com/simon.ugorji.106" class="btn btn-outline-info">
                <svg class="icon icon-2xl m-2">
                    <use xlink:href="vendors/@coreui/icons/svg/brand.svg#cib-facebook-f"></use>
                  </svg></a>

                  <a href="https://www.linkedin.com/in/simon-ugorji-57a6a41a3/" class="btn btn-info text-white">
                <svg class="icon icon-2xl m-2">
                    <use xlink:href="vendors/@coreui/icons/svg/brand.svg#cib-linkedin"></use>
                  </svg> </a>
              </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-sm-12">
            <div class="card">
              <div class="card-header">System Status</div>
              <div class="card-body">
                <?php include('includes/alert.php'); ?>
              </div>
            </div>
          </div>
        </div>
    
        <div class="row">
          <div class="col-md-12">
            <div class="card mb-4">
              <div class="card-header">Scripts</div>
              <div class="card-body">
                <div class="mb-3 alert alert-info">
                  <p class="mb-1">This section shows only 5 recently created scripts.
                  </p>
                  <a href="scripts/all_scripts.php" class="btn btn-info text-light radius-0">View all scripts</a>
                </div>
                <!-- /.row-->
                <div class="table-responsive">
                  <table class="table border mb-0">
                    <thead class="table-light fw-semibold">
                      <tr>
                        <th class="text-center">S/N</th>
                        <th>Tracking</th>
                        <th>List</th>
                        <th>Template</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Date Created</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (!empty($data) && count($data)): ?>
                      <?php foreach ($data as $d => $a): ?>
                      <tr>
                        <td class="text-center">
                          <?php print(++$d); ?>
                        </td>
                        <td>
                          <?php print($a['track_id']); ?>
                        </td>
                        <td>
                          <?php print($a['list_name']); ?>
                        </td>
                        <td>
                          <?php print($a['temp_name']); ?>
                        </td>
                        <td class="text-center">
                          <?php if ($a['has_started'] == "Yes"): ?>
                          <span class="badge bg-warning">Running</span>
                          <?php elseif ($a['has_started'] == "No"): ?>
                          <span class="badge bg-danger">Not running</span>
                          <?php else: ?>
                          <span class="badge bg-success">Completed</span>
                          <?php endif; ?>
                        </td>
                        <td class="text-center">
                          <?php print(gmdate('D, M d Y', $a['date_created'])); ?>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                      <?php else: ?>
                      <tr>
                        <td class="text-center" colspan="7">
                          No scripts found
                        </td>
                      </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <!-- /.col-->
        </div>
        <!-- /.row-->
      </div>
    </div>
    <!--footer -->
    <?php include('includes/footer.php'); ?>
  </div>
  <!-- CoreUI and necessary plugins-->
  <?php include('includes/foot.php'); ?>

</body>

</html>