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
            <select name="target_id" class="form-select" required>
              <option value="">Choose...</option>
              <?php foreach($users as $u): ?>
                <option value="<?=$u->id;?>" <?=($target_id==$u->id?'selected':'')?> ><?=$u->name;?> (<?=$u->role_id==2?'TL':'Emp';?>)</option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Start Month</label>
            <select name="start_period" class="form-select" required>
              <option value="">Start</option>
              <?php foreach($periods as $p): ?>
              <option value="<?=$p->id;?>" <?=($start_period_id==$p->id?'selected':'')?> ><?=date('F Y', strtotime($p->yearmonth.'-01'));?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">End Month</label>
            <select name="end_period" class="form-select" required>
              <option value="">End</option>
              <?php foreach($periods as $p): ?>
              <option value="<?=$p->id;?>" <?=($end_period_id==$p->id?'selected':'')?> ><?=date('F Y', strtotime($p->yearmonth.'-01'));?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">Apply</button></div>
        </form>
      </div>
    </div>

    <?php if(!empty($charts)): ?>
      <?php foreach($charts as $idx=>$chart): ?>
        <div class="mb-5">
          <h6 class="mb-3">Rated by <strong><?=$chart['submitter_name'];?></strong> (<?=strtoupper($chart['submitter_role']);?>)</h6>
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
                      position: 'right'
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
                      title: { display: true, text: 'Month' }
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
</script>
</body>
</html> 