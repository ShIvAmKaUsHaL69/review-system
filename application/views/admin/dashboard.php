<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Admin Dashboard</a>
    <div class="d-flex">
      <a href="<?=site_url('admin/users');?>" class="btn btn-outline-light me-2">Users</a>
      <a href="<?=site_url('admin/questions');?>" class="btn btn-outline-light me-2">Questions</a>
      <a href="<?=site_url('logout');?>" class="btn btn-danger">Logout</a>
    </div>
  </div>
</nav>
<div class="container">
<div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Team Leads</h5>
                    <p class="display-6"><?=count($tls);?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-success">
                <div class="card-body">
                    <h5 class="card-title">Total Employees</h5>
                    <p class="display-6"><?=count($employees) + count($tls);?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-secondary">
                <div class="card-body">
                    <h5 class="card-title">Total Submissions</h5>
                    <p class="display-6"><?=count($submissions);?></p>
                </div>
            </div>
        </div>
    </div>


    <h4>Submissions</h4>
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="<?=site_url('dashboard');?>" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Team Lead</label>
                    <select name="tl_id" class="form-select">
                        <option value="">All Team Leads</option>
                        <?php foreach($tls as $tl): ?>
                        <option value="<?=$tl->id;?>" <?=($filter_tl==$tl->id?'selected':'');?>><?=$tl->name;?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Rating Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="tl_emp" <?=($filter_type=='tl_emp'?'selected':'');?>>TL → Employee</option>
                        <option value="emp_tl" <?=($filter_type=='emp_tl'?'selected':'');?>>Employee → TL</option>
                        <option value="emp_emp" <?=($filter_type=='emp_emp'?'selected':'');?>>Employee → Employee</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Month</label>
                    <select name="period_id" class="form-select">
                        <option value="">All Months</option>
                        <?php foreach($periods as $period): ?>
                        <option value="<?=$period->id;?>" <?=($filter_period==$period->id?'selected':'');?>>
                            <?=date('F Y', strtotime($period->yearmonth.'-01'));?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-grow-1">Apply</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead class="table-dark"><tr>
            <th>#</th><th>Submitted By</th><th>Submitter Against</th><th>Month</th><th>Action</th>
        </tr></thead>
        <tbody>
        <?php foreach($submissions as $idx=>$s): ?>
        <tr>
            <td><?=($idx+1);?></td>
            <td><?=$s->submitter;?> (<?=$s->submitter_role == 'tl' ? 'TL' : ($s->submitter_role == 'employee' ? 'EM' : 'Admin')?>)</td>
            <td><?=$s->target;?> (<?=$s->target_role == 'tl' ? 'TL' : ($s->target_role == 'employee' ? 'EM' : 'Admin')?>)</td>
            <td><?=date('F Y', strtotime($s->yearmonth.'-01'));?></td>
            <td><button class="btn btn-sm btn-primary view-btn" data-id="<?=$s->id;?>">View</button></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<!-- Review Details Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Review Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <strong>Submitter:</strong> <span id="modal-submitter"></span>
          </div>
          <div class="col-md-6">
            <strong>Submitter against:</strong> <span id="modal-target"></span>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <strong>Month:</strong> <span id="modal-period"></span>
          </div>
          <div class="col-md-6">
            <strong>Date:</strong> <span id="modal-date"></span>
          </div>
        </div>
        <h6>Answers</h6>
        <table class="table table-striped" id="answers-table">
          <thead class="table-dark">
            <tr><th>Question</th><th width="100">Rating</th><th>Comment</th></tr>
          </thead>
          <tbody>
            <!-- Answers loaded via AJAX -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  $('.view-btn').click(function() {
    const id = $(this).data('id');
    
    // Clear previous content
    $('#answers-table tbody').empty();
    
    // Load submission details via AJAX
    $.ajax({
      url: '<?=site_url("admin/get_review_json/");?>' + id,
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        // Populate modal with submission details
        $('#modal-submitter').text(data.submission.submitter + ' (' + (data.submission.submitter_role == 'tl' ? 'TL' : data.submission.submitter_role == 'employee' ? 'EM' : 'Admin') + ')');
        $('#modal-target').text(data.submission.target + ' (' + (data.submission.target_role == 'tl' ? 'TL' : data.submission.target_role == 'employee' ? 'EM' : 'Admin') + ')');
        $('#modal-period').text(data.submission.yearmonth ? new Date(data.submission.yearmonth + '-01').toLocaleString('en-US', {month: 'long', year: 'numeric'}) : '');
        $('#modal-date').text(data.submission.created_at ? new Date(data.submission.created_at).toLocaleString('en-US', {month: 'long', day: 'numeric', year: 'numeric'}) : '');
        
        // Add answers to table
        data.answers.forEach(function(answer) {
          $('#answers-table tbody').append(
            `<tr>
              <td>${answer.text}</td>
              <td>${answer.rating}</td>
              <td>${answer.comment || ''}</td>
            </tr>`
          );
        });
        
        // Show modal
        $('#reviewModal').modal('show');
      },
      error: function() {
        alert('Error loading review details');
      }
    });
  });
});
</script>
</body>
</html> 