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
                        <h4 class="mb-sm-0">Riwayat Perubahan Status & Kelas</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('student-status'); ?>">Manajemen Status & Kelas</a></li>
                                <li class="breadcrumb-item active">Riwayat Perubahan</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($student_data): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Informasi Siswa</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3 row">
                                        <label class="col-sm-5 col-form-label">NIS:</label>
                                        <div class="col-sm-7">
                                            <p class="form-control-plaintext">
                                                <?php echo htmlspecialchars($student_data['nis'] ?? '-'); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Nama:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext">
                                                <?php echo htmlspecialchars($student_data['nama_lengkap'] ?? '-'); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3 row">
                                        <label class="col-sm-6 col-form-label">Status Saat Ini:</label>
                                        <div class="col-sm-6">
                                            <p class="form-control-plaintext">
                                                <span class="badge bg-<?php echo (($student_data['status'] ?? '') == 'aktif') ? 'success' : 'secondary'; ?>">
                                                    <?php echo ucfirst($student_data['status'] ?? '-'); ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">
                                <?php echo ($student_data ? 'Riwayat Perubahan - ' . htmlspecialchars($student_data['nama_lengkap'] ?? '-') : 'Semua Riwayat Perubahan'); ?>
                            </h4>
                            <a href="<?php echo Router::url('student-status'); ?>" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($history)): ?>
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <?php if (!$student_data): ?>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <?php endif; ?>
                                        <th>Status</th>
                                        <th>Kelas</th>
                                        <th>Keterangan</th>
                                        <th>Diubah Oleh</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($history as $index => $row): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo ($row['tanggal_perubahan'] ? date('d/m/Y H:i', strtotime($row['tanggal_perubahan'])) : '-'); ?></td>
                                        <?php if (!$student_data): ?>
                                        <td><?php echo htmlspecialchars($row['nis'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_siswa'] ?? '-'); ?></td>
                                        <?php endif; ?>
                                        <td>
                                            <?php if (!empty($row['status_sebelum']) && !empty($row['status_sesudah'])): ?>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-secondary me-1">
                                                        <?php echo ucfirst($row['status_sebelum']); ?>
                                                    </span>
                                                    <i class="mdi mdi-arrow-right mx-1 font-size-14 text-muted"></i> <span class="badge bg-primary">
                                                        <?php echo ucfirst($row['status_sesudah']); ?>
                                                    </span>
                                                </div>
                                            <?php elseif (!empty($row['status_sebelum'])): ?>
                                                <span class="badge bg-secondary">
                                                    <?php echo ucfirst($row['status_sebelum']); ?>
                                                </span>
                                            <?php elseif (!empty($row['status_sesudah'])): ?>
                                                <span class="badge bg-primary">
                                                    <?php echo ucfirst($row['status_sesudah']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['kelas_sebelum']) && !empty($row['kelas_sesudah'])): ?>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-info me-1">
                                                        <?php echo htmlspecialchars($row['kelas_sebelum']); ?>
                                                    </span>
                                                    <i class="mdi mdi-arrow-right mx-1 font-size-14 text-muted"></i> <span class="badge bg-success">
                                                        <?php echo htmlspecialchars($row['kelas_sesudah']); ?>
                                                    </span>
                                                </div>
                                            <?php elseif (!empty($row['kelas_sebelum'])): ?>
                                                <span class="badge bg-info">
                                                    <?php echo htmlspecialchars($row['kelas_sebelum']); ?>
                                                </span>
                                            <?php elseif (!empty($row['kelas_sesudah'])): ?>
                                                <span class="badge bg-success">
                                                    <?php echo htmlspecialchars($row['kelas_sesudah']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['keterangan'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['updated_by'] ?? '-'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php else: ?>
                            <div class="alert alert-info py-3 text-center">
                                <i class="mdi mdi-information mdi-24px"></i> Belum ada riwayat perubahan.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$custom_js = "
    $(document).ready(function() {
        // Initialize DataTable
        $('#datatable').DataTable({
            responsive: true,
            order: [[1, 'desc']], // Default order by date descending
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            },
            columnDefs: [
                // Assign higher responsive priorities to columns that should be visible first
                { responsivePriority: 1, targets: 0 },   // No
                { responsivePriority: 2, targets: 1 },   // Tanggal
                ";
                // Adjust column definitions based on whether $student_data is present (dynamic columns)
                if (!$student_data) { // If it's the 'All History' view (NIS and Nama Siswa columns present)
                    $custom_js .= "{ responsivePriority: 3, targets: 4 },   // Status (make it high priority)
                                       { responsivePriority: 4, targets: 5 },   // Kelas (make it high priority)
                                       { responsivePriority: 5, targets: 2 },   // NIS
                                       { responsivePriority: 6, targets: 3 },   // Nama Siswa
                                       { responsivePriority: 7, targets: 6 },   // Keterangan
                                       { responsivePriority: 8, targets: 7 }    // Diubah Oleh
                                        ";
                } else { // If it's specific student history (NIS and Nama Siswa columns are absent)
                    $custom_js .= "{ responsivePriority: 3, targets: 2 },   // Status
                                       { responsivePriority: 4, targets: 3 },   // Kelas
                                       { responsivePriority: 5, targets: 4 },   // Keterangan
                                       { responsivePriority: 6, targets: 5 }    // Diubah Oleh
                                        ";
                }
$custom_js .= "
            ]
        });
    });
";

include 'includes/footer.php';
?>