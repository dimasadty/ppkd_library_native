<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="/library/dashboard.php">
        <i class="mdi mdi-grid-large menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>
    <li class="nav-item nav-category">Menu</li>
    <?php if ($_SESSION['id_level'] == 1) { // Only show to administrators 
   ?>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
          <i class="menu-icon mdi mdi-floor-plan"></i>
          <span class="menu-title">Master Data</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="ui-basic">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> <a class="nav-link" href="/library/pagesusers/index.php">User</a></li>
            <li class="nav-item"> <a class="nav-link" href="/library/pageslevels/index.php">Level</a></li>
            <li class="nav-item"> <a class="nav-link" href="/library/pagesmembers/index.php">Member</a></li>
            <li class="nav-item"> <a class="nav-link" href="/library/pagesbooks/index.php">Books</a></li>
            <li class="nav-item"> <a class="nav-link" href="/library/pagesgenres/index.php">Genres</a></li>
            <li class="nav-item"> <a class="nav-link" href="/library/pageslocations/index.php">Locations</a></li>
          </ul>
        </div>
      </li>
    <?php }?>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#form-elements" aria-expanded="false" aria-controls="form-elements">
        <i class="menu-icon mdi mdi-card-text-outline"></i>
        <span class="menu-title">Transaction</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="form-elements">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link" href="/library/pagesborrowers/index.php">Books Out Data</a></li>
          <li class="nav-item"><a class="nav-link" href="/library/pagesstatusborrowers/index.php">Data Status Borrowers</a></li>
        </ul>
      </div>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
        <i class="menu-icon fa fa-wrench"></i>
        <span class="menu-title">Settings</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="auth">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"><a class="nav-link" href="/library/pagesmenus/index.php?page=menu&store">Customized Menu</a></li>
          <li class="nav-item"><a class="nav-link" href="/library/pagescards/index.php">Customized Card</a></li>
        </ul>
      </div>
    </li>
  </ul>
</nav>