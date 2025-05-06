<!DOCTYPE html><html><head>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<title>Rating form</title></head><body class="p-4">
<h4>Submit Rating</h4>
<?php if($this->session->flashdata('error')): ?>
<div class="alert alert-danger"><?=$this->session->flashdata('error');?></div><?php endif; ?>

<form method="post">
<div class="mb-3">
<label class="form-label">Select who you are rating:</label>
<select name="target_id" class="form-select" required>
    <option value="">-- choose --</option>
    <?php foreach($targets as $t): ?>
      <option value="<?=$t->id;?>" <?=isset($preselect) && $preselect==$t->id?'selected':'';?> ><?=$t->name;?></option>
    <?php endforeach; ?>
</select>
</div>

<table class="table">
<thead><tr><th>Question</th><th width="120">1</th><th width="120">2</th><th width="120">3</th><th width="120">4</th><th width="120">5</th><th>Comment</th></tr></thead>
<tbody>
<?php foreach($questions as $q): ?>
<tr>
  <td><?=$q->text;?></td>
  <?php for($i=1;$i<=5;$i++): ?>
  <td><input type="radio" name="rating[<?=$q->id;?>]" value="<?=$i;?>" required></td>
  <?php endfor; ?>
  <td><input type="text" name="comment[<?=$q->id;?>]" class="form-control"></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<button class="btn btn-success">Submit</button>
<a href="<?=site_url('dashboard');?>" class="btn btn-outline-secondary">Cancel</a>
</form>
</body></html>
