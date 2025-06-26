<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Jenis Pembayaran</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item">Pendapatan</li>
                                <li class="breadcrumb-item active">Jenis Pembayaran</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Daftar Jenis Pembayaran</h4>
                            <div class="d-flex">
                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="mdi mdi-plus"></i> Tambah Jenis Pembayaran
                                </button>
                                </div>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kode</th>
                                        <th>Nama Pembayaran</th>
                                        <th>Tipe</th>
                                        <th>Nominal Default</th>
                                        <th>Total Pembayaran</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payment_types as $index => $row): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><span class="badge badge-primary"><?php echo htmlspecialchars($row['kode_pembayaran']); ?></span></td>
                                        <td><?php echo htmlspecialchars($row['nama_pembayaran']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $row['tipe'] == 'bulanan' ? 'success' : 'info'; ?>">
                                                <?php echo ucfirst($row['tipe']); ?>
                                            </span>
                                        </td>
                                        <td>Rp <?php echo number_format($row['nominal_default'] ?? 0, 0, ',', '.'); ?></td>
                                        <td>Rp <?php echo number_format($row['total_pembayaran'] ?? 0, 0, ',', '.'); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-warning"
                                                        onclick="editPaymentType(<?php echo htmlspecialchars(json_encode($row)); ?>)"
                                                        title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="deletePaymentType(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nama_pembayaran']); ?>')"
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
                    <h5 class="modal-title" id="addModalLabel">Tambah Jenis Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('payment-types/create'); ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kode_pembayaran" class="form-label">Kode Pembayaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kode_pembayaran" name="kode_pembayaran" placeholder="SPP, DAFTAR, dll" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama_pembayaran" class="form-label">Nama Pembayaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_pembayaran" name="nama_pembayaran" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipe" class="form-label">Tipe <span class="text-danger">*</span></label>
                            <select class="form-control" id="tipe" name="tipe" required>
                                <option value="">Pilih Tipe</option>
                                <option value="bulanan">Bulanan</option>
                                <option value="bebas">Bebas</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nominal_default" class="form-label">Nominal Default</label>
                            <input type="number" class="form-control" id="nominal_default" name="nominal_default" min="0" step="1000">
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
                    <h5 class="modal-title" id="editModalLabel">Edit Jenis Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('payment-types/edit'); ?>">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_kode_pembayaran" class="form-label">Kode Pembayaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_kode_pembayaran" name="kode_pembayaran" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nama_pembayaran" class="form-label">Nama Pembayaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nama_pembayaran" name="nama_pembayaran" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_tipe" class="form-label">Tipe <span class="text-danger">*</span></label>
                            <select class="form-control" id="edit_tipe" name="tipe" required>
                                <option value="">Pilih Tipe</option>
                                <option value="bulanan">Bulanan</option>
                                <option value="bebas">Bebas</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nominal_default" class="form-label">Nominal Default</label>
                            <input type="number" class="form-control" id="edit_nominal_default" name="nominal_default" min="0" step="1000">
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
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                },
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },  // No
                    { responsivePriority: 2, targets: 2 },  // Nama Pembayaran
                    { responsivePriority: 3, targets: 1 },  // Kode Pembayaran
                    { responsivePriority: 4, targets: -1 }, // Aksi (last column)
                    { responsivePriority: 5, targets: 3 },  // Tipe
                    { responsivePriority: 6, targets: 4 },  // Nominal Default
                    { responsivePriority: 7, targets: 5 }   // Total Pembayaran
                ]
            });
        }
    });
    
    function editPaymentType(paymentType) {
        $('#edit_id').val(paymentType.id);
        $('#edit_kode_pembayaran').val(paymentType.kode_pembayaran);
        $('#edit_nama_pembayaran').val(paymentType.nama_pembayaran);
        $('#edit_tipe').val(paymentType.tipe);
        $('#edit_nominal_default').val(paymentType.nominal_default);
        $('#edit_keterangan').val(paymentType.keterangan || ''); // Handle null for keterangan
        $('#editModal').modal('show');
    }
    
    function deletePaymentType(id, nama_pembayaran) { // Added nama_pembayaran for better message
        iziToast.question({
            timeout: 20000,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question',
            zindex: 999,
            title: 'Konfirmasi',
            message: 'Apakah Anda yakin ingin menghapus jenis pembayaran \\'' + nama_pembayaran + '\\'?',
            position: 'center',
            buttons: [
                ['<button><b>Ya, Hapus</b></button>', function (instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '" . Router::url('payment-types/delete') . "';
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
    
    // Fungsi printData sudah ada di file lain atau bisa ditambahkan di sini jika diperlukan
    // function printData() {
    //     window.print();
    // }
";

include 'includes/footer.php';
?>