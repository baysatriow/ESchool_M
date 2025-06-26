<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="fs-16 fw-semibold mb-1 mb-md-2">Selamat Datang, <span class="text-primary"><?php echo Session::get('user_name'); ?>!</span></h4>
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
                                    <h4 class="mb-0"><?php echo $total_students; ?></h4>
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
                                    <h4 class="mb-0"><?php echo $total_employees; ?></h4>
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
                            <h4 class="card-title">Selamat Datang di ESchool_M</h4>
                        </div>
                        <div class="card-body">
                            <p>Sistem Manajemen Sekolah untuk SD IT Ahsanul Fikri</p>
                            <p>Gunakan menu di sebelah kiri untuk mengakses berbagai fitur aplikasi:</p>
                            <ul>
                                <li><strong>Pendapatan dan Pengeluaran:</strong> Kelola keuangan sekolah</li>
                                <li><strong>Manajemen Gaji:</strong> Kelola gaji dan presensi pegawai</li>
                                <li><strong>Buku Induk:</strong> Kelola data siswa dan pegawai</li>
                                <li><strong>Setting:</strong> Konfigurasi aplikasi dan pengguna</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
