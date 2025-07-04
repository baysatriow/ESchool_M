<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<style>
    .table-responsive table th,
    .table-responsive table td {
        white-space: normal; /* Allow all table cells to wrap text */
    }
    /* Specific width adjustments for better responsiveness on small columns */
    .table-responsive table th:nth-child(1),
    .table-responsive table td:nth-child(1) {
        width: 50px; /* Adjust width for 'No' column */
    }
    .table-responsive table th:last-child,
    .table-responsive table td:last-child {
        width: 150px; /* Adjust width for 'Aksi' column */
        white-space: nowrap; /* Prevent buttons from wrapping */
    }
    /* Ensure action buttons are compact */
    .table-responsive .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }
</style>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Detail Pegawai</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('employees'); ?>">Data Pegawai</a></li>
                                <li class="breadcrumb-item active">Detail</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center">
                                <div class="avatar-lg mx-auto mb-4">
                                    <div class="avatar-title bg-success rounded-circle text-white" style="font-size: 4rem;">
                                        <?php echo strtoupper(substr($employee['nama_lengkap'] ?? 'U', 0, 1)); ?>
                                    </div>
                                </div>
                                <h5 class="mb-1"><?php echo htmlspecialchars($employee['nama_lengkap'] ?? '-'); ?></h5>
                                <p class="text-muted mb-2"><?php echo htmlspecialchars($employee['nip'] ?? '-'); ?></p>
                                <span class="badge bg-<?php echo (($employee['status'] ?? '') == 'aktif') ? 'success' : 'secondary'; ?> badge-pill">
                                    <?php echo ucfirst($employee['status'] ?? '-'); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Informasi Pribadi</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 row">
                                <label class="col-sm-5 col-form-label">NIY:</label>
                                <div class="col-sm-7">
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($employee['nip'] ?? '-'); ?></p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-5 col-form-label">Jenis Kelamin:</label>
                                <div class="col-sm-7">
                                    <p class="form-control-plaintext">
                                        <?php echo (($employee['jenis_kelamin'] ?? '') == 'L') ? 'Laki-laki' : ((($employee['jenis_kelamin'] ?? '') == 'P') ? 'Perempuan' : '-'); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-5 col-form-label">Jabatan:</label>
                                <div class="col-sm-7">
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($employee['nama_jabatan'] ?? '-'); ?></p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-5 col-form-label">Tanggal Masuk:</label>
                                <div class="col-sm-7">
                                    <p class="form-control-plaintext"><?php echo ($employee['tanggal_masuk'] ? date('d/m/Y', strtotime($employee['tanggal_masuk'])) : '-'); ?></p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-5 col-form-label">Gaji Pokok:</label>
                                <div class="col-sm-7">
                                    <p class="form-control-plaintext">Rp <?php echo number_format($employee['gaji_pokok'] ?? 0, 0, ',', '.'); ?></p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-5 col-form-label">No. Telepon:</label>
                                <div class="col-sm-7">
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($employee['no_telepon'] ?? '-'); ?></p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-5 col-form-label">Email:</label>
                                <div class="col-sm-7">
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($employee['email'] ?? '-'); ?></p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-5 col-form-label">Alamat:</label>
                                <div class="col-sm-7">
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($employee['alamat'] ?? '-'); ?></p>
                                </div>
                            </div>
                            <?php if (!empty($employee['username'])): ?>
                            <div class="mb-3 row">
                                <label class="col-sm-5 col-form-label">Username:</label>
                                <div class="col-sm-7">
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($employee['username']); ?></p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-5 col-form-label">Role:</label>
                                <div class="col-sm-7">
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-info">
                                            <?php echo ucfirst(str_replace('_', ' ', $employee['role'] ?? '-')); ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#attendance" role="tab">
                                        <i class="fas fa-clock"></i> Presensi (30 hari terakhir)
                                    </a>
                                </li>
                                </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="attendance" role="tabpanel">
                                    <div class="table-responsive">
                                        <table id="attendanceHistoryTable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Tanggal</th>
                                                    <th>Status</th>
                                                    <th>Jam Masuk</th>
                                                    <th>Jam Pulang</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($attendance_history)): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-3">Belum ada data presensi untuk 30 hari terakhir.</td>
                                                </tr>
                                                <?php else: ?>
                                                <?php foreach ($attendance_history as $index => $attendance): ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($attendance['tanggal'])); ?></td>
                                                    <td>
                                                        <?php
                                                        $status_colors = [
                                                            'hadir' => 'success',
                                                            'terlambat' => 'warning',
                                                            'izin' => 'info',
                                                            'sakit' => 'secondary',
                                                            'alpa' => 'danger'
                                                        ];
                                                        $color = $status_colors[($attendance['status_kehadiran'] ?? 'alpa')] ?? 'secondary';
                                                        ?>
                                                        <span class="badge bg-<?php echo $color; ?>">
                                                            <?php echo ucfirst(str_replace('_', ' ', $attendance['status_kehadiran'] ?? '-')); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($attendance['jam_masuk'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($attendance['jam_pulang'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($attendance['keterangan'] ?? '-'); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$custom_js = "
    $(document).ready(function() {
        // Initialize DataTable for Attendance History
        $('#attendanceHistoryTable').DataTable({
            responsive: true,
            order: [[0, 'desc']], // Order by date descending initially
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 }, // No
                { responsivePriority: 2, targets: 1 }, // Tanggal
                { responsivePriority: 3, targets: 2 }, // Status
                { responsivePriority: 4, targets: 3 }, // Jam Masuk
                { responsivePriority: 5, targets: 4 }, // Jam Pulang
                { responsivePriority: 6, targets: 5 }  // Keterangan
            ]
        });
    });
";

include 'includes/footer.php';
?>