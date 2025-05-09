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
          <div class="col-md-4">
            <label class="form-label">Team Lead</label>
            <select name="tl_id" class="form-select">
              <option value="">All Team Leads</option>
              <?php foreach($tls as $tl): ?>
              <option value="<?=$tl->id;?>" <?=($filter_tl==$tl->id?'selected':'');?>><?=$tl->name;?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Performance</label>
            <select name="level" class="form-select">
              <option value="">All</option>
              <option value="outstanding" <?=($filter_level=='outstanding'?'selected':'');?>>Outstanding</option>
              <option value="good" <?=($filter_level=='good'?'selected':'');?>>Good</option>
              <option value="average" <?=($filter_level=='average'?'selected':'');?>>Average</option>
              <option value="bad" <?=($filter_level=='bad'?'selected':'');?>>Bad</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">Month</label>
            <select name="period_id" class="form-select">
              <option value="">All Months</option>
              <?php foreach($periods as $period): ?>
              <option value="<?=$period->id;?>" <?=($filter_period==$period->id?'selected':'');?> ><?=date('F Y', strtotime($period->yearmonth.'-01'));?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">Apply</button></div>
        </form>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead class="table-dark">
          <tr>
            <th>#</th><th>Submitted By</th><th>Submitter Against</th><th>Month</th>
            <?php foreach($questions as $q): ?>
              <th><?=htmlspecialchars($q->text);?></th>
            <?php endforeach; ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach($submissions as $idx=>$s): ?>
          <tr>
            <td><?=($idx+1);?></td>
            <td><?=$s->submitter;?> (<?=$s->submitter_role == 'tl' ? 'TL' : ($s->submitter_role == 'employee' ? 'EM' : 'Admin')?>)</td>
            <td><?=$s->target;?> (<?=$s->target_role == 'tl' ? 'TL' : ($s->target_role == 'employee' ? 'EM' : 'Admin')?>)</td>
            <td><?=date('F Y', strtotime($s->yearmonth.'-01'));?></td>
            <?php
              $CI =& get_instance();
              $db = $CI->load->database('', true);
              $answers = $db->select('q.text, sa.rating, sa.comment')
                            ->from('submission_answers sa')
                            ->join('questions q','q.id = sa.question_id')
                            ->where('sa.submission_id', $s->id)
                            ->get()->result();
              $answers_map = [];
              foreach($answers as $answer){ $answers_map[$answer->text] = $answer; }
              foreach($questions as $q){
                if(isset($answers_map[$q->text])){
                  $a=$answers_map[$q->text];
                  echo '<td><b>'.htmlspecialchars($a->rating).($a->comment?'</b> ('.htmlspecialchars($a->comment).')':'</b>').'</td>';
                }else{ echo '<td>-</td>'; }
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
</body>
</html> 