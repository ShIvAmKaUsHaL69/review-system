<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<a href="<?=site_url('dashboard');?>" class="btn btn-secondary mb-3">← Back</a>
<h4>Create New User</h4>
<form method="post" class="row g-3 mb-4">
    <div class="col-md-4"><input class="form-control" name="name" placeholder="Full name" required></div>
    <div class="col-md-4"><input type="email" class="form-control" name="email" placeholder="Email" required></div>
    <div class="col-md-2"><input type="password" class="form-control" name="password" placeholder="Password" required></div>
    <div class="col-md-2">
        <select name="role_id" class="form-select" required>
            <option value="2">Team Lead</option>
            <option value="3">Employee</option>
        </select>
    </div>
    <div class="col-md-3">
        <select name="tl_id" class="form-select">
            <option value="">Assign TL (for employee)</option>
            <?php foreach($tls as $tl): ?>
                <option value="<?=$tl->id;?>"><?=$tl->name;?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Add User</button></div>
</form>

<div class="table-responsive">
<table class="table table-bordered table-striped">
<thead class="table-dark"><tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Team Lead</th></tr></thead>
<tbody>
<?php foreach(array_merge($tls,$employees) as $idx=>$u): ?>
<tr>
  <td><?=($idx+1);?></td>
  <td><?=$u->name;?></td>
  <td><?=$u->email;?></td>
  <td><?=$u->role_id==2?'TL':'Employee';?></td>
  <td><?php if($u->tl_id){
      foreach($tls as $t) if($t->id==$u->tl_id) echo $t->name;
  } ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</body>
</html> 