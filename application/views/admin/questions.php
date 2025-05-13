<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
      body{overflow-x:hidden;}
      #sidebarMenu{min-width:240px;min-height:100vh;transition:transform .3s ease-in-out;}
      #page-content{flex-grow:1;}

      /* Sidebar backdrop */
      .sidebar-backdrop{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:1020;display:none;}
      .sidebar-backdrop.show{display:block;}

      /* Toggle button */
      .menu-toggle{position:fixed;top:10px;right:25px;z-index:1040;display:none;}

      /* Question item styling */
      .question-item {
          position: relative;
          cursor: pointer;
      }
      .question-item:hover {
          background-color: #f8f9fa;
      }
      .question-item:hover::after {
          content: "\f044"; /* Font Awesome edit icon */
          font-family: "Font Awesome 6 Free";
          font-weight: 900;
          position: absolute;
          right: 10px;
          color: #6c757d;
          opacity: 0.5;
      }
      .question-item.editing:hover::after {
          content: "";
      }
      .question-item.editing {
          background-color: #fff3cd;
      }
      .question-input {
          width: 100%;
          padding: 0.25rem 0.5rem;
          border: 1px solid #ced4da;
          border-radius: 0.25rem;
      }

      @media(max-width:768px){
        #sidebarMenu{position:fixed;top:0;left:0;z-index:1030;transform:translateX(-100%);}
        #sidebarMenu.show{transform:translateX(0);}
        .menu-toggle{display:block;}
      }
    </style>
</head>
<body>
<!-- Backdrop overlay for mobile when menu is open -->
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>

<!-- Mobile menu toggle button -->
<button class="btn btn-dark menu-toggle" id="menu-toggle"><i class="fa-solid fa-bars" id="menu-icon"></i></button>

<div class="d-flex" id="wrapper">
  <!-- Sidebar -->
  <nav id="sidebarMenu" class="bg-dark text-white p-3">
    <a href="#" class="d-flex align-items-center mb-3 text-white text-decoration-none">
      <span class="fs-4"><i class="fa-solid fa-chart-line me-2"></i>Admin</span>
    </a>
    <hr class="text-secondary" />
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item mb-2"><a href="<?=site_url('/dashboard');?>" class="nav-link text-white"><i class="fa-solid fa-table-columns me-2"></i>Dashboard</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('admin/users');?>" class="nav-link text-white"><i class="fa-solid fa-users-gear me-2"></i>Users</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('admin/questions');?>" class="nav-link text-white active bg-primary"><i class="fa-solid fa-question me-2"></i>Questions</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('admin/performance');?>" class="nav-link text-white <?php if(uri_string()==='admin/performance') echo 'active bg-primary';?>"><i class="fa-solid fa-chart-simple me-2"></i>Team Performance</a></li>
      <li class="nav-item mb-2"><a href="<?=site_url('admin/charts');?>" class="nav-link text-white <?php if(uri_string()==='admin/charts') echo 'active bg-primary';?>"><i class="fa-solid fa-border-all me-2"></i>Rating Charts</a></li>
    </ul>
    <hr class="text-secondary" />
    <a href="<?=site_url('logout'); ?>" class="btn btn-outline-danger w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
  </nav>
  <!-- /Sidebar -->

  <!-- Page content -->
  <div id="page-content" class="p-4">

<h4>Create Question</h4>
<form method="post" class="row g-3 mb-4">
    <div class="col-md-6"><input class="form-control" name="text" placeholder="Question text" required></div>
    <div class="col-md-2">
        <select name="for_role" class="form-select">
            <option value="2">For TL → TM Form</option>
            <option value="3">For TM → TL Form</option>
            <option value="4">For TM → TM Form</option>
        </select>
    </div>
    <div class="col-md-2">
    <select name="quater" class="form-select">
            <option value="0">Monthly</option>
            <option value="1">Quatar 1 ( Jan - Mar )</option>
            <option value="2">Quatar 2 ( Apr - June )</option>
            <option value="3">Quatar 3 ( July - Sept )</option>
            <option value="4">Quatar 4 ( Oct - Dec )</option>
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Add</button></div>
</form>

<div class="row">
<div class="col-md-4">
<h5 class="text-primary"><i class="fa-solid fa-user-tie me-1"></i> TL Form Questions</h5>
<ul class="list-group mb-4">
    <?php foreach($questions_tl as $q): ?>
    <li class="list-group-item d-flex justify-content-between align-items-center question-item" data-id="<?=$q->id;?>">
        <span class="question-text"><?=$q->text;?></span>
        <div class="edit-controls d-none">
            <button class="btn btn-sm btn-success save-question-btn"><i class="fa-solid fa-check"></i></button>
            <button class="btn btn-sm btn-secondary cancel-edit-btn ms-1"><i class="fa-solid fa-times"></i></button>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
</div>
<div class="col-md-4">
<h5 class="text-success"><i class="fa-solid fa-user me-1"></i> TM Form Questions</h5>
<ul class="list-group mb-4">
    <?php foreach($questions_emp as $q): ?>
    <li class="list-group-item d-flex justify-content-between align-items-center question-item" data-id="<?=$q->id;?>">
        <span class="question-text"><?=$q->text;?></span>
        <div class="edit-controls d-none">
            <button class="btn btn-sm btn-success save-question-btn"><i class="fa-solid fa-check"></i></button>
            <button class="btn btn-sm btn-secondary cancel-edit-btn ms-1"><i class="fa-solid fa-times"></i></button>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
</div>
<div class="col-md-4">
<h5 class="text-info"><i class="fa-solid fa-users me-1"></i> TM → TM Form Questions</h5>
<ul class="list-group mb-4">
    <?php foreach($questions_emp_to_emp as $q): ?>
    <li class="list-group-item d-flex justify-content-between align-items-center question-item" data-id="<?=$q->id;?>">
        <span class="question-text"><?=$q->text;?></span>
        <div class="edit-controls d-none">
            <button class="btn btn-sm btn-success save-question-btn"><i class="fa-solid fa-check"></i></button>
            <button class="btn btn-sm btn-secondary cancel-edit-btn ms-1"><i class="fa-solid fa-times"></i></button>
        </div>
    </li>
    <?php endforeach; ?>
</ul>
</div>
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

  function closeSidebar(){
    sidebarMenu.classList.remove('show');
    sidebarBackdrop.classList.remove('show');
    document.body.style.overflow='';
    const icon=document.getElementById('menu-icon');
    if(icon){icon.classList.remove('fa-xmark');icon.classList.add('fa-bars');}
  }

  function openSidebar(){
    sidebarMenu.classList.add('show');
    sidebarBackdrop.classList.add('show');
    document.body.style.overflow='hidden';
    const icon=document.getElementById('menu-icon');
    if(icon){icon.classList.remove('fa-bars');icon.classList.add('fa-xmark');}
  }

  if(menuToggle){menuToggle.addEventListener('click',function(e){e.preventDefault();sidebarMenu.classList.contains('show')?closeSidebar():openSidebar();});}
  if(sidebarBackdrop){sidebarBackdrop.addEventListener('click',closeSidebar);}
  if(sidebarMenu){sidebarMenu.querySelectorAll('.nav-link, .btn').forEach(l=>{l.addEventListener('click',()=>{if(window.innerWidth<=768)closeSidebar();});});}
  window.addEventListener('resize',()=>{if(window.innerWidth>768&&sidebarMenu.classList.contains('show'))closeSidebar();});

  // Question editing functionality
  const questionItems = document.querySelectorAll('.question-item');
  
  questionItems.forEach(item => {
    // Click to edit
    item.addEventListener('click', function(e) {
      // Don't trigger if we click on buttons
      if (e.target.tagName === 'BUTTON' || e.target.closest('button') || 
          e.target.tagName === 'I' || this.classList.contains('editing')) {
        return;
      }
      
      // Get question text and add editing class
      const textSpan = this.querySelector('.question-text');
      const currentText = textSpan.textContent.trim();
      textSpan.dataset.original = currentText;
      this.classList.add('editing');
      
      // Replace text with input
      textSpan.innerHTML = `<input type="text" class="question-input" value="${currentText}">`;
      const input = textSpan.querySelector('input');
      input.focus();
      input.setSelectionRange(0, input.value.length);
      
      // Show edit controls
      const controls = this.querySelector('.edit-controls');
      controls.classList.remove('d-none');
    });
    
    // Save question
    const saveBtn = item.querySelector('.save-question-btn');
    saveBtn.addEventListener('click', function() {
      const li = this.closest('.question-item');
      const input = li.querySelector('input');
      const newText = input.value.trim();
      const questionId = li.dataset.id;
      const textSpan = li.querySelector('.question-text');
      
      if (newText === '') {
        alert('Question text cannot be empty');
        input.focus();
        return;
      }
      
      // AJAX request to update
      fetch('<?=site_url('admin/update_question');?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${questionId}&text=${encodeURIComponent(newText)}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Update text and exit edit mode
          textSpan.innerHTML = newText;
          li.classList.remove('editing');
          li.querySelector('.edit-controls').classList.add('d-none');
          
          // Show success indicator
          const successIndicator = document.createElement('span');
          successIndicator.className = 'text-success ms-2';
          successIndicator.innerHTML = '<i class="fa-solid fa-check"></i>';
          textSpan.appendChild(successIndicator);
          
          // Remove indicator after a delay
          setTimeout(() => {
            successIndicator.remove();
          }, 2000);
        } else {
          alert('Failed to update question');
        }
      })
      .catch(error => {
        console.error('Error updating question:', error);
        alert('An error occurred while updating the question');
      });
    });
    
    // Cancel edit
    const cancelBtn = item.querySelector('.cancel-edit-btn');
    cancelBtn.addEventListener('click', function() {
      const li = this.closest('.question-item');
      const textSpan = li.querySelector('.question-text');
      const originalText = textSpan.dataset.original || textSpan.textContent;
      
      // Restore original text
      textSpan.innerHTML = originalText;
      li.classList.remove('editing');
      li.querySelector('.edit-controls').classList.add('d-none');
    });
  });
});
</script>
</body>
</html> 