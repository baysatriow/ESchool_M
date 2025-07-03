<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Pembayaran Siswa</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item">Pendapatan</li>
                                <li class="breadcrumb-item active">Pembayaran Siswa</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-primary-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm rounded-circle bg-primary">
                                    <i class="mdi mdi-account-group mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-primary mb-1">Total Siswa</p>
                                    <h4 class="mb-0"><?php echo count($students); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-info-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm rounded-circle bg-info">
                                    <i class="mdi mdi-currency-usd mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-info mb-1">Total Tagihan</p>
                                    <h4 class="mb-0">Rp <?php echo number_format(array_sum(array_column($students, 'total_harus_bayar')), 0, ',', '.'); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-success-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm rounded-circle bg-success">
                                    <i class="mdi mdi-check-circle mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-success mb-1">Sudah Dibayar</p>
                                    <h4 class="mb-0">Rp <?php echo number_format(array_sum(array_column($students, 'total_sudah_bayar')), 0, ',', '.'); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-warning-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm rounded-circle bg-warning">
                                    <i class="mdi mdi-clock-outline mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-warning mb-1">Sisa Tagihan</p>
                                    <h4 class="mb-0">Rp <?php echo number_format(array_sum(array_column($students, 'total_sisa_bayar')), 0, ',', '.'); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Filter Data</h4>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="<?php echo Router::url('student-payments'); ?>">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran</label>
                                            <select class="form-control select2" id="tahun_ajaran_id" name="tahun_ajaran_id">
                                                <option value="">Semua Tahun Ajaran</option>
                                                <?php foreach ($academic_years as $year): ?>
                                                <option value="<?php echo htmlspecialchars($year['id']); ?>" <?php echo ($filters['tahun_ajaran_id'] ?? '') == $year['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($year['tahun_ajaran']); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="kelas_id" class="form-label">Kelas</label>
                                            <select class="form-control select2" id="kelas_id" name="kelas_id">
                                                <option value="">Semua Kelas</option>
                                                <?php foreach ($classes as $class): ?>
                                                <option value="<?php echo htmlspecialchars($class['id']); ?>" <?php echo ($filters['kelas_id'] ?? '') == $class['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($class['nama_kelas']); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="status_pembayaran" class="form-label">Status Tagihan</label>
                                            <select class="form-control select2" id="status_pembayaran" name="status_pembayaran">
                                                <option value="">Semua Status</option>
                                                <option value="ada_tagihan" <?php echo ($filters['status_pembayaran'] ?? '') == 'ada_tagihan' ? 'selected' : ''; ?>>Ada Tagihan</option>
                                                <option value="tidak_ada_tagihan" <?php echo ($filters['status_pembayaran'] ?? '') == 'tidak_ada_tagihan' ? 'selected' : ''; ?>>Tidak Ada Tagihan</option>
                                                <option value="lunas" <?php echo ($filters['status_pembayaran'] ?? '') == 'lunas' ? 'selected' : ''; ?>>Lunas</option>
                                                <option value="sebagian" <?php echo ($filters['status_pembayaran'] ?? '') == 'sebagian' ? 'selected' : ''; ?>>Sebagian</option>
                                                <option value="belum_bayar" <?php echo ($filters['status_pembayaran'] ?? '') == 'belum_bayar' ? 'selected' : ''; ?>>Belum Bayar</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-filter"></i> Filter
                                            </button>
                                            <a href="<?php echo Router::url('student-payments'); ?>" class="btn btn-secondary">
                                                <i class="mdi mdi-refresh"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Daftar Siswa</h4>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Siswa</th>
                                        <th>Kelas</th>
                                        <th>Tagihan</th>
                                        <th>Total Harus Bayar</th>
                                        <th>Sudah Dibayar</th>
                                        <th>Sisa Tagihan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $index => $student): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($student['nama_lengkap'] ?? '-'); ?></strong><br>
                                            <small class="text-muted">NIS: <?php echo htmlspecialchars($student['nis'] ?? '-'); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($student['nama_kelas'] ?? '-'); ?></td>
                                        <td>
                                            <small>
                                                <span class="badge bg-success"><?php echo ($student['tagihan_lunas'] ?? 0); ?></span> Lunas<br>
                                                <span class="badge bg-warning"><?php echo ($student['tagihan_sebagian'] ?? 0); ?></span> Sebagian<br>
                                                <span class="badge bg-danger"><?php echo ($student['tagihan_belum_bayar'] ?? 0); ?></span> Belum Bayar
                                            </small>
                                        </td>
                                        <td>Rp <?php echo number_format($student['total_harus_bayar'] ?? 0, 0, ',', '.'); ?></td>
                                        <td>Rp <?php echo number_format($student['total_sudah_bayar'] ?? 0, 0, ',', '.'); ?></td>
                                        <td>Rp <?php echo number_format($student['total_sisa_bayar'] ?? 0, 0, ',', '.'); ?></td>
                                        <td>
                                            <?php if (($student['total_tagihan'] ?? 0) == 0): ?>
                                                <span class="badge bg-secondary">Tidak Ada Tagihan</span>
                                            <?php elseif (($student['total_sisa_bayar'] ?? 0) == 0): ?>
                                                <span class="badge bg-success">Lunas</span>
                                            <?php elseif (($student['total_sudah_bayar'] ?? 0) > 0): ?>
                                                <span class="badge bg-warning">Sebagian</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Belum Bayar</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo Router::url('student-payments/detail?id=' . ($student['id'] ?? '')); ?>" class="btn btn-sm btn-info" title="Lihat Detail">
                                                    <i class="mdi mdi-eye"></i> Detail
                                                </a>
                                                <?php if (($student['total_sisa_bayar'] ?? 0) > 0): ?>
                                                <a href="<?php echo Router::url('student-payments/pay?siswa_id=' . ($student['id'] ?? '')); ?>" class="btn btn-sm btn-success" title="Lakukan Pembayaran">
                                                    <i class="mdi mdi-currency-usd"></i> Bayar
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
        // Initialize Select2 for all filter dropdowns
        $('#tahun_ajaran_id').select2({
            placeholder: 'Pilih Tahun Ajaran',
            allowClear: true,
            width: '100%'
        });
        $('#kelas_id').select2({
            placeholder: 'Pilih Kelas',
            allowClear: true,
            width: '100%'
        });
        $('#status_pembayaran').select2({
            placeholder: 'Pilih Status Tagihan',
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: Infinity // Hide search box for this specific select
        });

        $('#datatable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            },
            order: [[1, 'asc']], // Adjust column for initial sorting if needed
            columnDefs: [
                { responsivePriority: 1, targets: 0 },   // No
                { responsivePriority: 2, targets: 1 },   // Siswa (Nama + NIS)
                { responsivePriority: 3, targets: 8 },   // Aksi
                { responsivePriority: 4, targets: 4 },   // Total Harus Bayar
                { responsivePriority: 5, targets: 7 },   // Status
            ]
        });
    });
";

include 'includes/footer.php';
?>