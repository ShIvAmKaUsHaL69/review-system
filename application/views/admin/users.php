<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            overflow-x: hidden;
        }

        #sidebarMenu {
            min-width: 240px;
            min-height: 100vh;
            transition: transform .3s ease-in-out;
        }

        #page-content {
            flex-grow: 1;
        }

        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1020;
            display: none;
        }

        .sidebar-backdrop.show { display: block; }

        .menu-toggle {
            position: fixed;
            top: 10px;
            right: 25px;
            z-index: 1040;
            display: none;
        }

        /* Editable fields styling */
        .editable {
            position: relative;
        }
        
        .user-row.editing {
            background-color: #fff3cd !important;
        }
        
        .editable input, 
        .editable select {
            width: 100%;
            padding: 0.25rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }
        
        .success-indicator {
            color: #198754;
            margin-left: 0.5rem;
        }

        /* Search input styling */
        .search-container {
            position: relative;
            margin-bottom: 1rem;
        }
        
        .search-container i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        #user-search {
            padding-right: 30px;
        }

        /* User details modal styling */
        .user-details-section {
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
        }
        
        .user-details-section:last-child {
            border-bottom: none;
        }
        
        .section-title {
            background-color: #f8f9fa;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border-left: 3px solid #0d6efd;
        }
        
        .team-member-list {
            list-style: none;
            padding-left: 0;
        }
        
        .team-member-list li {
            padding: 0.5rem;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .team-member-list li:last-child {
            border-bottom: none;
        }

        @media(max-width:768px) {
            #sidebarMenu {
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1030;
                transform: translateX(-100%);
            }

            #sidebarMenu.show { transform: translateX(0); }

            .menu-toggle { display: block; }

        }
    </style>
</head>

<body>
    <!-- Backdrop overlay for mobile when menu is open -->
    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    <!-- Mobile menu toggle button -->
    <button class="btn btn-dark menu-toggle" id="menu-toggle">
        <i class="fa-solid fa-bars" id="menu-icon"></i>
    </button>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <nav id="sidebarMenu" class="bg-dark text-white p-3">
            <a href="#" class="d-flex align-items-center mb-3 text-white text-decoration-none">
                <span class="fs-4"><i class="fa-solid fa-chart-line me-2"></i>Admin</span>
            </a>
            <hr class="text-secondary" />
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item mb-2"><a href="<?= site_url('/dashboard'); ?>" class="nav-link text-white"><i class="fa-solid fa-table-columns me-2"></i>Dashboard</a></li>
                <li class="nav-item mb-2"><a href="<?= site_url('admin/users'); ?>" class="nav-link text-white active bg-primary"><i class="fa-solid fa-users-gear me-2"></i>Users</a></li>
                <li class="nav-item mb-2"><a href="<?= site_url('admin/questions'); ?>" class="nav-link text-white"><i class="fa-solid fa-question me-2"></i>Questions</a></li>
                <li class="nav-item mb-2"><a href="<?= site_url('admin/performance'); ?>" class="nav-link text-white <?php if(uri_string()==='admin/performance') echo 'active bg-primary';?>"><i class="fa-solid fa-chart-simple me-2"></i>Team Performance</a></li>
                <li class="nav-item mb-2"><a href="<?= site_url('admin/charts'); ?>" class="nav-link text-white <?php if(uri_string()==='admin/charts') echo 'active bg-primary';?>"><i class="fa-solid fa-border-all me-2"></i>Rating Charts</a></li>
            </ul>
            <hr class="text-secondary" />
            <a href="<?= site_url('logout'); ?>" class="btn btn-outline-danger w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
        </nav>
        <!-- /Sidebar -->

        <!-- Page content -->
        <div id="page-content" class="p-4">

            <h4>Create New User</h4>
            <form method="post" class="row g-3 mb-4">
                <div class="col-md-2"><input class="form-control" name="name" placeholder="Full name" required></div>
                <div class="col-md-2"><input type="email" class="form-control" name="email" placeholder="Email" required></div>
                <div class="col-md-2"><input type="password" class="form-control" name="password" placeholder="Password" required></div>
                <div class="col-md-2">
                    <select name="role_id" class="form-select" required>
                        <option value="2">Team Lead</option>
                        <option value="3">Team Member</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tl_id" class="form-select">
                        <option value="">Assign TL (for employee)</option>
                        <?php foreach ($tls as $tl): ?>
                            <option value="<?= $tl->id; ?>"><?= $tl->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2"><input type="text" class="form-control" name="designation" placeholder="Designation" required></div>
                <div class="col-md-2"><button class="btn btn-primary w-100">Add User</button></div>
            </form>

            <h3>All Users</h3>

            <div class="mb-3 search-container">
                <input type="text" class="form-control" id="user-search" placeholder="Search by name...">
                <i class="fa-solid fa-search"></i>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Role</th>
                            <th>Team Lead</th>
                            <th>Designation</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_merge($tls, $employees) as $idx => $u): ?>
                            <tr class="user-row" data-id="<?= $u->id; ?>" data-role="<?= $u->role_id; ?>" data-tl="<?= $u->tl_id ?: ''; ?>">
                                <td><?= ($idx + 1); ?></td>
                                <td class="editable editable-name" data-field="name"><?= $u->name; ?></td>
                                <td class="editable editable-email" data-field="email"><?= $u->email; ?></td>
                                <td class="editable editable-password" data-field="password"><?= $u->password; ?></td>
                                <td class="editable editable-role" data-field="role_id"><?= $u->role_id == 2 ? '<span class="text-primary fw-bold">TL</span>' : '<span class="text-success">TM</span>'; ?></td>
                                <td class="editable editable-tl" data-field="tl_id"><?php if ($u->tl_id) {
                                        foreach ($tls as $t) if ($t->id == $u->tl_id) echo $t->name;
                                    } else { echo '-'; } ?></td>
                                <td class="editable editable-designation" data-field="designation"><?= $u->designation; ?></td>
                                <td>
                                    <div class="edit-controls d-none">
                                        <button class="btn btn-sm btn-success save-user-btn"><i class="fa-solid fa-check"></i> Save</button>
                                        <button class="btn btn-sm btn-secondary cancel-edit-btn ms-1"><i class="fa-solid fa-times"></i> Cancel</button>
                                    </div>
                                    <button class="btn btn-sm btn-info view-user-btn me-1" data-id="<?= $u->id; ?>"><i class="fa-solid fa-eye"></i> View</button>
                                    <button class="btn btn-sm btn-primary edit-user-btn"><i class="fa-solid fa-pen"></i> Edit</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    
    <!-- User Details Modal -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1" aria-labelledby="userDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userDetailsModalLabel">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="userDetailsContent">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuToggle = document.getElementById('menu-toggle');
        const sidebarMenu = document.getElementById('sidebarMenu');
        const sidebarBackdrop = document.getElementById('sidebar-backdrop');

        function closeSidebar() {
            if (sidebarMenu && sidebarBackdrop) {
                sidebarMenu.classList.remove('show');
                sidebarBackdrop.classList.remove('show');
                document.body.style.overflow = '';

                const menuIcon = document.getElementById('menu-icon');
                if (menuIcon) {
                    menuIcon.classList.remove('fa-xmark');
                    menuIcon.classList.add('fa-bars');
                }
            }
        }

        function openSidebar() {
            if (sidebarMenu && sidebarBackdrop) {
                sidebarMenu.classList.add('show');
                sidebarBackdrop.classList.add('show');
                document.body.style.overflow = 'hidden';

                const menuIcon = document.getElementById('menu-icon');
                if (menuIcon) {
                    menuIcon.classList.remove('fa-bars');
                    menuIcon.classList.add('fa-xmark');
                }
            }
        }

        if (menuToggle) {
            menuToggle.addEventListener('click', function (e) {
                e.preventDefault();
                if (sidebarMenu.classList.contains('show')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }

        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', closeSidebar);
        }

        if (sidebarMenu) {
            const navLinks = sidebarMenu.querySelectorAll('.nav-link, .btn');
            navLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth <= 768) {
                        closeSidebar();
                    }
                });
            });
        }

        window.addEventListener('resize', function () {
            if (window.innerWidth > 768 && sidebarMenu && sidebarMenu.classList.contains('show')) {
                closeSidebar();
            }
        });
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cache TL data for select dropdown
        const tlOptions = [
            { id: '', name: 'None' },
            <?php foreach($tls as $tl): ?>
            { id: '<?=$tl->id;?>', name: '<?=htmlspecialchars($tl->name);?>' },
            <?php endforeach; ?>
        ];
        
        // User search functionality
        const userSearch = document.getElementById('user-search');
        if (userSearch) {
            userSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('.user-row');
                
                // If search is empty, restore original order
                if (!searchTerm) {
                    rows.forEach(row => row.style.display = '');
                    return;
                }
                
                // Group the matching rows by TL
                const matchingTLs = new Set();
                const matchingEmployeesByTL = {};
                const otherMatching = [];
                
                rows.forEach(row => {
                    const name = row.querySelector('.editable-name').textContent.toLowerCase();
                    const isMatch = name.includes(searchTerm);
                    const isTL = row.dataset.role === '2';
                    const tlId = isTL ? row.dataset.id : row.dataset.tl;
                    
                    // Hide non-matching rows
                    row.style.display = isMatch ? '' : 'none';
                    
                    if (isMatch) {
                        if (isTL) {
                            matchingTLs.add(tlId);
                        } else if (tlId) {
                            if (!matchingEmployeesByTL[tlId]) {
                                matchingEmployeesByTL[tlId] = [];
                            }
                            matchingEmployeesByTL[tlId].push(row);
                        } else {
                            otherMatching.push(row);
                        }
                    }
                });
                
                // For each matching TL, show their employees even if they don't match
                matchingTLs.forEach(tlId => {
                    rows.forEach(row => {
                        if (row.dataset.tl === tlId && row.style.display === 'none') {
                            row.style.display = '';
                        }
                    });
                });
                
                // Reorder the table to show TLs first followed by their team members
                if (searchTerm && (matchingTLs.size > 0 || Object.keys(matchingEmployeesByTL).length > 0)) {
                    const tbody = document.querySelector('tbody');
                    
                    // First, hide all rows
                    rows.forEach(row => {
                        row.style.display = 'none';
                        // Store original index for restoring later
                        if (!row.dataset.originalIndex) {
                            row.dataset.originalIndex = Array.from(tbody.children).indexOf(row);
                        }
                    });
                    
                    // Create ordered array of rows to show
                    const orderedRows = [];
                    
                    // First add matching TLs and their team members
                    rows.forEach(row => {
                        if (row.dataset.role === '2' && matchingTLs.has(row.dataset.id)) {
                            orderedRows.push(row);
                            
                            // Find and add all employees of this TL
                            rows.forEach(empRow => {
                                if (empRow.dataset.tl === row.dataset.id) {
                                    orderedRows.push(empRow);
                                }
                            });
                        }
                    });
                    
                    // Add non-matching TLs with matching employees
                    Object.keys(matchingEmployeesByTL).forEach(tlId => {
                        if (!matchingTLs.has(tlId)) {
                            // Find TL row
                            rows.forEach(row => {
                                if (row.dataset.role === '2' && row.dataset.id === tlId) {
                                    orderedRows.push(row);
                                }
                            });
                            
                            // Add matching employees
                            matchingEmployeesByTL[tlId].forEach(empRow => {
                                if (!orderedRows.includes(empRow)) {
                                    orderedRows.push(empRow);
                                }
                            });
                        }
                    });
                    
                    // Add other matching rows not yet added
                    otherMatching.forEach(row => {
                        if (!orderedRows.includes(row)) {
                            orderedRows.push(row);
                        }
                    });
                    
                    // Show the rows in the new order
                    orderedRows.forEach((row, index) => {
                        row.style.display = '';
                        tbody.appendChild(row);
                    });
                } else if (!searchTerm) {
                    // Restore original order when search is cleared
                    const tbody = document.querySelector('tbody');
                    const rowsArray = Array.from(rows);
                    
                    // Sort by original index
                    rowsArray.sort((a, b) => {
                        return parseInt(a.dataset.originalIndex) - parseInt(b.dataset.originalIndex);
                    });
                    
                    // Reattach in original order
                    rowsArray.forEach(row => {
                        tbody.appendChild(row);
                        row.style.display = '';
                    });
                }
            });
        }
        
        // Edit button click handler
        document.querySelectorAll('.edit-user-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                
                // If already editing, do nothing
                if (row.classList.contains('editing')) return;
                
                // Store original values
                const editableCells = row.querySelectorAll('.editable');
                editableCells.forEach(cell => {
                    cell.dataset.original = cell.textContent.trim();
                });
                
                // Enter edit mode
                row.classList.add('editing');
                
                // Hide edit button, show save/cancel buttons
                this.classList.add('d-none');
                row.querySelector('.edit-controls').classList.remove('d-none');
                
                // Make cells editable
                makeEditable(row);
            });
        });
        
        // Save button click handler
        document.querySelectorAll('.save-user-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                saveUserChanges(row);
            });
        });
        
        // Cancel button click handler
        document.querySelectorAll('.cancel-edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                cancelEdit(row);
            });
        });
        
        // Role dropdown change handler
        function handleRoleChange(select) {
            select.addEventListener('change', function() {
                const row = this.closest('tr');
                const tlCell = row.querySelector('.editable-tl');
                const roleValue = this.value;
                
                // If changed to TL, clear TL assignment
                if (roleValue === '2') {
                    if (tlCell.querySelector('select')) {
                        tlCell.querySelector('select').value = '';
                        tlCell.querySelector('select').disabled = true;
                    }
                } else {
                    // If changed to Employee, enable TL dropdown
                    if (tlCell.querySelector('select')) {
                        tlCell.querySelector('select').disabled = false;
                    }
                }
            });
        }
        
        // Function to make cells editable
        function makeEditable(row) {
            const userId = row.dataset.id;
            const currentRole = row.dataset.role;
            const currentTl = row.dataset.tl;
            
            // Name field
            const nameCell = row.querySelector('.editable-name');
            const nameValue = nameCell.textContent.trim();
            nameCell.innerHTML = `<input type="text" name="name" value="${nameValue}" required>`;
            
            // Email field
            const emailCell = row.querySelector('.editable-email');
            const emailValue = emailCell.textContent.trim();
            emailCell.innerHTML = `<input type="email" name="email" value="${emailValue}" required>`;
            
            // Password field
            const passwordCell = row.querySelector('.editable-password');
            const passwordValue = passwordCell.textContent.trim();
            passwordCell.innerHTML = `<input type="text" name="password" value="${passwordValue}" required>`;

            const designationCell = row.querySelector('.editable-designation');
            const designationValue = designationCell.textContent.trim();
            designationCell.innerHTML = `<input type="text" name="designation" value="${designationValue}" required>`;
            
            // Role field
            const roleCell = row.querySelector('.editable-role');
            const roleValue = currentRole;
            roleCell.innerHTML = `
                <select name="role_id">
                    <option value="2" ${roleValue === '2' ? 'selected' : ''}>TL</option>
                    <option value="3" ${roleValue === '3' ? 'selected' : ''}>TM</option>
                </select>
            `;
            
            // Add role change handler
            const roleSelect = roleCell.querySelector('select');
            handleRoleChange(roleSelect);
            
            // Team Lead field
            const tlCell = row.querySelector('.editable-tl');
            const tlValue = currentTl;
            
            // Only employees can have TLs
            const isTL = roleValue === '2';
            
            let tlSelectHtml = `<select name="tl_id" ${isTL ? 'disabled' : ''}>`;
            tlOptions.forEach(option => {
                tlSelectHtml += `<option value="${option.id}" ${tlValue === option.id ? 'selected' : ''}>${option.name}</option>`;
            });
            tlSelectHtml += `</select>`;
            
            tlCell.innerHTML = tlSelectHtml;
        }
        
        // Function to save changes
        function saveUserChanges(row) {
            const userId = row.dataset.id;
            
            // Collect form data
            const nameInput = row.querySelector('[name="name"]');
            const emailInput = row.querySelector('[name="email"]');
            const passwordInput = row.querySelector('[name="password"]');
            const roleSelect = row.querySelector('[name="role_id"]');
            const tlSelect = row.querySelector('[name="tl_id"]');
            const designationInput = row.querySelector('[name="designation"]');
            // Validate inputs
            if (!nameInput.value.trim()) {
                alert('Name cannot be empty');
                nameInput.focus();
                return;
            }
            
            if (!emailInput.value.trim() || !emailInput.value.includes('@')) {
                alert('Please enter a valid email');
                emailInput.focus();
                return;
            }
            
            // Prepare data for submission
            const userData = {
                id: userId,
                name: nameInput.value.trim(),
                email: emailInput.value.trim(),
                password: passwordInput.value.trim(),
                role_id: roleSelect.value,
                designation: designationInput.value.trim()
            };
            
            // For employees, include TL ID
            if (roleSelect.value === '3' && tlSelect && !tlSelect.disabled) {
                userData.tl_id = tlSelect.value;
            } else if (roleSelect.value === '2') {
                // For TLs, clear TL ID
                userData.tl_id = '';
            }
            
            // AJAX request to update user
            fetch('<?=site_url('admin/update_user');?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: Object.entries(userData)
                    .map(([key, value]) => `${encodeURIComponent(key)}=${encodeURIComponent(value)}`)
                    .join('&')
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update row with new values
                    row.querySelector('.editable-name').textContent = userData.name;
                    row.querySelector('.editable-email').textContent = userData.email;
                    row.querySelector('.editable-password').textContent = userData.password;
                    row.querySelector('.editable-role').textContent = data.role_name || (userData.role_id === '2' ? 'TL' : 'Employee');
                    row.querySelector('.editable-designation').textContent = userData.designation;
                    // Update TL cell
                    const tlCell = row.querySelector('.editable-tl');
                    if (userData.role_id === '2') {
                        tlCell.textContent = '-';
                    } else if (userData.tl_id) {
                        tlCell.textContent = data.tl_name || tlSelect.options[tlSelect.selectedIndex].text;
                    } else {
                        tlCell.textContent = '-';
                    }
                    
                    // Update row's data attributes
                    row.dataset.role = userData.role_id;
                    row.dataset.tl = userData.tl_id || '';
                    
                    // Exit edit mode
                    row.classList.remove('editing');
                    row.querySelector('.edit-controls').classList.add('d-none');
                    row.querySelector('.edit-user-btn').classList.remove('d-none');
                    
                    // Show success indicator
                    const successIndicator = document.createElement('span');
                    successIndicator.className = 'success-indicator';
                    successIndicator.innerHTML = '<i class="fa-solid fa-check"></i> Updated';
                    row.querySelector('.editable-name').appendChild(successIndicator);
                    
                    // Remove indicator after delay
                    setTimeout(() => {
                        successIndicator.remove();
                    }, 3000);
                } else {
                    alert('Failed to update user: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error updating user:', error);
                alert('An error occurred while updating the user');
            });
        }
        
        // Function to cancel editing
        function cancelEdit(row) {
            // Restore original values
            const editableCells = row.querySelectorAll('.editable');
            editableCells.forEach(cell => {
                cell.textContent = cell.dataset.original || '';
                delete cell.dataset.original;
            });
            
            // Exit edit mode
            row.classList.remove('editing');
            row.querySelector('.edit-controls').classList.add('d-none');
            row.querySelector('.edit-user-btn').classList.remove('d-none');
        }

        // View button click handler
        const userDetailsModal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
        const userDetailsContent = document.getElementById('userDetailsContent');
        
        document.querySelectorAll('.view-user-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.dataset.id;
                
                // Show loading spinner
                userDetailsContent.innerHTML = '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
                
                // Show modal
                userDetailsModal.show();
                
                // Collect user data
                const userRow = this.closest('tr');
                const userData = {
                    id: userId,
                    name: userRow.querySelector('.editable-name').textContent.trim(),
                    email: userRow.querySelector('.editable-email').textContent.trim(),
                    role: userRow.querySelector('.editable-role').textContent.trim(),
                    designation: userRow.querySelector('.editable-designation').textContent.trim(),
                    tlName: userRow.querySelector('.editable-tl').textContent.trim() !== '-' ? 
                            userRow.querySelector('.editable-tl').textContent.trim() : null,
                    roleId: userRow.dataset.role,
                    tlId: userRow.dataset.tl || null
                };
                
                // Prepare HTML content
                let modalHtml = '';
                
                // 1. User details section
                modalHtml += `
                <div class="user-details-section">
                    <h5 class="section-title">User Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> ${userData.name}</p>
                            <p><strong>Email:</strong> ${userData.email}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Role:</strong> ${userData.role}</p>
                            <p><strong>Designation:</strong> ${userData.designation}</p>
                        </div>
                    </div>
                </div>`;
                
                // If the user is a TL (role_id = 2), show their team members
                if (userData.roleId === '2') {
                    // Find team members
                    const teamMembers = [];
                    document.querySelectorAll('.user-row').forEach(row => {
                        if (row.dataset.tl === userId) {
                            teamMembers.push({
                                name: row.querySelector('.editable-name').textContent.trim(),
                                email: row.querySelector('.editable-email').textContent.trim(),
                                designation: row.querySelector('.editable-designation').textContent.trim()
                            });
                        }
                    });
                    
                    // Add team members section
                    modalHtml += `
                    <div class="user-details-section">
                        <h5 class="section-title">Team Members (${teamMembers.length})</h5>
                        ${teamMembers.length > 0 ? '<ul class="team-member-list">' : '<p>No team members found.</p>'}`;
                    
                    if (teamMembers.length > 0) {
                        teamMembers.forEach(member => {
                            modalHtml += `
                            <li>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>${member.name}</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted">${member.designation}</span>
                                    </div>
                                </div>
                                <div class="text-muted small">${member.email}</div>
                            </li>`;
                        });
                        modalHtml += '</ul>';
                    }
                    
                    modalHtml += '</div>';
                } else {
                    // For regular employees, show their TL first (if available)
                    if (userData.tlId) {
                        // Find TL details
                        let tlDetails = null;
                        document.querySelectorAll('.user-row').forEach(row => {
                            if (row.dataset.id === userData.tlId) {
                                tlDetails = {
                                    name: row.querySelector('.editable-name').textContent.trim(),
                                    email: row.querySelector('.editable-email').textContent.trim(),
                                    designation: row.querySelector('.editable-designation').textContent.trim()
                                };
                            }
                        });
                        
                        if (tlDetails) {
                            // Add TL section
                            modalHtml += `
                            <div class="user-details-section">
                                <h5 class="section-title">Team Lead</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Name:</strong> ${tlDetails.name}</p>
                                        <p><strong>Email:</strong> ${tlDetails.email}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Designation:</strong> ${tlDetails.designation}</p>
                                    </div>
                                </div>
                            </div>`;
                        }
                        
                        // Find fellow team members
                        const teamMembers = [];
                        document.querySelectorAll('.user-row').forEach(row => {
                            if (row.dataset.tl === userData.tlId && row.dataset.id !== userData.id) {
                                teamMembers.push({
                                    name: row.querySelector('.editable-name').textContent.trim(),
                                    email: row.querySelector('.editable-email').textContent.trim(),
                                    designation: row.querySelector('.editable-designation').textContent.trim()
                                });
                            }
                        });
                        
                        // Add team members section
                        modalHtml += `
                        <div class="user-details-section">
                            <h5 class="section-title">Fellow Team Members (${teamMembers.length})</h5>
                            ${teamMembers.length > 0 ? '<ul class="team-member-list">' : '<p>No other team members found.</p>'}`;
                        
                        if (teamMembers.length > 0) {
                            teamMembers.forEach(member => {
                                modalHtml += `
                                <li>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>${member.name}</strong>
                                        </div>
                                        <div class="col-md-6">
                                            <span class="text-muted">${member.designation}</span>
                                        </div>
                                    </div>
                                    <div class="text-muted small">${member.email}</div>
                                </li>`;
                            });
                            modalHtml += '</ul>';
                        }
                        
                        modalHtml += '</div>';
                    }
                }
                
                // Update modal content
                userDetailsContent.innerHTML = modalHtml;
            });
        });
    });
    </script>
</body>

</html>