<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

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
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Daftar Tahun Ajaran</h4>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="mdi mdi-plus"></i> Tambah Tahun Ajaran
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Periode</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
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
                                        $startYear = $years[0];
                                        $endYear = isset($years[1]) ? $years[1] : $startYear + 1;
                                        
                                        $startMonth = getMonthName($bulanMulai);
                                        $endMonth = getMonthName($bulanSelesai);
                                        
                                        return $startMonth . ' ' . $startYear . ' - ' . $endMonth . ' ' . $endYear;
                                    }
                                    
                                    foreach ($academic_years as $index => $row): 
                                        $periode = getAcademicYearPeriod(
                                            $row['tahun_ajaran'], 
                                            $row['bulan_mulai'] ?? 7, 
                                            $row['bulan_selesai'] ?? 6
                                        );
                                    ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($row['tahun_ajaran']); ?></td>
                                        <td><?php echo $periode; ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $row['status'] == 'aktif' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $row['status'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning" onclick="editAcademicYear(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                                <i class="mdi mdi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteAcademicYear(<?php echo $row['id']; ?>)">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
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
                    <h5 class="modal-title" id="addModalLabel">Tambah Tahun Ajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('academic-years/create'); ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                            <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" placeholder="2024/2025" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bulan_mulai" class="form-label">Bulan Mulai</label>
                                    <select class="form-control" id="bulan_mulai" name="bulan_mulai" required>
                                        <option value="">Pilih Bulan</option>
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7" selected>Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bulan_selesai" class="form-label">Bulan Selesai</label>
                                    <select class="form-control" id="bulan_selesai" name="bulan_selesai" required>
                                        <option value="">Pilih Bulan</option>
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6" selected>Juni</option>
                                        <option value="7">Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="tidak_aktif">Tidak Aktif</option>
                                <option value="aktif">Aktif</option>
                            </select>
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Tahun Ajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="<?php echo Router::url('academic-years/edit'); ?>">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label for="edit_tahun_ajaran" class="form-label">Tahun Ajaran</label>
                            <input type="text" class="form-control" id="edit_tahun_ajaran" name="tahun_ajaran" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_bulan_mulai" class="form-label">Bulan Mulai</label>
                                    <select class="form-control" id="edit_bulan_mulai" name="bulan_mulai" required>
                                        <option value="">Pilih Bulan</option>
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7">Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_bulan_selesai" class="form-label">Bulan Selesai</label>
                                    <select class="form-control" id="edit_bulan_selesai" name="bulan_selesai" required>
                                        <option value="">Pilih Bulan</option>
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7">Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-control" id="edit_status" name="status" required>
                                <option value="tidak_aktif">Tidak Aktif</option>
                                <option value="aktif">Aktif</option>
                            </select>
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
        if (typeof $.fn.DataTable !== 'undefined') {
            $('#datatable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
                }
            });
        }
    });
    
    function editAcademicYear(academicYear) {
        $('#edit_id').val(academicYear.id);
        $('#edit_tahun_ajaran').val(academicYear.tahun_ajaran);
        $('#edit_bulan_mulai').val(academicYear.bulan_mulai || 7);
        $('#edit_bulan_selesai').val(academicYear.bulan_selesai || 6);
        $('#edit_status').val(academicYear.status);
        $('#editModal').modal('show');
    }
    
    function deleteAcademicYear(id) {
        if (confirm('Apakah Anda yakin ingin menghapus tahun ajaran ini?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '" . Router::url('academic-years/delete') . "';
            form.innerHTML = '<input type=\"hidden\" name=\"id\" value=\"' + id + '\">';
            document.body.appendChild(form);
            form.submit();
        }
    }
";

include 'includes/footer.php'; 
?>
