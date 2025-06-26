<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Detail Siswa</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('students'); ?>">Data Siswa</a></li>
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
                                    <div class="avatar-title bg-primary rounded-circle text-white" style="font-size: 2rem;">
                                        <?php echo strtoupper(substr($student['nama_lengkap'], 0, 1)); ?>
                                    </div>
                                </div>
                                <h5 class="mb-1"><?php echo htmlspecialchars($student['nama_lengkap']); ?></h5>
                                <p class="text-muted mb-2"><?php echo htmlspecialchars($student['nis']); ?></p>
                                <span class="badge badge-<?php echo $student['status'] == 'aktif' ? 'success' : 'secondary'; ?> badge-pill">
                                    <?php echo ucfirst($student['status']); ?>
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
                                            <th class="ps-0" scope="row">NIS :</th>
                                            <td class="text-muted"><?php echo htmlspecialchars($student['nis']); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0" scope="row">Jenis Kelamin :</th>
                                            <td class="text-muted"><?php echo $student['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0" scope="row">Kelas :</th>
                                            <td class="text-muted"><?php echo htmlspecialchars($student['nama_kelas'] ?? '-'); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0" scope="row">Tingkat :</th>
                                            <td class="text-muted"><?php echo $student['tingkat'] ?? '-'; ?></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0" scope="row">Tahun Masuk :</th>
                                            <td class="text-muted"><?php echo $student['tahun_masuk']; ?></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0" scope="row">Nama Wali :</th>
                                            <td class="text-muted"><?php echo htmlspecialchars($student['nama_wali'] ?? '-'); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="ps-0" scope="row">No HP Wali :</th>
                                            <td class="text-muted"><?php echo htmlspecialchars($student['no_hp_wali'] ?? '-'); ?></td>
                                        </tr>
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
                                    <a class="nav-link active" data-bs-toggle="tab" href="#payments" role="tab">
                                        <i class="fas fa-money-bill-wave"></i> Riwayat Pembayaran
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#status-history" role="tab">
                                        <i class="fas fa-history"></i> Riwayat Status
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="payments" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No. Kuitansi</th>
                                                    <th>Tanggal</th>
                                                    <th>Total Bayar</th>
                                                    <th>Metode</th>
                                                    <th>Kasir</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($payment_history)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">Belum ada riwayat pembayaran</td>
                                                </tr>
                                                <?php else: ?>
                                                <?php foreach ($payment_history as $payment): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($payment['no_kuitansi']); ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($payment['tanggal_bayar'])); ?></td>
                                                    <td>Rp <?php echo number_format($payment['total_bayar'], 0, ',', '.'); ?></td>
                                                    <td>
                                                        <span class="badge badge-<?php echo $payment['metode_bayar'] == 'tunai' ? 'success' : 'info'; ?>">
                                                            <?php echo ucfirst($payment['metode_bayar']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($payment['kasir']); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="status-history" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Status Sebelum</th>
                                                    <th>Status Sesudah</th>
                                                    <th>Diubah Oleh</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($status_history)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">Belum ada riwayat perubahan status</td>
                                                </tr>
                                                <?php else: ?>
                                                <?php foreach ($status_history as $history): ?>
                                                <tr>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($history['tanggal_perubahan'])); ?></td>
                                                    <td>
                                                        <span class="badge badge-secondary">
                                                            <?php echo ucfirst($history['status_sebelum']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-<?php echo $history['status_sesudah'] == 'aktif' ? 'success' : 'warning'; ?>">
                                                            <?php echo ucfirst($history['status_sesudah']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($history['updated_by']); ?></td>
                                                    <td><?php echo htmlspecialchars($history['keterangan'] ?? '-'); ?></td>
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

<?php include 'includes/footer.php'; ?>
