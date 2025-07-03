<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<style>
    /* Ensure Select2 dropdown z-index is high enough for modals */
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
                        <h4 class="mb-sm-0">Tahun Ajaran</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item">Referensi</li>
                                <li class="breadcrumb-item active">Tahun Ajaran</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Daftar Tahun Ajaran</h4>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="mdi mdi-plus"></i> Tambah Tahun Ajaran
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th width="50px">No</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Periode</th>
                                        <th width="100px">Status</th>
                                        <th width="120px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Helper function untuk nama bulan
                                    function getMonthName($monthNumber) {
                                        $months = [
                                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                        ];
                                        return isset($months[$monthNumber]) ? $months[$monthNumber] : '';
                                    }

                                    // Helper function untuk periode tahun ajaran
                                    function getAcademicYearPeriod($tahunAjaran, $bulanMulai, $bulanSelesai) {
                                        $years = explode('/', $tahunAjaran);
                                        $startYear = $years[0] ?? '';
                                        // Handle cases like '2024' instead of '2024/2025'
                                        $endYear = isset($years[1]) ? $years[1] : (empty($startYear) ? '' : ($startYear + 1));
                                        
                                        $startMonth = getMonthName($bulanMulai);
                                        $endMonth = getMonthName($bulanSelesai);
                                        
                                        if (empty($startMonth) || empty($endMonth) || empty($startYear)) {
                                            return '-'; // Return dash if data is incomplete
                                        }

                                        return $startMonth . ' ' . $startYear . ' - ' . $endMonth . ' ' . $endYear;
                                    }
                                    
                                    foreach ($academic_years as $index => $row):
                                        $periode = getAcademicYearPeriod(
                                            $row['tahun_ajaran'] ?? '',
                                            $row['bulan_mulai'] ?? null,
                                            $row['bulan_selesai'] ?? null
                                        );
                                    ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($row['tahun_ajaran'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($periode); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo (($row['status'] ?? '') == 'aktif') ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $row['status'] ?? '-')); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-warning" onclick="editAcademicYear(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>)" title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteAcademicYear(<?php echo ($row['id'] ?? 'null'); ?>, '<?php echo htmlspecialchars($row['tahun_ajaran'] ?? '', ENT_QUOTES, 'UTF-8'); ?>')" title="Hapus">
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

    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form Tambah Tahun Ajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="<?= Router::url('academic-years/create') ?>">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tahun Mulai *</label>
                                    <select class="form-select" name="tahun_mulai" required>
                                        <option value="">Pilih Tahun</option>
                                        <?php 
                                        $currentYear = date('Y');
                                        for ($year = $currentYear - 2; $year <= $currentYear + 5; $year++): 
                                        ?>
                                            <option value="<?= $year ?>" <?= $year == $currentYear ? 'selected' : '' ?>>
                                                <?= $year ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tahun Selesai *</label>
                                    <select class="form-select" name="tahun_selesai" required>
                                        <option value="">Pilih Tahun</option>
                                        <?php 
                                        for ($year = $currentYear - 1; $year <= $currentYear + 6; $year++): 
                                        ?>
                                            <option value="<?= $year ?>" <?= $year == ($currentYear + 1) ? 'selected' : '' ?>>
                                                <?= $year ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Bulan Mulai *</label>
                                    <select class="form-select" name="bulan_mulai" required>
                                        <option value="">Pilih Bulan</option>
                                        <?php 
                                        $months = [
                                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                        ];
                                        foreach ($months as $num => $name): 
                                        ?>
                                            <option value="<?= $num ?>" <?= $num == 7 ? 'selected' : '' ?>>
                                                <?= $name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Bulan Selesai *</label>
                                    <select class="form-select" name="bulan_selesai" required>
                                        <option value="">Pilih Bulan</option>
                                        <?php foreach ($months as $num => $name): ?>
                                            <option value="<?= $num ?>" <?= $num == 6 ? 'selected' : '' ?>>
                                                <?= $name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Status *</label>
                            <select class="form-select" name="status" required>
                                <option value="tidak_aktif">Tidak Aktif</option>
                                <option value="aktif">Aktif</option>
                            </select>
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


    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form Edit Tahun Ajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="<?= Router::url('academic-years/edit') ?>">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tahun Mulai *</label>
                                    <select class="form-select" name="tahun_mulai" id="edit_tahun_mulai" required>
                                        <option value="">Pilih Tahun</option>
                                        <?php 
                                        for ($year = $currentYear - 2; $year <= $currentYear + 5; $year++): 
                                        ?>
                                            <option value="<?= $year ?>"><?= $year ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Tahun Selesai *</label>
                                    <select class="form-select" name="tahun_selesai" id="edit_tahun_selesai" required>
                                        <option value="">Pilih Tahun</option>
                                        <?php 
                                        for ($year = $currentYear - 1; $year <= $currentYear + 6; $year++): 
                                        ?>
                                            <option value="<?= $year ?>"><?= $year ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Bulan Mulai *</label>
                                    <select class="form-select" name="bulan_mulai" id="edit_bulan_mulai" required>
                                        <option value="">Pilih Bulan</option>
                                        <?php foreach ($months as $num => $name): ?>
                                            <option value="<?= $num ?>"><?= $name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Bulan Selesai *</label>
                                    <select class="form-select" name="bulan_selesai" id="edit_bulan_selesai" required>
                                        <option value="">Pilih Bulan</option>
                                        <?php foreach ($months as $num => $name): ?>
                                            <option value="<?= $num ?>"><?= $name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Status *</label>
                            <select class="form-select" name="status" id="edit_status" required>
                                <option value="tidak_aktif">Tidak Aktif</option>
                                <option value="aktif">Aktif</option>
                            </select>
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
        $('#datatable').DataTable({
            'language': {
                'url': '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
            },
            'responsive': true,
            'autoWidth': false,
            'order': [[1, 'desc']],
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 1 },
                { responsivePriority: 3, targets: 3 },
                { responsivePriority: 4, targets: 4 },
                { responsivePriority: 5, targets: 2 }
            ]
        });

        $('select[name=\"tahun_mulai\"]').select2({
            dropdownParent: $('#addModal'),
            placeholder: 'Pilih Tahun',
            allowClear: true,
            width: '100%'
        });

        $('select[name=\"tahun_selesai\"]').select2({
            dropdownParent: $('#addModal'),
            placeholder: 'Pilih Tahun',
            allowClear: true,
            width: '100%'
        });

        $('select[name=\"bulan_mulai\"]').select2({
            dropdownParent: $('#addModal'),
            placeholder: 'Pilih Bulan',
            allowClear: true,
            width: '100%'
        });

        $('select[name=\"bulan_selesai\"]').select2({
            dropdownParent: $('#addModal'),
            placeholder: 'Pilih Bulan',
            allowClear: true,
            width: '100%'
        });

        $('#edit_tahun_mulai').select2({
            dropdownParent: $('#editModal'),
            placeholder: 'Pilih Tahun',
            allowClear: true,
            width: '100%'
        });

        $('#edit_tahun_selesai').select2({
            dropdownParent: $('#editModal'),
            placeholder: 'Pilih Tahun',
            allowClear: true,
            width: '100%'
        });

        $('#edit_bulan_mulai').select2({
            dropdownParent: $('#editModal'),
            placeholder: 'Pilih Bulan',
            allowClear: true,
            width: '100%'
        });

        $('#edit_bulan_selesai').select2({
            dropdownParent: $('#editModal'),
            placeholder: 'Pilih Bulan',
            allowClear: true,
            width: '100%'
        });

        $('#addModal form').on('submit', function(e) {
            const tahunMulai = parseInt($('select[name=\"tahun_mulai\"]').val());
            const tahunSelesai = parseInt($('select[name=\"tahun_selesai\"]').val());
            
            if (tahunMulai >= tahunSelesai) {
                e.preventDefault();
                iziToast.error({
                    title: 'Error!',
                    message: 'Tahun selesai harus lebih besar dari tahun mulai!',
                    position: 'topRight'
                });
                return false;
            }
        });

        $('#editModal form').on('submit', function(e) {
            const tahunMulai = parseInt($('#edit_tahun_mulai').val());
            const tahunSelesai = parseInt($('#edit_tahun_selesai').val());
            
            if (tahunMulai >= tahunSelesai) {
                e.preventDefault();
                iziToast.error({
                    title: 'Error!',
                    message: 'Tahun selesai harus lebih besar dari tahun mulai!',
                    position: 'topRight'
                });
                return false;
            }
        });

        $('select[name=\"tahun_mulai\"]').on('change', function() {
            const selectedYear = parseInt($(this).val());
            if (selectedYear) {
                $('select[name=\"tahun_selesai\"]').val(selectedYear + 1).trigger('change');
            }
        });

        $('#edit_tahun_mulai').on('change', function() {
            const selectedYear = parseInt($(this).val());
            if (selectedYear) {
                $('#edit_tahun_selesai').val(selectedYear + 1).trigger('change');
            }
        });
    });

    function editAcademicYear(rowData) {
        $('#edit_id').val(rowData.id);
        const years = rowData.tahun_ajaran.split('/');
        $('#edit_tahun_mulai').val(years[0]).trigger('change');
        $('#edit_tahun_selesai').val(years[1]).trigger('change');
        
        $('#edit_bulan_mulai').val(rowData.bulan_mulai).trigger('change');
        $('#edit_bulan_selesai').val(rowData.bulan_selesai).trigger('change');
        $('#edit_status').val(rowData.status);
        
        $('#editModal').modal('show');
    }

    function deleteAcademicYear(id, tahunAjaran) {
        iziToast.question({
            timeout: 20000,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question',
            zindex: 999,
            title: 'Konfirmasi',
            message: 'Apakah Anda yakin ingin menghapus tahun ajaran \\'' + tahunAjaran + '\\'?',
            position: 'center',
            buttons: [
                ['<button><b>Ya, Hapus</b></button>', function (instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '" . Router::url('academic-years/delete') . "';
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