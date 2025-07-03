<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Assign Pembayaran ke Siswa</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('data-pembayaran'); ?>">Data Pembayaran</a></li>
                                <li class="breadcrumb-item active">Assign Pembayaran</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Informasi Pembayaran</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Nama Pembayaran:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext"><?php echo htmlspecialchars($payment_data['nama_pembayaran'] ?? '-'); ?></p>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Jenis:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext"><?php echo htmlspecialchars($payment_data['jenis_nama'] ?? '-'); ?> (<?php echo ucfirst($payment_data['tipe'] ?? '-'); ?>)</p>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Tahun Ajaran:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext"><?php echo htmlspecialchars($payment_data['tahun_ajaran'] ?? '-'); ?></p>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Nominal:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext">Rp <?php echo number_format($payment_data['nominal'] ?? 0, 0, ',', '.'); ?></p>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Batas Waktu:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext"><?php echo ($payment_data['batas_waktu'] ? date('d/m/Y', strtotime($payment_data['batas_waktu'])) : '-'); ?></p>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Dapat Dicicil:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext"><?php echo ($payment_data['dapat_dicicil'] ? 'Ya (Max ' . ($payment_data['maksimal_cicilan'] ?? '-') . 'x)' : 'Tidak'); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border border-info h-100">
                                        <div class="card-body d-flex flex-column justify-content-between">
                                            <h6 class="card-title text-info text-center mb-3">Statistik Assignment</h6>
                                            <div class="row text-center flex-grow-1 align-items-center">
                                                <div class="col-4">
                                                    <h4 class="text-primary"><?php echo $summary['total_siswa'] ?? 0; ?></h4>
                                                    <small>Total Siswa</small>
                                                </div>
                                                <div class="col-4">
                                                    <h4 class="text-success"><?php echo $summary['siswa_lunas'] ?? 0; ?></h4>
                                                    <small>Sudah Lunas</small>
                                                </div>
                                                <div class="col-4">
                                                    <h4 class="text-warning"><?php echo $summary['siswa_belum_bayar'] ?? 0; ?></h4>
                                                    <small>Belum Bayar</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Assign ke Siswa Baru</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?php echo Router::url('data-pembayaran/process-assign'); ?>">
                                <input type="hidden" name="data_pembayaran_id" value="<?php echo htmlspecialchars($payment_data['id'] ?? ''); ?>">

                                <div class="mb-3">
                                    <label for="filter_kelas" class="form-label">Filter Kelas</label>
                                    <select class="form-control select2" id="filter_kelas" onchange="filterStudents()">
                                        <option value="">Semua Kelas</option>
                                        <?php foreach ($classes as $class): ?>
                                        <option value="<?php echo htmlspecialchars($class['id']); ?>"><?php echo htmlspecialchars($class['nama_kelas']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <?php if (($payment_data['dapat_dicicil'] ?? false)): ?>
                                <div class="mb-3">
                                    <label for="jumlah_cicilan" class="form-label">Jumlah Cicilan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="jumlah_cicilan" name="jumlah_cicilan" required>
                                        <?php for ($i = 1; $i <= ($payment_data['maksimal_cicilan'] ?? 1); $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?>x Cicilan</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">Pilih Siswa <span class="text-danger">*</span></label>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">Pilih Semua</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">Batal Pilih</button>
                                        </div>
                                    </div>

                                    <div style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; padding: 10px; border-radius: .25rem;">
                                        <?php
                                        $assignedStudentIds = array_column($assignments, 'siswa_id');
                                        foreach ($students as $student):
                                            if (in_array($student['id'], $assignedStudentIds)) continue;
                                        ?>
                                        <div class="form-check student-item py-1" data-kelas="<?php echo htmlspecialchars($student['kelas_id'] ?? ''); ?>">
                                            <input class="form-check-input student-checkbox" type="checkbox" name="student_ids[]" value="<?php echo htmlspecialchars($student['id'] ?? ''); ?>" id="student_<?php echo htmlspecialchars($student['id'] ?? ''); ?>">
                                            <label class="form-check-label" for="student_<?php echo htmlspecialchars($student['id'] ?? ''); ?>">
                                                <strong><?php echo htmlspecialchars($student['nama_lengkap'] ?? '-'); ?></strong><br>
                                                <small class="text-muted">NIS: <?php echo htmlspecialchars($student['nis'] ?? '-'); ?> | Kelas: <?php echo htmlspecialchars($student['nama_kelas'] ?? '-'); ?></small>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-account-multiple-plus"></i> Assign ke Siswa Terpilih
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Siswa yang Sudah Di-assign</h4>
                        </div>
                        <div class="card-body">
                            <?php if (empty($assignments)): ?>
                                <div class="text-center text-muted py-4">
                                    <i class="mdi mdi-account-off mdi-48px"></i>
                                    <p>Belum ada siswa yang di-assign untuk pembayaran ini.</p>
                                </div>
                            <?php else: ?>
                                <div style="max-height: 400px; overflow-y: auto;">
                                    <?php foreach ($assignments as $assignment): ?>
                                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                        <div>
                                            <strong><?php echo htmlspecialchars($assignment['nama_lengkap'] ?? '-'); ?></strong><br>
                                            <small class="text-muted">
                                                NIS: <?php echo htmlspecialchars($assignment['nis'] ?? '-'); ?> |
                                                Kelas: <?php echo htmlspecialchars($assignment['nama_kelas'] ?? '-'); ?>
                                            </small><br>
                                            <small>
                                                <span class="badge bg-<?php
                                                    $statusClass = [
                                                        'sudah_bayar' => 'success',
                                                        'sebagian' => 'warning',
                                                        'belum_bayar' => 'danger'
                                                    ];
                                                    echo $statusClass[$assignment['status_pembayaran'] ?? 'belum_bayar'] ?? 'secondary';
                                                ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $assignment['status_pembayaran'] ?? 'Tidak Diketahui')); ?>
                                                </span>
                                                Rp <?php echo number_format($assignment['nominal_yang_harus_dibayar'] ?? 0, 0, ',', '.'); ?>
                                                <?php if (($assignment['jumlah_cicilan'] ?? 0) > 1): ?>
                                                    <br><small class="text-info"><?php echo ($assignment['jumlah_cicilan'] ?? '-'); ?>x Cicilan</small>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <div>
                                            <?php if (($assignment['nominal_yang_sudah_dibayar'] ?? 0) == 0): ?>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeAssignment(<?php echo htmlspecialchars($assignment['data_pembayaran_id'] ?? 'null'); ?>, <?php echo htmlspecialchars($assignment['siswa_id'] ?? 'null'); ?>, '<?php echo htmlspecialchars($assignment['nama_lengkap'] ?? ''); ?>')">
                                                <i class="mdi mdi-delete"></i> Hapus
                                            </button>
                                            <?php else: ?>
                                            <span class="text-muted"><i class="mdi mdi-lock"></i></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$custom_js = "
    $(document).ready(function() {
        // Initialize Select2 for the filter_kelas dropdown
        $('.select2').select2({
            placeholder: 'Pilih Kelas',
            allowClear: true, // Allow clearing the selection
            width: '100%' // Ensure it takes full width of its container
        });

        // Initialize Select2 for the jumlah_cicilan dropdown in the add form
        $('#jumlah_cicilan').select2({
            placeholder: 'Pilih Jumlah Cicilan',
            minimumResultsForSearch: Infinity, // No search box for small lists
            width: '100%'
        });

        // Initialize Select2 for the jumlah_cicilan dropdown in the edit form (if it exists)
        $('#edit_maksimal_cicilan').select2({
            placeholder: 'Pilih Maksimal Cicilan',
            minimumResultsForSearch: Infinity,
            width: '100%'
        });
    });

    function filterStudents() {
        const kelasId = $('#filter_kelas').val(); // Use jQuery for Select2 value
        const studentItems = document.querySelectorAll('.student-item');

        studentItems.forEach(item => {
            if (kelasId === '' || item.getAttribute('data-kelas') === kelasId) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
                item.querySelector('.student-checkbox').checked = false; // Deselect hidden students
            }
        });
    }

    function selectAll() {
        const visibleCheckboxes = document.querySelectorAll('.student-item:not([style*=\"display: none\"]) .student-checkbox');
        visibleCheckboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
    }

    function deselectAll() {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    function removeAssignment(dataPembayaranId, siswaId, studentName) { // Added studentName
        iziToast.question({
            timeout: 20000,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question',
            zindex: 999,
            title: 'Konfirmasi Hapus',
            message: 'Apakah Anda yakin ingin menghapus assignment pembayaran ini dari siswa \\'' + studentName + '\\'?',
            position: 'center',
            buttons: [
                ['<button><b>Ya, Hapus</b></button>', function (instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');

                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '" . Router::url('data-pembayaran/remove-assign') . "';
                    form.innerHTML = '<input type=\"hidden\" name=\"data_pembayaran_id\" value=\"' + dataPembayaranId + '\">' +
                                     '<input type=\"hidden\" name=\"siswa_id\" value=\"' + siswaId + '\">';
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