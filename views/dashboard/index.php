<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<style>
    .card .dataTables_wrapper .row {
        margin-left: 0;
        margin-right: 0;
    }
    .card .dataTables_wrapper .col-sm-12 {
        padding-left: 0;
        padding-right: 0;
    }
    .table-responsive .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }
    .table-responsive table th,
    .table-responsive table td {
        white-space: normal;
    }
   
    .table-responsive table th:first-child,
    .table-responsive table td:first-child {
        width: 50px;
    }
</style>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="fs-16 fw-semibold mb-1 mb-md-2">Selamat Datang, <span class="text-primary"><?php echo htmlspecialchars(Session::get('user_name')); ?>!</span></h4>
                            <p class="text-muted mb-0">Berikut adalah ringkasan aktivitas sekolah hari ini.</p>
                        </div>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">ESchool_M</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-primary-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm avatar-label-primary">
                                    <i class="mdi mdi-account-group mt-1"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-primary mb-1">Total Siswa</p>
                                    <h4 class="mb-0"><?php echo number_format($total_students); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-success-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm avatar-label-success">
                                    <i class="mdi mdi-cash-usd-outline mt-1"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-success mb-1">Pendapatan Bulan Ini</p>
                                    <h4 class="mb-0">Rp <?php echo number_format($monthly_income, 0, ',', '.'); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-warning-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm avatar-label-warning">
                                    <i class="mdi mdi-cash-minus mt-1"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-warning mb-1">Pengeluaran Bulan Ini</p>
                                    <h4 class="mb-0">Rp <?php echo number_format($monthly_expense, 0, ',', '.'); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-info-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm avatar-label-info">
                                    <i class="mdi mdi-account-tie mt-1"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-info mb-1">Total Pegawai</p>
                                    <h4 class="mb-0"><?php echo number_format($total_employees); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-4">
                    <div class="card <?php echo $net_income >= 0 ? 'bg-success-subtle' : 'bg-danger-subtle'; ?>">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm <?php echo $net_income >= 0 ? 'avatar-label-success' : 'avatar-label-danger'; ?>">
                                    <i class="mdi <?php echo $net_income >= 0 ? 'mdi-trending-up' : 'mdi-trending-down'; ?> mt-1"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="<?php echo $net_income >= 0 ? 'text-success' : 'text-danger'; ?> mb-1">Laba</p>
                                    <h4 class="mb-0">Rp <?php echo number_format($net_income, 0, ',', '.'); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tren Keuangan 12 Bulan Terakhir</h4>
                        </div>
                        <div class="card-body">
                            <div id="financial-trends-chart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Status Pembayaran Siswa</h4>
                        </div>
                        <div class="card-body">
                            <div id="payment-status-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Kategori Pengeluaran</h4>
                        </div>
                        <div class="card-body">
                            <div id="expense-categories-chart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Sumber Pendapatan</h4>
                        </div>
                        <div class="card-body">
                            <div id="income-sources-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Transaksi Terbaru</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="recentTransactionsTable" class="table table-hover table-striped dt-responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Jenis</th>
                                            <th>Referensi</th>
                                            <th>Deskripsi</th>
                                            <th>Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recent_transactions)): ?>
                                            <?php foreach ($recent_transactions as $transaction): ?>
                                            <tr>
                                                <td><?php echo date('d/m/Y', strtotime($transaction['date'])); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo htmlspecialchars($transaction['status']); ?>">
                                                        <?php echo htmlspecialchars($transaction['type']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($transaction['reference']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($transaction['description'], 0, 50)) . (strlen($transaction['description']) > 50 ? '...' : ''); ?></td>
                                                <td>Rp <?php echo number_format($transaction['amount'], 0, ',', '.'); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">Tidak ada transaksi terbaru</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tunggakan Terbesar</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="outstandingPaymentsTable" class="table table-sm table-hover table-striped dt-responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Siswa</th>
                                            <th>Tunggakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($outstanding_payments)): ?>
                                            <?php foreach ($outstanding_payments as $payment): ?>
                                            <tr>
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($payment['nama_lengkap']); ?></strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($payment['nis']); ?> - <?php echo htmlspecialchars($payment['nama_kelas']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-danger fw-bold">
                                                        Rp <?php echo number_format($payment['outstanding'], 0, ',', '.'); ?>
                                                    </span><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($payment['jumlah_tagihan']); ?> tagihan</small>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">Tidak ada tunggakan</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Distribusi Status Siswa</h4>
                        </div>
                        <div class="card-body">
                            <div id="student-status-chart"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Perbandingan Tahunan</h4>
                        </div>
                        <div class="card-body">
                            <div id="yearly-comparison-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/libs/apexcharts/apexcharts.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prepare data for charts with proper JSON encoding
    var financialTrendsData = <?php echo json_encode($financial_trends, JSON_NUMERIC_CHECK); ?>;
    var paymentStatusData = <?php echo json_encode($payment_status, JSON_NUMERIC_CHECK); ?>;
    var expenseCategoriesData = <?php echo json_encode($expense_categories, JSON_NUMERIC_CHECK); ?>;
    var incomeSourcesData = <?php echo json_encode($income_sources, JSON_NUMERIC_CHECK); ?>;
    var studentStatusData = <?php echo json_encode($student_status, JSON_NUMERIC_CHECK); ?>;
    var yearlyComparisonData = <?php echo json_encode($yearly_comparison, JSON_NUMERIC_CHECK); ?>;

    // --- Chart Initializations ---

    // Financial Trends Chart
    if (financialTrendsData && financialTrendsData.length > 0) {
        var financialTrendsOptions = {
            series: [{
                name: 'Pendapatan',
                data: financialTrendsData.map(function(item) { return item.income || 0; })
            }, {
                name: 'Pengeluaran',
                data: financialTrendsData.map(function(item) { return item.expenses || 0; })
            }, {
                name: 'Laba',
                data: financialTrendsData.map(function(item) { return item.net || 0; })
            }],
            chart: {
                height: 350,
                type: 'line',
                zoom: { enabled: false }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'straight', width: 2 },
            title: { text: 'Tren Keuangan 12 Bulan Terakhir', align: 'left' },
            grid: {
                row: { colors: ['#f3f3f3', 'transparent'], opacity: 0.5 }
            },
            xaxis: {
                categories: financialTrendsData.map(function(item) { return item.month; })
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            },
            colors: ['#28a745', '#dc3545', '#007bff'],
            tooltip: {
                y: {
                    formatter: function (val) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            }
        };

        var financialTrendsChart = new ApexCharts(document.querySelector('#financial-trends-chart'), financialTrendsOptions);
        financialTrendsChart.render();
    } else {
        document.querySelector('#financial-trends-chart').innerHTML = '<p class="text-center text-muted py-3">Tidak ada data tren keuangan</p>';
    }

    // Payment Status Chart
    var totalPayments = paymentStatusData.lunas + paymentStatusData.sebagian + paymentStatusData.belum_bayar;
    if (totalPayments > 0) {
        var paymentStatusOptions = {
            series: [paymentStatusData.lunas, paymentStatusData.sebagian, paymentStatusData.belum_bayar],
            chart: { width: 380, type: 'pie' },
            labels: ['Lunas', 'Sebagian', 'Belum Bayar'],
            colors: ['#28a745', '#ffc107', '#dc3545'],
            responsive: [{
                breakpoint: 480,
                options: { chart: { width: 200 }, legend: { position: 'bottom' } }
            }],
            legend: { position: 'bottom' }
        };

        var paymentStatusChart = new ApexCharts(document.querySelector('#payment-status-chart'), paymentStatusOptions);
        paymentStatusChart.render();
    } else {
        document.querySelector('#payment-status-chart').innerHTML = '<p class="text-center text-muted py-3">Tidak ada data pembayaran</p>';
    }

    // Expense Categories Chart
    if (expenseCategoriesData && expenseCategoriesData.length > 0) {
        var expenseCategoriesOptions = {
            series: [{
                data: expenseCategoriesData.map(function(item) { return item.total || 0; })
            }],
            chart: { type: 'bar', height: 350 },
            plotOptions: { bar: { borderRadius: 4, horizontal: true } },
            dataLabels: { enabled: false },
            xaxis: {
                categories: expenseCategoriesData.map(function(item) { return item.nama_kategori; }),
                labels: {
                    formatter: function (val) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            },
            colors: ['#dc3545'],
            tooltip: {
                x: {
                    formatter: function (val) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            }
        };

        var expenseCategoriesChart = new ApexCharts(document.querySelector('#expense-categories-chart'), expenseCategoriesOptions);
        expenseCategoriesChart.render();
    } else {
        document.querySelector('#expense-categories-chart').innerHTML = '<p class="text-center text-muted py-3">Tidak ada data pengeluaran</p>';
    }

    // Income Sources Chart
    if (incomeSourcesData && incomeSourcesData.length > 0) {
        var incomeSourcesOptions = {
            series: incomeSourcesData.map(function(item) { return item.total || 0; }),
            chart: { width: 380, type: 'donut' },
            labels: incomeSourcesData.map(function(item) { return item.source; }),
            colors: ['#007bff', '#28a745', '#17a2b8', '#ffc107', '#6f42c1'],
            responsive: [{
                breakpoint: 480,
                options: { chart: { width: 200 }, legend: { position: 'bottom' } }
            }],
            legend: { position: 'bottom' }
        };

        var incomeSourcesChart = new ApexCharts(document.querySelector('#income-sources-chart'), incomeSourcesOptions);
        incomeSourcesChart.render();
    } else {
        document.querySelector('#income-sources-chart').innerHTML = '<p class="text-center text-muted py-3">Tidak ada data pendapatan</p>';
    }

    // Student Status Chart
    if (studentStatusData && studentStatusData.length > 0) {
        var studentStatusOptions = {
            series: studentStatusData.map(function(item) { return item.total || 0; }),
            chart: { width: 380, type: 'pie' },
            labels: studentStatusData.map(function(item) { return item.status; }),
            colors: ['#28a745', '#007bff', '#ffc107', '#dc3545', '#6c757d'],
            responsive: [{
                breakpoint: 480,
                options: { chart: { width: 200 }, legend: { position: 'bottom' } }
            }],
            legend: { position: 'bottom' }
        };

        var studentStatusChart = new ApexCharts(document.querySelector('#student-status-chart'), studentStatusOptions);
        studentStatusChart.render();
    } else {
        document.querySelector('#student-status-chart').innerHTML = '<p class="text-center text-muted py-3">Tidak ada data siswa</p>';
    }

    // Yearly Comparison Chart
    if (yearlyComparisonData && yearlyComparisonData.length > 0) {
        var yearlyComparisonOptions = {
            series: [{
                name: 'Pendapatan',
                data: yearlyComparisonData.map(function(item) { return item.total_income || 0; })
            }, {
                name: 'Pengeluaran',
                data: yearlyComparisonData.map(function(item) { return item.total_expense || 0; })
            }],
            chart: { type: 'bar', height: 350 },
            plotOptions: {
                bar: { horizontal: false, columnWidth: '55%', endingShape: 'rounded' }
            },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            xaxis: {
                categories: yearlyComparisonData.map(function(item) { return item.year; })
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            },
            fill: { opacity: 1 },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            },
            colors: ['#28a745', '#dc3545']
        };

        var yearlyComparisonChart = new ApexCharts(document.querySelector('#yearly-comparison-chart'), yearlyComparisonOptions);
        yearlyComparisonChart.render();
    } else {
        document.querySelector('#yearly-comparison-chart').innerHTML = '<p class="text-center text-muted py-3">Tidak ada data perbandingan tahunan</p>';
    }

    // --- DataTable Initializations ---

    // Recent Transactions Table
    $('#recentTransactionsTable').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
        },
        columnDefs: [
            { responsivePriority: 1, targets: 0 },
            { responsivePriority: 2, targets: 4 },
            { responsivePriority: 3, targets: 1 },
            { responsivePriority: 4, targets: 3 },
            { responsivePriority: 5, targets: 2 } 
        ]
    });

    // Outstanding Payments Table
    $('#outstandingPaymentsTable').DataTable({
        responsive: true,
        order: [[1, 'desc']],
        paging: false,      
        searching: false,    
        info: false,        
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
        },
        columnDefs: [
            { responsivePriority: 1, targets: 0 }, 
            { responsivePriority: 2, targets: 1 }  
        ]
    });
});
</script>

<?php include 'includes/footer.php'; ?>