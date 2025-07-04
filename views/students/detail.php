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
                                    <div class="avatar-title bg-primary rounded-circle text-white" style="font-size: 4rem;">
                                        <?php echo strtoupper(substr($student['nama_lengkap'] ?? 'U', 0, 1)); ?>
                                    </div>
                                </div>
                                <h5 class="mb-1"><?php echo htmlspecialchars($student['nama_lengkap'] ?? '-'); ?></h5>
                                <p class="text-muted mb-2"><?php echo htmlspecialchars($student['nis'] ?? '-'); ?></p>
                                <span class="badge bg-<?php echo (($student['status'] ?? '') == 'aktif') ? 'success' : 'secondary'; ?> badge-pill">
                                    <?php echo ucfirst($student['status'] ?? '-'); ?>
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
                                <label class="col-sm-5 col-form-label">NIS:</label>
                                <div class="col-sm-7">
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($student['nis'] ?? '-'); ?></p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-5 col-form-label">Jenis Kelamin:</label>
                                <div class="col-sm-7">
                                    <p class="form-control-plaintext"><?php echo (($student['jenis_kelamin'] ?? '') == 'L') ? 'Laki-laki' : ((($student['jenis_kelamin'] ?? '') == 'P') ? 'Perempuan' : '-'); ?></p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-5 col-form-label">Kelas:</label>
                                <div class="col-sm-7">
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($student['nama_kelas'] ?? '-'); ?></p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-5 col-form-label">Tingkat:</label>
                                <div class="col-sm-7">
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($student['tingkat'] ?? '-'); ?></p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-5 col-form-label">Tahun Masuk:</label>
                                <div class="col-sm-7">
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($student['tahun_masuk'] ?? '-'); ?></p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-5 col-form-label">Nama Wali:</label>
                                <div class="col-sm-7">
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($student['nama_wali'] ?? '-'); ?></p>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-5 col-form-label">No HP Wali:</label>
                                <div class="col-sm-7">
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($student['no_hp_wali'] ?? '-'); ?></p>
                                </div>
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
                                        <table id="paymentHistoryTable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>No</th> <th>No. Kuitansi</th>
                                                    <th>Tanggal</th>
                                                    <th>Total Bayar</th>
                                                    <th>Metode</th>
                                                    <th>Kasir</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($payment_history)): ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-3">Belum ada riwayat pembayaran.</td>
                                                </tr>
                                                <?php else: ?>
                                                <?php foreach ($payment_history as $index => $payment): ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><span class="badge bg-primary"><?php echo htmlspecialchars($payment['no_kuitansi'] ?? '-'); ?></span></td>
                                                    <td><?php echo ($payment['tanggal_bayar'] ? date('d/m/Y', strtotime($payment['tanggal_bayar'])) : '-'); ?></td>
                                                    <td>Rp <?php echo number_format($payment['total_bayar'] ?? 0, 0, ',', '.'); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo (($payment['metode_bayar'] ?? '') == 'tunai') ? 'success' : 'info'; ?>">
                                                            <?php echo ucfirst($payment['metode_bayar'] ?? '-'); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($payment['kasir'] ?? '-'); ?></td>
                                                    <td>
                                                        <a href="<?php echo Router::url('student-payments/receipt?payment_id=' . ($payment['id'] ?? '')); ?>" target="_blank" class="btn btn-sm btn-success" title="Cetak Kuitansi">
                                                            <i class="mdi mdi-printer"></i> Kuitansi
                                                        </a>
                                                        <a href="<?php echo Router::url('student-payments/detail?id=' . ($student['id'] ?? '')); ?>" class="btn btn-sm btn-info" title="Lihat Detail">
                                                            <i class="mdi mdi-eye"></i> Detail
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="status-history" role="tabpanel">
                                    <div class="table-responsive">
                                        <table id="statusHistoryTable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Tanggal</th>
                                                    <th>Status</th>
                                                    <th>Kelas</th>
                                                    <th>Keterangan</th>
                                                    <th>Diubah Oleh</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($status_history)): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-3">Belum ada riwayat perubahan status.</td>
                                                </tr>
                                                <?php else: ?>
                                                <?php foreach ($status_history as $index => $row): ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo ($row['tanggal_perubahan'] ? date('d/m/Y H:i', strtotime($row['tanggal_perubahan'])) : '-'); ?></td>
                                                    <td>
                                                        <?php if (!empty($row['status_sebelum']) && !empty($row['status_sesudah'])): ?>
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge bg-secondary me-1">
                                                                    <?php echo ucfirst($row['status_sebelum']); ?>
                                                                </span>
                                                                <i class="mdi mdi-arrow-right mx-1"></i>
                                                                <span class="badge bg-primary">
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
                                                                <i class="mdi mdi-arrow-right mx-1"></i>
                                                                <span class="badge bg-success">
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
        // Initialize DataTable for Payment History
        $('#paymentHistoryTable').DataTable({
            responsive: true,
            order: [[0, 'asc']], // Order by No. ascending
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 }, // No
                { responsivePriority: 2, targets: 2 }, // Tanggal
                { responsivePriority: 3, targets: 3 }, // Total Bayar
                { responsivePriority: 4, targets: 6 }, // Aksi
                { responsivePriority: 5, targets: 1 }, // No. Kuitansi
                { responsivePriority: 6, targets: 4 }, // Metode
                { responsivePriority: 7, targets: 5 }  // Kasir
            ]
        });

        // Initialize DataTable for Status History
        $('#statusHistoryTable').DataTable({
            responsive: true,
            order: [[0, 'asc']], // Order by No. ascending
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 }, // No
                { responsivePriority: 2, targets: 1 }, // Tanggal
                { responsivePriority: 3, targets: 2 }, // Status
                { responsivePriority: 4, targets: 3 }, // Kelas
                { responsivePriority: 5, targets: 4 }, // Keterangan
                { responsivePriority: 6, targets: 5 }  // Diubah Oleh
            ]
        });
    });
";

include 'includes/footer.php';
?>