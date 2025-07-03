<?php
// Debug: tampilkan role untuk debugging
$userRole = Session::getUserRole();
$userId = Session::get('user_id');

function hasRole($allowedRoles, $currentRole) {
    if (empty($currentRole)) {
        return false;
    }
    
    if (is_array($allowedRoles)) {
        return in_array($currentRole, $allowedRoles);
    }
    
    return $currentRole === $allowedRoles;
}

function isAdmin($currentRole) {
    return $currentRole === 'admin';
}

function isBendahara($currentRole) {
    return in_array($currentRole, ['admin', 'bendahara']);
}

function isOperator($currentRole) {
    return in_array($currentRole, ['admin', 'operator']);
}
?>

<div class="sidebar-left">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="left-menu list-unstyled" id="side-menu">
                <li>
                    <a href="<?php echo Router::url('dashboard'); ?>" class="">
                        <i class="fas fa-desktop"></i>
                        <span>Beranda</span>
                    </a>
                </li>

                <?php if (isBendahara($userRole)): ?>
                <li class="menu-title">Pendapatan dan Pengeluaran</li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fa fa-money-bill-wave"></i>
                        <span>Pendapatan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="<?php echo Router::url('income'); ?>"><i class="mdi mdi-checkbox-blank-circle align-middle"></i>Pendapatan</a></li>
                        <li><a href="<?php echo Router::url('student-payments'); ?>"><i class="mdi mdi-checkbox-blank-circle align-middle"></i>Pembayaran Siswa</a></li>
                        <li><a href="<?php echo Router::url('income-categories'); ?>"><i class="mdi mdi-checkbox-blank-circle align-middle"></i>Kategori Pendapatan</a></li>
                        <li><a href="<?php echo Router::url('payment-types'); ?>"><i class="mdi mdi-checkbox-blank-circle align-middle"></i>Jenis Pembayaran</a></li>
                        <li><a href="<?php echo Router::url('data-pembayaran'); ?>"><i class="mdi mdi-checkbox-blank-circle align-middle"></i>Data Pembayaran</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fa fa-money-bill"></i>
                        <span>Pengeluaran</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="<?php echo Router::url('expenses'); ?>"><i class="mdi mdi-checkbox-blank-circle align-middle"></i>Pengeluaran</a></li>
                        <li><a href="<?php echo Router::url('expense-categories'); ?>"><i class="mdi mdi-checkbox-blank-circle align-middle"></i>Kategori Pengeluaran</a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if (isBendahara($userRole)): ?>
                <li class="menu-title">Manajemen Presensi</li>
                <li><a href="<?php echo Router::url('attendance'); ?>"><i class="fa fa-calendar-check"></i> <span>Presensi Pegawai</span></a></li>
                <?php endif; ?>

                <?php if (isBendahara($userRole)): ?>
                <li class="menu-title">Manajemen Laporan</li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fa fa-chart-line"></i>
                        <span>Laporan Keuangan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="<?php echo Router::url('financial-reports'); ?>"><i class="mdi mdi-checkbox-blank-circle align-middle"></i>Laporan Keuangan Siswa</a></li>
                        <li><a href="<?php echo Router::url('arrears-reports'); ?>"><i class="mdi mdi-checkbox-blank-circle align-middle"></i>Laporan Tunggakan Siswa</a></li>
                    </ul>
                </li>
                <!-- <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fa fa-chart-bar"></i>
                        <span>Laporan Gaji</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="<?php echo Router::url('salary-reports'); ?>"><i class="mdi mdi-checkbox-blank-circle align-middle"></i>Laporan Gaji Pegawai</a></li>
                    </ul>
                </li> -->
                <?php endif; ?>

                <?php if (isOperator($userRole)): ?>
                <li class="menu-title">Buku Induk Siswa dan Pegawai</li>
                <?php if (isAdmin($userRole)): ?>
                <li><a href="<?php echo Router::url('school-identity'); ?>"><i class="fa fa-school"></i> <span>Identitas Sekolah</span></a></li>
                <?php endif; ?>
                <li><a href="<?php echo Router::url('employees'); ?>"><i class="fa fa-users"></i> <span>Pegawai</span></a></li>
                <li><a href="<?php echo Router::url('students'); ?>"><i class="fa fa-user-graduate"></i> <span>Siswa</span></a></li>
                <li><a href="<?php echo Router::url('student-status'); ?>"><i class="fa fa-edit"></i> <span>Ubah Status Siswa</span></a></li>
                <li><a href="<?php echo Router::url('status-history'); ?>"><i class="fa fa-history"></i> <span>Riwayat Status Siswa</span></a></li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fa fa-cogs"></i>
                        <span>Referensi</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="<?php echo Router::url('classes'); ?>"><i class="mdi mdi-checkbox-blank-circle align-middle"></i>Kelas</a></li>
                        <li><a href="<?php echo Router::url('academic-years'); ?>"><i class="mdi mdi-checkbox-blank-circle align-middle"></i>Tahun Ajaran</a></li>
                        <li><a href="<?php echo Router::url('positions'); ?>"><i class="mdi mdi-checkbox-blank-circle align-middle"></i>Jabatan</a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if (isAdmin($userRole)): ?>
                <li class="menu-title">Aplikasi</li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fa fa-cog"></i>
                        <span>Setting</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <!-- <li><a href="<?php echo Router::url('app-settings'); ?>"><i class="mdi mdi-checkbox-blank-circle align-middle"></i>Setting Aplikasi</a></li> -->
                        <li><a href="<?php echo Router::url('user-management'); ?>"><i class="mdi mdi-checkbox-blank-circle align-middle"></i>Manajemen Pengguna</a></li>
                        <li><a href="<?php echo Router::url('logout'); ?>"><i class="mdi mdi-checkbox-blank-circle align-middle"></i>Keluar Aplikasi</a></li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
