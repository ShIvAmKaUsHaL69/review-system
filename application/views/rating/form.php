<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<title>Rating form</title>
<style>
  body{overflow-x:hidden;}
  #sidebarMenu{width:240px;min-height:100vh;transition:transform .3s ease-in-out;}
  #page-content{flex-grow:1;}
  
  /* Backdrop overlay */
  .sidebar-backdrop{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:1020;display:none;}
  .sidebar-backdrop.show{display:block;}
  
  /* Menu toggle button */
  .menu-toggle{position:fixed;top:10px;right:25px;z-index:1040;display:none;}
  
  @media(max-width:768px){
    #sidebarMenu{position:fixed;top:0;left:0;z-index:1030;transform:translateX(-100%);}
    #sidebarMenu.show{transform:translateX(0);}
    .menu-toggle{display:block;}
  }
</style>
</head>
<body>
<!-- Backdrop overlay for mobile -->
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>

<!-- Mobile menu toggle button -->
<button class="btn btn-dark menu-toggle" id="menu-toggle">
  <i class="fa-solid fa-bars" id="menu-icon"></i>
</button>

<?php
  // Determine role-based header/icon
  $roleId = isset($auth_user->role_id) ? $auth_user->role_id : 0;
  $headerTxt = 'User';
  $iconCls   = 'fa-user';
  if($roleId == 2){ $headerTxt = 'Team Lead'; $iconCls='fa-user-tie'; }
  elseif($roleId == 3){ $headerTxt = 'Employee'; $iconCls='fa-user'; }
  elseif($roleId == 1){ $headerTxt = 'Admin'; $iconCls='fa-chart-line'; }
?>
<div class="d-flex" id="wrapper">
  <!-- Sidebar -->
  <nav id="sidebarMenu" class="bg-dark text-white p-3">
    <a href="#" class="d-flex align-items-center mb-3 text-white text-decoration-none">
      <span class="fs-4"><i class="fa-solid <?=$iconCls;?> me-2"></i><?=$headerTxt;?></span>
    </a>
    <hr class="text-secondary" />
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item mb-2">
        <a href="<?=site_url('/dashboard');?>" class="nav-link text-white <?php if(uri_string()==='dashboard') echo 'active bg-primary';?>"><i class="fa-solid fa-table-columns me-2"></i>Dashboard</a>
      </li>
      <li class="nav-item mb-2">
        <a href="<?=site_url('submit-rating'); ?>" class="nav-link text-white active bg-primary"><i class="fa-solid fa-star me-2"></i>Submit Rating</a>
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

<h4>Submit Rating</h4>
<?php if($this->session->flashdata('error')): ?>
<div class="alert alert-danger"><?=$this->session->flashdata('error');?></div><?php endif; ?>

<form method="post">
<div class="mb-3">
<label class="form-label">Select who you are rating:</label>
<select name="target_id" id="target_select" class="form-select" required>
    <option value="">-- choose --</option>
    <?php foreach($targets as $t): ?>
      <option value="<?=$t->id;?>" <?=isset($preselect) && $preselect==$t->id?'selected':'';?> ><?=$t->name;?></option>
    <?php endforeach; ?>
</select>
</div>

<table class="table">
<thead><tr><th>Question</th><th width="120">Rating</th><th>Comment</th></tr></thead>
<tbody>
<?php foreach($questions as $q): ?>
<tr>
  <td><?=$q->text;?></td>
  <td>
    <select name="rating[<?=$q->id;?>]" class="form-select" required>
      <option value="">Select</option>
      <?php for($i=1;$i<=10;$i++): ?>
      <option value="<?=$i;?>"><?=$i;?></option>
      <?php endfor; ?>
    </select>
  </td>
  <td><input type="text" name="comment[<?=$q->id;?>]" class="form-control"></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<button class="btn btn-success">Submit</button>
<a href="<?=site_url('dashboard'); ?>" class="btn btn-outline-secondary">Cancel</a>
</form>

  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('target_select').addEventListener('change', function() {
  const targetId = this.value;
  if (targetId) {
    // Redirect to the same page with the target_id parameter
    window.location.href = '<?=site_url('rating/submit');?>?target_id=' + targetId;
  }
});

// Mobile sidebar functionality
document.addEventListener('DOMContentLoaded', function() {
  const menuToggle = document.getElementById('menu-toggle');
  const sidebarMenu = document.getElementById('sidebarMenu');
  const sidebarBackdrop = document.getElementById('sidebar-backdrop');
  
  function closeSidebar() {
    if (sidebarMenu && sidebarBackdrop) {
      sidebarMenu.classList.remove('show');
      sidebarBackdrop.classList.remove('show');
      document.body.style.overflow = '';
      
      const menuIcon = document.getElementById('menu-icon');
      if (menuIcon) {
        menuIcon.classList.remove('fa-xmark');
        menuIcon.classList.add('fa-bars');
      }
    }
  }
  
  function openSidebar() {
    if (sidebarMenu && sidebarBackdrop) {
      sidebarMenu.classList.add('show');
      sidebarBackdrop.classList.add('show');
      document.body.style.overflow = 'hidden';
      
      const menuIcon = document.getElementById('menu-icon');
      if (menuIcon) {
        menuIcon.classList.remove('fa-bars');
        menuIcon.classList.add('fa-xmark');
      }
    }
  }
  
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
  
  if (sidebarBackdrop) {
    sidebarBackdrop.addEventListener('click', closeSidebar);
  }
  
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
  
  window.addEventListener('resize', function() {
    if (window.innerWidth > 768 && sidebarMenu && sidebarMenu.classList.contains('show')) {
      closeSidebar();
    }
  });
});
</script>
</body></html>
