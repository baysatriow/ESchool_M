<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Kategori Pengeluaran</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item">Pengeluaran</li>
                                <li class="breadcrumb-item active">Kategori Pengeluaran</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Daftar Kategori Pengeluaran</h4>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="mdi mdi-plus"></i> Tambah Kategori
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kategori</th>
                                        <th>Total Pengeluaran</th>
                                        <th>Jumlah Transaksi</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($expense_categories as $index => $row): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                                        <td>Rp <?php echo number_format($row['total_pengeluaran'] ?? 0, 0, ',', '.'); ?></td>
                                        <td>
                                            <span class="badge badge-info"><?php echo $row['jumlah_transaksi']; ?> transaksi</span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['keterangan'] ?? '-'); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning" onclick="editExpenseCategory(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                                <i class="mdi mdi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteExpenseCategory(<?php echo $row['id']; ?>)">
                                                <i class="mdi mdi-delete"></i>
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah Kategori Pengeluaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('expense-categories/create'); ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_kategori" class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php 
$custom_js = "
    $(document).ready(function() {
        $('#datatable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            }
        });
    });
    
    function editExpenseCategory(category) {
        // Implementation for edit modal
    }
    
    function deleteExpenseCategory(id) {
        iziToast.question({
            timeout: 20000,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question',
            zindex: 999,
            title: 'Konfirmasi',
            message: 'Apakah Anda yakin ingin menghapus kategori ini?',
            position: 'center',
            buttons: [
                ['<button><b>Ya, Hapus</b></button>', function (instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '" . Router::url('expense-categories/delete') . "';
                    form.innerHTML = '<input type=\"hidden\" name=\"id\" value=\"' + id + '\">';
                    document.body.appendChild(form);
                    form.submit();
                }, true],
                ['<button>Batal</button>', function (instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                }],
            ]
        });
    }
";

include 'includes/footer.php'; 
?>
