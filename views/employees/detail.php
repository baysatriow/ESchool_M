<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

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
                                <!-- <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#payroll" role="tab">
                                        <i class="fas fa-money-check-alt"></i> Riwayat Gaji
                                    </a>
                                </li> -->
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="attendance" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered mb-0">
                                            <thead>
                                                <tr>
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
                                                    <td colspan="5" class="text-center text-muted py-3">Belum ada data presensi untuk 30 hari terakhir.</td>
                                                </tr>
                                                <?php else: ?>
                                                <?php foreach ($attendance_history as $attendance): ?>
                                                <tr>
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
$custom_js = ""; // No custom JavaScript needed for Select2 on this page based on current elements

include 'includes/footer.php';
?>