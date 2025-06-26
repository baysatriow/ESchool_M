<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Laporan Keuangan</h4>
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

            <!-- Filter Form -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" action="<?php echo Router::url('financial-reports'); ?>">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-filter"></i> Filter
                                            </button>
                                            <button type="button" class="btn btn-success" onclick="printReport()">
                                                <i class="mdi mdi-printer"></i> Print
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row">
                <div class="col-xl-4">
                    <div class="card bg-success-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm avatar-label-success">
                                    <i class="mdi mdi-cash-plus mt-1"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-success mb-1">Total Pendapatan</p>
                                    <h4 class="mb-0">
                                        Rp <?php 
                                        $total_income = array_sum(array_column($payment_data, 'total_pendapatan'));
                                        echo number_format($total_income, 0, ',', '.'); 
                                        ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-4">
                    <div class="card bg-danger-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm avatar-label-danger">
                                    <i class="mdi mdi-cash-minus mt-1"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-danger mb-1">Total Pengeluaran</p>
                                    <h4 class="mb-0">
                                        Rp <?php 
                                        $total_expense = array_sum(array_column($expense_data, 'total_pengeluaran'));
                                        echo number_format($total_expense, 0, ',', '.'); 
                                        ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-4">
                    <div class="card bg-info-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm avatar-label-info">
                                    <i class="mdi mdi-calculator mt-1"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-info mb-1">Saldo</p>
                                    <h4 class="mb-0 <?php echo ($total_income - $total_expense) >= 0 ? 'text-success' : 'text-danger'; ?>">
                                        Rp <?php echo number_format($total_income - $total_expense, 0, ',', '.'); ?>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Report -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Detail Pendapatan</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th class="text-end">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($payment_data as $row): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                            <td class="text-end text-success">
                                                Rp <?php echo number_format($row['total_pendapatan'], 0, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Detail Pengeluaran</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th class="text-end">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($expense_data as $row): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                            <td class="text-end text-danger">
                                                Rp <?php echo number_format($row['total_pengeluaran'], 0, ',', '.'); ?>
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

<?php 
$custom_js = "
    function printReport() {
        window.print();
    }
";

include 'includes/footer.php'; 
?>
