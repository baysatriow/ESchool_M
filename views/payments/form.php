<input type="hidden" name="siswa_id" value="<?php echo $student['id']; ?>">

<div class="row mb-3">
    <div class="col-md-6">
        <h6>Informasi Siswa</h6>
        <p><strong><?php echo htmlspecialchars($student['nama_lengkap']); ?></strong><br>
        NIS: <?php echo htmlspecialchars($student['nis']); ?></p>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="tanggal_bayar" class="form-label">Tanggal Bayar</label>
            <input type="date" class="form-control" id="tanggal_bayar" name="tanggal_bayar" value="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div class="mb-3">
            <label for="metode_bayar" class="form-label">Metode Bayar</label>
            <select class="form-control" id="metode_bayar" name="metode_bayar" required>
                <option value="tunai">Tunai</option>
                <option value="transfer">Transfer</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="bukti_foto" class="form-label">Bukti Pembayaran (Foto)</label>
            <input type="file" class="form-control" id="bukti_foto" name="bukti_foto" accept="image/*" onchange="previewImage(this)">
            <img id="photo_preview" src="#" alt="Preview" style="max-width: 200px; margin-top: 10px; display: none;">
            <div id="photo_error" class="text-danger"></div>
        </div>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-12">
        <h6>Pilih Pembayaran</h6>
        <?php if (empty($available_payments)): ?>
            <div class="alert alert-info">
                Tidak ada pembayaran yang tersedia atau semua pembayaran sudah lunas.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="select_all" onchange="toggleAll()">
                            </th>
                            <th>Jenis Pembayaran</th>
                            <th>Periode</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($available_payments as $index => $payment): ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="selected_payments[]" value="<?php echo $index; ?>" 
                                       onchange="calculateTotal()" class="payment-checkbox">
                            </td>
                            <td><?php echo htmlspecialchars($payment['nama_pembayaran']); ?></td>
                            <td>
                                <?php if ($payment['tipe'] == 'bulanan'): ?>
                                    <?php 
                                    $months = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                    echo $months[$payment['bulan']] . ' ' . $payment['tahun'];
                                    ?>
                                <?php else: ?>
                                    <?php echo $payment['tahun']; ?>
                                <?php endif; ?>
                            </td>
                            <td>Rp <?php echo number_format($payment['nominal'], 0, ',', '.'); ?></td>
                        </tr>
                        
                        <!-- Hidden inputs for payment data -->
                        <input type="hidden" name="payment_items[<?php echo $index; ?>][jenis_pembayaran_id]" value="<?php echo $payment['jenis_pembayaran_id']; ?>">
                        <input type="hidden" name="payment_items[<?php echo $index; ?>][bulan]" value="<?php echo $payment['bulan']; ?>">
                        <input type="hidden" name="payment_items[<?php echo $index; ?>][tahun]" value="<?php echo $payment['tahun']; ?>">
                        <input type="hidden" name="payment_items[<?php echo $index; ?>][nominal]" value="<?php echo $payment['nominal']; ?>">
                        
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <strong>Total Pembayaran: <span id="total_display">Rp 0</span></strong>
                        <input type="hidden" name="total_bayar" id="total_bayar" value="0">
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleAll() {
    const selectAll = document.getElementById('select_all');
    const checkboxes = document.querySelectorAll('.payment-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    calculateTotal();
}

function calculateTotal() {
    const checkboxes = document.querySelectorAll('.payment-checkbox:checked');
    let total = 0;
    
    checkboxes.forEach(checkbox => {
        const index = checkbox.value;
        const nominal = document.querySelector(`input[name="payment_items[${index}][nominal]"]`).value;
        total += parseInt(nominal);
    });
    
    document.getElementById('total_display').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('total_bayar').value = total;
}

function previewImage(input) {
    const preview = document.getElementById('photo_preview');
    const file = input.files[0];
    const reader = new FileReader();
    const errorDiv = document.getElementById('photo_error');

    errorDiv.textContent = ''; // Clear previous errors

    if (file) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            errorDiv.textContent = 'Jenis file tidak diizinkan. Hanya JPEG, PNG, dan GIF yang diperbolehkan.';
            input.value = ''; // Clear the input
            preview.style.display = 'none';
            preview.src = "#";
            return;
        }

        if (file.size > 2048000) { // 2MB
            errorDiv.textContent = 'Ukuran file terlalu besar. Maksimal 2MB.';
            input.value = ''; // Clear the input
            preview.style.display = 'none';
            preview.src = "#";
            return;
        }

        reader.onloadend = function() {
            preview.src = reader.result;
            preview.style.display = "block";
        }

        reader.readAsDataURL(file);
    } else {
        preview.src = "#";
        preview.style.display = "none";
    }
}
</script>
