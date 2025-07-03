<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Data Pegawai</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item active">Data Pegawai</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Daftar Pegawai</h4>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="mdi mdi-plus"></i> Tambah Pegawai
                                </button>
                                <!-- <button type="button" class="btn btn-success" onclick="printData()">
                                    <i class="mdi mdi-printer"></i> Print
                                </button> -->
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>NIY</th>
                                        <th>Nama Lengkap</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Jabatan</th>
                                        <th>No. Telepon</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($employees as $index => $row): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($row['nip'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_lengkap'] ?? '-'); ?></td>
                                        <td>
                                            <?php
                                            $gender_text = '';
                                            $gender_badge_class = '';
                                            if (($row['jenis_kelamin'] ?? '') == 'L') {
                                                $gender_text = 'Laki-laki';
                                                $gender_badge_class = 'primary';
                                            } elseif (($row['jenis_kelamin'] ?? '') == 'P') {
                                                $gender_text = 'Perempuan';
                                                $gender_badge_class = 'warning'; // Using warning for "pink" substitute
                                            } else {
                                                $gender_text = 'Tidak Diketahui';
                                                $gender_badge_class = 'secondary';
                                            }
                                            ?>
                                            <span class="badge bg-<?php echo $gender_badge_class; ?>">
                                                <?php echo $gender_text; ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['nama_jabatan'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['no_telepon'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['email'] ?? '-'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo (($row['status'] ?? '') == 'aktif') ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($row['status'] ?? '-'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo Router::url('employees/detail?id=' . ($row['id'] ?? '')); ?>" class="btn btn-sm btn-info" title="Detail">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="editEmployee(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>)" title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteEmployee(<?php echo ($row['id'] ?? 'null'); ?>, '<?php echo htmlspecialchars($row['nama_lengkap'] ?? '', ENT_QUOTES, 'UTF-8'); ?>')" title="Hapus">
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Form Tambah Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('employees/create'); ?>">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="nip" class="form-label">NIP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nip" name="nip" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="jabatan_id" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                    <select class="form-control select2-modal" id="jabatan_id" name="jabatan_id" required>
                                        <option value="">Pilih Jabatan</option>
                                        <?php foreach ($positions as $position): ?>
                                        <option value="<?php echo htmlspecialchars($position['id']); ?>"><?php echo htmlspecialchars($position['nama_jabatan']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tanggal_masuk" class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="gaji_pokok" class="form-label">Gaji Pokok</label>
                                    <input type="number" class="form-control" id="gaji_pokok" name="gaji_pokok" min="0" placeholder="0">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="no_telepon" class="form-label">No. Telepon</label>
                                    <input type="text" class="form-control" id="no_telepon" name="no_telepon" placeholder="08xxxxxxxxxx">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">Link ke User</label>
                                    <select class="form-control select2-modal" id="user_id" name="user_id">
                                        <option value="">Pilih User (Opsional)</option>
                                        <?php foreach ($users as $user): ?>
                                        <option value="<?php echo htmlspecialchars($user['id']); ?>"><?php echo htmlspecialchars($user['username'] . ' - ' . $user['nama_lengkap']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Form Edit Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('employees/edit'); ?>">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_nip" class="form-label">NIP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_nip" name="nip" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_nama_lengkap" name="nama_lengkap" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-control" id="edit_jenis_kelamin" name="jenis_kelamin" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_jabatan_id" class="form-label">Jabatan <span class="text-danger">*</span></label>
                                    <select class="form-control select2-modal" id="edit_jabatan_id" name="jabatan_id" required>
                                        <option value="">Pilih Jabatan</option>
                                        <?php foreach ($positions as $position): ?>
                                        <option value="<?php echo htmlspecialchars($position['id']); ?>"><?php echo htmlspecialchars($position['nama_jabatan']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_tanggal_masuk" class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="edit_tanggal_masuk" name="tanggal_masuk" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="edit_gaji_pokok" class="form-label">Gaji Pokok</label>
                                    <input type="number" class="form-control" id="edit_gaji_pokok" name="gaji_pokok" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="edit_alamat" name="alamat" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit_no_telepon" class="form-label">No. Telepon</label>
                                    <input type="text" class="form-control" id="edit_no_telepon" name="no_telepon">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="edit_email" name="email">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="edit_status" name="status" required>
                                        <option value="aktif">Aktif</option>
                                        <option value="tidak_aktif">Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit_user_id" class="form-label">Link ke User</label>
                                    <select class="form-control select2-modal" id="edit_user_id" name="user_id">
                                        <option value="">Pilih User (Opsional)</option>
                                        <?php foreach ($users as $user): ?>
                                        <option value="<?php echo htmlspecialchars($user['id']); ?>"><?php echo htmlspecialchars($user['username'] . ' - ' . $user['nama_lengkap']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
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
                    { responsivePriority: 1, targets: 0 },    // No
                    { responsivePriority: 2, targets: 2 },    // Nama Lengkap
                    { responsivePriority: 3, targets: 1 },    // NIP
                    { responsivePriority: 4, targets: 4 },    // Jabatan
                    { responsivePriority: 5, targets: -1 },   // Aksi (last column)
                    { responsivePriority: 6, targets: 7 },    // Status
                    { responsivePriority: 7, targets: 3 },    // Jenis Kelamin
                    { responsivePriority: 8, targets: 5 },    // No. Telepon
                    { responsivePriority: 9, targets: 6 }     // Email
                ]
            });
        }

        // Initialize Select2 for dropdowns in Add Modal
        $('#jabatan_id').select2({
            dropdownParent: $('#addModal'),
            placeholder: 'Pilih Jabatan',
            allowClear: true,
            width: '100%'
        });
        $('#user_id').select2({
            dropdownParent: $('#addModal'),
            placeholder: 'Pilih User (Opsional)',
            allowClear: true,
            width: '100%'
        });

        // Initialize Select2 for dropdowns in Edit Modal
        $('#edit_jabatan_id').select2({
            dropdownParent: $('#editModal'),
            placeholder: 'Pilih Jabatan',
            allowClear: true,
            width: '100%'
        });
        $('#edit_user_id').select2({
            dropdownParent: $('#editModal'),
            placeholder: 'Pilih User (Opsional)',
            allowClear: true,
            width: '100%'
        });
    });
    
    function editEmployee(employee) {
        $('#edit_id').val(employee.id);
        $('#edit_nip').val(employee.nip || '');
        $('#edit_nama_lengkap').val(employee.nama_lengkap || '');
        $('#edit_alamat').val(employee.alamat || ''); 
        $('#edit_no_telepon').val(employee.no_telepon || '');
        $('#edit_email').val(employee.email || ''); 
        $('#edit_tanggal_masuk').val(employee.tanggal_masuk || '');
        $('#edit_gaji_pokok').val(employee.gaji_pokok || 0);
        
        // Set values for regular selects
        $('#edit_jenis_kelamin').val(employee.jenis_kelamin || '');
        $('#edit_status').val(employee.status || '');
        
        // Set Select2 values and trigger change
        $('#edit_jabatan_id').val(employee.jabatan_id || '').trigger('change');
        $('#edit_user_id').val(employee.user_id || '').trigger('change');
        
        $('#editModal').modal('show');
    }
    
    function deleteEmployee(id, nama_lengkap) { 
        iziToast.question({
            timeout: 20000,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question',
            zindex: 999,
            title: 'Konfirmasi',
            message: 'Apakah Anda yakin ingin menghapus data pegawai \'' + nama_lengkap + '\'?',
            position: 'center',
            buttons: [
                ['<button><b>Ya, Hapus</b></button>', function (instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '" . Router::url('employees/delete') . "';
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