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
                                <li class="breadcrumb-item">Referensi</li>
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
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="mdi mdi-plus"></i> Tambah Jenis Pembayaran
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama Pembayaran</th>
                                        <th>Tipe</th>
                                        <th>Usage</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payment_types as $index => $row): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($row['kode_pembayaran']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_pembayaran']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $row['tipe'] == 'bulanan' ? 'info' : 'secondary'; ?>">
                                                <?php echo ucfirst($row['tipe']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                Data: <?php echo ($row['jumlah_data_pembayaran'] ?? 0); ?><br>
                                                Assigned: <?php echo ($row['jumlah_assignment'] ?? 0); ?>
                                            </small>
                                        </td>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Form Tambah Jenis Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('payment-types/create'); ?>">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kode_pembayaran" class="form-label">Kode Pembayaran <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="kode_pembayaran" name="kode_pembayaran" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipe" class="form-label">Tipe <span class="text-danger">*</span></label>
                                    <select class="form-control" id="tipe" name="tipe" required>
                                        <option value="">Pilih Tipe</option>
                                        <option value="bulanan">Bulanan</option>
                                        <option value="bebas">Bebas</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="nama_pembayaran" class="form-label">Nama Pembayaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_pembayaran" name="nama_pembayaran" required>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Form Edit Jenis Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('payment-types/edit'); ?>">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_kode_pembayaran" class="form-label">Kode Pembayaran <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_kode_pembayaran" name="kode_pembayaran" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_tipe" class="form-label">Tipe <span class="text-danger">*</span></label>
                                    <select class="form-control" id="edit_tipe" name="tipe" required>
                                        <option value="">Pilih Tipe</option>
                                        <option value="bulanan">Bulanan</option>
                                        <option value="bebas">Bebas</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nama_pembayaran" class="form-label">Nama Pembayaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nama_pembayaran" name="nama_pembayaran" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json' // Consistent with previous pages
                },
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },   // No
                    { responsivePriority: 2, targets: 2 },   // Nama Pembayaran
                    { responsivePriority: 3, targets: 5 },   // Aksi (last column)
                    { responsivePriority: 4, targets: 1 },   // Kode
                    { responsivePriority: 5, targets: 3 },   // Tipe
                    { responsivePriority: 6, targets: 4 }    // Usage
                ]
            });
        }
    });

    function editPaymentType(paymentType) {
        $('#edit_id').val(paymentType.id);
        $('#edit_kode_pembayaran').val(paymentType.kode_pembayaran || '');
        $('#edit_nama_pembayaran').val(paymentType.nama_pembayaran || '');
        $('#edit_tipe').val(paymentType.tipe || '');
        $('#edit_keterangan').val(paymentType.keterangan || '');
        $('#editModal').modal('show');
    }

    function deletePaymentType(id, nama_pembayaran) { // Added nama_pembayaran parameter for iziToast
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
";

include 'includes/footer.php';
?>