<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Performance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
      body{overflow-x:hidden;}
      #sidebarMenu{min-width:240px;min-height:100vh;transition:transform .3s ease-in-out;}
      #page-content{flex-grow:1;}
      .sidebar-backdrop{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:1020;display:none;}
      .sidebar-backdrop.show{display:block;}
      .menu-toggle{position:fixed;top:10px;right:25px;z-index:1040;display:none;}
      @media(max-width:768px){#wrapper{display:block !important;}#sidebarMenu{position:fixed;top:0;left:0;z-index:1030;transform:translateX(-100%);}#sidebarMenu.show{transform:translateX(0);} .menu-toggle{display:block;}}
    </style>
</head>
<body>
<!-- Backdrop overlay -->
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
<!-- Mobile toggle -->
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

    <h4>Employee Stats</h4>
    <div class="card mb-4">
      <div class="card-body">
        <form method="get" action="<?=site_url('admin/performance');?>" class="row g-3">
          <div class="col-md-2">
            <label class="form-label">Team Lead</label>
            <select name="tl_id" class="form-select">
              <option value="">All Team Leads</option>
              <?php foreach($tls as $tl): ?>
              <option value="<?=$tl->id;?>" <?=($filter_tl==$tl->id?'selected':'');?>><?=$tl->name;?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Review Type Filter -->
          <div class="col-md-2">
            <label class="form-label">Review Type</label>
            <select name="review_type" class="form-select">
              <option value="" <?=($filter_type==''?'selected':'');?>>All</option>
              <option value="tl_emp" <?=($filter_type=='tl_emp'?'selected':'');?>>TL → TM</option>
              <option value="emp_emp" <?=($filter_type=='emp_emp'?'selected':'');?>>TM → TM</option>
              <option value="emp_tl" <?=($filter_type=='emp_tl'?'selected':'');?>>TM → TL</option>
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label">Performance</label>
            <select name="level" class="form-select">
              <option value="">All</option>
              <option value="outstanding" <?=($filter_level=='outstanding'?'selected':'');?>>Outstanding</option>
              <option value="good" <?=($filter_level=='good'?'selected':'');?>>Good</option>
              <option value="average" <?=($filter_level=='average'?'selected':'');?>>Average</option>
              <option value="bad" <?=($filter_level=='bad'?'selected':'');?>>Bad</option>
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label">Month</label>
            <div class="dropdown custom-select-dropdown">
              <input type="hidden" name="period_id" id="selected-period" value="<?=$filter_period;?>">
              <button class="dropdown-toggle form-control text-start" type="button" id="period-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <?php 
                  $selected_month = "All Months";
                  foreach($periods as $period) {
                    if($filter_period == $period->id) {
                      $selected_month = date('F Y', strtotime($period->yearmonth.'-01'));
                      break;
                    }
                  }
                  echo htmlspecialchars($selected_month);
                ?>
              </button>
              <div class="dropdown-menu w-100 p-0" aria-labelledby="period-dropdown">
                <div class="p-2">
                  <input type="text" class="form-control period-search" placeholder="Search months...">
                </div>
                <div class="dropdown-divider m-0"></div>
                <div class="period-options-container" style="max-height:200px;overflow-y:auto;">
                  <?php foreach($periods as $period): ?>
                    <button class="dropdown-item" type="button" data-id="<?=$period->id;?>"><?=date('F Y', strtotime($period->yearmonth.'-01'));?></button>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">Apply</button></div>
        </form>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead class="table-dark">
          <tr>
            <th>#</th><th>Submitted By</th><th>Submitted To</th><th>Month</th><th>Average</th>
            <?php foreach($questions as $q): ?>
              <th><?=htmlspecialchars($q->text);?></th>
            <?php endforeach; ?>
            
          </tr>
        </thead>
        <tbody>
          <?php foreach($submissions as $idx=>$s): ?>
          <tr>
            <td><?=($idx+1);?></td>
            <td><?=$s->submitter;?> (<?=$s->submitter_role == 'tl' ? 'TL' : ($s->submitter_role == 'employee' ? 'TM' : 'Admin')?>)</td>
            <td><?=$s->target;?> (<?=$s->target_role == 'tl' ? 'TL' : ($s->target_role == 'employee' ? 'TM' : 'Admin')?>)</td>
            <td><?=date('F Y', strtotime($s->yearmonth.'-01'));?></td>
            <td><b><?= $s->avg_rating !== null ? htmlspecialchars($s->avg_rating) : '-' ?></b></td>
            <?php
              $answers_map = isset($s->answers) ? $s->answers : [];
              foreach($questions as $q){
                if(isset($answers_map[$q->text])){
                  $a=$answers_map[$q->text];
                  echo '<td><b>'.htmlspecialchars($a->rating).'</b>';
                  if($a->comment) {
                    echo ' <a href="#" class="comment-link" data-bs-toggle="modal" data-bs-target="#commentModal" data-comment="'.htmlspecialchars($a->comment).'"><i class="fa-solid fa-comment-dots text-secondary"></i></a>';
                  }
                  echo '</td>';
                } else {
                  echo '<td>-</td>';
                }
              }
            ?>
            
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>
<script>
// JS for sidebar behaviour (same as other admin pages)
document.addEventListener('DOMContentLoaded',function(){
  const menuToggle=document.getElementById('menu-toggle');
  const sidebarMenu=document.getElementById('sidebarMenu');
  const sidebarBackdrop=document.getElementById('sidebar-backdrop');
  const closeSidebar=()=>{sidebarMenu.classList.remove('show');sidebarBackdrop.classList.remove('show');document.body.style.overflow='';const icon=document.getElementById('menu-icon');if(icon){icon.classList.remove('fa-xmark');icon.classList.add('fa-bars');}};
  const openSidebar=()=>{sidebarMenu.classList.add('show');sidebarBackdrop.classList.add('show');document.body.style.overflow='hidden';const icon=document.getElementById('menu-icon');if(icon){icon.classList.remove('fa-bars');icon.classList.add('fa-xmark');}};
  if(menuToggle){menuToggle.addEventListener('click',e=>{e.preventDefault();sidebarMenu.classList.contains('show')?closeSidebar():openSidebar();});}
  if(sidebarBackdrop){sidebarBackdrop.addEventListener('click',closeSidebar);}  
  if(sidebarMenu){sidebarMenu.querySelectorAll('.nav-link, .btn').forEach(l=>{l.addEventListener('click',()=>{if(window.innerWidth<=768)closeSidebar();});});}
  window.addEventListener('resize',()=>{if(window.innerWidth>768&&sidebarMenu.classList.contains('show'))closeSidebar();});
});
</script>

<!-- Comment Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="commentModalLabel">Comment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="modalCommentText"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
// Script to handle comment modal
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.comment-link').forEach(link => {
    link.addEventListener('click', function(e) {
      const comment = this.getAttribute('data-comment');
      document.getElementById('modalCommentText').textContent = comment;
    });
  });

  // Period dropdown search functionality
  const periodSearchInput = document.querySelector('.period-search');
  if (periodSearchInput) {
    periodSearchInput.addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase();
      const options = document.querySelectorAll('.period-options-container .dropdown-item');
      
      options.forEach(option => {
        const optionText = option.textContent.toLowerCase();
        if (searchTerm === '' || optionText.includes(searchTerm)) {
          option.style.display = '';
        } else {
          option.style.display = 'none';
        }
      });
    });
  }
  
  // Period dropdown option selection
  const periodOptionsContainer = document.querySelector('.period-options-container');
  if (periodOptionsContainer) {
    periodOptionsContainer.querySelectorAll('.dropdown-item').forEach(option => {
      option.addEventListener('click', function() {
        const periodId = this.getAttribute('data-id');
        document.getElementById('selected-period').value = periodId;
        document.getElementById('period-dropdown').textContent = this.textContent;
      });
    });
  }
});
</script>
</body>
</html> 