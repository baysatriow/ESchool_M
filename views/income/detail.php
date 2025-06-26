<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Detail Pendapatan</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('income'); ?>">Pendapatan</a></li>
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
                            <h4 class="card-title">Informasi Pendapatan</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>No. Bukti</strong></td>
                                            <td>: <?php echo htmlspecialchars($income['no_bukti'] ?? '-'); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal</strong></td>
                                            <td>: <?php echo date('d/m/Y', strtotime($income['tanggal'])); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Kategori</strong></td>
                                            <td>: <?php echo htmlspecialchars($income['nama_kategori']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nominal</strong></td>
                                            <td>: <span class="text-success fw-bold">Rp <?php echo number_format($income['nominal'], 0, ',', '.'); ?></span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Dibuat Oleh</strong></td>
                                            <td>: <?php echo htmlspecialchars($income['created_by'] ?? '-'); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Dibuat</strong></td>
                                            <td>: <?php echo date('d/m/Y H:i', strtotime($income['created_at'])); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Terakhir Update</strong></td>
                                            <td>: <?php echo date('d/m/Y H:i', strtotime($income['updated_at'])); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6><strong>Keterangan:</strong></h6>
                                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($income['keterangan'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <?php if (!empty($income['bukti_foto'])): ?>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Bukti Foto</h4>
                        </div>
                        <div class="card-body text-center">
                            <img src="<?php echo BASE_URL; ?>/uploads/income/<?php echo htmlspecialchars($income['bukti_foto']); ?>" 
                                 class="img-fluid rounded" 
                                 alt="Bukti Pendapatan"
                                 style="max-height: 300px; cursor: pointer;"
                                 onclick="showImageModal(this.src)">
                            <div class="mt-2">
                                <a href="<?php echo BASE_URL; ?>/uploads/income/<?php echo htmlspecialchars($income['bukti_foto']); ?>" 
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
                                <button type="button" class="btn btn-warning" onclick="editIncome(<?php echo htmlspecialchars(json_encode($income)); ?>)">
                                    <i class="mdi mdi-pencil"></i> Edit Pendapatan
                                </button>
                                <button type="button" class="btn btn-danger" onclick="deleteIncome(<?php echo $income['id']; ?>)">
                                    <i class="mdi mdi-delete"></i> Hapus Pendapatan
                                </button>
                                <a href="<?php echo Router::url('income'); ?>" class="btn btn-secondary">
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
                    <h5 class="modal-title" id="imageModalLabel">Bukti Pendapatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="/placeholder.svg" class="img-fluid" alt="Bukti Pendapatan">
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
    
    function editIncome(income) {
        // Redirect to edit page or show edit modal
        window.location.href = '" . Router::url('income') . "?edit=' + income.id;
    }
    
    function deleteIncome(id) {
        if (confirm('Apakah Anda yakin ingin menghapus pendapatan ini?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '" . Router::url('income/delete') . "';
            form.innerHTML = '<input type=\"hidden\" name=\"id\" value=\"' + id + '\">';
            document.body.appendChild(form);
            form.submit();
        }
    }
";

include 'includes/footer.php'; 
?>
