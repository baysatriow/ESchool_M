<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Data Kelas</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item">Referensi</li>
                                <li class="breadcrumb-item active">Data Kelas</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Daftar Kelas</h4>
                            <div class="d-flex">
                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="mdi mdi-plus"></i> Tambah Kelas
                                </button>
                                </div>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kelas</th>
                                        <th>Tingkat</th>
                                        <th>Kapasitas</th>
                                        <th>Jumlah Siswa</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($classes as $index => $row): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_kelas']); ?></td>
                                        <td>
                                        <?php if (($row['nama_kelas']) == "ALUMNI" || ($row['nama_kelas']) == "MUTASI"){?>
                                            -
                                        <?php } else {?>
                                            <?php echo $row['tingkat']; ?>
                                        <?php }?>
                                        </td>
                                        <td>
                                        <?php if (($row['nama_kelas']) == "ALUMNI" || ($row['nama_kelas']) == "MUTASI"){?>
                                            -
                                        <?php } else {?>
                                            <?php echo $row['kapasitas']; ?>
                                        <?php }?>
                                        </td>
                                        <td><?php echo $row['jumlah_siswa']; ?></td>
                                        <td>
                                            <?php if (($row['nama_kelas']) == "ALUMNI" || ($row['nama_kelas']) == "MUTASI"){?>
                                                -
                                            <?php } else {?>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="editClass(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteClass(<?php echo $row['id']; ?>)">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            <?php }?>
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
                    <h5 class="modal-title" id="addModalLabel">Tambah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('classes/create'); ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_kelas" class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control" id="nama_kelas" name="nama_kelas" required>
                        </div>
                        <div class="mb-3">
                            <label for="tingkat" class="form-label">Tingkat</label>
                            <select class="form-control" id="tingkat" name="tingkat" required>
                                <option value="">Pilih Tingkat</option>
                                <option value="1">Kelas 1</option>
                                <option value="2">Kelas 2</option>
                                <option value="3">Kelas 3</option>
                                <option value="4">Kelas 4</option>
                                <option value="5">Kelas 5</option>
                                <option value="6">Kelas 6</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kapasitas" class="form-label">Kapasitas</label>
                            <input type="number" class="form-control" id="kapasitas" name="kapasitas" value="30" min="1" required>
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
                    <h5 class="modal-title" id="editModalLabel">Edit Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('classes/edit'); ?>">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label for="edit_nama_kelas" class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control" id="edit_nama_kelas" name="nama_kelas" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_tingkat" class="form-label">Tingkat</label>
                            <select class="form-control" id="edit_tingkat" name="tingkat" required>
                                <option value="">Pilih Tingkat</option>
                                <option value="1">Kelas 1</option>
                                <option value="2">Kelas 2</option>
                                <option value="3">Kelas 3</option>
                                <option value="4">Kelas 4</option>
                                <option value="5">Kelas 5</option>
                                <option value="6">Kelas 6</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_kapasitas" class="form-label">Kapasitas</label>
                            <input type="number" class="form-control" id="edit_kapasitas" name="kapasitas" min="1" required>
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

<?php
$custom_js = "
    $(document).ready(function() {
        $('#datatable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            }
        });
        
        // Initialize Select2 for add modal's 'tingkat' dropdown
        $('#tingkat').select2({
            dropdownParent: $('#addModal'),
            placeholder: 'Pilih Tingkat',
            allowClear: true,
            width: '100%'
        });

        // Initialize Select2 for edit modal's 'tingkat' dropdown
        $('#edit_tingkat').select2({
            dropdownParent: $('#editModal'),
            placeholder: 'Pilih Tingkat',
            allowClear: true,
            width: '100%'
        });
    });
    
    function editClass(classData) {
        $('#edit_id').val(classData.id);
        $('#edit_nama_kelas').val(classData.nama_kelas);
        // Set value for Select2 dropdown and trigger change to update display
        $('#edit_tingkat').val(classData.tingkat).trigger('change');
        $('#edit_kapasitas').val(classData.kapasitas);
        $('#editModal').modal('show');
    }
    
    function deleteClass(id) {
        iziToast.question({
            timeout: 20000,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question',
            zindex: 999,
            title: 'Konfirmasi',
            message: 'Apakah Anda yakin ingin menghapus data kelas ini?',
            position: 'center',
            buttons: [
                ['<button><b>Ya, Hapus</b></button>', function (instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '" . Router::url('classes/delete') . "';
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
    
    function printData() {
        window.print();
    }
";

include 'includes/footer.php';
?>