<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<style>
    /* Ensure datepicker z-index is high enough for modals */
    .datepicker.datepicker-dropdown {
        z-index: 9999 !important;
    }
    /* Adjust Select2 dropdown z-index for modals */
    .select2-container--open {
        z-index: 9999 !important;
    }
    /* Custom styling for compact badges in recent changes feed */
    .activity-feed .badge {
        font-size: 0.8em; /* Slightly larger for readability */
        padding: 0.35em 0.7em; /* More padding */
        vertical-align: middle;
        margin-right: 0.3rem; /* Space between badges */
        white-space: nowrap; /* Prevent badge text from wrapping */
    }
    .activity-feed .mdi {
        font-size: 1.1rem; /* Adjust icon size in feed */
    }
    .activity-feed .feed-item .flex-shrink-0 {
        padding-top: 0.25rem; /* Align icon with text */
    }
    .activity-feed .feed-item {
        margin-bottom: 1rem; /* Space between feed items */
        padding-bottom: 1rem; /* Padding before border */
        border-bottom: 1px solid #e9ecef; /* Separator line */
    }
    .activity-feed .feed-item:last-child {
        border-bottom: none; /* No border for the last item */
        padding-bottom: 0;
        margin-bottom: 0;
    }
    /* Ensure DataTables wraps text in relevant columns to avoid horizontal scroll */
    #datatable th, #datatable td {
        white-space: normal; /* Allow all table cells to wrap text */
    }
    /*
     * Removed specific fixed widths for th/td to let DataTables Responsive
     * handle dynamic sizing. Only apply minimal width for very small,
     * consistent content columns if absolutely necessary, but generally,
     * let DataTables manage it.
     */
    #datatable th:first-child, /* Checkbox */
    #datatable td:first-child {
        width: 30px; /* Small fixed width for checkbox */
    }
    #datatable th:nth-child(2), /* No */
    #datatable td:nth-child(2) {
        width: 50px; /* Small fixed width for No */
    }
    /* DataTables search input might cause overflow, give it more space */
    .dataTables_filter input {
        min-width: 150px; /* Ensure search input has minimum width */
    }
</style>
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Manajemen Status & Kelas Siswa</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item active">Manajemen Status & Kelas</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($statistics)): ?>
            <div class="row">
                <?php
                // Define colors and icons for consistency based on status names
                $status_card_colors = [
                    'aktif' => ['bg' => 'primary', 'icon' => 'mdi-account-check'],
                    'lulus' => ['bg' => 'success', 'icon' => 'mdi-school'],
                    'pindah' => ['bg' => 'info', 'icon' => 'mdi-run'],
                    'dikeluarkan' => ['bg' => 'danger', 'icon' => 'mdi-account-remove'],
                    'alumni' => ['bg' => 'secondary', 'icon' => 'mdi-account-group'],
                ];
                ?>
                <?php foreach ($statistics as $stat):
                    $status_key = strtolower($stat['status'] ?? 'aktif');
                    $card_style = $status_card_colors[$status_key] ?? ['bg' => 'secondary', 'icon' => 'mdi-account'];
                ?>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="card mini-stats-wid bg-<?php echo $card_style['bg']; ?>-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm rounded-circle bg-<?php echo $card_style['bg']; ?>">
                                    <span class="avatar-title">
                                        <i class="mdi <?php echo $card_style['icon']; ?> font-size-24"></i>
                                    </span>
                                </div>
                                <div class="ms-3">
                                    <p class="text-<?php echo $card_style['bg']; ?> mb-1"><?php echo ucfirst($stat['status'] ?? '-'); ?></p>
                                    <h4 class="mb-0"><?php echo ($stat['jumlah'] ?? 0); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Filter Siswa</h4>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="<?php echo Router::url('student-status'); ?>">
                                <div class="row align-items-end">
                                    <div class="col-md-7">
                                        <div class="mb-3">
                                            <label for="kelas_id_filter" class="form-label">Filter Berdasarkan Kelas</label>
                                            <select class="form-control select2" id="kelas_id_filter" name="kelas_id">
                                                <option value="">Semua Kelas</option>
                                                <?php if (!empty($classes)): ?>
                                                    <?php foreach ($classes as $class): ?>
                                                    <option value="<?php echo htmlspecialchars($class['id']); ?>"
                                                        <?php echo (($selected_class ?? '') == $class['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($class['nama_kelas']); ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="mb-3 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-filter"></i> Filter
                                            </button>
                                            <a href="<?php echo Router::url('student-status'); ?>" class="btn btn-secondary">
                                                <i class="mdi mdi-refresh"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">
                                    Daftar Siswa
                                    <?php if (!empty($selected_class)): ?>
                                        <?php
                                        $selected_class_name = '';
                                        if (!empty($classes)) {
                                            foreach ($classes as $class) {
                                                if ($class['id'] == $selected_class) {
                                                    $selected_class_name = $class['nama_kelas'];
                                                    break;
                                                }
                                            }
                                        }
                                        ?>
                                        - <span class="text-info"><?php echo htmlspecialchars($selected_class_name); ?></span>
                                    <?php endif; ?>
                                </h4>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-warning" id="bulkUpdateBtn" disabled>
                                        <i class="mdi mdi-account-edit"></i> Ubah Status & Kelas Terpilih
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($students)): ?>
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th width="30px">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th width="50px">No</th>
                                        <th>NIS</th>
                                        <th>Nama Lengkap</th>
                                        <th>Kelas</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $index => $row): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input student-checkbox"
                                                value="<?php echo htmlspecialchars($row['id'] ?? ''); ?>"
                                                data-name="<?php echo htmlspecialchars($row['nama_lengkap'] ?? ''); ?>"
                                                data-nis="<?php echo htmlspecialchars($row['nis'] ?? ''); ?>"
                                                data-class="<?php echo htmlspecialchars($row['nama_kelas'] ?? '-'); ?>"
                                                data-status="<?php echo htmlspecialchars($row['status'] ?? ''); ?>"
                                                data-class-id="<?php echo htmlspecialchars($row['kelas_id'] ?? ''); ?>">
                                        </td>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($row['nis'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_lengkap'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_kelas'] ?? '-'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php
                                                $status_badge_colors = [
                                                    'aktif' => 'success',
                                                    'lulus' => 'primary',
                                                    'pindah' => 'info',
                                                    'dikeluarkan' => 'danger',
                                                    'alumni' => 'secondary'
                                                ];
                                                echo $status_badge_colors[($row['status'] ?? 'alumni')] ?? 'secondary';
                                            ?>">
                                                <?php echo ucfirst($row['status'] ?? '-'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning"
                                                    onclick="updateStatusClass([<?php echo htmlspecialchars(json_encode($row['id'] ?? 'null')); ?>], '<?php echo htmlspecialchars($row['nama_lengkap'] ?? '', ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($row['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($row['kelas_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($row['nama_kelas'] ?? '', ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($row['nis'] ?? '', ENT_QUOTES, 'UTF-8'); ?>')">
                                                <i class="mdi mdi-account-edit"></i> Ubah
                                            </button>
                                            <a href="<?php echo Router::url('status-history?student_id=' . ($row['id'] ?? '')); ?>"
                                               class="btn btn-sm btn-secondary">
                                                <i class="mdi mdi-history"></i> Riwayat
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php else: ?>
                            <div class="alert alert-info py-3 text-center">
                                <i class="mdi mdi-information mdi-24px"></i> Tidak ada data siswa yang ditemukan.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Perubahan Terbaru</h4>
                            <a href="<?php echo Router::url('status-history'); ?>" class="btn btn-sm btn-link">
                                <i class="mdi mdi-history me-1"></i>Lihat Semua
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recent_changes)): ?>
                                <div class="activity-feed" style="max-height: 500px; overflow-y: auto;">
                                    <?php foreach ($recent_changes as $change): ?>
                                    <div class="feed-item d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="mdi mdi-account-edit text-primary font-size-18"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($change['nama_siswa'] ?? '-'); ?></h6>
                                            <p class="text-muted mb-1 font-size-12">
                                                <?php if (!empty($change['status_sebelum']) && !empty($change['status_sesudah'])): ?>
                                                    Status: <span class="badge bg-secondary"><?php echo ucfirst($change['status_sebelum']); ?></span> → <span class="badge bg-primary"><?php echo ucfirst($change['status_sesudah']); ?></span>
                                                <?php endif; ?>
                                                <?php if (!empty($change['kelas_sebelum']) && !empty($change['kelas_sesudah'])): ?>
                                                    <br>Kelas: <span class="badge bg-info"><?php echo htmlspecialchars($change['kelas_sebelum']); ?></span> → <span class="badge bg-success"><?php echo htmlspecialchars($change['kelas_sesudah']); ?></span>
                                                <?php endif; ?>
                                            </p>
                                            <p class="mb-0 font-size-11 text-muted">
                                                <?php echo date('d/m/Y H:i', strtotime($change['tanggal_perubahan'] ?? 'now')); ?> oleh <?php echo htmlspecialchars($change['updated_by'] ?? '-'); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center py-3">Belum ada perubahan terbaru.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Ubah Status & Kelas Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('student-status/update'); ?>" id="updateForm">
                    <div class="modal-body">
                        <div id="selectedStudentsInfo"></div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status_baru" class="form-label">Status Baru <span class="text-danger">*</span></label>
                                    <select class="form-control select2-modal" id="status_baru" name="status_baru" required>
                                        <option value="">Pilih Status</option>
                                        <?php if (!empty($status_options)): ?>
                                            <?php foreach ($status_options as $key => $value): ?>
                                            <option value="<?php echo htmlspecialchars($key); ?>">
                                                <?php echo htmlspecialchars($value); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kelas_id_new" class="form-label">Kelas Baru</label>
                                    <select class="form-control select2-modal" id="kelas_id_new" name="kelas_id">
                                        <option value="">Pilih Kelas</option>
                                        <?php if (!empty($classes)): ?>
                                            <?php foreach ($classes as $class): ?>
                                            <option value="<?php echo htmlspecialchars($class['id']); ?>">
                                                <?php echo htmlspecialchars($class['nama_kelas']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                                        placeholder="Alasan perubahan status dan kelas..."></textarea>
                        </div>
                        
                        <div id="validationMessage" class="alert alert-warning" style="display: none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php
$custom_js = "
    let selectedStudents = []; // Global array to hold selected student data
    
    $(document).ready(function() {
        // Initialize DataTable
        $('#datatable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            },
            columnDefs: [
                { orderable: false, targets: [0, 6] }, // Disable sorting for checkbox and Aksi columns
                // Define priorities to control column hiding/showing on smaller screens
                { responsivePriority: 1, targets: 0 },   // Checkbox
                { responsivePriority: 2, targets: 3 },   // Nama Lengkap
                { responsivePriority: 3, targets: 5 },   // Status
                { responsivePriority: 4, targets: 6 },   // Aksi
                { responsivePriority: 5, targets: 2 },   // NIS
                { responsivePriority: 6, targets: 4 },   // Kelas
                { responsivePriority: 7, targets: 1 }    // No
            ]
        });
        
        // Initialize Select2 for filter dropdown
        $('#kelas_id_filter').select2({
            placeholder: 'Semua Kelas',
            allowClear: true,
            width: '100%'
        });

        // Select All functionality
        $('#selectAll').change(function() {
            // Use the DataTable API to get all rows, even hidden ones from pagination
            var table = $('#datatable').DataTable();
            table.rows().nodes().to$().find('.student-checkbox').prop('checked', this.checked);
            updateSelectedStudents();
        });
        
        // Individual checkbox change - use event delegation for dynamically added rows (e.g., from search/pagination)
        $(document).on('change', '.student-checkbox', function() {
            updateSelectedStudents();
            
            // Update select all checkbox based on all checkboxes in the table
            const totalCheckboxes = $('#datatable').DataTable().rows().nodes().to$().find('.student-checkbox').length;
            const checkedCheckboxes = $('#datatable').DataTable().rows().nodes().to$().find('.student-checkbox:checked').length;
            $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
        });
        
        // Bulk update button click handler
        $('#bulkUpdateBtn').click(function() {
            if (selectedStudents.length > 0) {
                // Call the helper function to show the modal
                // Note: when clicking bulk update, selectedStudents is already populated
                updateStatusClass(selectedStudents.map(s => s.id)); 
            } else {
                iziToast.warning({
                    title: 'Peringatan',
                    message: 'Pilih setidaknya satu siswa untuk mengubah status/kelas.',
                    position: 'topRight'
                });
            }
        });
        
        // Initialize Select2 for dropdowns inside the modal (important: after modal is shown)
        $('#updateModal').on('shown.bs.modal', function () {
            $('#status_baru').select2({
                dropdownParent: $('#updateModal'),
                placeholder: 'Pilih Status',
                allowClear: true,
                width: '100%'
            });
            $('#kelas_id_new').select2({
                dropdownParent: $('#updateModal'),
                placeholder: 'Pilih Kelas',
                allowClear: true,
                width: '100%'
            });
            // Initial validation check when modal opens
            validateStatusClass(); 
        });

        // Status/Class change validation in modal
        $('#status_baru, #kelas_id_new').change(function() {
            validateStatusClass();
        });
        
        // Form submission validation (client-side)
        $('#updateForm').submit(function(e) {
            const status = $('#status_baru').val();
            
            if (!status) {
                iziToast.error({
                    title: 'Error',
                    message: 'Status baru harus dipilih!',
                    position: 'topRight'
                });
                e.preventDefault();
                return false;
            }
            
            // Re-run validation just before submit to catch last-minute issues
            validateStatusClass();
            if ($('#validationMessage').is(':visible')) {
                iziToast.error({
                    title: 'Error Validasi',
                    message: $('#validationMessage').text(),
                    position: 'topRight'
                });
                e.preventDefault();
                return false;
            }

            return true;
        });

        // Initial update of selected students and button state on page load
        updateSelectedStudents();
    });
    
    function updateSelectedStudents() {
        selectedStudents = [];
        // Iterate over *all* student checkboxes in the table, including those on other pages/hidden by filters
        $('#datatable').DataTable().rows().nodes().to$().find('.student-checkbox:checked').each(function() {
            selectedStudents.push({
                id: $(this).val(),
                name: $(this).data('name'),
                nis: $(this).data('nis'),
                class: $(this).data('class'), // Class name (e.g., '4A')
                status: $(this).data('status'),
                class_id: $(this).data('class-id') // Class ID (numeric)
            });
        });
        
        // Enable/disable bulk update button
        $('#bulkUpdateBtn').prop('disabled', selectedStudents.length === 0);
        
        // Update button text
        if (selectedStudents.length > 0) {
            $('#bulkUpdateBtn').html('<i class=\"mdi mdi-account-edit\"></i> Ubah ' + selectedStudents.length + ' Siswa Terpilih');
        } else {
            $('#bulkUpdateBtn').html('<i class=\"mdi mdi-account-edit\"></i> Ubah Status & Kelas Terpilih');
        }
    }
    
    // Function to open the update modal and pre-fill details for single student or show bulk update info
    // It's called from both individual 'Ubah' buttons and the 'bulkUpdateBtn'
    function updateStatusClass(studentIdsArray, studentName = '', studentStatus = '', studentClassId = '', studentClassName = '', studentNis = '') {
        // If called from a single 'Ubah' button in the table
        if (studentIdsArray.length === 1 && studentName) {
            selectedStudents = [{
                id: studentIdsArray[0],
                name: studentName,
                nis: studentNis,
                class: studentClassName,
                status: studentStatus,
                class_id: studentClassId
            }];
        } 
        // If called from 'bulkUpdateBtn', selectedStudents is already populated globally
        
        // Populate modal with selected student info
        $('#selectedStudentsInfo').empty();
        let studentsInfoHtml = '<div class=\"alert alert-primary\"><strong>Siswa yang akan diubah (' + selectedStudents.length + '):</strong><ul class=\"mb-0 mt-2\">';
        
        // Display info for up to 3 students, then a summary
        if (selectedStudents.length <= 3) {
            selectedStudents.forEach(function(student) {
                studentsInfoHtml += '<li>' + (student.nis ? student.nis + ' - ' : '') + student.name + ' (Status Awal: ' + student.status + (student.class ? ', Kelas Awal: ' + student.class : '') + ')</li>';
            });
        } else {
            for (let i = 0; i < 3; i++) {
                let student = selectedStudents[i];
                studentsInfoHtml += '<li>' + (student.nis ? student.nis + ' - ' : '') + student.name + '</li>';
            }
            studentsInfoHtml += '<li><em>... dan ' + (selectedStudents.length - 3) + ' siswa lainnya</em></li>';
        }
        studentsInfoHtml += '</ul></div>';
        $('#selectedStudentsInfo').append(studentsInfoHtml);

        // Add hidden inputs for student IDs for form submission
        // Remove existing ones first to prevent duplicates
        $('input[name=\"siswa_ids[]\"]').remove(); 
        selectedStudents.forEach(function(student) {
            $('#selectedStudentsInfo').append('<input type=\"hidden\" name=\"siswa_ids[]\" value=\"' + student.id + '\">');
        });
        
        // Reset modal form fields
        $('#status_baru').val('').trigger('change'); // Clear and trigger Select2 update
        $('#kelas_id_new').val('').trigger('change'); // Clear and trigger Select2 update
        $('#keterangan').val('');
        $('#validationMessage').hide();

        // If a single student is selected from the table 'Ubah' button, pre-select their current status/class in the modal
        if (studentIdsArray.length === 1 && studentName) {
            $('#status_baru').val(studentStatus).trigger('change');
            $('#kelas_id_new').val(studentClassId).trigger('change');
        }
        
        // Show the modal
        $('#updateModal').modal('show');
    }
    
    function validateStatusClass() {
        const status = $('#status_baru').val();
        const classId = $('#kelas_id_new').val();
        
        const validationMessageDiv = $('#validationMessage');
        validationMessageDiv.hide().text(''); // Clear previous messages
        
        if (!status) {
            // If no status is selected, let the 'required' attribute handle it on submit.
            // No need to show a warning here if they just opened the modal.
            return;
        }
        
        // Client-side validation rules
        const noClassStatuses = ['lulus', 'pindah', 'dikeluarkan', 'alumni']; // Added 'alumni' here
        const requireClassStatuses = ['aktif', 'naik_kelas']; 
        
        // Special case: if status is 'naik_kelas', it must have a new class
        if (status === 'naik_kelas' && !classId) {
            validationMessageDiv.text('Untuk status \"Naik Kelas\", kelas baru harus dipilih.').show();
            return;
        }

        // If status requires no class, but a class is selected
        if (noClassStatuses.includes(status) && classId) {
            validationMessageDiv.text('Status \"' + status + '\" tidak memerlukan pilihan kelas. Mohon kosongkan pilihan kelas.').show();
            return;
        }
        
        // If status requires a class, but no class is selected (excluding 'naik_kelas' which is handled above)
        if (requireClassStatuses.includes(status) && !classId) {
             validationMessageDiv.text('Status \"' + status + '\" memerlukan pilihan kelas. Mohon pilih kelas yang sesuai.').show();
             return;
        }
    }
";

include 'includes/footer.php';
?>