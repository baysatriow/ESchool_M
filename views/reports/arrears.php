<?php include 'includes/header.php'; ?>

<?php include 'includes/sidebar.php'; ?>

<style>
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
                        <h4 class="mb-sm-0">Laporan Tunggakan Siswa</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item">Laporan</li>
                                <li class="breadcrumb-item active">Laporan Tunggakan</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" action="<?php echo Router::url('arrears-reports'); ?>" id="filterForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="kelas_id" class="form-label">Filter Kelas</label>
                                        <select class="form-control" id="kelas_id" name="kelas_id">
                                            <option value="">Semua Kelas</option>
                                            <?php foreach ($classes as $kelas): ?>
                                            <option value="<?php echo $kelas['id']; ?>" <?php echo $selected_kelas == $kelas['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($kelas['nama_kelas']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="status_filter" class="form-label">Filter Status</label>
                                        <select class="form-control" id="status_filter" name="status_filter">
                                            <option value="menunggak" <?php echo $selected_status == 'menunggak' ? 'selected' : ''; ?>>Menunggak</option>
                                            <option value="lancar" <?php echo $selected_status == 'lancar' ? 'selected' : ''; ?>>Lancar</option>
                                            <option value="semua" <?php echo $selected_status == 'semua' ? 'selected' : ''; ?>>Semua</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-filter"></i> Filter
                                            </button>
                                            <button type="button" class="btn btn-success" onclick="exportToExcel()">
                                                <i class="mdi mdi-file-excel"></i> Export Excel
                                            </button>
                                            <a href="<?php echo Router::url('arrears-reports'); ?>" class="btn btn-secondary">
                                                <i class="mdi mdi-refresh"></i> Reset Filter
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-primary-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm rounded-circle bg-primary">
                                    <i class="mdi mdi-account-group mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-primary mb-1">Total Siswa</p>
                                    <h4 class="mb-0"><?php echo count($arrears_data); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-danger-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm rounded-circle bg-danger">
                                    <i class="mdi mdi-alert-circle mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-danger mb-1">Siswa Menunggak</p>
                                    <h4 class="mb-0">
                                        <?php 
                                        $menunggak = array_filter($arrears_data, function($row) {
                                            return $row['tunggakan'] > 0;
                                        });
                                        echo count($menunggak);
                                        ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-success-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm rounded-circle bg-success">
                                    <i class="mdi mdi-check-circle mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-success mb-1">Siswa Lancar</p>
                                    <h4 class="mb-0">
                                        <?php 
                                        $lancar = array_filter($arrears_data, function($row) {
                                            return $row['tunggakan'] == 0;
                                        });
                                        echo count($lancar);
                                        ?>
                                    </h4>
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
                                    <i class="mdi mdi-cash mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-warning mb-1">Total Tunggakan</p>
                                    <h4 class="mb-0">
                                        Rp <?php 
                                        $total_tunggakan = array_sum(array_column($arrears_data, 'total_nominal_tunggakan'));
                                        echo number_format($total_tunggakan, 0, ',', '.'); 
                                        ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Data Tunggakan Siswa</h4>
                                <div class="text-muted">
                                    Menampilkan: 
                                    <?php 
                                    if ($selected_status == 'menunggak') echo 'Siswa Menunggak';
                                    elseif ($selected_status == 'lancar') echo 'Siswa Lancar';
                                    else echo 'Semua Siswa';
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Total Tagihan</th>
                                        <th>Sudah Bayar</th>
                                        <th>Tunggakan</th>
                                        <th>Nominal Tunggakan</th>
                                        <th>Status</th>
                                        <th>Detail Tunggakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($arrears_data as $index => $row): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($row['nis']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_kelas'] ?? '-'); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-info"><?php echo $row['total_tagihan']; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success"><?php echo $row['sudah_bayar']; ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger"><?php echo $row['tunggakan']; ?></span>
                                        </td>
                                        <td class="text-end">
                                            <span class="text-danger fw-bold">
                                                Rp <?php echo number_format($row['total_nominal_tunggakan'], 0, ',', '.'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($row['tunggakan'] > 0): ?>
                                                <span class="badge bg-danger">Menunggak</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Lancar</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($row['detail_tunggakan']) && $row['tunggakan'] > 0): ?>
                                                <button type="button" class="btn btn-sm btn-outline-info" 
                                                        onclick="showDetail(<?php echo htmlspecialchars(json_encode($row['detail_tunggakan']), ENT_QUOTES, 'UTF-8'); ?>)">
                                                    <i class="mdi mdi-eye"></i> Lihat Detail
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">Tidak ada tunggakan</span>
                                            <?php endif; ?>
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
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Tunggakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="detailTunggakanTable">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Pembayaran</th>
                                <th>Batas Waktu</th>
                                <th>Nominal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?php 
$custom_js = "
    $(document).ready(function() {
        // Initialize main DataTable
        $('#datatable').DataTable({
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 },  // No
                { responsivePriority: 2, targets: 2 },  // Nama Siswa
                { responsivePriority: 3, targets: 3 },  // Kelas
                { responsivePriority: 4, targets: 6 },  // Tunggakan
                { responsivePriority: 5, targets: 8 },  // Status
                { responsivePriority: 6, targets: -1 }, // Detail (last column)
            ]
        });

        // Initialize Select2 for filter dropdowns
        $('#kelas_id').select2({
            placeholder: 'Semua Kelas',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#filterForm') // Ensure dropdown is visible above other elements
        });

        $('#status_filter').select2({
            placeholder: 'Pilih Status',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#filterForm') // Ensure dropdown is visible above other elements
        });

        // Handle modal show event for the detail table
        $('#detailModal').on('shown.bs.modal', function () {
            // Only initialize if it hasn't been initialized yet
            if (!$.fn.DataTable.isDataTable('#detailTunggakanTable')) {
                $('#detailTunggakanTable').DataTable({
                    responsive: true,
                    searching: false, // No search in modal
                    paging: false,    // No pagination in modal
                    info: false,      // No info text in modal
                    ordering: false,  // Disable sorting in modal if not needed
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                    }
                });
            } else {
                // If already initialized, just redraw and adjust columns for responsiveness
                $('#detailTunggakanTable').DataTable().columns.adjust().responsive.recalc();
            }
        });

        // Destroy DataTable when modal is hidden to reset for next open
        $('#detailModal').on('hidden.bs.modal', function () {
            if ($.fn.DataTable.isDataTable('#detailTunggakanTable')) {
                $('#detailTunggakanTable').DataTable().destroy();
                // Clear the tbody content manually after destroying DataTables
                $('#detailTableBody').empty(); 
            }
        });
    });

    function exportToExcel() {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        formData.append('export', 'excel');
        
        const params = new URLSearchParams(formData);
        window.location.href = '" . Router::url('arrears-reports') . "?' + params.toString();
    }

    function showDetail(detailData) {
        const tableBody = document.getElementById('detailTableBody');
        tableBody.innerHTML = ''; // Clear previous content
        
        if (detailData && detailData.length > 0) {
            detailData.forEach(function(item, index) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>\${index + 1}</td>
                    <td>\${item.nama_pembayaran}</td>
                    <td>
                        <span class=\"badge bg-danger\">\${item.batas_waktu_formatted}</span>
                    </td>
                    <td class=\"text-end\">
                        <strong>Rp \${item.nominal_formatted}</strong>
                    </td>
                    <td>
                        <span class=\"badge bg-warning\">Belum Bayar</span>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            tableBody.innerHTML = '<tr><td colspan=\"5\" class=\"text-center text-muted\">Tidak ada data tunggakan</td></tr>';
        }
        
        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();
    }
";

include 'includes/footer.php'; 
?>