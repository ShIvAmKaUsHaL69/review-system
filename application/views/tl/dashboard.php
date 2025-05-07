<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TL Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body{overflow-x:hidden;}
    #sidebarMenu{width:240px;min-height:100vh;transition:transform .3s ease-in-out;}
    #page-content{flex-grow:1;}
    /* Backdrop */
    .sidebar-backdrop{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:1020;display:none;}
    .sidebar-backdrop.show{display:block;}

    .menu-toggle{position:fixed;top:10px;right:25px;z-index:1040;display:none;}

    @media(max-width:768px){#sidebarMenu{position:fixed;top:0;left:0;z-index:1030;transform:translateX(-100%);}#sidebarMenu.show{transform:translateX(0);} .menu-toggle{display:block;} #page-content{width:100%;padding-top:60px !important;} }
  </style>
</head>
<body>
<div class="d-flex" id="wrapper">
  <!-- Sidebar -->
  <nav id="sidebarMenu" class="bg-dark text-white p-3">
    <a href="#" class="d-flex align-items-center mb-3 text-white text-decoration-none">
      <span class="fs-4"><i class="fa-solid fa-user-tie me-2"></i>Team Lead</span>
    </a>
    <hr class="text-secondary" />
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item mb-2"><a href="<?=site_url('/dashboard');?>" class="nav-link text-white <?php if(uri_string()==='dashboard') echo 'active bg-primary';?>"><i class="fa-solid fa-table-columns me-2"></i>Dashboard</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('submit-rating'); ?>" class="nav-link text-white"><i class="fa-solid fa-star me-2"></i>Submit Rating</a></li>
    </ul>
    <hr class="text-secondary" />
    <a href="<?=site_url('logout'); ?>" class="btn btn-outline-danger w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
  </nav>
  <!-- /Sidebar -->

  <!-- Page content -->
  <div id="page-content" class="p-4">

<h4>My Team Members</h4>
<div class="table-responsive">
<table class="table table-bordered table-striped">
<thead class="table-dark"><tr><th>#</th><th>Name</th><th>Email</th><th>Action</th></tr></thead>
<tbody>
<?php foreach($employees as $idx=>$emp): ?>
<?php $CI=&get_instance(); $can=$CI->Submission_model->can_submit_current($auth_user->id,$emp->id); ?>
<tr>
  <td><?=($idx+1);?></td>
  <td><?=$emp->name;?></td>
  <td><?=$emp->email;?></td>
  <td>
  <?php if($can): ?>
  <a class="btn btn-sm btn-success" href="<?=site_url('submit-rating?target_id='.$emp->id);?>">Rate</a>
  <?php else: ?>
  
  <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

  </div>
</div>
<!-- Backdrop overlay -->
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>

<!-- Toggle button -->
<button class="btn btn-dark menu-toggle" id="menu-toggle"><i class="fa-solid fa-bars" id="menu-icon"></i></button>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded',function(){const t=document.getElementById('menu-toggle'),s=document.getElementById('sidebarMenu'),b=document.getElementById('sidebar-backdrop');function c(){s.classList.remove('show');b.classList.remove('show');document.body.style.overflow='';const i=document.getElementById('menu-icon');i&&(i.classList.remove('fa-xmark'),i.classList.add('fa-bars'))}function o(){s.classList.add('show');b.classList.add('show');document.body.style.overflow='hidden';const i=document.getElementById('menu-icon');i&&(i.classList.remove('fa-bars'),i.classList.add('fa-xmark'))}t&&t.addEventListener('click',e=>{e.preventDefault(),s.classList.contains('show')?c():o()}),b&&b.addEventListener('click',c),s&&s.querySelectorAll('.nav-link, .btn').forEach(l=>l.addEventListener('click',()=>{window.innerWidth<=768&&c()})),window.addEventListener('resize',()=>{window.innerWidth>768&&s.classList.contains('show')&&c()})});
</script>
</body>
</html> 