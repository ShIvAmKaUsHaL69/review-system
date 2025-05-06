<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>All Reviews</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<a href="<?=site_url('dashboard'); ?>" class="btn btn-secondary mb-3">← Back</a>
<h4>All Submitted Reviews</h4>
<table class="table table-bordered table-striped">
<thead class="table-dark"><tr><th>#</th><th>Submitter</th><th>Target</th><th>Period</th><th>Date</th><th>Action</th></tr></thead>
<tbody>
<?php foreach($submissions as $idx=>$s): ?>
<tr>
 <td><?=$idx+1;?></td>
 <td><?=$s->submitter;?></td>
 <td><?=$s->target;?></td>
 <td><?=$s->yearmonth;?></td>
 <td><?=$s->created_at;?></td>
 <td><a class="btn btn-sm btn-primary" href="<?=site_url('admin/reviews/'.$s->id);?>">View</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</body>
</html> 