<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Review Detail</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<a href="<?=site_url('admin/reviews'); ?>" class="btn btn-secondary mb-3">← Back</a>
<h4>Review Detail</h4>
<table class="table table-bordered w-50">
<tr><th>Submitter</th><td><?=$submission->submitter;?></td></tr>
<tr><th>Target</th><td><?=$submission->target;?></td></tr>
<tr><th>Period</th><td><?=$submission->yearmonth;?></td></tr>
<tr><th>Date</th><td><?=$submission->created_at;?></td></tr>
</table>
<h5>Answers</h5>
<table class="table table-striped">
<thead class="table-dark"><tr><th>Question</th><th>Rating</th><th>Comment</th></tr></thead>
<tbody>
<?php foreach($answers as $a): ?>
<tr>
  <td><?=$a->text;?></td>
  <td><?=$a->rating;?></td>
  <td><?=$a->comment;?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</body>
</html> 