<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Ubah Status Siswa</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item active">Ubah Status Siswa</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Daftar Siswa</h4>
                                <a href="<?php echo Router::url('status-history'); ?>" class="btn btn-info">
                                    <i class="mdi mdi-history"></i> Riwayat Status
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIS</th>
                                        <th>Nama Lengkap</th>
                                        <th>Kelas</th>
                                        <th>Status Saat Ini</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $index => $row): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($row['nis']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_kelas'] ?? '-'); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $row['status'] == 'aktif' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning" onclick="changeStatus(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                                <i class="mdi mdi-account-edit"></i> Ubah Status
                                            </button>
                                            <a href="<?php echo Router::url('status-history?student_id=' . $row['id']); ?>" class="btn btn-sm btn-info">
                                                <i class="mdi mdi-history"></i> Riwayat
                                            </a>
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

    <!-- Change Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Ubah Status Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('student-status/update'); ?>">
                    <div class="modal-body">
                        <input type="hidden" name="siswa_id" id="siswa_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Siswa</label>
                            <input type="text" class="form-control" id="nama_siswa" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Status Saat Ini</label>
                            <input type="text" class="form-control" id="status_current" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Status Baru</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="lulus">Lulus</option>
                                <option value="pindah">Pindah</option>
                                <option value="dikeluarkan">Dikeluarkan</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Alasan perubahan status..."></textarea>
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
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            }
        });
    });
    
    function changeStatus(student) {
        $('#siswa_id').val(student.id);
        $('#nama_siswa').val(student.nama_lengkap);
        $('#status_current').val(student.status.charAt(0).toUpperCase() + student.status.slice(1));
        $('#status').val('');
        $('#keterangan').val('');
        $('#statusModal').modal('show');
    }
";

include 'includes/footer.php'; 
?>
