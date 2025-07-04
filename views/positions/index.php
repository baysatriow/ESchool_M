<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Data Jabatan</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item">Referensi</li>
                                <li class="breadcrumb-item active">Data Jabatan</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Daftar Jabatan</h4>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                <i class="mdi mdi-plus"></i> Tambah Jabatan
                            </button>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Jabatan</th> <th>Jumlah Pegawai</th>
                                        <th>Keterangan</th>
                                        <th width="15%">Aksi</th> </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($positions as $index => $row): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($row['nama_jabatan']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-white">
                                                <?php echo $row['jumlah_pegawai']; ?> orang
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($row['keterangan'] ?? '-'); ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-warning"
                                                        onclick="editPosition(<?php echo htmlspecialchars(json_encode($row)); ?>)"
                                                        title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="deletePosition(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nama_jabatan']); ?>', <?php echo $row['jumlah_pegawai']; ?>)"
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
                    <h5 class="modal-title" id="addModalLabel">Tambah Jabatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('positions/create'); ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_jabatan" class="form-label">
                                Nama Jabatan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="nama_jabatan" name="nama_jabatan" required>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"
                                      placeholder="Deskripsi jabatan (opsional)"></textarea>
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
                    <h5 class="modal-title" id="editModalLabel">Edit Jabatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('positions/edit'); ?>">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label for="edit_nama_jabatan" class="form-label">
                                Nama Jabatan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="edit_nama_jabatan" name="nama_jabatan" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3"
                                      placeholder="Deskripsi jabatan (opsional)"></textarea>
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
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                },
                // Add these options to help with long content in responsive mode
                // You might need to adjust columnDefs based on actual column indices
                columnDefs: [
                    { responsivePriority: 1, targets: 0 }, // No
                    { responsivePriority: 2, targets: 1 }, // Nama Jabatan
                    { responsivePriority: 3, targets: -1 }, // Aksi (last column)
                    // If Keterangan often has very long text, make it less priority
                    { responsivePriority: 10, targets: 3 } // Keterangan
                ]
            });
        }
    });

    function editPosition(position) {
        $('#edit_id').val(position.id);
        $('#edit_nama_jabatan').val(position.nama_jabatan);
        $('#edit_keterangan').val(position.keterangan || ''); // Use '' if keterangan is null
        $('#editModal').modal('show');
    }

    function deletePosition(id, nama, jumlahPegawai) {
        if (jumlahPegawai > 0) {
            iziToast.error({
                title: 'Gagal Hapus!',
                message: 'Tidak dapat menghapus jabatan \\'' + nama + '\\' karena masih memiliki ' + jumlahPegawai + ' pegawai aktif!',
                position: 'topRight'
            });
            return;
        }

        iziToast.question({
            timeout: 20000,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question',
            zindex: 999,
            title: 'Konfirmasi',
            message: 'Apakah Anda yakin ingin menghapus jabatan \\'' + nama + '\\'?',
            position: 'center',
            buttons: [
                ['<button><b>Ya, Hapus</b></button>', function (instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '" . Router::url('positions/delete') . "';
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