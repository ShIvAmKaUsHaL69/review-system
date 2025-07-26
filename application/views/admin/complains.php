<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Anonymous List</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body{overflow-x:hidden;}
    #sidebarMenu{min-width:240px;min-height:100vh;transition:transform .3s ease-in-out;}
    #page-content{flex-grow:1;}
    .sidebar-backdrop{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:1020;display:none;}
    .sidebar-backdrop.show{display:block;}
    .menu-toggle{position:fixed;top:10px;right:25px;z-index:1040;display:none;}
    @media(max-width:768px){#sidebarMenu{position:fixed;top:0;left:0;z-index:1030;transform:translateX(-100%);}#sidebarMenu.show{transform:translateX(0);} .menu-toggle{display:block;}}
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
      <li class="nav-item mb-2"><a href="<?=site_url('/dashboard');?>" class="nav-link text-white"><i class="fa-solid fa-table-columns me-2"></i>Dashboard</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('admin/users');?>" class="nav-link text-white <?php if(uri_string()==='admin/users') echo 'active bg-primary';?>"><i class="fa-solid fa-users-gear me-2"></i>Users</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('admin/questions');?>" class="nav-link text-white <?php if(uri_string()==='admin/questions') echo 'active bg-primary';?>"><i class="fa-solid fa-question me-2"></i>Questions</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('admin/performance');?>" class="nav-link text-white <?php if(uri_string()==='admin/performance') echo 'active bg-primary';?>"><i class="fa-solid fa-chart-simple me-2"></i>Team Performance</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('admin/charts');?>" class="nav-link text-white <?php if(uri_string()==='admin/charts') echo 'active bg-primary';?>"><i class="fa-solid fa-border-all me-2"></i>Rating Charts</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('admin/complains');?>" class="nav-link text-white active bg-primary"><i class="fa-solid fa-comment-dots me-2"></i>Anonymous</a></li>
    </ul>
    <hr class="text-secondary" />
    <a href="<?=site_url('logout'); ?>" class="btn btn-outline-danger w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
  </nav>
  <!-- /Sidebar -->

  <!-- Page content -->
  <div id="page-content" class="p-4">
    <h4>Anonymous Reviews</h4>

    <!-- Filter form -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="get" action="<?=site_url('admin/complains');?>" class="row g-3">
          <!-- Against dropdown -->
          <div class="col-md-4">
            <label class="form-label">Against</label>
            <div class="dropdown custom-select-dropdown">
              <input type="hidden" name="against_id" id="selected-against-id" value="<?=($filter_against_id);?>">
              <button class="btn-outline-secondary dropdown-toggle form-control text-start" type="button" id="against-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <?php
                  $selected_against_name = $filter_against_id ? 'Unknown' : 'All';
                  if($filter_against_id) {
                    foreach($users as $u) {
                      if($u->id == $filter_against_id) { $selected_against_name = $u->name; break; }
                    }
                  }
                  echo htmlspecialchars($selected_against_name);
                ?>
              </button>
              <div class="dropdown-menu w-100 p-0" aria-labelledby="against-dropdown">
                <div class="p-2">
                  <input type="text" id="against-search" class="form-control" placeholder="Search users...">
                </div>
                <div class="dropdown-divider m-0"></div>
                <div class="against-options-container" style="max-height:200px;overflow-y:auto;">
                  <button class="dropdown-item" type="button" data-id="">All</button>
                  <?php foreach($users as $u): ?>
                    <button class="dropdown-item" type="button" data-id="<?=$u->id;?>"><?=$u->name;?></button>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Month dropdown -->
          <div class="col-md-3">
            <label class="form-label">Month</label>
            <div class="dropdown custom-select-dropdown">
              <input type="hidden" name="month" id="selected-month" value="<?=htmlspecialchars($filter_month);?>">
              <button class="btn-outline-secondary dropdown-toggle form-control text-start" type="button" id="month-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <?php
                  $selected_month_text = ($filter_month === 'all') ? 'All' : date('F Y', strtotime($filter_month.'-01'));
                  echo htmlspecialchars($selected_month_text);
                ?>
              </button>
              <div class="dropdown-menu w-100 p-0" aria-labelledby="month-dropdown">
                <div class="p-2">
                  <input type="text" class="form-control month-search" placeholder="Search months...">
                </div>
                <div class="dropdown-divider m-0"></div>
                <div class="month-options-container" style="max-height:200px;overflow-y:auto;">
                  <button class="dropdown-item" type="button" data-id="all">All</button>
                  <?php foreach($months as $m): ?>
                    <button class="dropdown-item" type="button" data-id="<?=$m;?>"><?=date('F Y', strtotime($m.'-01'));?></button>
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
      <table class="table table-bordered table-striped">
        <thead class="table-dark"><tr><th>#</th><th>Against</th><th>Anonymous Review</th><th>Date</th></tr></thead>
        <tbody>
          <?php foreach($complains as $idx=>$c): ?>
            <tr>
              <td><?=($idx+1);?></td>
              <td><?=htmlspecialchars($c->against_name);?></td>
              <td><?=htmlspecialchars($c->complain);?></td>
              <td><?=date('d M Y', strtotime($c->created_at));?></td>
            </tr>
          <?php endforeach; ?>
          <?php if(empty($complains)): ?>
            <tr><td colspan="5" class="text-center">No Anonymous Review yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Mobile sidebar toggle
$(function(){
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

// Searchable dropdowns functionality
$(function(){
  // Against dropdown search
  const againstSearch = $('#against-search');
  const againstOptions = $('.against-options-container .dropdown-item');
  const againstHidden  = $('#selected-against-id');
  const againstBtn     = $('#against-dropdown');

  againstSearch.on('input', function(){
    const term = $(this).val().toLowerCase();
    againstOptions.each(function(){
      const text = $(this).text().toLowerCase();
      $(this).toggle(term === '' || text.includes(term));
    });
  });

  againstOptions.on('click', function(){
    const id = $(this).data('id');
    againstHidden.val(id);
    againstBtn.text($(this).text());
  });

  // Month dropdown search
  const monthOptions = $('.month-options-container .dropdown-item');
  $('.month-search').on('input', function(){
    const term = $(this).val().toLowerCase();
    monthOptions.each(function(){
      const text = $(this).text().toLowerCase();
      $(this).toggle(term === '' || text.includes(term));
    });
  });

  monthOptions.on('click', function(){
    const val = $(this).data('id');
    $('#selected-month').val(val);
    const btn = $('#month-dropdown');
    btn.text($(this).text());
  });
});
</script>
</body>
</html> 