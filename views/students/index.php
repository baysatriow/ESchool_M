<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<style>
    /* Ensure datepicker z-index is high enough for modals */
    .datepicker.datepicker-dropdown {
        z-index: 9999 !important;
    }
    /* Adjust Select2 dropdown z-index for modals */
    .select2-container--open {
        z-index: 9999 !important;
    }
</style>
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Data Siswa</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item active">Data Siswa</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Daftar Siswa</h4>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="mdi mdi-plus"></i> Tambah Siswa
                                </button>
                                </div>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>NIS</th>
                                        <th>Nama Lengkap</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Kelas</th> <th>Tahun Masuk</th>
                                        <th>Nama Wali</th>
                                        <th>No HP Wali</th>
                                        <th>Status</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $index => $row): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($row['nis'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_lengkap'] ?? '-'); ?></td>
                                        <td>
                                            <?php
                                            $gender_text = '';
                                            $gender_badge_class = '';
                                            if (($row['jenis_kelamin'] ?? '') == 'L') {
                                                $gender_text = 'Laki-laki';
                                                $gender_badge_class = 'info'; // Consistent with primary color for male
                                            } elseif (($row['jenis_kelamin'] ?? '') == 'P') {
                                                $gender_text = 'Perempuan';
                                                $gender_badge_class = 'danger'; // Consistent with danger color for female
                                            } else {
                                                $gender_text = 'Tidak Diketahui';
                                                $gender_badge_class = 'secondary';
                                            }
                                            ?>
                                            <span class="badge bg-<?php echo $gender_badge_class; ?>">
                                                <?php echo $gender_text; ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['nama_kelas'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['tahun_masuk'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_wali'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['no_hp_wali'] ?? '-'); ?></td>
                                        <td>
                                            <?php
                                            $status_text = ucfirst($row['status'] ?? '-');
                                            $status_badge_class = 'secondary'; // Default for unknown/other
                                            switch ($row['status']) {
                                                case 'aktif':
                                                    $status_badge_class = 'success';
                                                    break;
                                                case 'lulus':
                                                    $status_badge_class = 'info'; // Changed from secondary to info for 'Lulus'
                                                    break;
                                                case 'pindah':
                                                    $status_badge_class = 'warning'; // Added warning for 'Pindah'
                                                    break;
                                                case 'dikeluarkan':
                                                    $status_badge_class = 'danger'; // Added danger for 'Dikeluarkan'
                                                    break;
                                                case 'alumni':
                                                    $status_badge_class = 'primary'; // Added primary for 'Alumni'
                                                    break;
                                                case 'naik_kelas':
                                                    $status_badge_class = 'dark'; // Added dark for 'Naik Kelas'
                                                    break;
                                            }
                                            ?>
                                            <span class="badge bg-<?php echo $status_badge_class; ?>">
                                                <?php echo $status_text; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo Router::url('students/detail?id=' . ($row['id'] ?? '')); ?>" class="btn btn-sm btn-info" title="Detail">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="editStudent(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>)" title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteStudent(<?php echo ($row['id'] ?? 'null'); ?>, '<?php echo htmlspecialchars($row['nama_lengkap'] ?? '', ENT_QUOTES, 'UTF-8'); ?>')" title="Hapus">
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
                    <h5 class="modal-title" id="addModalLabel">Form Tambah Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('students/create'); ?>">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nis" class="form-label">NIS <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nis" name="nis" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="kelas_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                                    <select class="form-control select2-modal" id="kelas_id" name="kelas_id" required>
                                        <option value="">Pilih Kelas</option>
                                        <?php foreach ($classes as $class): ?>
                                        <option value="<?php echo htmlspecialchars($class['id']); ?>"><?php echo htmlspecialchars($class['nama_kelas']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tahun_masuk" class="form-label">Tahun Masuk <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control yearpicker" id="tahun_masuk" name="tahun_masuk" required readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_wali" class="form-label">Nama Wali</label>
                                    <input type="text" class="form-control" id="nama_wali" name="nama_wali">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="no_hp_wali" class="form-label">No HP Wali</label>
                                    <input type="text" class="form-control" id="no_hp_wali" name="no_hp_wali" placeholder="08xxxxxxxxxx">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="add_status" name="status" required>
                                        <option value="aktif">Aktif</option>
                                        <option value="lulus">Lulus</option>
                                        <option value="pindah">Pindah</option>
                                        <option value="dikeluarkan">Dikeluarkan</option>
                                        <option value="ALUMNI">Alumni</option>
                                        <option value="naik_kelas">Naik Kelas</option>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Form Edit Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('students/edit'); ?>">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_nis" class="form-label">NIS <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_nis" name="nis" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_nama_lengkap" name="nama_lengkap" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-control" id="edit_jenis_kelamin" name="jenis_kelamin" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_kelas_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                                    <select class="form-control select2-modal" id="edit_kelas_id" name="kelas_id" required>
                                        <option value="">Pilih Kelas</option>
                                        <?php foreach ($classes as $class): ?>
                                        <option value="<?php echo htmlspecialchars($class['id']); ?>"><?php echo htmlspecialchars($class['nama_kelas']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_tahun_masuk" class="form-label">Tahun Masuk <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control yearpicker" id="edit_tahun_masuk" name="tahun_masuk" required readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_nama_wali" class="form-label">Nama Wali</label>
                                    <input type="text" class="form-control" id="edit_nama_wali" name="nama_wali">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_no_hp_wali" class="form-label">No HP Wali</label>
                                    <input type="text" class="form-control" id="edit_no_hp_wali" name="no_hp_wali" placeholder="08xxxxxxxxxx">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="edit_status" name="status" required>
                                        <option value="aktif">Aktif</option>
                                        <option value="lulus">Lulus</option>
                                        <option value="pindah">Pindah</option>
                                        <option value="dikeluarkan">Dikeluarkan</option>
                                        <option value="ALUMNI">Alumni</option>
                                        <option value="naik_kelas">Naik Kelas</option>
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
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                },
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },    // No
                    { responsivePriority: 2, targets: 2 },    // Nama Lengkap
                    { responsivePriority: 3, targets: 1 },    // NIS
                    { responsivePriority: 4, targets: -1 },   // Aksi (last column)
                    { responsivePriority: 5, targets: 4 },    // Kelas
                    { responsivePriority: 6, targets: 8 },    // Status
                    { responsivePriority: 7, targets: 3 },    // Jenis Kelamin
                    { responsivePriority: 8, targets: 5 },    // Tahun Masuk
                    { responsivePriority: 9, targets: 6 },    // Nama Wali
                    { responsivePriority: 10, targets: 7 }    // No HP Wali
                ]
            });
        }

        // Initialize Year Picker
        $('.yearpicker').datepicker({
            format: 'yyyy',
            viewMode: 'years',
            minViewMode: 'years',
            autoclose: true,
            orientation: 'bottom',
            startDate: '2000',
            endDate: '" . date('Y') . "'
        });

        // Event listener saat modal add dibuka untuk memastikan yearpicker berfungsi
        $('#addModal').on('shown.bs.modal', function () {
            $('#tahun_masuk').datepicker('update'); // Memperbarui tampilan picker jika diperlukan
            // Initialize Select2 here for elements in addModal once modal is shown
            $('#kelas_id').select2({
                dropdownParent: $('#addModal'), // Crucial for Select2 inside modals
                placeholder: 'Pilih Kelas',
                allowClear: true,
                width: '100%'
            });
            // Re-initialize other simple selects if needed, though Select2 is usually primary concern
        });

        // Event listener saat modal edit dibuka untuk mengisi dan memastikan yearpicker berfungsi
        $('#editModal').on('shown.bs.modal', function () {
            // Data already filled by editStudent, we just need to ensure datepicker is updated
            $('#edit_tahun_masuk').datepicker('update');
            // Initialize Select2 here for elements in editModal once modal is shown
            $('#edit_kelas_id').select2({
                dropdownParent: $('#editModal'), // Crucial for Select2 inside modals
                placeholder: 'Pilih Kelas',
                allowClear: true,
                width: '100%'
            });
            // Trigger change for Select2 to display selected value
            $('#edit_kelas_id').val($('#edit_kelas_id').val()).trigger('change');
        });
    });
    
    function editStudent(student) {
        $('#edit_id').val(student.id);
        $('#edit_nis').val(student.nis || '');
        $('#edit_nama_lengkap').val(student.nama_lengkap || '');
        $('#edit_jenis_kelamin').val(student.jenis_kelamin || '');
        // Set Select2 value for kelas_id and trigger change
        $('#edit_kelas_id').val(student.kelas_id || '').trigger('change'); 
        $('#edit_tahun_masuk').val(student.tahun_masuk || ''); 
        $('#edit_nama_wali').val(student.nama_wali || '');
        $('#edit_no_hp_wali').val(student.no_hp_wali || '');
        $('#edit_status').val(student.status || '');
        $('#editModal').modal('show');
    }
    
    function deleteStudent(id, nama_lengkap) {
        iziToast.question({
            timeout: 20000,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question',
            zindex: 999,
            title: 'Konfirmasi',
            message: 'Apakah Anda yakin ingin menghapus data siswa \'' + nama_lengkap + '\'?',
            position: 'center',
            buttons: [
                ['<button><b>Ya, Hapus</b></button>', function (instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '" . Router::url('students/delete') . "';
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