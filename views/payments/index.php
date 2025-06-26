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
                                <li class="breadcrumb-item active">Pembayaran Siswa</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Kelas</label>
                                    <select name="kelas_id" class="form-control">
                                        <option value="">Semua Kelas</option>
                                        <?php foreach ($classes as $class): ?>
                                        <option value="<?php echo $class['id']; ?>" <?php echo (isset($_GET['kelas_id']) && $_GET['kelas_id'] == $class['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($class['nama_kelas']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tahun Ajaran</label>
                                    <select name="tahun_ajaran_id" class="form-control">
                                        <?php foreach ($academic_years as $year): ?>
                                        <option value="<?php echo $year['id']; ?>" <?php echo (isset($_GET['tahun_ajaran_id']) && $_GET['tahun_ajaran_id'] == $year['id']) ? 'selected' : (($year['status'] == 'aktif') ? 'selected' : ''); ?>>
                                            <?php echo htmlspecialchars($year['tahun_ajaran']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">Semua Status</option>
                                        <option value="lunas" <?php echo (isset($_GET['status']) && $_GET['status'] == 'lunas') ? 'selected' : ''; ?>>Lunas</option>
                                        <option value="tunggakan" <?php echo (isset($_GET['status']) && $_GET['status'] == 'tunggakan') ? 'selected' : ''; ?>>Ada Tunggakan</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="mdi mdi-filter"></i> Filter
                                        </button>
                                        <a href="<?php echo Router::url('student-payments'); ?>" class="btn btn-secondary">
                                            <i class="mdi mdi-refresh"></i> Reset
                                        </a>
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
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Data Pembayaran Siswa</h4>
                                <button type="button" class="btn btn-success" onclick="printData()">
                                    <i class="mdi mdi-printer"></i> Print
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Status Pembayaran</th>
                                        <th>Total Bayar</th>
                                        <th>Tunggakan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students_payment_status as $index => $row): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($row['nis']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_kelas'] ?? '-'); ?></td>
                                        <td>
                                            <?php if ($row['total_tunggakan'] > 0): ?>
                                                <span class="badge badge-danger">Ada Tunggakan</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">Lunas</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                                        <td>
                                            <?php if ($row['total_tunggakan'] > 0): ?>
                                                <span class="text-danger">Rp <?php echo number_format($row['total_tunggakan'], 0, ',', '.'); ?></span>
                                            <?php else: ?>
                                                <span class="text-success">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="showPaymentDetail(<?php echo $row['id']; ?>)">
                                                <i class="mdi mdi-eye"></i> Detail
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success" onclick="addPayment(<?php echo $row['id']; ?>)">
                                                <i class="mdi mdi-plus"></i> Bayar
                                            </button>
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

    <!-- Payment Detail Modal -->
    <div class="modal fade" id="paymentDetailModal" tabindex="-1" aria-labelledby="paymentDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentDetailModalLabel">Detail Pembayaran Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="paymentDetailContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Add Payment Modal -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPaymentModalLabel">Tambah Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('student-payments/create'); ?>">
                    <div class="modal-body" id="addPaymentContent">
                        <!-- Content will be loaded via AJAX -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php 
$custom_js = "
    $(document).ready(function() {
        if (typeof $.fn.DataTable !== 'undefined') {
            $('#datatable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                }
            });
        }
    });
    
    function showPaymentDetail(studentId) {
        $.ajax({
            url: '" . Router::url('student-payments/detail') . "',
            type: 'GET',
            data: { student_id: studentId },
            success: function(response) {
                $('#paymentDetailContent').html(response);
                $('#paymentDetailModal').modal('show');
            },
            error: function() {
                alert('Gagal memuat detail pembayaran');
            }
        });
    }
    
    function addPayment(studentId) {
        $.ajax({
            url: '" . Router::url('student-payments/form') . "',
            type: 'GET',
            data: { student_id: studentId },
            success: function(response) {
                $('#addPaymentContent').html(response);
                $('#addPaymentModal').modal('show');
            },
            error: function() {
                alert('Gagal memuat form pembayaran');
            }
        });
    }
    
    function printData() {
        window.print();
    }
";

include 'includes/footer.php'; 
?>
