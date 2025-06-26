<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

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
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Data Tunggakan Siswa</h4>
                                <button type="button" class="btn btn-success" onclick="printData()">
                                    <i class="mdi mdi-printer"></i> Print
                                </button>
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
                                        <th>Total Bayar</th>
                                        <th>Jumlah Pembayaran</th>
                                        <th>Status Tunggakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($arrears_data as $index => $row): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($row['nis']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_kelas'] ?? '-'); ?></td>
                                        <td>Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                                        <td>
                                            <span class="badge badge-info"><?php echo $row['jumlah_pembayaran']; ?> kali</span>
                                        </td>
                                        <td>
                                            <?php 
                                            $current_month = date('n');
                                            $expected_payments = $current_month; // Assuming monthly payments
                                            $actual_payments = $row['jumlah_pembayaran'];
                                            $arrears = $expected_payments - $actual_payments;
                                            ?>
                                            <?php if ($arrears > 0): ?>
                                                <span class="badge badge-danger">Tunggakan <?php echo $arrears; ?> bulan</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">Lunas</span>
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
    
    function printData() {
        window.print();
    }
";

include 'includes/footer.php'; 
?>
