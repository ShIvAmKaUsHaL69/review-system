<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Employee Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Employee Dashboard</a>
    <div class="d-flex">
      <a href="<?=site_url('submit-rating'); ?>" class="btn btn-outline-light me-2">Submit Rating</a>
      <a href="<?=site_url('logout'); ?>" class="btn btn-danger">Logout</a>
    </div>
  </div>
</nav>

<h4>My Team Lead</h4>
<?php if($tl): ?>
<div class="card mb-4">
  <div class="card-body">
    <h5 class="card-title"><?=$tl->name;?></h5>
    <p class="card-text">Email: <?=$tl->email;?></p>
    <a href="<?=site_url('submit-rating?target_id='.$tl->id);?>" class="btn btn-success">Rate TL</a>
  </div>
</div>
<?php endif; ?>

<h4>Fellow Employees</h4>
<div class="table-responsive">
<table class="table table-bordered table-striped">
<thead class="table-dark"><tr><th>#</th><th>Name</th><th>Email</th><th>Action</th></tr></thead>
<tbody>
<?php foreach($fellow_employees as $idx=>$emp): ?>
<?php 
$CI = get_instance(); 
/** @var Submission_model $submission_model */
$submission_model = $CI->Submission_model;
$can = $submission_model->can_submit_current($auth_user->id, $emp->id); 
?>
<tr>
  <td><?=($idx+1);?></td>
  <td><?=$emp->name;?></td>
  <td><?=$emp->email;?></td>
  <td>
  <?php if($can): ?>
  <a class="btn btn-sm btn-success" href="<?=site_url('submit-rating?target_id='.$emp->id);?>">Rate</a>
  <?php else: ?><?php endif; ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

</body>
</html> 