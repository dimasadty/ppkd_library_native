<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="/library/dashboardinstructor.php">
        <i class="mdi mdi-grid-large menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    <li class="nav-item nav-category">Menu</li>
    
    <?php if ($_SESSION['id_level'] == 1) { // Only show to administrators ?>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
        <i class="menu-icon mdi mdi-floor-plan"></i>
        <span class="menu-title">Master Data</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="/library/pagesuser/index.php">User</a></li>
          <li class="nav-item"> <a class="nav-link" href="/library/pageslevel/index.php">Level</a></li>
          <li class="nav-item"> <a class="nav-link" href="/library/pagesmajor/index.php">Major</a></li>
          <li class="nav-item"> <a class="nav-link" href="/library/pagesclassofyear/index.php">Class Of Year</a></li>
          <li class="nav-item"> <a class="nav-link" href="/library/pagesparticipant/index.php">Participant</a></li>
        </ul>
      </div>
    </li>
    <?php } ?>
    
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
        <i class="menu-icon fa fa-wrench"></i>
        <span class="menu-title">Settings</span>
        <i class="menu-arrow"></i>
      </a>
    </li>
  </ul>
</nav>