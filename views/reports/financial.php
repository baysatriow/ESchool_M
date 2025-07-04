<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<style>

    .table-responsive {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #e9ecef; 
    }
    .card-body .table thead th {
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 10;
        border-bottom: 1px solid #dee2e6;
    }
</style>
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Laporan Keuangan Sekolah</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item">Laporan</li>
                                <li class="breadcrumb-item active">Laporan Keuangan</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" action="<?php echo Router::url('financial-reports'); ?>" id="filterForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
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
                    <div class="card bg-success-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm rounded-circle bg-success">
                                    <i class="mdi mdi-cash-plus mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-success mb-1">Total Pendapatan Lain</p>
                                    <h4 class="mb-0">
                                        Rp <?php echo number_format($financial_data['summary']['total_income'], 0, ',', '.'); ?>
                                    </h4>
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
                                    <i class="mdi mdi-school mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-primary mb-1">Pembayaran Siswa</p>
                                    <h4 class="mb-0">
                                        Rp <?php echo number_format($financial_data['summary']['total_payment'], 0, ',', '.'); ?>
                                    </h4>
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
                                    <i class="mdi mdi-cash-minus mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-danger mb-1">Total Pengeluaran</p>
                                    <h4 class="mb-0">
                                        Rp <?php echo number_format($financial_data['summary']['total_expense'], 0, ',', '.'); ?>
                                    </h4>
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
                                    <i class="mdi mdi-calculator mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-info mb-1">Saldo Akhir</p>
                                    <h4 class="mb-0 <?php echo $financial_data['summary']['saldo'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                        Rp <?php echo number_format($financial_data['summary']['saldo'], 0, ',', '.'); ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Detail Pendapatan Lain</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="incomeDataTable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Kategori</th>
                                            <th class="text-end">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($financial_data['income_data'] as $row): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                            <td><?php echo htmlspecialchars($row['sub_kategori']); ?></td>
                                            <td class="text-end text-success">
                                                Rp <?php echo number_format($row['nominal'], 0, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($financial_data['income_data'])): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Tidak ada data</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Detail Pembayaran Siswa</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="paymentDataTable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Jenis Pembayaran</th>
                                            <th class="text-end">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($financial_data['payment_data'] as $row): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                            <td><?php echo htmlspecialchars($row['sub_kategori']); ?></td>
                                            <td class="text-end text-primary">
                                                Rp <?php echo number_format($row['nominal'], 0, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($financial_data['payment_data'])): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Tidak ada data</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Detail Pengeluaran</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="expenseDataTable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Kategori</th>
                                            <th class="text-end">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($financial_data['expense_data'] as $row): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                            <td><?php echo htmlspecialchars($row['sub_kategori']); ?></td>
                                            <td class="text-end text-danger">
                                                Rp <?php echo number_format($row['nominal'], 0, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($financial_data['expense_data'])): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Tidak ada data</td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$custom_js = "
    $(document).ready(function() {
        // Initialize DataTables for each report table
        $('#incomeDataTable').DataTable({
            responsive: true,
            searching: false, // Disable search for these smaller tables
            paging: true,    // Enable pagination
            info: false,     // Disable info text (e.g., 'Showing 1 to 10 of X entries')
            order: [[0, 'desc']], // Order by date descending
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            }
        });

        $('#paymentDataTable').DataTable({
            responsive: true,
            searching: false,
            paging: true,
            info: false,
            order: [[0, 'desc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            }
        });

        $('#expenseDataTable').DataTable({
            responsive: true,
            searching: false,
            paging: true,
            info: false,
            order: [[0, 'desc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            }
        });
    });

    function exportToExcel() {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        formData.append('export', 'excel');
        
        const params = new URLSearchParams(formData);
        window.location.href = '" . Router::url('financial-reports') . "?' + params.toString();
    }
    
    // function printReport() {
    //     window.print();
    // }
";

include 'includes/footer.php'; 
?>