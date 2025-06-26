<div class="row">
    <div class="col-md-6">
        <h6>Informasi Siswa</h6>
        <table class="table table-sm">
            <tr>
                <td>NIS</td>
                <td>: <?php echo htmlspecialchars($student['nis']); ?></td>
            </tr>
            <tr>
                <td>Nama</td>
                <td>: <?php echo htmlspecialchars($student['nama_lengkap']); ?></td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>: <?php echo htmlspecialchars($student['nama_kelas'] ?? '-'); ?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h6>Tahun Ajaran Aktif</h6>
        <p><strong><?php echo htmlspecialchars($active_year['tahun_ajaran']); ?></strong></p>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-12">
        <h6>Jadwal Pembayaran</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Jenis Pembayaran</th>
                        <th>Tipe</th>
                        <th>Nominal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payment_schedule as $schedule): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($schedule['nama_pembayaran']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $schedule['tipe'] == 'bulanan' ? 'info' : 'warning'; ?>">
                                <?php echo ucfirst($schedule['tipe']); ?>
                            </span>
                        </td>
                        <td>Rp <?php echo number_format($schedule['nominal'], 0, ',', '.'); ?></td>
                        <td>
                            <?php if ($schedule['tipe'] == 'bulanan'): ?>
                                <small class="text-muted">Per bulan</small>
                            <?php else: ?>
                                <small class="text-muted">Sekali bayar</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-12">
        <h6>Riwayat Pembayaran</h6>
        <?php if (empty($payment_history)): ?>
            <p class="text-muted">Belum ada pembayaran</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No. Kuitansi</th>
                            <th>Jenis Pembayaran</th>
                            <th>Periode</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payment_history as $history): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($history['tanggal_bayar'])); ?></td>
                            <td><?php echo htmlspecialchars($history['no_kuitansi']); ?></td>
                            <td><?php echo htmlspecialchars($history['nama_pembayaran']); ?></td>
                            <td>
                                <?php if ($history['tipe'] == 'bulanan' && $history['bulan_pembayaran']): ?>
                                    <?php 
                                    $months = [
                                        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                                        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Ags',
                                        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
                                    ];
                                    echo $months[$history['bulan_pembayaran']] . ' ' . $history['tahun_pembayaran'];
                                    ?>
                                <?php else: ?>
                                    <?php echo $history['tahun_pembayaran']; ?>
                                <?php endif; ?>
                            </td>
                            <td>Rp <?php echo number_format($history['nominal_bayar'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
