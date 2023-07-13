<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <!--<a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
    </a>-->
    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
      <?php if ($_SESSION['admin_role'] == 1){
              echo '<a class="nav-link" href="/adminlogin">';
            } else {
              echo '<a class="nav-link" href="/adminlogin">';
            }?>
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Úvod</span></a>
    </li>

    <!-- Divider -->
    <?php if ($_SESSION['admin_role'] == 1){
    ?>
      <hr class="sidebar-divider" style="margin-bottom: 0!important">

      <!-- Heading -->

      <!-- Nav Item - Pages Collapse Menu -->
      <li class="nav-item">
          <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSettings"
              aria-expanded="true" aria-controls="collapseSettings">
              <i class="fas fa-fw fa-wrench"></i>
              <span><?php echo $message['Settings']?></span>
          </a>
          <div id="collapseSettings" class="collapse" aria-labelledby="headingSettings" data-parent="#accordionSidebar">
              <div class="bg-white py-2 collapse-inner rounded">
                  <a class="collapse-item" href="/adminlogin/settings.php"><?php echo $message['Global settings']?></a>
                  <a class="collapse-item" href="/adminlogin/localization_settings.php"><?php echo $message['Localization']?></a>
              </div>
          </div>
      </li>

      <!-- Nav Item - Utilities Collapse Menu -->
      <li class="nav-item">
          <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAccounts"
              aria-expanded="true" aria-controls="collapseAccounts">
              <i class="fas fa-fw fa-users"></i>
              <span><?php echo $message['Accounts']?></span>
          </a>
          <div id="collapseAccounts" class="collapse" aria-labelledby="headingAccounts"
              data-parent="#accordionSidebar">
              <div class="bg-white py-2 collapse-inner rounded">
                  <a class="collapse-item" href="/adminlogin/admins.php"><?php echo $message['Admins']?></a>
                  <a class="collapse-item" href="/adminlogin/admin_permission.php"><?php echo $message['Admin permissions']?></a>
                  <a class="collapse-item" href="/adminlogin/admin_module_permissions"><?php echo $message['Admin module permissions']?></a>
                  <a class="collapse-item" href="/adminlogin/users.php"><?php echo $message['Users']?></a>
                  <a class="collapse-item" href="/adminlogin/user_permissions.php"><?php echo $message['User permissions']?></a>
                  <a class="collapse-item" href="/adminlogin/user_module_permissions.php"><?php echo $message['User module permissions']?></a>
              </div>
          </div>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">
    <?php } ?>
    <!-- Heading -->
    <div class="sidebar-heading">
        Doplnky
    </div>
    <?php

    $module = new ModuleView();
    $modules = $module->getAllModules();
    foreach($modules as $id=>$m){
      include ABSPATH."/adminlogin/modules/".$m['path']."/sidebar.php";
    }

    ?>

    <!-- Nav Item - Pages Collapse Menu -->
    <!--<li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
            aria-expanded="true" aria-controls="collapsePages">
            <i class="fas fa-fw fa-folder"></i>
            <span>Pages</span>
        </a>
        <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Login Screens:</h6>
                <a class="collapse-item" href="login.html">Login</a>
                <a class="collapse-item" href="register.html">Register</a>
                <a class="collapse-item" href="forgot-password.html">Forgot Password</a>
                <div class="collapse-divider"></div>
                <h6 class="collapse-header">Other Pages:</h6>
                <a class="collapse-item" href="404.html">404 Page</a>
                <a class="collapse-item" href="blank.html">Blank Page</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Charts -->
    <!--<li class="nav-item">
        <a class="nav-link" href="charts.html">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Charts</span></a>
    </li>

    <!-- Nav Item - Tables -->
    <!--<li class="nav-item">
        <a class="nav-link" href="tables.html">
            <i class="fas fa-fw fa-table"></i>
            <span>Tables</span></a>
    </li>-->

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
