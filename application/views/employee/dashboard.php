<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { overflow-x:hidden; }
    #sidebarMenu { width:240px; min-height:100vh; transition:transform .3s ease-in-out; }
    #page-content { flex-grow:1; }
    /* Backdrop */
    .sidebar-backdrop{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:1020;display:none;}
    .sidebar-backdrop.show{display:block;}

    .menu-toggle{position:fixed;top:10px;right:25px;z-index:1040;display:none;}

    @media(max-width:768px){
      #sidebarMenu{position:fixed;top:0;left:0;z-index:1030;transform:translateX(-100%);}
      #sidebarMenu.show{transform:translateX(0);}
      .menu-toggle{display:block;}
    }
  </style>
</head>
<body>
<!-- Backdrop overlay -->
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>

<!-- Toggle button -->
<button class="btn btn-dark menu-toggle" id="menu-toggle"><i class="fa-solid fa-bars" id="menu-icon"></i></button>
<div class="d-flex" id="wrapper">
  <!-- Sidebar -->
  <nav id="sidebarMenu" class="bg-dark text-white p-3">
    <a href="#" class="d-flex align-items-center mb-3 text-white text-decoration-none">
      <span class="fs-4"><i class="fa-solid fa-user me-2"></i>Employee</span>
    </a>
    <hr class="text-secondary" />
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item mb-2">
        <a href="<?=site_url('/dashboard');?>" class="nav-link text-white <?php if(uri_string()==='dashboard') echo 'active bg-primary';?>"><i class="fa-solid fa-table-columns me-2"></i>Dashboard</a>
      </li>
      <li class="nav-item mb-2">
        <a href="<?=site_url('submit-rating'); ?>" class="nav-link text-white"><i class="fa-solid fa-star me-2"></i>Submit Rating</a>
      </li>
      <li class="nav-item mb-2">
        <a href="<?=site_url('change-password'); ?>" class="nav-link text-white <?php if(uri_string()==='change-password') echo 'active bg-primary';?>"><i class="fa-solid fa-key me-2"></i>Change Password</a>
      </li>
      <li class="nav-item mb-2">
        <a href="<?=site_url('submit-complain'); ?>" class="nav-link text-white <?php if(uri_string()==='submit-complain') echo 'active bg-primary';?>"><i class="fa-solid fa-comment-dots me-2"></i>Anonymous</a>
      </li>
    </ul>
    <hr class="text-secondary" />
    <a href="<?=site_url('logout'); ?>" class="btn btn-outline-danger w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
  </nav>
  <!-- /Sidebar -->

  <!-- Page content -->
  <div id="page-content" class="p-4">

<div class="row mb-4">
  <!-- Team Lead Card -->
  <div class="col-md-6">
    <div class="card h-100 border-primary">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fa-solid fa-user-tie me-2"></i>My Team Lead</h5>
      </div>
      <div class="card-body">
        <?php if($tl): ?>
          <h5 class="card-title"><?=$tl->name;?></h5>
          <p class="card-text"><i class="fa-solid fa-envelope me-2"></i><?=$tl->email;?></p>
          <?php 
          $CI = get_instance(); 
          $submission_model = $CI->Submission_model;
          $can_rate_tl = $submission_model->can_submit_current($auth_user->id, $tl->id);
          ?>
          <?php if($can_rate_tl): ?>
          <a href="<?=site_url('submit-rating?target_id='.$tl->id);?>" class="btn btn-success"><i class="fa-solid fa-star me-2"></i>Rate TL</a>
          <?php else: ?>
          <p class="text-muted">You have already rated your TL for this month.</p>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <!-- Team Members Count Card -->
  <div class="col-md-6">
    <div class="card h-100 border-info">
      <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fa-solid fa-users me-2"></i>Team Overview</h5>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-center align-items-center h-100">
          <div class="text-center">
            <h1 class="display-4"><?=count($fellow_employees);?></h1>
            <p class="lead">Team Members</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<h4><i class="fa-solid fa-user-group me-2"></i>Fellow Employees</h4>
<div class="table-responsive">
<table class="table table-bordered table-striped">
<thead class="table-dark"><tr><th>#</th><th>Name</th><th>Email</th><th>Action</th></tr></thead>
<tbody>
<?php 
$count = 1;
foreach($fellow_employees as $idx=>$emp): 
  // Skip if this is the currently logged in user
  if($emp->id == $auth_user->id) continue;
?>
<tr>
  <td><?=$count++;?></td>
  <td><?=$emp->name;?></td>
  <td><?=$emp->email;?></td>
  <td>
  <?php 
  $CI = get_instance(); 
  $submission_model = $CI->Submission_model;
  $can_rate = $submission_model->can_submit_current($auth_user->id, $emp->id);
  ?>
  <?php if($can_rate): ?>
  <a class="btn btn-sm btn-success" href="<?=site_url('submit-rating?target_id='.$emp->id);?>"><i class="fa-solid fa-star me-1"></i>Rate</a>
  <?php else: ?>
  <span class="badge bg-secondary">Already Rated</span>
  <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded',function(){
  const menuToggle=document.getElementById('menu-toggle');
  const sidebarMenu=document.getElementById('sidebarMenu');
  const sidebarBackdrop=document.getElementById('sidebar-backdrop');

  function closeSidebar(){sidebarMenu.classList.remove('show');sidebarBackdrop.classList.remove('show');document.body.style.overflow='';const icon=document.getElementById('menu-icon');if(icon){icon.classList.remove('fa-xmark');icon.classList.add('fa-bars');}}
  function openSidebar(){sidebarMenu.classList.add('show');sidebarBackdrop.classList.add('show');document.body.style.overflow='hidden';const icon=document.getElementById('menu-icon');if(icon){icon.classList.remove('fa-bars');icon.classList.add('fa-xmark');}}

  if(menuToggle){menuToggle.addEventListener('click',e=>{e.preventDefault();sidebarMenu.classList.contains('show')?closeSidebar():openSidebar();});}
  if(sidebarBackdrop){sidebarBackdrop.addEventListener('click',closeSidebar);}  
  if(sidebarMenu){sidebarMenu.querySelectorAll('.nav-link, .btn').forEach(l=>l.addEventListener('click',()=>{if(window.innerWidth<=768)closeSidebar();}));}
  window.addEventListener('resize',()=>{if(window.innerWidth>768&&sidebarMenu.classList.contains('show'))closeSidebar();});
});
</script>
</body>
</html> 