<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Detail Pembayaran Siswa</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('student-payments'); ?>">Pembayaran Siswa</a></li>
                                <li class="breadcrumb-item active">Detail</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Informasi Siswa</h4>
                            <div class="d-flex gap-2 align-items-center">
                                <form method="GET" action="<?php echo Router::url('student-payments/detail'); ?>" class="d-inline-flex align-items-center gap-2">
                                    <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                                    <label for="tahun_ajaran_id_filter" class="form-label mb-0 d-none d-sm-block">Tahun Ajaran:</label>
                                    <select id="tahun_ajaran_id_filter" name="tahun_ajaran_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="">Semua</option>
                                        <?php foreach ($academic_years as $year): ?>
                                        <option value="<?php echo $year['id']; ?>" <?php echo $current_tahun_ajaran_id == $year['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($year['tahun_ajaran']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                                <?php
                                $unpaid_count = 0;
                                foreach ($assignments as $assignment) {
                                    if ($assignment['status_pembayaran'] != 'sudah_bayar') {
                                        $unpaid_count++;
                                    }
                                }
                                ?>
                                <?php if ($unpaid_count > 0): ?>
                                <a href="<?php echo Router::url('student-payments/pay?siswa_id=' . $student['id']); ?>" class="btn btn-success btn-sm">
                                    <i class="mdi mdi-currency-usd"></i> Bayar Tagihan
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">NIS:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext"><?php echo htmlspecialchars($student['nis']); ?></p>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Nama Lengkap:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext"><?php echo htmlspecialchars($student['nama_lengkap']); ?></p>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Kelas:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext"><?php echo htmlspecialchars($student['nama_kelas']); ?></p>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Jenis Kelamin:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext"><?php echo $student['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Tahun Masuk:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext"><?php echo $student['tahun_masuk']; ?></p>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Status Siswa:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext">
                                                <span class="badge bg-<?php echo $student['status'] == 'aktif' ? 'success' : 'secondary'; ?>"><?php echo ucfirst($student['status']); ?></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">Nama Wali:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext"><?php echo htmlspecialchars($student['nama_wali'] ?? '-'); ?></p>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">No. HP Wali:</label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext"><?php echo htmlspecialchars($student['no_hp_wali'] ?? '-'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-info-subtle">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="avatar avatar-sm rounded-circle bg-info">
                                    <i class="mdi mdi-file-document-multiple mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-info mb-1">Total Tagihan</p>
                                    <h4 class="mb-0"><?php echo count($assignments); ?></h4>
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
                                    <p class="text-success mb-1">Sudah Lunas</p>
                                    <h4 class="mb-0">
                                        <?php
                                        $lunas = array_filter($assignments, function($a) { return $a['status_pembayaran'] == 'sudah_bayar'; });
                                        echo count($lunas);
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
                                    <i class="mdi mdi-alert-circle mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-warning mb-1">Belum Lunas</p>
                                    <h4 class="mb-0">
                                        <?php
                                        $belum_lunas = array_filter($assignments, function($a) { return $a['status_pembayaran'] != 'sudah_bayar'; });
                                        echo count($belum_lunas);
                                        ?>
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
                                    <i class="mdi mdi-cash mt-1 text-white"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-primary mb-1">Total Nominal</p>
                                    <h4 class="mb-0">
                                        Rp <?php echo number_format(array_sum(array_column($assignments, 'nominal_yang_harus_dibayar')), 0, ',', '.'); ?>
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
                            <h4 class="card-title mb-0">Daftar Tagihan</h4>
                        </div>
                        <div class="card-body">
                            <?php if (empty($assignments)): ?>
                                <div class="text-center py-4">
                                    <i class="mdi mdi-file-document-outline mdi-48px text-muted"></i>
                                    <p class="text-muted">Tidak ada tagihan untuk siswa ini pada tahun ajaran ini.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;" id="assignmentsTable">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Jenis Pembayaran</th>
                                                <th>Nama Pembayaran</th>
                                                <th>Tahun Ajaran</th>
                                                <th>Nominal</th>
                                                <th>Sudah Dibayar</th>
                                                <th>Sisa</th>
                                                <th>Batas Waktu</th>
                                                <th>Cicilan</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($assignments as $index => $assignment): ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $assignment['tipe'] == 'bulanan' ? 'info' : 'secondary'; ?>">
                                                        <?php echo htmlspecialchars($assignment['jenis_nama'] ?? ''); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($assignment['nama_pembayaran'] ?? ''); ?>
                                                    <?php if ($assignment['bulan_pembayaran']): ?>
                                                        <br><small class="text-muted"><?php echo date('F Y', strtotime($assignment['bulan_pembayaran'] . '-01')); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($assignment['tahun_ajaran'] ?? ''); ?></td>
                                                <td>Rp <?php echo number_format($assignment['nominal_yang_harus_dibayar'] ?? 0, 0, ',', '.'); ?></td>
                                                <td>Rp <?php echo number_format($assignment['nominal_yang_sudah_dibayar'] ?? 0, 0, ',', '.'); ?></td>
                                                <td>Rp <?php echo number_format(($assignment['nominal_yang_harus_dibayar'] ?? 0) - ($assignment['nominal_yang_sudah_dibayar'] ?? 0), 0, ',', '.'); ?></td>
                                                <td>
                                                    <?php if ($assignment['batas_waktu']): ?>
                                                        <?php
                                                        $batas_waktu = strtotime($assignment['batas_waktu']);
                                                        $today = strtotime(date('Y-m-d'));
                                                        $is_overdue = $batas_waktu < $today && ($assignment['status_pembayaran'] ?? '') != 'sudah_bayar';
                                                        ?>
                                                        <span class="<?php echo $is_overdue ? 'text-danger' : ''; ?>">
                                                            <?php echo date('d/m/Y', $batas_waktu); ?>
                                                        </span>
                                                        <?php if ($is_overdue): ?>
                                                            <br><small class="text-danger">Terlambat</small>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (($assignment['dapat_dicicil'] ?? false) && ($assignment['jumlah_cicilan'] ?? 0) > 1): ?>
                                                        <span class="badge bg-info">
                                                            <?php echo ($assignment['cicilan_terbayar'] ?? 0); ?>/<?php echo ($assignment['jumlah_cicilan'] ?? 0); ?> Cicilan
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Tidak Cicil</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $statusClass = [
                                                        'belum_bayar' => 'danger',
                                                        'sebagian' => 'warning',
                                                        'sudah_bayar' => 'success'
                                                    ];
                                                    $currentStatus = $assignment['status_pembayaran'] ?? 'belum_bayar';
                                                    $statusText = [
                                                        'belum_bayar' => 'Belum Bayar',
                                                        'sebagian' => 'Sebagian',
                                                        'sudah_bayar' => 'Lunas'
                                                    ];
                                                    ?>
                                                    <span class="badge bg-<?php echo $statusClass[$currentStatus] ?? 'secondary'; ?>">
                                                        <?php echo $statusText[$currentStatus] ?? 'Tidak Diketahui'; ?>
                                                    </span>
                                                    <?php if (($assignment['tanggal_lunas'] ?? false)): ?>
                                                        <br><small class="text-muted">Lunas: <?php echo date('d/m/Y', strtotime($assignment['tanggal_lunas'])); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <?php if (($assignment['status_pembayaran'] ?? '') != 'sudah_bayar'): ?>
                                                            <a href="<?php echo Router::url('student-payments/pay?siswa_id=' . $student['id'] . '&focus=' . ($assignment['id'] ?? '')); ?>" class="btn btn-sm btn-success" title="Lakukan Pembayaran">
                                                                <i class="mdi mdi-currency-usd"></i> Bayar
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if (($assignment['dapat_dicicil'] ?? false) && ($assignment['jumlah_cicilan'] ?? 0) > 1): ?>
                                                            <button type="button" class="btn btn-sm btn-info" onclick="viewInstallments(<?php echo $assignment['id'] ?? 'null'; ?>)" title="Lihat Detail Cicilan">
                                                                <i class="mdi mdi-view-list"></i> Cicilan
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Riwayat Pembayaran</h4>
                        </div>
                        <div class="card-body">
                            <?php if (empty($history)): ?>
                                <div class="text-center py-4">
                                    <i class="mdi mdi-history mdi-48px text-muted"></i>
                                    <p class="text-muted">Belum ada riwayat pembayaran untuk siswa ini pada tahun ajaran ini.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;" id="historyTable">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>No. Kuitansi</th>
                                                <th>Jenis Pembayaran</th>
                                                <th>Periode & Nama Pembayaran</th>
                                                <th>Nominal</th>
                                                <th>Metode</th>
                                                <th>Petugas</th>
                                                <th>Bukti</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $grouped_history = [];
                                            // Group by payment_id
                                            foreach ($history as $h) {
                                                $grouped_history[$h['id']][] = $h;
                                            }
                                            $no = 1;
                                            ?>
                                            <?php foreach ($grouped_history as $payment_id => $payment_details): ?>
                                                <?php $main_payment = $payment_details[0]; // Ambil data utama dari detail pembayaran pertama ?>
                                                <tr>
                                                    <td rowspan="<?php echo count($payment_details); ?>"><?php echo $no++; ?></td>
                                                    <td rowspan="<?php echo count($payment_details); ?>"><?php echo date('d/m/Y', strtotime($main_payment['tanggal_bayar'])); ?></td>
                                                    <td rowspan="<?php echo count($payment_details); ?>"><span class="badge bg-primary"><?php echo htmlspecialchars($main_payment['no_kuitansi'] ?? ''); ?></span></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo ($payment_details[0]['tipe'] ?? '') == 'bulanan' ? 'info' : 'secondary'; ?>">
                                                            <?php echo htmlspecialchars($payment_details[0]['jenis_nama'] ?? 'N/A'); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($payment_details[0]['bulan_pembayaran'])): ?>
                                                            <?php echo date('F Y', strtotime($payment_details[0]['bulan_pembayaran'] . '-01')); ?>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($payment_details[0]['nama_pembayaran'] ?? ''); ?></small>
                                                    </td>
                                                    <td>Rp <?php echo number_format($payment_details[0]['nominal_bayar'] ?? 0, 0, ',', '.'); ?></td>
                                                    <td rowspan="<?php echo count($payment_details); ?>"><?php echo ucfirst($main_payment['metode_bayar'] ?? ''); ?></td>
                                                    <td rowspan="<?php echo count($payment_details); ?>"><?php echo htmlspecialchars($main_payment['user_name'] ?? 'N/A'); ?></td>
                                                    <td rowspan="<?php echo count($payment_details); ?>">
                                                        <?php if (!empty($main_payment['bukti_foto'])): ?>
                                                            <button type="button" class="btn btn-sm btn-info" onclick="viewProof('<?php echo BASE_URL; ?>uploads/payment_proofs/<?php echo htmlspecialchars($main_payment['bukti_foto']); ?>')" title="Lihat Bukti">
                                                                <i class="mdi mdi-eye"></i> Lihat
                                                            </button>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td rowspan="<?php echo count($payment_details); ?>">
                                                        <a href="<?php echo Router::url('student-payments/receipt?payment_id=' . ($payment_id ?? '')); ?>" target="_blank" class="btn btn-sm btn-success" title="Cetak Kuitansi">
                                                            <i class="mdi mdi-printer"></i> Kuitansi
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php for ($i = 1; $i < count($payment_details); $i++): ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-<?php echo ($payment_details[$i]['tipe'] ?? '') == 'bulanan' ? 'info' : 'secondary'; ?>">
                                                            <?php echo htmlspecialchars($payment_details[$i]['jenis_nama'] ?? 'N/A'); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($payment_details[$i]['bulan_pembayaran'])): ?>
                                                            <?php echo date('F Y', strtotime($payment_details[$i]['bulan_pembayaran'] . '-01')); ?>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($payment_details[$i]['nama_pembayaran'] ?? ''); ?></small>
                                                    </td>
                                                    <td>Rp <?php echo number_format($payment_details[$i]['nominal_bayar'] ?? 0, 0, ',', '.'); ?></td>
                                                </tr>
                                                <?php endfor; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="installmentsModal" tabindex="-1" aria-labelledby="installmentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="installmentsModalLabel">Detail Cicilan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="installmentsContent">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Memuat detail cicilan...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="proofModal" tabindex="-1" aria-labelledby="proofModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="proofModalLabel">Bukti Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="proofImage" src="/placeholder.svg" alt="Bukti Pembayaran" class="img-fluid" style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>

<?php
$custom_js = "
    $(document).ready(function() {
        // Initialize DataTables for both tables, if they exist
        if ($.fn.DataTable.isDataTable('#assignmentsTable')) {
            $('#assignmentsTable').DataTable().destroy();
        }
        $('#assignmentsTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            },
            paging: false, // Often for detail pages, you don't need pagination
            info: false,   // And often no info text
            searching: false, // Or searching
            order: [[0, 'asc']], // Order by No
            columnDefs: [
                { responsivePriority: 1, targets: 0 },   // No
                { responsivePriority: 2, targets: 2 },   // Nama Pembayaran
                { responsivePriority: 3, targets: 9 },   // Status
                { responsivePriority: 4, targets: 10 },  // Aksi
            ]
        });

        if ($.fn.DataTable.isDataTable('#historyTable')) {
            $('#historyTable').DataTable().destroy();
        }
        $('#historyTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            },
            paging: true,
            info: true,
            searching: true,
            order: [[1, 'desc']], // Order by Date descending
            columnDefs: [
                { responsivePriority: 1, targets: 0 },   // No
                { responsivePriority: 2, targets: 1 },   // Tanggal
                { responsivePriority: 3, targets: 2 },   // No. Kuitansi
                { responsivePriority: 4, targets: 9 },  // Aksi
                { responsivePriority: 5, targets: 5 },   // Nominal
            ]
        });
    });

    function viewInstallments(assignId) {
        $('#installmentsModal').modal('show');

        // Show loading spinner
        $('#installmentsContent').html(
            '<div class=\"text-center py-5\">' +
                '<div class=\"spinner-border text-primary\" role=\"status\">' +
                    '<span class=\"visually-hidden\">Loading...</span>' +
                '</div>' +
                '<p class=\"mt-2 text-muted\">Memuat detail cicilan...</p>' +
            '</div>'
        );

        // Perform AJAX request to fetch installment details
        $.ajax({
            url: '<?php echo Router::url('api/get_installments'); ?>', // Adjust this URL to your API endpoint
            method: 'GET',
            data: { assignment_id: assignId },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    let html = '<div class=\"table-responsive\"><table class=\"table table-bordered table-striped\"><thead><tr><th>Cicilan Ke-</th><th>Tanggal Bayar</th><th>Nominal</th><th>Metode Bayar</th><th>Status</th><th>Bukti</th></tr></thead><tbody>';
                    response.data.forEach(function(installment, idx) { // Changed to traditional function for older JS environments
                        let statusBadge = '';
                        if (installment.status == 'lunas') {
                            statusBadge = '<span class=\"badge bg-success\">Lunas</span>';
                        } else {
                            statusBadge = '<span class=\"badge bg-warning\">Belum Lunas</span>';
                        }

                        let proofBtn = '-';
                        if (installment.bukti_foto) {
                            proofBtn = '<button type=\"button\" class=\"btn btn-sm btn-info\" onclick=\"viewProof(\'<?php echo BASE_URL; ?>uploads/payment_proofs/' + installment.bukti_foto + '\')\">' +
                                            '<i class=\"mdi mdi-eye\"></i> Lihat' +
                                        '</button>';
                        }

                        html += '<tr>' +
                                    '<td>' + (idx + 1) + '</td>' +
                                    '<td>' + (installment.tanggal_bayar ? new Date(installment.tanggal_bayar).toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric'}) : '-') + '</td>' +
                                    '<td>Rp ' + Number(installment.nominal).toLocaleString('id-ID') + '</td>' +
                                    '<td>' + (installment.metode_bayar ? installment.metode_bayar.charAt(0).toUpperCase() + installment.metode_bayar.slice(1) : '-') + '</td>' +
                                    '<td>' + statusBadge + '</td>' +
                                    '<td>' + proofBtn + '</td>' +
                                '</tr>';
                    });
                    html += '</tbody></table></div>';
                    $('#installmentsContent').html(html);
                } else {
                    $('#installmentsContent').html('<p class=\"text-muted text-center py-4\">Tidak ada detail cicilan ditemukan.</p>');
                }
            },
            error: function() {
                $('#installmentsContent').html('<p class=\"text-danger text-center py-4\">Gagal memuat detail cicilan.</p>');
            }
        });
    }

    function viewProof(imagePath) {
        $('#proofImage').attr('src', imagePath);
        $('#proofModal').modal('show');
    }
";

include 'includes/footer.php';
?>