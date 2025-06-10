<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password</title>
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

    @media(max-width:768px){#sidebarMenu{position:fixed;top:0;left:0;z-index:1030;transform:translateX(-100%);}#sidebarMenu.show{transform:translateX(0);} .menu-toggle{display:block;} }
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
      <li class="nav-item mb-2"><a href="<?=site_url('/dashboard');?>" class="nav-link text-white"><i class="fa-solid fa-table-columns me-2"></i>Dashboard</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('submit-rating'); ?>" class="nav-link text-white"><i class="fa-solid fa-star me-2"></i>Submit Rating</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('change-password'); ?>" class="nav-link text-white active bg-primary"><i class="fa-solid fa-key me-2"></i>Change Password</a></li>
    </ul>
    <hr class="text-secondary" />
    <a href="<?=site_url('logout'); ?>" class="btn btn-outline-danger w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
  </nav>
  <!-- /Sidebar -->

  <!-- Page content -->
  <div id="page-content" class="p-4">
    <h4><i class="fa-solid fa-key me-2"></i>Change Password</h4>

    <?php if ($this->session->flashdata('error')): ?>
      <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('success')): ?>
      <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
    <?php endif; ?>

    <form method="post" class="mt-3" style="max-width:400px;">
      <div class="mb-3">
        <label class="form-label">Enter New Password</label>
        <input type="password" name="new_password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary">Save</button>
    </form>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Same sidebar toggle logic as dashboard page
document.addEventListener('DOMContentLoaded',function(){const t=document.getElementById('menu-toggle'),s=document.getElementById('sidebarMenu'),b=document.getElementById('sidebar-backdrop');function c(){s.classList.remove('show');b.classList.remove('show');document.body.style.overflow='';const i=document.getElementById('menu-icon');i&&(i.classList.remove('fa-xmark'),i.classList.add('fa-bars'))}function o(){s.classList.add('show');b.classList.add('show');document.body.style.overflow='hidden';const i=document.getElementById('menu-icon');i&&(i.classList.remove('fa-bars'),i.classList.add('fa-xmark'))}t&&t.addEventListener('click',e=>{e.preventDefault(),s.classList.contains('show')?c():o()}),b&&b.addEventListener('click',c),s&&s.querySelectorAll('.nav-link, .btn').forEach(l=>l.addEventListener('click',()=>{window.innerWidth<=768&&c()})),window.addEventListener('resize',()=>{window.innerWidth>768&&s.classList.contains('show')&&c()})});
</script>
</body>
</html> 