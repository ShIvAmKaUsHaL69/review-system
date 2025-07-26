<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Submit Complaint</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    #sidebarMenu{position:fixed;top:0;left:0;z-index:1030;transform:translateX(-100%);}#sidebarMenu.show{transform:translateX(0);} .menu-toggle{display:block;}
  }
  /* Custom dropdown styling */
  .custom-select-dropdown .dropdown-menu {
    width: 100%;
    max-height: 250px;
  }
  .custom-select-dropdown .dropdown-item {
    cursor: pointer;
    padding: 8px 16px;
  }
  .custom-select-dropdown .dropdown-item:hover {
    background-color: #f8f9fa;
  }
</style>
</head>
<body>
<!-- Backdrop overlay for mobile -->
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
<!-- Mobile menu toggle button -->
<button class="btn btn-dark menu-toggle" id="menu-toggle"><i class="fa-solid fa-bars" id="menu-icon"></i></button>

<?php
  // Determine role-based header/icon
  $roleId = isset($auth_user->role_id) ? $auth_user->role_id : 0;
  $headerTxt = 'User';
  $iconCls   = 'fa-user';
  if($roleId == 2){ $headerTxt = 'Team Lead'; $iconCls='fa-user-tie'; }
  elseif($roleId == 3){ $headerTxt = 'Employee'; $iconCls='fa-user'; }
?>
<div class="d-flex" id="wrapper">
  <!-- Sidebar -->
  <nav id="sidebarMenu" class="bg-dark text-white p-3">
    <a href="#" class="d-flex align-items-center mb-3 text-white text-decoration-none"><span class="fs-4"><i class="fa-solid <?=$iconCls;?> me-2"></i><?=$headerTxt;?></span></a>
    <hr class="text-secondary" />
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item mb-2"><a href="<?=site_url('/dashboard');?>" class="nav-link text-white <?php if(uri_string()==='dashboard') echo 'active bg-primary';?>"><i class="fa-solid fa-table-columns me-2"></i>Dashboard</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('submit-rating'); ?>" class="nav-link text-white"><i class="fa-solid fa-star me-2"></i>Submit Rating</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('change-password'); ?>" class="nav-link text-white <?php if(uri_string()==='change-password') echo 'active bg-primary';?>"><i class="fa-solid fa-key me-2"></i>Change Password</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('submit-complain'); ?>" class="nav-link text-white active bg-primary"><i class="fa-solid fa-comment-dots me-2"></i>Anonymous</a></li>
    </ul>
    <hr class="text-secondary" />
    <a href="<?=site_url('logout'); ?>" class="btn btn-outline-danger w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
  </nav>
  <!-- /Sidebar -->

  <!-- Page content -->
  <div id="page-content" class="p-4">
    <h4><i class="fa-solid fa-comment-dots me-2"></i>Submit Anonymous</h4>

    <?php if ($this->session->flashdata('error')): ?>
      <div class="alert alert-danger"><?=$this->session->flashdata('error');?></div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('success')): ?>
      <div class="alert alert-success"><?=$this->session->flashdata('success');?></div>
    <?php endif; ?>

    <form method="post" class="mt-3" style="max-width:600px;">
      <div class="mb-3">
        <label class="form-label">Select User</label>
        <div class="dropdown custom-select-dropdown">
          <input type="hidden" name="against_id" id="selected-user-id" value="" required>
          <button class=" btn-outline-secondary dropdown-toggle form-control text-start" type="button" id="user-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
            Choose user...
          </button>
          <div class="dropdown-menu w-100 p-0" aria-labelledby="user-dropdown">
            <div class="p-2">
              <input type="text" id="user-search" class="form-control" placeholder="Search user by name...">
            </div>
            <div class="dropdown-divider m-0"></div>
            <div class="user-options-container" style="max-height:200px;overflow-y:auto;">
              <?php foreach($users as $u): 
                if($u->id != $auth_user->id && strtolower($u->name) !== 'admin'): ?>
                <button class="dropdown-item" type="button" data-id="<?=$u->id;?>"><?=$u->name;?></button>
              <?php endif; endforeach; ?>
            </div>
          </div>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Complaint</label>
        <textarea name="complain" class="form-control" rows="5" required></textarea>
      </div>
      <button type="submit" class="btn btn-success">Submit</button>
      <a href="<?=site_url('dashboard'); ?>" class="btn btn-outline-secondary">Cancel</a>
    </form>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function(){
  // User dropdown search and selection
  const userSearch = $('#user-search');
  const userDropdown = $('#user-dropdown');
  const selectedUserId = $('#selected-user-id');
  
  // Filter users based on search
  userSearch.on('keyup', function(){
    const term = $(this).val().toLowerCase();
    $('.user-options-container .dropdown-item').each(function(){
      $(this).toggle($(this).text().toLowerCase().includes(term));
    });
  });

  // Handle user selection
  $('.user-options-container .dropdown-item').on('click', function(){
    const id = $(this).data('id');
    const name = $(this).text();
    selectedUserId.val(id);
    userDropdown.text(name);
  });

  // Mobile sidebar toggle
  const toggleBtn = $('#menu-toggle');
  const sidebarMenu = $('#sidebarMenu');
  const sidebarBackdrop = $('#sidebar-backdrop');

  function closeSidebar(){sidebarMenu.removeClass('show');sidebarBackdrop.removeClass('show');$('body').css('overflow','');$('#menu-icon').removeClass('fa-xmark').addClass('fa-bars');}
  function openSidebar(){sidebarMenu.addClass('show');sidebarBackdrop.addClass('show');$('body').css('overflow','hidden');$('#menu-icon').removeClass('fa-bars').addClass('fa-xmark');}

  toggleBtn.on('click', function(e){ e.preventDefault(); sidebarMenu.hasClass('show')?closeSidebar():openSidebar(); });
  sidebarBackdrop.on('click', closeSidebar);
  sidebarMenu.find('.nav-link, .btn').on('click', function(){ if(window.innerWidth<=768) closeSidebar(); });
  $(window).on('resize', function(){ if(window.innerWidth>768 && sidebarMenu.hasClass('show')) closeSidebar(); });
});
</script>
</body>
</html> 