<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Questions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<a href="<?=site_url('dashboard'); ?>" class="btn btn-secondary mb-3">← Back</a>
<h4>Create Question</h4>
<form method="post" class="row g-3 mb-4">
    <div class="col-md-8"><input class="form-control" name="text" placeholder="Question text" required></div>
    <div class="col-md-2">
        <select name="for_role" class="form-select">
            <option value="2">For TL Form</option>
            <option value="3">For Employee Form</option>
        </select>
    </div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Add</button></div>
</form>

<div class="row">
<div class="col-md-6">
<h5>Questions for TL Form</h5>
<ul class="list-group mb-4">
    <?php foreach($questions_tl as $q): ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <?=$q->text;?>
    </li>
    <?php endforeach; ?>
</ul>
</div>
<div class="col-md-6">
<h5>Questions for Employee Form</h5>
<ul class="list-group mb-4">
    <?php foreach($questions_emp as $q): ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <?=$q->text;?>
    </li>
    <?php endforeach; ?>
</ul>
</div>
</div>
</body>
</html> 