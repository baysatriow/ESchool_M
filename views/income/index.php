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

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Daftar Pendapatan</h4>
                            <div class="d-flex">
                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="mdi mdi-plus"></i> Tambah Pendapatan
                                </button>
                                <button type="button" class="btn btn-success" onclick="printData()">
                                    <i class="mdi mdi-printer"></i> Print
                                </button>
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
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($incomes as $index => $row): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['no_bukti'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_kategori'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                                        <td class="text-success fw-bold">Rp <?php echo number_format($row['nominal'], 0, ',', '.'); ?></td>
                                        <td>
                                            <?php if ($row['bukti_foto']): ?>
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
                                                <button type="button" class="btn btn-sm btn-info" onclick="showDetail(<?php echo htmlspecialchars(json_encode($row)); ?>)" title="Detail">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="editIncome(<?php echo htmlspecialchars(json_encode($row)); ?>)" title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteIncome(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['no_bukti'] ?? $row['nama_kategori']); ?>')" title="Hapus">
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
                    <h5 class="modal-title" id="addModalLabel">Tambah Pendapatan</h5>
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
                            <select class="form-control" id="kategori_id" name="kategori_id" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['nama_kategori']); ?></option>
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
                    <h5 class="modal-title" id="editModalLabel">Edit Pendapatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('income/edit'); ?>" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_no_bukti" class="form-label">No. Bukti</label>
                            <input name="no_bukti" type="text" class="form-control" id="edit_no_bukti" disabled readonly style="background-color: #f8f9fa;">
                            <small class="form-text text-muted">No. bukti tidak dapat diubah</small>
                        </div>
                        <div class="mb-3">
                            <label for="edit_tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_kategori_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-control" id="edit_kategori_id" name="kategori_id" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['nama_kategori']); ?></option>
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

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Pendapatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Tanggal:</strong> <span id="detail_tanggal"></span></p>
                            <p><strong>No. Bukti:</strong> <span id="detail_no_bukti"></span></p>
                            <p><strong>Kategori:</strong> <span id="detail_kategori"></span></p>
                            <p><strong>Keterangan:</strong> <span id="detail_keterangan"></span></p>
                            <p><strong>Nominal:</strong> <span id="detail_nominal" class="text-success fw-bold"></span></p>
                            <p><strong>Dibuat Oleh:</strong> <span id="detail_created_by"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Bukti Foto:</strong></p>
                            <div id="detail_foto"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagePreviewModalLabel">Pratinjau Gambar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modal_image_preview" src="" class="img-fluid" alt="Pratinjau Gambar" style="max-width: 100%; height: auto;">
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
                    // Pastikan URL i18n ini benar dan konsisten.
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
        $('#edit_tanggal').val(income.tanggal); // Assuming tanggal is 'YYYY-MM-DD'
        $('#edit_no_bukti').val(income.no_bukti || '-');
        $('#edit_kategori_id').val(income.kategori_id);
        $('#edit_keterangan').val(income.keterangan || '');
        $('#edit_nominal').val(income.nominal);

        // Handle current photo display in edit modal
        if (income.bukti_foto) {
            var fullPath = 'uploads/income/' + income.bukti_foto;
            $('#current_photo').attr('src', fullPath);
            $('#current_photo_link').attr('onclick', \"showImagePreview('\" + fullPath + \"')\"); // Set click for gallery preview
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
    
    function deleteIncome(id, no_bukti) { // Using no_bukti for message
        iziToast.question({
            timeout: 20000,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question',
            zindex: 999,
            title: 'Konfirmasi',
            message: 'Apakah Anda yakin ingin menghapus pendapatan dengan No. Bukti \\'' + no_bukti + '\\'?',
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
    
    function printData() {
        window.print();
    }

    function showDetail(income) {
        // Format tanggal ke 'DD/MM/YYYY'
        const dateObj = new Date(income.tanggal);
        const formattedDate = dateObj.toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric'});

        $('#detail_tanggal').text(formattedDate);
        $('#detail_no_bukti').text(income.no_bukti || '-');
        $('#detail_kategori').text(income.nama_kategori || '-');
        $('#detail_keterangan').text(income.keterangan || '-');
        $('#detail_nominal').text('Rp ' + Number(income.nominal).toLocaleString('id-ID'));
        $('#detail_created_by').text(income.created_by || '-');

        var fotoHtml = '';
        if (income.bukti_foto) {
            var fullPath = 'uploads/income/' + income.bukti_foto;
            fotoHtml = '<a href=\"javascript:void(0);\" onclick=\"showImagePreview(\'' + fullPath + '\')\">' +
                         '<img src=\"' + fullPath + '\" alt=\"Bukti Foto\" class=\"img-fluid img-thumbnail\" style=\"max-width: 200px;\">' +
                         '</a>';
        } else {
            fotoHtml = '<span class=\"text-muted\">Tidak ada foto</span>';
        }
        $('#detail_foto').html(fotoHtml);

        $('#detailModal').modal('show');
    }
";

include 'includes/footer.php';
?>