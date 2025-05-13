<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
      body {
        overflow-x: hidden;
      }
      #sidebarMenu {
        min-width: 240px;
        min-height: 100vh;
        transition: transform .3s ease-in-out;
      }
      #page-content {
        flex-grow: 1;
      }
      .sidebar-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1020;
        display: none;
      }
      .sidebar-backdrop.show {
        display: block;
      }
      .menu-toggle {
        position: fixed;
        top: 10px;
        right: 25px;
        z-index: 1040;
        display: none;
      }
      @media (max-width: 768px) {
        #wrapper {
          display: block !important;
        }
        #sidebarMenu {
          position: fixed;
          top: 0;
          left: 0;
          z-index: 1030;
          transform: translateX(-100%);
        }
        #sidebarMenu.show {
          transform: translateX(0);
        }
        .menu-toggle {
          display: block;
        }
      }
    </style>
</head>
<body>
<!-- Backdrop overlay for mobile when menu is open -->
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>

<!-- Mobile menu toggle button -->
<button class="btn btn-dark menu-toggle" id="menu-toggle">
  <i class="fa-solid fa-bars" id="menu-icon"></i>
</button>

<div class="d-flex" id="wrapper">
  <!-- Sidebar -->
  <nav id="sidebarMenu" class="bg-dark text-white p-3">
    <a href="#" class="d-flex align-items-center mb-3 mb-md-0 text-white text-decoration-none">
      <span class="fs-4"><i class="fa-solid fa-chart-line me-2"></i>Admin</span>
    </a>
    <hr class="text-secondary" />
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item mb-2">
        <a href="<?=site_url('/dashboard');?>" class="nav-link text-white <?php if(current_url()==site_url('/dashboard')) echo 'active bg-primary';?>"><i class="fa-solid fa-table-columns me-2"></i>Dashboard</a>
      </li>
      <li class="nav-item mb-2">
        <a href="<?=site_url('admin/users');?>" class="nav-link text-white <?php if(uri_string()==='admin/users') echo 'active bg-primary';?>"><i class="fa-solid fa-users-gear me-2"></i>Users</a>
      </li>
      <li class="nav-item mb-2">
        <a href="<?=site_url('admin/questions');?>" class="nav-link text-white <?php if(uri_string()==='admin/questions') echo 'active bg-primary';?>"><i class="fa-solid fa-question me-2"></i>Questions</a>
      </li>
      <li class="nav-item mb-2">
        <a href="<?=site_url('admin/performance');?>" class="nav-link text-white <?php if(uri_string()==='admin/performance') echo 'active bg-primary';?>"><i class="fa-solid fa-chart-simple me-2"></i>Team Performance</a>
      </li>
      <li class="nav-item mb-2">
        <a href="<?=site_url('admin/charts');?>" class="nav-link text-white <?php if(uri_string()==='admin/charts') echo 'active bg-primary';?>"><i class="fa-solid fa-border-all me-2"></i>Rating Charts</a>
      </li>
    </ul>
    <hr class="text-secondary" />
    <div>
      <a href="<?=site_url('logout');?>" class="btn btn-outline-danger w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
    </div>
  </nav>
  <!-- /Sidebar -->

  <!-- Page content -->
  <div id="page-content" class="p-4">
      <h4>Admin Dashboard</h4>
<div class="">
<!-- Month filter -->


<div class="row g-2 mb-4">
        <div class="col-md-3">
           <div class="card text-bg-dark">  
                <div class="card-body">
                    <h5 class="card-title">Total Employees</h5>
                    <p class="display-6"><?=count($employees) + count($tls);?></p>
                </div>
            </div>  

        </div>
        <div class="col-md-3">
            <div class="card text-bg-success">
                <div class="card-body">
                    <h5 class="card-title">Total Team Leads</h5>
                    <p class="display-6"><?=count($tls)?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Team Members</h5>
                    <p class="display-6"><?=count($employees);?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-secondary">
                <div class="card-body">
                    <h5 class="card-title">Total Submissions (<?= date('F Y', strtotime($selected_month.'-01')); ?>)</h5>
                    <p class="display-6"><?php 
                      $current_month_submissions = array_filter($submissions, function($s) use ($selected_month) {
                          return $s->yearmonth === $selected_month;
                      });
                      echo count($current_month_submissions);
                    ?></p>
                </div>
            </div>
        </div>
    </div>
    <h5>Filter Performance by Month</h5>
    
    <div class="row mb-3">
  <div class="col-md-3">
    <div class="dropdown custom-select-dropdown">
      <input type="hidden" name="month" id="selected-period" value="<?=$filter_month;?>">
      <button class="dropdown-toggle form-control text-start" type="button" id="period-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <?php 
          $selected_month_display = "Current Month (" . date('F Y') . ")";
          foreach($periods as $period) {
            if($filter_month == $period->yearmonth) {
              $selected_month_display = date('F Y', strtotime($period->yearmonth.'-01'));
              break;
            }
          }
          echo htmlspecialchars($selected_month_display);
        ?>
      </button>
      <div class="dropdown-menu w-100 p-0" aria-labelledby="period-dropdown">
        <div class="p-2">
          <input type="text" class="form-control period-search" placeholder="Search months...">
        </div>
        <div class="dropdown-divider m-0"></div>
        <div class="period-options-container" style="max-height:200px;overflow-y:auto;">
          <a href="<?= site_url('dashboard') ?>" class="dropdown-item">Current Month (<?= date('F Y') ?>)</a>
          <?php foreach($periods as $period): ?>
            <a href="<?= site_url('dashboard?month=' . $period->yearmonth) ?>" class="dropdown-item" data-id="<?=$period->yearmonth;?>"><?=date('F Y', strtotime($period->yearmonth.'-01'));?></a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>


    <div class="row mb-4">
  <div class="col-md-6 mb-3">
    <div class="card border-success h-100">
      <div class="card-header bg-success text-white">Outstanding Performers (<?= date('F Y', strtotime($selected_month.'-01')); ?>)</div>
      <div class="card-body">
        <?php if(!empty($outstanding)): ?>
          <ul class="list-unstyled mb-0">
            <?php foreach($outstanding as $o): ?>
              <li><i class="fa-solid fa-star text-warning me-2"></i><?=htmlspecialchars($o->name);?></li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="mb-0 text-muted">No outstanding reviews for <?= date('F Y', strtotime($selected_month.'-01')); ?>.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-md-6 mb-3">
    <div class="card border-danger h-100">
      <div class="card-header bg-danger text-white">Needs Improvement (<?= date('F Y', strtotime($selected_month.'-01')); ?>)</div>
      <div class="card-body">
        <?php if(!empty($low_performers)): ?>
          <ul class="list-unstyled mb-0">
            <?php foreach($low_performers as $lp): ?>
              <li><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i><?=htmlspecialchars($lp->name);?></li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="mb-0 text-muted">No low ratings for <?= date('F Y', strtotime($selected_month.'-01')); ?>.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>   
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Mobile sidebar functionality
  const menuToggle = document.getElementById('menu-toggle');
  const sidebarMenu = document.getElementById('sidebarMenu');
  const sidebarBackdrop = document.getElementById('sidebar-backdrop');
  
  // Function to close sidebar
  function closeSidebar() {
    if (sidebarMenu && sidebarBackdrop) {
      sidebarMenu.classList.remove('show');
      sidebarBackdrop.classList.remove('show');
      document.body.style.overflow = '';
      
      // Change icon to bars
      const menuIcon = document.getElementById('menu-icon');
      if (menuIcon) {
        menuIcon.classList.remove('fa-xmark');
        menuIcon.classList.add('fa-bars');
      }
    }
  }
  
  // Function to open sidebar
  function openSidebar() {
    if (sidebarMenu && sidebarBackdrop) {
      sidebarMenu.classList.add('show');
      sidebarBackdrop.classList.add('show');
      document.body.style.overflow = 'hidden'; // Prevent body scrolling when sidebar is open
      
      // Change icon to X (close)
      const menuIcon = document.getElementById('menu-icon');
      if (menuIcon) {
        menuIcon.classList.remove('fa-bars');
        menuIcon.classList.add('fa-xmark');
      }
    }
  }
  
  // Toggle sidebar when menu button is clicked
  if (menuToggle) {
    menuToggle.addEventListener('click', function(e) {
      e.preventDefault();
      if (sidebarMenu.classList.contains('show')) {
        closeSidebar();
      } else {
        openSidebar();
      }
    });
  }
  
  // Close sidebar when backdrop is clicked
  if (sidebarBackdrop) {
    sidebarBackdrop.addEventListener('click', closeSidebar);
  }
  
  // Close sidebar when links are clicked (on mobile)
  if (sidebarMenu) {
    const navLinks = sidebarMenu.querySelectorAll('.nav-link, .btn');
    navLinks.forEach(function(link) {
      link.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
          closeSidebar();
        }
      });
    });
  }
  
  // Close sidebar when resizing to desktop
  window.addEventListener('resize', function() {
    if (window.innerWidth > 768 && sidebarMenu && sidebarMenu.classList.contains('show')) {
      closeSidebar();
    }
  });
  
  // Period dropdown search functionality
  const periodSearch = document.querySelector('.period-search');
  if (periodSearch) {
    periodSearch.addEventListener('input', function() {
      const searchValue = this.value.toLowerCase();
      const periodOptions = document.querySelectorAll('.period-options-container .dropdown-item');
      
      periodOptions.forEach(option => {
        const text = option.textContent.toLowerCase();
        if (text.includes(searchValue)) {
          option.style.display = 'block';
        } else {
          option.style.display = 'none';
        }
      });
    });
  }
  
});
</script>
</div>
</div>
</body>
</html>

