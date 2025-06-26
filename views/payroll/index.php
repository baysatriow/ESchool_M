<?php
$page_title = $page_title ?? 'Data Penggajian';
include_once 'includes/header.php';
?>

<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0"><?= $page_title ?></h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active"><?= $page_title ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><?= $page_title ?></h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="fas fa-plus"></i> Tambah Penggajian
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Periode</th>
                                    <th>Pegawai</th>
                                    <th>NIP</th>
                                    <th>Jabatan</th>
                                    <th>Gaji Pokok</th>
                                    <th>Tunjangan</th>
                                    <th>Potongan</th>
                                    <th>Gaji Bersih</th>
                                    <th>Tanggal Bayar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($payrolls as $payroll): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($payroll['periode_gaji']) ?></td>
                                    <td><?= htmlspecialchars($payroll['nama_lengkap']) ?></td>
                                    <td><?= htmlspecialchars($payroll['nip']) ?></td>
                                    <td><?= htmlspecialchars($payroll['nama_jabatan']) ?></td>
                                    <td>Rp <?= number_format($payroll['gaji_pokok'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($payroll['total_tunjangan'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($payroll['total_potongan'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($payroll['gaji_bersih'], 0, ',', '.') ?></td>
                                    <td><?= date('d/m/Y', strtotime($payroll['tanggal_pembayaran'])) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" onclick="printPayroll(<?= $payroll['id'] ?>)">
                                            <i class="fas fa-print"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deletePayroll(<?= $payroll['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
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

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Penggajian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="payroll/create" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pegawai</label>
                                <select name="pegawai_id" class="form-select" required>
                                    <option value="">Pilih Pegawai</option>
                                    <!-- Options will be populated via AJAX -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Periode Gaji</label>
                                <input type="month" name="periode_gaji" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Pembayaran</label>
                                <input type="date" name="tanggal_pembayaran" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Gaji Pokok</label>
                                <input type="number" id="gaji_pokok" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total Tunjangan</label>
                                <input type="number" name="total_tunjangan" id="total_tunjangan" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Total Potongan</label>
                                <input type="number" name="total_potongan" id="total_potongan" class="form-control" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gaji Bersih</label>
                        <input type="number" id="gaji_bersih" class="form-control" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#datatable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
            }
        });
    }
    
    // Calculate gaji bersih
    $('#total_tunjangan, #total_potongan').on('input', function() {
        calculateGajiBersih();
    });
    
    function calculateGajiBersih() {
        var gajiPokok = parseInt($('#gaji_pokok').val()) || 0;
        var tunjangan = parseInt($('#total_tunjangan').val()) || 0;
        var potongan = parseInt($('#total_potongan').val()) || 0;
        var gajiBersih = gajiPokok + tunjangan - potongan;
        $('#gaji_bersih').val(gajiBersih);
    }
});

function deletePayroll(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data penggajian ini?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'payroll/delete';
        
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = id;
        form.appendChild(input);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function printPayroll(id) {
    window.open('payroll/print/' + id, '_blank');
}
</script>

<?php include_once 'includes/footer.php'; ?>
