<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Form Pembayaran</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('student-payments'); ?>">Pembayaran Siswa</a></li>
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('student-payments/detail?id=' . $student['id']); ?>">Detail</a></li>
                                <li class="breadcrumb-item active">Pembayaran</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Info -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Informasi Siswa</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="120"><strong>NIS</strong></td>
                                            <td>: <?php echo htmlspecialchars($student['nis']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nama Lengkap</strong></td>
                                            <td>: <?php echo htmlspecialchars($student['nama_lengkap']); ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="120"><strong>Kelas</strong></td>
                                            <td>: <?php echo htmlspecialchars($student['nama_kelas']); ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nama Wali</strong></td>
                                            <td>: <?php echo htmlspecialchars($student['nama_wali'] ?? '-'); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="row">
                <div class="col-12">
                    <form method="POST" action="<?php echo Router::url('student-payments/process-payment'); ?>" enctype="multipart/form-data" id="paymentForm">
                        <input type="hidden" name="siswa_id" value="<?php echo $student['id']; ?>">
                        
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Pilih Tagihan yang Akan Dibayar</h4>
                            </div>
                            <div class="card-body">
                                <?php if (empty($assignments)): ?>
                                    <div class="text-center py-4">
                                        <i class="mdi mdi-check-circle mdi-48px text-success"></i>
                                        <h5 class="text-success">Semua Tagihan Sudah Lunas!</h5>
                                        <p class="text-muted">Siswa ini tidak memiliki tagihan yang belum dibayar.</p>
                                        <a href="<?php echo Router::url('student-payments/detail?id=' . $student['id']); ?>" class="btn btn-primary">
                                            <i class="mdi mdi-arrow-left"></i> Kembali ke Detail
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered">
                                            <thead>
                                                <tr>
                                                    <th width="50">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                                            <label class="form-check-label" for="selectAll">Pilih</label>
                                                        </div>
                                                    </th>
                                                    <th>Jenis Pembayaran</th>
                                                    <th>Nama Pembayaran</th>
                                                    <th>Nominal Tagihan</th>
                                                    <th>Sudah Dibayar</th>
                                                    <th>Sisa Tagihan</th>
                                                    <th>Cicilan</th>
                                                    <th>Nominal Bayar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($assignments as $index => $assignment): ?>
                                                <tr class="assignment-row" data-assignment-id="<?php echo $assignment['id']; ?>">
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input assignment-checkbox" type="checkbox" 
                                                                   name="payment_items[<?php echo $index; ?>][selected]" 
                                                                   value="1" 
                                                                   id="assignment_<?php echo $assignment['id']; ?>"
                                                                   onchange="togglePaymentRow(this, <?php echo $index; ?>)">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-<?php echo $assignment['tipe'] == 'bulanan' ? 'info' : 'secondary'; ?>">
                                                            <?php echo htmlspecialchars($assignment['jenis_nama']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($assignment['nama_pembayaran']); ?>
                                                        <?php if (!empty($assignment['bulan_pembayaran'])): ?>
                                                            <br><small class="text-muted"><?php echo date('F Y', strtotime($assignment['bulan_pembayaran'] . '-01')); ?></small>
                                                        <?php endif; ?>
                                                        
                                                        <!-- Hidden inputs for assignment data -->
                                                        <input type="hidden" name="payment_items[<?php echo $index; ?>][assign_pembayaran_id]" value="<?php echo $assignment['id']; ?>">
                                                        <input type="hidden" name="payment_items[<?php echo $index; ?>][jenis_pembayaran_id]" value="<?php echo $assignment['jenis_pembayaran_id']; ?>">
                                                        <?php
                                                        // Inisialisasi variabel untuk menghindari error jika kunci tidak ada
                                                        $bulan_val = '';
                                                        $tahun_val = '';

                                                        if (isset($assignment['bulan_pembayaran']) && !empty($assignment['bulan_pembayaran'])) {
                                                            $date_string = $assignment['bulan_pembayaran']; // Contoh: '2025-07'

                                                            // Metode 1: Menggunakan explode() (lebih sederhana untuk format YYYY-MM)
                                                            $parts = explode('-', $date_string);
                                                            if (count($parts) === 2) {
                                                                $tahun_val = $parts[0]; // '2025'
                                                                $bulan_val = $parts[1]; // '07'
                                                            }
                                                        } else {
                                                            $bulan_val = date('m'); // Format: 'MM', misal '07' untuk Juli
                                                            $tahun_val = date('Y');  // Format: 'YYYY', misal '2025'
                                                        }
                                                        ?>

                                                        <input type="hidden" name="payment_items[<?php echo $index; ?>][bulan_pembayaran]" value="<?php echo htmlspecialchars($bulan_val); ?>">
                                                        <input type="hidden" name="payment_items[<?php echo $index; ?>][tahun_pembayaran]" value="<?php echo htmlspecialchars($tahun_val); ?>">
                                                    </td>
                                                    <td>Rp <?php echo number_format($assignment['nominal_yang_harus_dibayar'], 0, ',', '.'); ?></td>
                                                    <td>Rp <?php echo number_format($assignment['nominal_yang_sudah_dibayar'], 0, ',', '.'); ?></td>
                                                    <td class="sisa-tagihan">Rp <?php echo number_format($assignment['nominal_yang_harus_dibayar'] - $assignment['nominal_yang_sudah_dibayar'], 0, ',', '.'); ?></td>
                                                    <td>
                                                        <?php if ($assignment['dapat_dicicil'] && isset($assignment['installments']) && !empty($assignment['installments'])): ?>
                                                            <select class="form-control form-control-sm cicilan-select" 
                                                                    name="payment_items[<?php echo $index; ?>][cicilan_ke]" 
                                                                    onchange="updateInstallmentAmount(this, <?php echo $index; ?>)" 
                                                                    disabled>
                                                                <option value="0">Bayar Penuh</option>
                                                                <?php foreach ($assignment['installments'] as $installment): ?>
                                                                    <?php if ($installment['status'] == 'belum_bayar'): ?>
                                                                    <option value="<?php echo $installment['cicilan_ke']; ?>" 
                                                                            data-nominal="<?php echo $installment['nominal_cicilan']; ?>">
                                                                        Cicilan ke-<?php echo $installment['cicilan_ke']; ?> 
                                                                        (Rp <?php echo number_format($installment['nominal_cicilan'], 0, ',', '.'); ?>)
                                                                    </option>
                                                                    <?php endif; ?>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">Tidak Cicil</span>
                                                            <input type="hidden" name="payment_items[<?php echo $index; ?>][cicilan_ke]" value="0">
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <input type="number" 
                                                               class="form-control form-control-sm nominal-bayar" 
                                                               name="payment_items[<?php echo $index; ?>][nominal_bayar]" 
                                                               value="<?php echo $assignment['nominal_yang_harus_dibayar'] - $assignment['nominal_yang_sudah_dibayar']; ?>"
                                                               max="<?php echo $assignment['nominal_yang_harus_dibayar'] - $assignment['nominal_yang_sudah_dibayar']; ?>"
                                                               min="1"
                                                               onchange="updateTotal()"
                                                               disabled>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-info">
                                                    <td colspan="7"><strong>Total Pembayaran</strong></td>
                                                    <td><strong id="totalPembayaran">Rp 0</strong></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($assignments)): ?>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Detail Pembayaran</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tanggal_bayar" class="form-label">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="tanggal_bayar" name="tanggal_bayar" value="<?php echo date('Y-m-d'); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="metode_bayar" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                                            <select class="form-control" id="metode_bayar" name="metode_bayar" required>
                                                <option value="">Pilih Metode Pembayaran</option>
                                                <option value="tunai">Tunai</option>
                                                <option value="transfer">Transfer Bank</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="total_bayar" class="form-label">Total Pembayaran <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="total_bayar" name="total_bayar" readonly required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="bukti_foto" class="form-label">Bukti Pembayaran</label>
                                            <input type="file" class="form-control" id="bukti_foto" name="bukti_foto" accept="image/*">
                                            <small class="text-muted">Upload bukti pembayaran (opsional)</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between">
                                            <a href="<?php echo Router::url('student-payments/detail?id=' . $student['id']); ?>" class="btn btn-secondary">
                                                <i class="mdi mdi-arrow-left"></i> Kembali
                                            </a>
                                            <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                                                <i class="mdi mdi-content-save"></i> Proses Pembayaran
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php
$custom_js = "
    $(document).ready(function() {
        updateTotal();
        
        // Focus on specific assignment if provided
        const urlParams = new URLSearchParams(window.location.search);
        const focusId = urlParams.get('focus');
        if (focusId) {
            const row = document.querySelector('.assignment-row[data-assignment-id=\"' + focusId + '\"]');
            if (row) {
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                row.style.backgroundColor = '#fff3cd';
                setTimeout(() => {
                    row.style.backgroundColor = '';
                }, 3000);
            }
        }
    });
    
    function togglePaymentRow(checkbox, index) {
        const row = checkbox.closest('tr');
        const nominalInput = row.querySelector('.nominal-bayar');
        const cicilanSelect = row.querySelector('.cicilan-select');
        
        if (checkbox.checked) {
            nominalInput.disabled = false;
            if (cicilanSelect) {
                cicilanSelect.disabled = false;
            }
            row.classList.add('table-warning');
        } else {
            nominalInput.disabled = true;
            if (cicilanSelect) {
                cicilanSelect.disabled = true;
                cicilanSelect.value = '0';
            }
            row.classList.remove('table-warning');
        }
        
        updateTotal();
        updateSubmitButton();
    }
    
    function updateInstallmentAmount(select, index) {
        const selectedOption = select.options[select.selectedIndex];
        const nominalInput = select.closest('tr').querySelector('.nominal-bayar');
        
        if (select.value === '0') {
            // Bayar penuh
            const sisaTagihan = select.closest('tr').querySelector('.sisa-tagihan').textContent;
            const nominal = parseInt(sisaTagihan.replace(/[^0-9]/g, ''));
            nominalInput.value = nominal;
            nominalInput.max = nominal;
        } else {
            // Bayar cicilan
            const nominal = parseInt(selectedOption.getAttribute('data-nominal'));
            nominalInput.value = nominal;
            nominalInput.max = nominal;
        }
        
        updateTotal();
    }
    
    function updateTotal() {
        let total = 0;
        const checkedRows = document.querySelectorAll('.assignment-checkbox:checked');
        
        checkedRows.forEach(function(checkbox) {
            const row = checkbox.closest('tr');
            const nominalInput = row.querySelector('.nominal-bayar');
            if (nominalInput && !nominalInput.disabled) {
                total += parseInt(nominalInput.value) || 0;
            }
        });
        
        document.getElementById('totalPembayaran').textContent = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('total_bayar').value = total;
        
        updateSubmitButton();
    }
    
    function updateSubmitButton() {
        const checkedRows = document.querySelectorAll('.assignment-checkbox:checked');
        const submitBtn = document.getElementById('submitBtn');
        
        if (checkedRows.length > 0) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }
    
    // Select All functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.assignment-checkbox');
        checkboxes.forEach(function(checkbox, index) {
            checkbox.checked = this.checked;
            togglePaymentRow(checkbox, index);
        }, this);
    });
    
    // Form validation
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        const checkedRows = document.querySelectorAll('.assignment-checkbox:checked');
        if (checkedRows.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu tagihan untuk dibayar!');
            return false;
        }
        
        const totalBayar = parseInt(document.getElementById('total_bayar').value);
        if (totalBayar <= 0) {
            e.preventDefault();
            alert('Total pembayaran harus lebih dari 0!');
            return false;
        }
        payment_items
        return confirm('Apakah Anda yakin ingin memproses pembayaran ini?');
    });
";

include 'includes/footer.php';
?>
