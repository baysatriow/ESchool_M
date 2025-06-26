<?php
$page_title = $page_title ?? 'Tarif Pembayaran';
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
                            <i class="fas fa-plus"></i> Tambah Tarif
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Kelas</th>
                                    <th>Jenis Pembayaran</th>
                                    <th>Nominal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($payment_rates as $rate): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($rate['tahun_ajaran']) ?></td>
                                    <td><?= htmlspecialchars($rate['nama_kelas']) ?></td>
                                    <td><?= htmlspecialchars($rate['nama_pembayaran']) ?></td>
                                    <td>Rp <?= number_format($rate['nominal'], 0, ',', '.') ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" onclick="editRate(<?= $rate['id'] ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteRate(<?= $rate['id'] ?>)">
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Tarif Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="payment-rates/create" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" class="form-select" required>
                            <option value="">Pilih Tahun Ajaran</option>
                            <!-- Options will be populated via AJAX -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kelas</label>
                        <select name="kelas_id" class="form-select" required>
                            <option value="">Pilih Kelas</option>
                            <!-- Options will be populated via AJAX -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Pembayaran</label>
                        <select name="jenis_pembayaran_id" class="form-select" required>
                            <option value="">Pilih Jenis Pembayaran</option>
                            <!-- Options will be populated via AJAX -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nominal</label>
                        <input type="number" name="nominal" class="form-control" required>
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
});

function deleteRate(id) {
    if (confirm('Apakah Anda yakin ingin menghapus tarif pembayaran ini?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'payment-rates/delete';
        
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = id;
        form.appendChild(input);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include_once 'includes/footer.php'; ?>
