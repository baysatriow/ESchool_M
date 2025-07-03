<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Data Pembayaran</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item">Pendapatan</li>
                                <li class="breadcrumb-item active">Data Pembayaran</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Daftar Data Pembayaran</h4>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="mdi mdi-plus"></i> Tambah Data Pembayaran
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pembayaran</th>
                                        <th>Jenis</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Nominal</th>
                                        <th>Batas Waktu</th>
                                        <th>Cicilan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data_pembayaran as $index => $row): ?>
                                    <?php
                                        // Get assignment summary (ensure this logic is outside of HTML loop if possible for performance)
                                        // For now, keeping it as is since it's part of the original logic you provided
                                        $assignPembayaran = new AssignPembayaran($this->db);
                                        $summary = $assignPembayaran->getPaymentSummary($row['id']);
                                    ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_pembayaran'] ?? ''); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo ($row['tipe'] ?? '') == 'bulanan' ? 'info' : 'secondary'; ?>">
                                                <?php echo ucfirst($row['tipe'] ?? ''); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['tahun_ajaran'] ?? ''); ?></td>
                                        <td>Rp <?php echo number_format($row['nominal'] ?? 0, 0, ',', '.'); ?></td>
                                        <td>
                                            <?php if (!empty($row['batas_waktu'])): ?>
                                                <?php echo date('d/m/Y', strtotime($row['batas_waktu'])); ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (($row['dapat_dicicil'] ?? false)): ?>
                                                <span class="badge bg-success">Max <?php echo ($row['maksimal_cicilan'] ?? 0); ?>x</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Tidak</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                Assigned: <?php echo ($summary['total_siswa'] ?? 0); ?> siswa<br>
                                                Lunas: <?php echo ($summary['siswa_lunas'] ?? 0); ?> siswa
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-info" onclick="window.location.href='<?php echo Router::url('data-pembayaran/assign?id=' . ($row['id'] ?? '')); ?>'" title="Assign ke Siswa">
                                                    <i class="mdi mdi-account-multiple"></i> Assign
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="editDataPembayaran(<?php echo htmlspecialchars(json_encode($row)); ?>)" title="Edit Data">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteDataPembayaran(<?php echo ($row['id'] ?? 'null'); ?>, '<?php echo htmlspecialchars($row['nama_pembayaran'] ?? ''); ?>')" title="Hapus Data">
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
                    <h5 class="modal-title" id="addModalLabel">Form Tambah Data Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('data-pembayaran/create'); ?>">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="jenis_pembayaran_id" class="form-label">Jenis Pembayaran <span class="text-danger">*</span></label>
                                    <select class="form-control select2-modal" id="jenis_pembayaran_id" name="jenis_pembayaran_id" required onchange="toggleMonthlyGenerate()">
                                        <option value="">Pilih Jenis Pembayaran</option>
                                        <?php foreach ($payment_types as $type): ?>
                                        <option value="<?php echo htmlspecialchars($type['id']); ?>" data-tipe="<?php echo htmlspecialchars($type['tipe'] ?? ''); ?>">
                                            <?php echo htmlspecialchars($type['nama_pembayaran'] ?? ''); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                                    <select class="form-control select2-modal" id="tahun_ajaran_id" name="tahun_ajaran_id" required>
                                        <option value="">Pilih Tahun Ajaran</option>
                                        <?php foreach ($academic_years as $year): ?>
                                        <option value="<?php echo htmlspecialchars($year['id']); ?>" <?php echo ($year['status'] ?? '') == 'aktif' ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($year['tahun_ajaran'] ?? ''); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="nama_pembayaran" class="form-label">Nama Pembayaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_pembayaran" name="nama_pembayaran" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nominal" class="form-label">Nominal <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="nominal" name="nominal" required min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="batas_waktu" class="form-label">Batas Waktu</label>
                                    <input type="date" class="form-control" id="batas_waktu" name="batas_waktu">
                                    <small class="text-muted" id="due_date_help" style="display: none;">
                                        Untuk pembayaran bulanan: tanggal ini akan digunakan sebagai tanggal jatuh tempo setiap bulan
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="dapat_dicicil" name="dapat_dicicil" onchange="toggleInstallmentOptions()">
                                        <label class="form-check-label" for="dapat_dicicil">
                                            Dapat Dicicil
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3" id="installment_options" style="display: none;">
                                    <label for="maksimal_cicilan" class="form-label">Maksimal Cicilan <span class="text-danger">*</span></label>
                                    <select class="form-control select2-modal" id="maksimal_cicilan" name="maksimal_cicilan" required>
                                        <option value="1">1x</option>
                                        <option value="2">2x</option>
                                        <option value="3">3x</option>
                                        <option value="4">4x</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3" id="monthly_generate_option" style="display: none;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="generate_monthly" name="generate_monthly">
                                <label class="form-check-label" for="generate_monthly">
                                    Generate Pembayaran Bulanan Otomatis
                                </label>
                                <small class="form-text text-muted">
                                    Akan membuat pembayaran untuk setiap bulan sesuai tahun ajaran yang dipilih
                                </small>
                            </div>
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
                    <h5 class="modal-title" id="editModalLabel">Form Edit Data Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('data-pembayaran/edit'); ?>">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_jenis_pembayaran_id" class="form-label">Jenis Pembayaran <span class="text-danger">*</span></label>
                                    <select class="form-control select2-modal" id="edit_jenis_pembayaran_id" name="jenis_pembayaran_id" required>
                                        <option value="">Pilih Jenis Pembayaran</option>
                                        <?php foreach ($payment_types as $type): ?>
                                        <option value="<?php echo htmlspecialchars($type['id']); ?>" data-tipe="<?php echo htmlspecialchars($type['tipe'] ?? ''); ?>">
                                            <?php echo htmlspecialchars($type['nama_pembayaran'] ?? ''); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_tahun_ajaran_id" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                                    <select class="form-control select2-modal" id="edit_tahun_ajaran_id" name="tahun_ajaran_id" required>
                                        <option value="">Pilih Tahun Ajaran</option>
                                        <?php foreach ($academic_years as $year): ?>
                                        <option value="<?php echo htmlspecialchars($year['id']); ?>">
                                            <?php echo htmlspecialchars($year['tahun_ajaran'] ?? ''); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nama_pembayaran" class="form-label">Nama Pembayaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nama_pembayaran" name="nama_pembayaran" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_nominal" class="form-label">Nominal <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="edit_nominal" name="nominal" required min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_batas_waktu" class="form-label">Batas Waktu</label>
                                    <input type="date" class="form-control" id="edit_batas_waktu" name="batas_waktu">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="edit_dapat_dicicil" name="dapat_dicicil" onchange="toggleEditInstallmentOptions()">
                                        <label class="form-check-label" for="edit_dapat_dicicil">
                                            Dapat Dicicil
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3" id="edit_installment_options" style="display: none;">
                                    <label for="edit_maksimal_cicilan" class="form-label">Maksimal Cicilan <span class="text-danger">*</span></label>
                                    <select class="form-control select2-modal" id="edit_maksimal_cicilan" name="maksimal_cicilan" required>
                                        <option value="1">1x</option>
                                        <option value="2">2x</option>
                                        <option value="3">3x</option>
                                        <option value="4">4x</option>
                                    </select>
                                </div>
                            </div>
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
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                },
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },   // No
                    { responsivePriority: 2, targets: 1 },   // Nama Pembayaran
                    { responsivePriority: 3, targets: 8 },   // Aksi (last column)
                    { responsivePriority: 4, targets: 4 },   // Nominal
                    { responsivePriority: 5, targets: 7 },   // Status
                ]
            });
        }

        // Initialize Select2 for category dropdowns in modals
        $('#jenis_pembayaran_id').select2({
            dropdownParent: $('#addModal'), // Important for modal Select2
            placeholder: 'Pilih Jenis Pembayaran',
            allowClear: true,
            width: '100%'
        });
        $('#tahun_ajaran_id').select2({
            dropdownParent: $('#addModal'), // Important for modal Select2
            placeholder: 'Pilih Tahun Ajaran',
            allowClear: true,
            width: '100%'
        });
        $('#maksimal_cicilan').select2({
            dropdownParent: $('#addModal'), // Important for modal Select2
            placeholder: 'Pilih Maksimal Cicilan',
            minimumResultsForSearch: Infinity, // No search box for small lists
            width: '100%'
        });


        $('#edit_jenis_pembayaran_id').select2({
            dropdownParent: $('#editModal'), // Important for modal Select2
            placeholder: 'Pilih Jenis Pembayaran',
            allowClear: true,
            width: '100%'
        });
        $('#edit_tahun_ajaran_id').select2({
            dropdownParent: $('#editModal'), // Important for modal Select2
            placeholder: 'Pilih Tahun Ajaran',
            allowClear: true,
            width: '100%'
        });
        $('#edit_maksimal_cicilan').select2({
            dropdownParent: $('#editModal'), // Important for modal Select2
            placeholder: 'Pilih Maksimal Cicilan',
            minimumResultsForSearch: Infinity, // No search box for small lists
            width: '100%'
        });

        // Preview image functions (not relevant to this specific task, but kept for completeness)
        $('#bukti_foto').change(function() {
            previewImageToElement(this, 'preview_foto_add');
        });
        $('#edit_bukti_foto').change(function() {
            previewImageToElement(this, 'preview_foto_edit');
        });
    });

    // Function to preview image in an <img> element
    function previewImageToElement(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#' + previewId).attr('src', e.target.result);
                $('#' + previewId).css('display', 'block');
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            $('#' + previewId).attr('src', '#').css('display', 'none');
        }
    }

    // Function to show image in a dedicated modal (gallery-like)
    function showImagePreview(imageUrl) {
        $('#modal_image_preview').attr('src', imageUrl);
        $('#imagePreviewModal').modal('show');
    }

    function toggleMonthlyGenerate() {
        const jenisSelect = document.getElementById('jenis_pembayaran_id');
        const selectedOption = jenisSelect.options[jenisSelect.selectedIndex];
        const tipe = selectedOption.getAttribute('data-tipe');
        const monthlyOption = document.getElementById('monthly_generate_option');
        const dueDateHelp = document.getElementById('due_date_help');
        const maksimalCicilanAdd = document.getElementById('maksimal_cicilan'); // For 'Add' modal
        const dapatDicicilCheckbox = document.getElementById('dapat_dicicil');
        const installmentOptionsDiv = document.getElementById('installment_options');

        if (tipe === 'bulanan') {
            monthlyOption.style.display = 'block';
            dueDateHelp.style.display = 'block';
            // Disable installment options for monthly payments
            dapatDicicilCheckbox.checked = false;
            installmentOptionsDiv.style.display = 'none';
            maksimalCicilanAdd.removeAttribute('required'); // Remove required attribute
        } else {
            monthlyOption.style.display = 'none';
            dueDateHelp.style.display = 'none';
            document.getElementById('generate_monthly').checked = false;
            // Re-enable required for non-monthly if installment checkbox is checked
            if (dapatDicicilCheckbox.checked) {
                maksimalCicilanAdd.setAttribute('required', 'required');
            }
        }
        // Ensure Select2 updates if visibility changes
        $('#maksimal_cicilan').select2('destroy').select2({
            dropdownParent: $('#addModal'),
            placeholder: 'Pilih Maksimal Cicilan',
            minimumResultsForSearch: Infinity,
            width: '100%'
        });
    }

    function toggleInstallmentOptions() {
        const checkbox = document.getElementById('dapat_dicicil');
        const options = document.getElementById('installment_options');
        const maksimalCicilanAdd = document.getElementById('maksimal_cicilan');

        if (checkbox.checked) {
            options.style.display = 'block';
            maksimalCicilanAdd.setAttribute('required', 'required');
        } else {
            options.style.display = 'none';
            maksimalCicilanAdd.removeAttribute('required'); // Remove required if not enabled
        }
        // Ensure Select2 updates if visibility changes
        $('#maksimal_cicilan').select2('destroy').select2({
            dropdownParent: $('#addModal'),
            placeholder: 'Pilih Maksimal Cicilan',
            minimumResultsForSearch: Infinity,
            width: '100%'
        });
    }

    function toggleEditInstallmentOptions() {
        const checkbox = document.getElementById('edit_dapat_dicicil');
        const options = document.getElementById('edit_installment_options');
        const maksimalCicilanEdit = document.getElementById('edit_maksimal_cicilan'); // For 'Edit' modal

        if (checkbox.checked) {
            options.style.display = 'block';
            maksimalCicilanEdit.setAttribute('required', 'required');
        } else {
            options.style.display = 'none';
            maksimalCicilanEdit.removeAttribute('required'); // Remove required if not enabled
        }
        // Ensure Select2 updates if visibility changes
        $('#edit_maksimal_cicilan').select2('destroy').select2({
            dropdownParent: $('#editModal'),
            placeholder: 'Pilih Maksimal Cicilan',
            minimumResultsForSearch: Infinity,
            width: '100%'
        });
    }

    function editDataPembayaran(data) {
        $('#edit_id').val(data.id);
        
        // Set Select2 values for edit form and trigger change
        $('#edit_jenis_pembayaran_id').val(data.jenis_pembayaran_id || '').trigger('change');
        $('#edit_tahun_ajaran_id').val(data.tahun_ajaran_id || '').trigger('change');
        
        $('#edit_nama_pembayaran').val(data.nama_pembayaran || '');
        $('#edit_nominal').val(data.nominal || 0);
        $('#edit_batas_waktu').val(data.batas_waktu || '');
        $('#edit_keterangan').val(data.keterangan || '');
        
        // Handle 'Dapat Dicicil' checkbox and options
        if (data.dapat_dicicil == 1) {
            $('#edit_dapat_dicicil').prop('checked', true);
            $('#edit_installment_options').show();
            $('#edit_maksimal_cicilan').val(data.maksimal_cicilan || 1).trigger('change'); // Set value, default to 1 if null
            $('#edit_maksimal_cicilan').attr('required', 'required'); // Ensure required when checked
        } else {
            $('#edit_dapat_dicicil').prop('checked', false);
            $('#edit_installment_options').hide();
            $('#edit_maksimal_cicilan').removeAttr('required'); // Remove required when unchecked
        }
        
        // No auto-toggle for monthly generate in edit modal (assuming it's not needed for existing entries)

        $('#editModal').modal('show');
    }

    function deleteDataPembayaran(id, nama_pembayaran) { // Added nama_pembayaran parameter
        iziToast.question({
            timeout: 20000,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question',
            zindex: 999,
            title: 'Konfirmasi',
            message: 'Apakah Anda yakin ingin menghapus data pembayaran \\'' + nama_pembayaran + '\\'?',
            position: 'center',
            buttons: [
                ['<button><b>Ya, Hapus</b></button>', function (instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '" . Router::url('data-pembayaran/delete') . "';
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