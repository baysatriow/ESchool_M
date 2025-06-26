<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Detail Pembayaran Siswa</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('student-payments'); ?>">Pembayaran Siswa</a></li>
                                <li class="breadcrumb-item active">Detail</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Informasi Pembayaran</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>No. Kuitansi</strong></td>
                                            <td>: <?php echo htmlspecialchars($payment['no_kuitansi']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Bayar</strong></td>
                                            <td>: <?php echo date('d/m/Y', strtotime($payment['tanggal_bayar'])); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Metode Bayar</strong></td>
                                            <td>: <?php echo ucfirst($payment['metode_bayar']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Bayar</strong></td>
                                            <td>: <span class="text-success fw-bold">Rp <?php echo number_format($payment['total_bayar'], 0, ',', '.'); ?></span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Nama Siswa</strong></td>
                                            <td>: <?php echo htmlspecialchars($student['nama_lengkap']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>NIS</strong></td>
                                            <td>: <?php echo htmlspecialchars($student['nis']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Kelas</strong></td>
                                            <td>: <?php echo htmlspecialchars($student['nama_kelas'] ?? '-'); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dibuat Oleh</strong></td>
                                            <td>: <?php echo htmlspecialchars($payment['created_by'] ?? '-'); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Detail Pembayaran</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Jenis Pembayaran</th>
                                            <th>Periode</th>
                                            <th>Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($payment_details as $index => $detail): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($detail['nama_pembayaran']); ?></td>
                                            <td>
                                                <?php if ($detail['bulan_pembayaran']): ?>
                                                    <?php 
                                                    $months = [
                                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                                    ];
                                                    echo $months[$detail['bulan_pembayaran']] . ' ' . $detail['tahun_pembayaran'];
                                                    ?>
                                                <?php else: ?>
                                                    <?php echo $detail['tahun_pembayaran']; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>Rp <?php echo number_format($detail['nominal_bayar'], 0, ',', '.'); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-success">
                                            <th colspan="3" class="text-end">Total:</th>
                                            <th>Rp <?php echo number_format($payment['total_bayar'], 0, ',', '.'); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <?php if (!empty($payment['bukti_foto'])): ?>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Bukti Pembayaran</h4>
                        </div>
                        <div class="card-body text-center">
                            <img src="<?php echo BASE_URL; ?>/uploads/payments/<?php echo htmlspecialchars($payment['bukti_foto']); ?>" 
                                 class="img-fluid rounded" 
                                 alt="Bukti Pembayaran"
                                 style="max-height: 300px; cursor: pointer;"
                                 onclick="showImageModal(this.src)">
                            <div class="mt-2">
                                <a href="<?php echo BASE_URL; ?>/uploads/payments/<?php echo htmlspecialchars($payment['bukti_foto']); ?>" 
                                   target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="mdi mdi-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Aksi</h4>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="<?php echo Router::url('student-payments/print?id=' . $payment['id']); ?>" 
                                   target="_blank" class="btn btn-success">
                                    <i class="mdi mdi-printer"></i> Cetak Kuitansi
                                </a>
                                <button type="button" class="btn btn-danger" onclick="deletePayment(<?php echo $payment['id']; ?>)">
                                    <i class="mdi mdi-delete"></i> Hapus Pembayaran
                                </button>
                                <a href="<?php echo Router::url('student-payments'); ?>" class="btn btn-secondary">
                                    <i class="mdi mdi-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Bukti Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="/placeholder.svg" class="img-fluid" alt="Bukti Pembayaran">
                </div>
            </div>
        </div>
    </div>

<?php 
$custom_js = "
    function showImageModal(src) {
        $('#modalImage').attr('src', src);
        $('#imageModal').modal('show');
    }
    
    function deletePayment(id) {
        if (confirm('Apakah Anda yakin ingin menghapus pembayaran ini?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '" . Router::url('student-payments/delete') . "';
            form.innerHTML = '<input type=\"hidden\" name=\"id\" value=\"' + id + '\">';
            document.body.appendChild(form);
            form.submit();
        }
    }
";

include 'includes/footer.php'; 
?>
