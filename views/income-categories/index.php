<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Kategori Pendapatan</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item">Master Data</li>
                                <li class="breadcrumb-item active">Kategori Pendapatan</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Daftar Kategori Pendapatan</h4>
                            <div class="d-flex">
                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="mdi mdi-plus"></i> Tambah Kategori
                                </button>
                                </div>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Kategori</th>
                                        <th>Keterangan</th>
                                        <th>Total Transaksi</th>
                                        <th>Total Nominal</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $index => $row): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                                        <td><?php echo htmlspecialchars($row['keterangan'] ?? '-'); ?></td>
                                        <td><?php echo number_format($row['total_transaksi'], 0, ',', '.'); ?></td>
                                        <td class="text-success fw-bold">Rp <?php echo number_format($row['total_nominal'], 0, ',', '.'); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-warning"
                                                        onclick="editCategory(<?php echo htmlspecialchars(json_encode($row)); ?>)"
                                                        title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="deleteCategory(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nama_kategori']); ?>')"
                                                        title="Hapus">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
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

    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah Kategori Pendapatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('income-categories/create'); ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
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

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Kategori Pendapatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('income-categories/edit'); ?>">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nama_kategori" name="nama_kategori" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
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
                    // Pastikan URL i18n ini benar. Sebelumnya Anda pakai 1.11.5, sekarang 1.10.24.
                    // Sebaiknya konsisten. Jika Bootstrap 5, gunakan yang terbaru (1.11.5 atau lebih baru).
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json' 
                },
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },  // No
                    { responsivePriority: 2, targets: 1 },  // Nama Kategori
                    { responsivePriority: 3, targets: -1 }, // Aksi (last column)
                    { responsivePriority: 4, targets: 2 },  // Keterangan
                    { responsivePriority: 5, targets: 3 },  // Total Transaksi
                    { responsivePriority: 6, targets: 4 }   // Total Nominal
                ]
            });
        }
    });
    
    function editCategory(category) {
        $('#edit_id').val(category.id);
        $('#edit_nama_kategori').val(category.nama_kategori);
        $('#edit_keterangan').val(category.keterangan || ''); // Handle null for keterangan
        $('#editModal').modal('show');
    }
    
    function deleteCategory(id, nama_kategori) { // Added nama_kategori for better message
        iziToast.question({
            timeout: 20000,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question',
            zindex: 999,
            title: 'Konfirmasi',
            message: 'Apakah Anda yakin ingin menghapus kategori \\'' + nama_kategori + '\\'?',
            position: 'center',
            buttons: [
                ['<button><b>Ya, Hapus</b></button>', function (instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '" . Router::url('income-categories/delete') . "';
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
    
    // Fungsi printData bisa ditambahkan di sini jika diperlukan
    // function printData() {
    //     window.print();
    // }
";

include 'includes/footer.php';
?>