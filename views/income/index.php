<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Data Pendapatan</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item">Pendapatan</li>
                                <li class="breadcrumb-item active">Data Pendapatan</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Filter Data</h4>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="<?php echo Router::url('income'); ?>" class="row g-3">
                                <div class="col-md-3">
                                    <label for="start_date" class="form-label">Tanggal Dari</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date" class="form-label">Tanggal Sampai</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="filter_kategori_id" class="form-label">Kategori</label>
                                    <select class="form-control" id="filter_kategori_id" name="kategori_id">
                                        <option value="">Semua Kategori</option>
                                        <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category['id']); ?>" <?php echo ($selected_kategori == $category['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['nama_kategori']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="mdi mdi-filter"></i> Filter
                                        </button>
                                        <a href="<?php echo Router::url('income'); ?>" class="btn btn-secondary">
                                            <i class="mdi mdi-refresh"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-success-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm rounded-circle bg-success">
                                    <i class="mdi mdi-cash-multiple mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-success mb-1">Total Pendapatan</p>
                                    <h4 class="mb-0">Rp <?php echo number_format($summary['total_pendapatan'] ?? 0, 0, ',', '.'); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-primary-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm rounded-circle bg-primary">
                                    <i class="mdi mdi-file-document-multiple mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-primary mb-1">Total Transaksi</p>
                                    <h4 class="mb-0"><?php echo number_format($summary['total_transaksi'] ?? 0, 0, ',', '.'); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-info-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm rounded-circle bg-info">
                                    <i class="mdi mdi-calendar-month mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-info mb-1">Pendapatan Bulan Ini</p>
                                    <h4 class="mb-0">Rp <?php echo number_format($summary['pendapatan_bulan_ini'] ?? 0, 0, ',', '.'); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-warning-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm rounded-circle bg-warning">
                                    <i class="mdi mdi-chart-line mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-warning mb-1">Rata-rata Pendapatan</p>
                                    <h4 class="mb-0">Rp <?php echo number_format($summary['rata_rata_pendapatan'] ?? 0, 0, ',', '.'); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Daftar Pendapatan</h4>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="mdi mdi-plus"></i> Tambah Pendapatan
                                </button>
                                <button type="button" class="btn btn-success" onclick="exportToExcel()">
                                    <i class="mdi mdi-file-excel"></i> Export Excel
                                </button>
                                <!-- <button type="button" class="btn btn-info" onclick="printData()">
                                    <i class="mdi mdi-printer"></i> Print
                                </button> -->
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Tanggal</th>
                                        <th>No. Bukti</th>
                                        <th>Kategori</th>
                                        <th>Keterangan</th>
                                        <th>Nominal</th>
                                        <th>Bukti Foto</th>
                                        <th>Dibuat Oleh</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($incomes as $index => $row): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                        <td><span class="badge bg-primary"><?php echo htmlspecialchars($row['no_bukti'] ?? '-'); ?></span></td>
                                        <td><?php echo htmlspecialchars($row['nama_kategori'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['keterangan'] ?? ''); ?></td>
                                        <td class="text-success fw-bold">Rp <?php echo number_format($row['nominal'] ?? 0, 0, ',', '.'); ?></td>
                                        <td>
                                            <?php if (!empty($row['bukti_foto'])): ?>
                                                <a href="javascript:void(0);" onclick="showImagePreview('uploads/income/<?php echo htmlspecialchars($row['bukti_foto']); ?>')">
                                                    <img src="uploads/income/<?php echo htmlspecialchars($row['bukti_foto']); ?>" alt="Bukti Foto" width="50" class="img-thumbnail">
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Tidak Ada</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['created_by'] ?? '-'); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-info" onclick="showDetail(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>)" title="Detail">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
                                                <a href="<?php echo Router::url('income/receipt?income_id=' . ($row['id'] ?? '')); ?>" class="btn btn-sm btn-success" target="_blank" title="Cetak Kuitansi">
                                                    <i class="mdi mdi-printer"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="editIncome(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>)" title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteIncome(<?php echo ($row['id'] ?? 'null'); ?>, '<?php echo htmlspecialchars($row['no_bukti'] ?? $row['nama_kategori'] ?? '', ENT_QUOTES, 'UTF-8'); ?>')" title="Hapus">
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Form Tambah Pendapatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('income/create'); ?>" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="kategori_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-control select2-modal" id="kategori_id" name="kategori_id" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['nama_kategori']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="nominal" class="form-label">Nominal <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="nominal" name="nominal" min="0" step="1000" required>
                        </div>
                        <div class="mb-3">
                            <label for="bukti_foto" class="form-label">Bukti Foto (Opsional)</label>
                            <input type="file" class="form-control" id="bukti_foto" name="bukti_foto" accept="image/*">
                            <small class="form-text text-muted">Format: JPG, JPEG, PNG, GIF. Maksimal 5MB</small>
                            <img id="preview_foto_add" src="#" alt="Preview Foto" style="max-width: 100px; margin-top: 10px; display: none;" class="img-thumbnail">
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Form Edit Pendapatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('income/edit'); ?>" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_no_bukti" class="form-label">No. Bukti</label>
                            <input type="text" class="form-control" id="edit_no_bukti" disabled readonly style="background-color: #f8f9fa;">
                            <small class="form-text text-muted">No. bukti tidak dapat diubah</small>
                        </div>
                        <div class="mb-3">
                            <label for="edit_tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_kategori_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-control select2-modal" id="edit_kategori_id" name="kategori_id" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['nama_kategori']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_keterangan" class="form-label">Keterangan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nominal" class="form-label">Nominal <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="edit_nominal" name="nominal" min="0" step="1000" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_bukti_foto" class="form-label">Bukti Foto</label>
                            <input type="file" class="form-control" id="edit_bukti_foto" name="bukti_foto" accept="image/*">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah foto</small>
                            <div id="current_photo_container" style="margin-top: 10px;">
                                <p id="no_photo_text" style="display: none;" class="text-muted mb-0">Tidak ada foto saat ini</p>
                                <a href="javascript:void(0);" id="current_photo_link" style="display: none;" onclick="showImagePreview(this.querySelector('img').src)">
                                    <img id="current_photo" src="#" alt="Foto Saat Ini" style="max-width: 100px;" class="img-thumbnail">
                                </a>
                            </div>
                            <img id="preview_foto_edit" src="#" alt="Preview Foto Baru" style="max-width: 100px; margin-top: 10px; display: none;" class="img-thumbnail">
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

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Pendapatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label">Tanggal:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext" id="detail_tanggal"></p>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label">No. Bukti:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext"><span id="detail_no_bukti" class="badge bg-primary"></span></p>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label">Kategori:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext" id="detail_kategori"></p>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label">Keterangan:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext" id="detail_keterangan"></p>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label">Nominal:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext text-success fw-bold" id="detail_nominal"></p>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label">Dibuat Oleh:</label>
                        <div class="col-sm-9">
                            <p class="form-control-plaintext" id="detail_created_by"></p>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label">Bukti Foto:</label>
                        <div class="col-sm-9">
                            <div id="detail_foto"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="#" id="detail_receipt_link" class="btn btn-success" target="_blank">
                        <i class="mdi mdi-printer"></i> Cetak Kuitansi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagePreviewModalLabel">Pratinjau Gambar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modal_image_preview" src="/placeholder.svg" class="img-fluid" alt="Pratinjau Gambar" style="max-width: 100%; height: auto;">
                </div>
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
                    { responsivePriority: 1, targets: 0 },   // No
                    { responsivePriority: 2, targets: 1 },   // Tanggal
                    { responsivePriority: 3, targets: 4 },   // Keterangan
                    { responsivePriority: 4, targets: 5 },   // Nominal
                    { responsivePriority: 5, targets: -1 },  // Aksi (last column)
                    { responsivePriority: 6, targets: 3 },   // Kategori
                    { responsivePriority: 7, targets: 2 },   // No. Bukti
                    { responsivePriority: 8, targets: 7 },   // Dibuat Oleh
                    { responsivePriority: 9, targets: 6 }    // Bukti Foto
                ]
            });
        }

        // Initialize Select2 for ALL category dropdowns
        $('#kategori_id').select2({
            dropdownParent: $('#addModal'),
            placeholder: 'Pilih Kategori',
            allowClear: true,
            width: '100%'
        });

        $('#edit_kategori_id').select2({
            dropdownParent: $('#editModal'),
            placeholder: 'Pilih Kategori',
            allowClear: true,
            width: '100%'
        });

        // Initialize Select2 for filter kategori dropdown
        $('#filter_kategori_id').select2({
            placeholder: 'Semua Kategori',
            allowClear: true,
            width: '100%'
        });

        // Preview image for Add Modal
        $('#bukti_foto').change(function() {
            previewImageToElement(this, 'preview_foto_add');
        });

        // Preview image for Edit Modal
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

    function editIncome(income) {
        $('#edit_id').val(income.id);
        $('#edit_tanggal').val(income.tanggal || '');
        $('#edit_no_bukti').val(income.no_bukti || '-');
        
        // Set Select2 value for edit_kategori_id
        $('#edit_kategori_id').val(income.kategori_id || '').trigger('change'); 
        $('#edit_keterangan').val(income.keterangan || '');
        $('#edit_nominal').val(income.nominal || 0);
        
        // Handle current photo display in edit modal
        if (income.bukti_foto) {
            var fullPath = 'uploads/income/' + income.bukti_foto;
            $('#current_photo').attr('src', fullPath);
            $('#current_photo_link').attr('onclick', 'showImagePreview(\'' + fullPath + '\')');
            $('#current_photo_link').css('display', 'block');
            $('#no_photo_text').css('display', 'none');
        } else {
            $('#current_photo').attr('src', '#');
            $('#current_photo_link').css('display', 'none');
            $('#no_photo_text').css('display', 'block');
        }
        
        // Reset new photo preview
        $('#preview_foto_edit').attr('src', '#').css('display', 'none');
        $('#editModal').modal('show');
    }

    function deleteIncome(id, no_bukti) {
        iziToast.question({
            timeout: 20000,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question',
            zindex: 999,
            title: 'Konfirmasi',
            message: 'Apakah Anda yakin ingin menghapus pendapatan dengan No. Bukti \'' + no_bukti + '\'?',
            position: 'center',
            buttons: [
                ['<button><b>Ya, Hapus</b></button>', function (instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '" . Router::url('income/delete') . "';
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

    function exportToExcel() {
        // Get current filter values
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var kategori_id = $('#filter_kategori_id').val();
        
        // Build export URL with current filters
        var exportUrl = '" . Router::url('income') . "?export=excel';
        if (start_date) exportUrl += '&start_date=' + start_date;
        if (end_date) exportUrl += '&end_date=' + end_date;
        if (kategori_id) exportUrl += '&kategori_id=' + kategori_id;
        
        // Open export URL
        window.open(exportUrl, '_blank');
    }

    function printData() {
        window.print();
    }

    function showDetail(income) {
        // Format tanggal ke 'DD/MM/YYYY'
        const dateObj = new Date(income.tanggal);
        const formattedDate = dateObj.toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric'});
        $('#detail_tanggal').text(formattedDate);
        
        // Untuk No. Bukti, karena ada badge, kita perlu memastikan HTML-nya dimasukkan dengan benar
        $('#detail_no_bukti').closest('p').html('<span id=\"detail_no_bukti\" class=\"badge bg-primary\">' + (income.no_bukti || '-') + '</span>');
        $('#detail_kategori').text(income.nama_kategori || '-');
        $('#detail_keterangan').text(income.keterangan || '-');
        $('#detail_nominal').text('Rp ' + Number(income.nominal || 0).toLocaleString('id-ID'));
        $('#detail_created_by').text(income.created_by || '-');

        // Set receipt link
        $('#detail_receipt_link').attr('href', '" . Router::url('income/receipt') . "?income_id=' + (income.id || ''));

        var fotoHtml = '';
        if (income.bukti_foto) {
            var fullPath = 'uploads/income/' + income.bukti_foto;
            fotoHtml = '<a href=\"javascript:void(0);\" onclick=\"showImagePreview(\'' + fullPath + '\')\"><img src=\"' + fullPath + '\" alt=\"Bukti Foto\" class=\"img-fluid img-thumbnail\" style=\"max-width: 200px;\"></a>';
        } else {
            fotoHtml = '<span class=\"text-muted\">Tidak ada foto</span>';
        }
        $('#detail_foto').html(fotoHtml);
        $('#detailModal').modal('show');
    }
";

include 'includes/footer.php';
?>
