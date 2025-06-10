<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rating Charts</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body{overflow-x:hidden;}
    #sidebarMenu{min-width:240px;min-height:100vh;transition:transform .3s ease-in-out;}
    #page-content{flex-grow:1;}
    .sidebar-backdrop{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:1020;display:none;}
    .sidebar-backdrop.show{display:block;}
    .menu-toggle{position:fixed;top:10px;right:25px;z-index:1040;display:none;}
    @media(max-width:768px){#wrapper{display:block !important;}#sidebarMenu{position:fixed;top:0;left:0;z-index:1030;transform:translateX(-100%);}#sidebarMenu.show{transform:translateX(0);} .menu-toggle{display:block;}}

    /* Chart Table styling */
    .chart-table{border-collapse:collapse;min-width:600px;}
    .chart-table th,.chart-table td{border:1px solid #555;padding:8px;text-align:center;}
    .chart-table th.question-col{text-align:left;background:#f5f5f5;}
    
    /* Chart container styling */
    .chart-container {
      position: relative;
      /* max-width: 900px; */
      height: 500px;
      margin: 0 auto 30px;
    }
    @media (max-width: 768px) {
      .chart-container {
        height: 250px;
      }
    }
  </style>
</head>
<body>
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
<button class="btn btn-dark menu-toggle" id="menu-toggle"><i class="fa-solid fa-bars" id="menu-icon"></i></button>
<div class="d-flex" id="wrapper">
  <!-- Sidebar -->
  <nav id="sidebarMenu" class="bg-dark text-white p-3">
    <a href="#" class="d-flex align-items-center mb-3 text-white text-decoration-none"><span class="fs-4"><i class="fa-solid fa-chart-line me-2"></i>Admin</span></a>
    <hr class="text-secondary" />
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item mb-2"><a href="<?=site_url('/dashboard');?>" class="nav-link text-white <?php if(current_url()==site_url('/dashboard')) echo 'active bg-primary';?>"><i class="fa-solid fa-table-columns me-2"></i>Dashboard</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('admin/users');?>" class="nav-link text-white <?php if(uri_string()==='admin/users') echo 'active bg-primary';?>"><i class="fa-solid fa-users-gear me-2"></i>Users</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('admin/questions');?>" class="nav-link text-white <?php if(uri_string()==='admin/questions') echo 'active bg-primary';?>"><i class="fa-solid fa-question me-2"></i>Questions</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('admin/performance');?>" class="nav-link text-white <?php if(uri_string()==='admin/performance') echo 'active bg-primary';?>"><i class="fa-solid fa-chart-simple me-2"></i>Team Performance</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('admin/charts');?>" class="nav-link text-white <?php if(uri_string()==='admin/charts') echo 'active bg-primary';?>"><i class="fa-solid fa-border-all me-2"></i>Rating Charts</a></li>
    </ul>
    <hr class="text-secondary" />
    <a href="<?=site_url('logout'); ?>" class="btn btn-outline-danger w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
  </nav>
  <!-- /Sidebar -->

  <!-- Page content -->
  <div id="page-content" class="p-4">
    <h4>Rating Charts</h4>

    <div class="card mb-4">
      <div class="card-body">
        <form method="get" action="<?=site_url('admin/charts');?>" class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Select Employee / TL</label>
            <div class="dropdown custom-select-dropdown">
              <input type="hidden" name="target_id" id="selected-user-id" value="<?=$target_id;?>" required>
              <button class=" btn-outline-secondary dropdown-toggle form-control text-start" type="button" id="user-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <?php 
                  $selected_name = "Choose...";
                  foreach($users as $u) {
                    if($target_id == $u->id) {
                      $selected_name = $u->name . ' (' . ($u->role_id==2?'TL':'TM') . ')';
                      break;
                    }
                  }
                  echo htmlspecialchars($selected_name);
                ?>
              </button>
              <div class="dropdown-menu w-100 p-0" aria-labelledby="user-dropdown">
                <div class="p-2">
                  <input type="text" id="user-search" class="form-control" placeholder="Search employees/TLs...">
                </div>
                <div class="dropdown-divider m-0"></div>
                <div class="user-options-container" style="max-height:200px;overflow-y:auto;">
                  <?php foreach($users as $u): 
                  if($u->id != "1") {
                  ?>
                  
                    <button class="dropdown-item" type="button" data-id="<?=$u->id;?>"><?=$u->name;?> (<?=$u->role_id==2?'TL':'TM';?>)</button>
                  <?php 
                  }
                  endforeach; ?>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <label class="form-label">Start Month</label>
            <div class="dropdown custom-select-dropdown">
              <input type="hidden" name="start_period" id="selected-start-period" value="<?=$start_period_id;?>" required>
              <button class=" btn-outline-secondary dropdown-toggle form-control text-start" type="button" id="start-period-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <?php 
                  $selected_start_month = "Start";
                  foreach($periods as $p) {
                    if($start_period_id == $p->id) {
                      $selected_start_month = date('F Y', strtotime($p->yearmonth.'-01'));
                      break;
                    }
                  }
                  echo htmlspecialchars($selected_start_month);
                ?>
              </button>
              <div class="dropdown-menu w-100 p-0" aria-labelledby="start-period-dropdown">
                <div class="p-2">
                  <input type="text" class="form-control period-search" placeholder="Search months...">
                </div>
                <div class="dropdown-divider m-0"></div>
                <div class="period-options-container" style="max-height:200px;overflow-y:auto;">
                  <?php foreach($periods as $p): ?>
                    <button class="dropdown-item" type="button" data-id="<?=$p->id;?>"><?=date('F Y', strtotime($p->yearmonth.'-01'));?></button>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-3">
            <label class="form-label">End Month</label>
            <div class="dropdown custom-select-dropdown">
              <input type="hidden" name="end_period" id="selected-end-period" value="<?=$end_period_id;?>" required>
              <button class=" btn-outline-secondary dropdown-toggle form-control text-start" type="button" id="end-period-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <?php 
                  $selected_end_month = "End";
                  foreach($periods as $p) {
                    if($end_period_id == $p->id) {
                      $selected_end_month = date('F Y', strtotime($p->yearmonth.'-01'));
                      break;
                    }
                  }
                  echo htmlspecialchars($selected_end_month);
                ?>
              </button>
              <div class="dropdown-menu w-100 p-0" aria-labelledby="end-period-dropdown">
                <div class="p-2">
                  <input type="text" class="form-control period-search" placeholder="Search months...">
                </div>
                <div class="dropdown-divider m-0"></div>
                <div class="period-options-container" style="max-height:200px;overflow-y:auto;">
                  <?php foreach($periods as $p): ?>
                    <button class="dropdown-item" type="button" data-id="<?=$p->id;?>"><?=date('F Y', strtotime($p->yearmonth.'-01'));?></button>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">Apply</button></div>
        </form>
      </div>
    </div>

    <?php if(!empty($charts)): ?>
      <?php foreach($charts as $idx=>$chart): ?>
        <div class="mb-5">
          <?php
            // Calculate average rating across all questions & months for this submitter
            $totalRating = 0;
            $ratingCount = 0;
            foreach($chart['ratings'] as $question => $monthRatings){
              foreach($monthRatings as $ym => $val){
                if($val !== null){
                  $totalRating += (int)$val;
                  $ratingCount++;
                }
              }
            }
            $avgRating = $ratingCount ? round($totalRating / $ratingCount, 1) : 0;
          ?>
          <h6 class="mb-3">Rated by <strong><?=$chart['submitter_name'];?></strong> (<?=strtoupper($chart['submitter_role']);?>) : Average rating <?=$avgRating;?></h6>
          <?php
            // Prepare JavaScript data structures for the line-chart
            $canvasId   = 'chart_'.$idx;
            $labelArr   = array_map(fn($m)=>date('M', strtotime($m.'-01')), $months_range);
            $colorPalette = ['#3b82f6','#10b981','#f59e0b','#ef4444','#0ea5e9','#a855f7','#14b8a6','#eab308'];

            $datasetsArr = [];
            $qIdx = 0;
            $questionKeys = array_keys($chart['ratings']);
            sort($questionKeys);
            foreach($questionKeys as $q){
              $points = [];
              foreach($months_range as $m){
                $points[] = isset($chart['ratings'][$q][$m]) ? (int)$chart['ratings'][$q][$m] : null;
              }
              $color = $colorPalette[$qIdx % count($colorPalette)];
              $datasetsArr[] = [
                'label'           => $q,
                'data'            => $points,
                'borderColor'     => $color,
                'backgroundColor' => $color,
                'fill'            => false,
                'spanGaps'        => true,
                'tension'         => 0.3,
                'pointRadius'     => 5
              ];
              $qIdx++;
            }
          ?>
          <div class="chart-container">
            <canvas id="<?=$canvasId;?>"></canvas>
          </div>
          <script>
            (function(){
              const ctx = document.getElementById('<?=$canvasId;?>').getContext('2d');
              new Chart(ctx, {
                type: 'line',
                data: {
                  labels: <?=json_encode($labelArr);?>,
                  datasets: <?=json_encode($datasetsArr);?>
                },
                options: {
                  responsive: true,
                  maintainAspectRatio: true,
                  plugins: {
                    legend: {
                      position: 'top'
                    }
                  },
                  scales: {
                    y: {
                      beginAtZero: true,
                      suggestedMax: 10,
                      ticks: {
                        stepSize: 2,
                        callback: function(value) {
                          if (value % 2 === 0) return value;
                        }
                      },
                      title: { display: true, text: 'Rating' }
                    },
                    x: {
                      title: { display: true, text: 'Month' },
                      offset: true,
                      grid: {
                        offset: true
                      }
                    }
                  }
                }
              });
            })();
          </script>
        </div>
      <?php endforeach; ?>
    <?php elseif($target_id): ?>
      <div class="alert alert-warning">No ratings found for selected criteria.</div>
    <?php endif; ?>
  </div>
</div>
<script>
// JS for sidebar behaviour (copied from other admin pages)
(function(){
  const menuToggle=document.getElementById('menu-toggle');
  const sidebarMenu=document.getElementById('sidebarMenu');
  const sidebarBackdrop=document.getElementById('sidebar-backdrop');
  const closeSidebar=()=>{sidebarMenu.classList.remove('show');sidebarBackdrop.classList.remove('show');document.body.style.overflow='';const icon=document.getElementById('menu-icon');if(icon){icon.classList.remove('fa-xmark');icon.classList.add('fa-bars');}};
  const openSidebar=()=>{sidebarMenu.classList.add('show');sidebarBackdrop.classList.add('show');document.body.style.overflow='hidden';const icon=document.getElementById('menu-icon');if(icon){icon.classList.remove('fa-bars');icon.classList.add('fa-xmark');}};
  if(menuToggle){menuToggle.addEventListener('click',e=>{e.preventDefault();sidebarMenu.classList.contains('show')?closeSidebar():openSidebar();});}
  if(sidebarBackdrop){sidebarBackdrop.addEventListener('click',closeSidebar);}  
  if(sidebarMenu){sidebarMenu.querySelectorAll('.nav-link, .btn').forEach(l=>{l.addEventListener('click',()=>{if(window.innerWidth<=768)closeSidebar();});});}
  window.addEventListener('resize',()=>{if(window.innerWidth>768&&sidebarMenu.classList.contains('show'))closeSidebar();});
})();

// Add search functionality for user dropdown
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('user-search');
  const userDropdown = document.querySelector('.custom-select-dropdown');
  const userOptions = Array.from(userDropdown.querySelectorAll('.user-options-container .dropdown-item'));
  const selectedIdField = document.getElementById('selected-user-id');
  const dropdownButton = document.getElementById('user-dropdown');
  
  searchInput.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    
    userOptions.forEach(option => {
      const optionText = option.textContent.toLowerCase();
      if (searchTerm === '' || optionText.includes(searchTerm)) {
        option.style.display = '';
      } else {
        option.style.display = 'none';
      }
    });
  });
  
  // Handle option selection
  userDropdown.querySelectorAll('.user-options-container .dropdown-item').forEach(option => {
    option.addEventListener('click', function() {
      const userId = this.getAttribute('data-id');
      selectedIdField.value = userId;
      dropdownButton.textContent = this.textContent;
    });
  });
  
  // Period dropdowns search functionality
  document.querySelectorAll('.period-search').forEach(searchField => {
    searchField.addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase();
      const optionsContainer = this.closest('.dropdown-menu').querySelector('.period-options-container');
      const options = Array.from(optionsContainer.querySelectorAll('.dropdown-item'));
      
      options.forEach(option => {
        const optionText = option.textContent.toLowerCase();
        if (searchTerm === '' || optionText.includes(searchTerm)) {
          option.style.display = '';
        } else {
          option.style.display = 'none';
        }
      });
    });
  });
  
  // Period option selection
  document.querySelectorAll('.custom-select-dropdown').forEach(dropdown => {
    const isStartPeriod = dropdown.querySelector('#start-period-dropdown');
    const isEndPeriod = dropdown.querySelector('#end-period-dropdown');
    
    if (!isStartPeriod && !isEndPeriod) return; // Skip user dropdown which is handled separately
    
    const hiddenField = isStartPeriod ? document.getElementById('selected-start-period') : 
                        isEndPeriod ? document.getElementById('selected-end-period') : null;
    const dropdownBtn = isStartPeriod ? document.getElementById('start-period-dropdown') : 
                        isEndPeriod ? document.getElementById('end-period-dropdown') : null;
    
    if (!hiddenField || !dropdownBtn) return;
    
    dropdown.querySelectorAll('.period-options-container .dropdown-item').forEach(option => {
      option.addEventListener('click', function() {
        const periodId = this.getAttribute('data-id');
        hiddenField.value = periodId;
        dropdownBtn.textContent = this.textContent;
      });
    });
  });
});
</script>
</body>
</html> 