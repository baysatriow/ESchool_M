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
                                    <div class="avatar-title bg-success rounded-circle text-white" style="font-size: 2rem;">
                                        <?php echo strtoupper(substr($employee['nama_lengkap'], 0, 1)); ?>
                                    </div>
                                </div>
                                <h5 class="mb-1"><?php echo htmlspecialchars($employee['nama_lengkap']); ?></h5>
                                <p class="text-muted mb-2"><?php echo htmlspecialchars($employee['nip']); ?></p>
                                <span class="badge badge-<?php echo $employee['status'] == 'aktif' ? 'success' : 'secondary'; ?> badge-pill">
                                    <?php echo ucfirst($employee['status']); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Informasi Pribadi</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="ps-0" scope="row">NIP :</th>
                                            <td class="text-muted"><?php echo htmlspecialchars($employee['nip']); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0" scope="row">Jenis Kelamin :</th>
                                            <td class="text-muted"><?php echo $employee['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0" scope="row">Jabatan :</th>
                                            <td class="text-muted"><?php echo htmlspecialchars($employee['nama_jabatan'] ?? '-'); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0" scope="row">Tanggal Masuk :</th>
                                            <td class="text-muted"><?php echo date('d/m/Y', strtotime($employee['tanggal_masuk'])); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0" scope="row">Gaji Pokok :</th>
                                            <td class="text-muted">Rp <?php echo number_format($employee['gaji_pokok'], 0, ',', '.'); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0" scope="row">No. Telepon :</th>
                                            <td class="text-muted"><?php echo htmlspecialchars($employee['no_telepon'] ?? '-'); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0" scope="row">Email :</th>
                                            <td class="text-muted"><?php echo htmlspecialchars($employee['email'] ?? '-'); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0" scope="row">Alamat :</th>
                                            <td class="text-muted"><?php echo htmlspecialchars($employee['alamat'] ?? '-'); ?></td>
                                        </tr>
                                        <?php if ($employee['username']): ?>
                                        <tr>
                                            <th class="ps-0" scope="row">Username :</th>
                                            <td class="text-muted"><?php echo htmlspecialchars($employee['username']); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0" scope="row">Role :</th>
                                            <td class="text-muted">
                                                <span class="badge badge-info">
                                                    <?php echo ucfirst(str_replace('_', ' ', $employee['role'])); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
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
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#payroll" role="tab">
                                        <i class="fas fa-money-check-alt"></i> Riwayat Gaji
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="attendance" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
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
                                                    <td colspan="5" class="text-center text-muted">Belum ada data presensi</td>
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
                                                        $color = $status_colors[$attendance['status_kehadiran']] ?? 'secondary';
                                                        ?>
                                                        <span class="badge badge-<?php echo $color; ?>">
                                                            <?php echo ucfirst($attendance['status_kehadiran']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo $attendance['jam_masuk'] ?? '-'; ?></td>
                                                    <td><?php echo $attendance['jam_pulang'] ?? '-'; ?></td>
                                                    <td><?php echo htmlspecialchars($attendance['keterangan'] ?? '-'); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="payroll" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Periode</th>
                                                    <th>Gaji Pokok</th>
                                                    <th>Tunjangan</th>
                                                    <th>Potongan</th>
                                                    <th>Gaji Bersih</th>
                                                    <th>Tanggal Bayar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($payroll_history)): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">Belum ada riwayat penggajian</td>
                                                </tr>
                                                <?php else: ?>
                                                <?php foreach ($payroll_history as $payroll): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($payroll['periode_gaji']); ?></td>
                                                    <td>Rp <?php echo number_format($payroll['gaji_pokok'], 0, ',', '.'); ?></td>
                                                    <td>Rp <?php echo number_format($payroll['total_tunjangan'], 0, ',', '.'); ?></td>
                                                    <td>Rp <?php echo number_format($payroll['total_potongan'], 0, ',', '.'); ?></td>
                                                    <td><strong>Rp <?php echo number_format($payroll['gaji_bersih'], 0, ',', '.'); ?></strong></td>
                                                    <td><?php echo date('d/m/Y', strtotime($payroll['tanggal_pembayaran'])); ?></td>
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

<?php include 'includes/footer.php'; ?>
