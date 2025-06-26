<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Presensi Pegawai</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item active">Presensi Pegawai</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date Filter -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" action="<?php echo Router::url('attendance'); ?>">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="date" class="form-label">Tanggal</label>
                                        <input type="date" class="form-control" id="date" name="date" value="<?php echo $selected_date; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-filter"></i> Filter
                                            </button>
                                            <button type="button" class="btn btn-success" onclick="printData()">
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

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Presensi Cepat</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <form method="POST" action="<?php echo Router::url('attendance/clock-in'); ?>" class="d-inline">
                                        <div class="input-group">
                                            <select name="pegawai_id" class="form-control" required>
                                                <option value="">Pilih Pegawai untuk Clock In</option>
                                                <?php foreach ($employees as $emp): ?>
                                                <option value="<?php echo $emp['id']; ?>">
                                                    <?php echo htmlspecialchars($emp['nama_lengkap'] . ' - ' . $emp['nama_jabatan']); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="btn btn-success">
                                                <i class="mdi mdi-clock-in"></i> Clock In
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form method="POST" action="<?php echo Router::url('attendance/clock-out'); ?>" class="d-inline">
                                        <div class="input-group">
                                            <select name="pegawai_id" class="form-control" required>
                                                <option value="">Pilih Pegawai untuk Clock Out</option>
                                                <?php foreach ($employees as $emp): ?>
                                                <option value="<?php echo $emp['id']; ?>">
                                                    <?php echo htmlspecialchars($emp['nama_lengkap'] . ' - ' . $emp['nama_jabatan']); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="btn btn-warning">
                                                <i class="mdi mdi-clock-out"></i> Clock Out
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Data -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Presensi - <?php echo date('d/m/Y', strtotime($selected_date)); ?></h4>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-hover table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIP</th>
                                        <th>Nama Pegawai</th>
                                        <th>Jabatan</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Keluar</th>
                                        <th>Status</th>
                                        <th>Durasi Kerja</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($attendances as $index => $row): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($row['nip']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama_jabatan']); ?></td>
                                        <td>
                                            <?php if ($row['jam_masuk']): ?>
                                                <span class="badge badge-success"><?php echo $row['jam_masuk']; ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['jam_keluar']): ?>
                                                <span class="badge badge-info"><?php echo $row['jam_keluar']; ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $row['status'] == 'hadir' ? 'success' : 
                                                    ($row['status'] == 'izin' ? 'warning' : 
                                                    ($row['status'] == 'sakit' ? 'info' : 'danger')); 
                                            ?>">
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            if ($row['jam_masuk'] && $row['jam_keluar']) {
                                                $masuk = new DateTime($row['jam_masuk']);
                                                $keluar = new DateTime($row['jam_keluar']);
                                                $durasi = $masuk->diff($keluar);
                                                echo $durasi->format('%h jam %i menit');
                                            } else {
                                                echo '-';
                                            }
                                            ?>
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
