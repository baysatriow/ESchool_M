<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Riwayat Status Siswa</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('student-status'); ?>">Ubah Status Siswa</a></li>
                                <li class="breadcrumb-item active">Riwayat Status</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($student_data): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>NIS:</strong> <?php echo htmlspecialchars($student_data['nis']); ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Nama:</strong> <?php echo htmlspecialchars($student_data['nama_lengkap']); ?>
                                </div>
                                <div class="col-md-3">
                                    <strong>Status:</strong> 
                                    <span class="badge badge-<?php echo $student_data['status'] == 'aktif' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($student_data['status']); ?>
                                    </span>
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
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title">
                                    <?php echo $student_data ? 'Riwayat Status - ' . $student_data['nama_lengkap'] : 'Semua Riwayat Status'; ?>
                                </h4>
                                <a href="<?php echo Router::url('student-status'); ?>" class="btn btn-secondary">
                                    <i class="mdi mdi-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <?php if (!$student_data): ?>
                                        <th>Nama Siswa</th>
                                        <?php endif; ?>
                                        <th>Status Sebelum</th>
                                        <th>Status Sesudah</th>
                                        <th>Keterangan</th>
                                        <th>Diubah Oleh</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($history as $index => $row): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['tanggal_perubahan'])); ?></td>
                                        <?php if (!$student_data): ?>
                                        <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                                        <?php endif; ?>
                                        <td>
                                            <span class="badge badge-secondary">
                                                <?php echo ucfirst($row['status_sebelum']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo $row['status_sesudah'] == 'aktif' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($row['status_sesudah']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['keterangan'] ?: '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['updated_by']); ?></td>
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

<?php 
$custom_js = "
    $(document).ready(function() {
        $('#datatable').DataTable({
            responsive: true,
            order: [[1, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            }
        });
    });
";

include 'includes/footer.php'; 
?>
