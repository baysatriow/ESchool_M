<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Pengaturan Aplikasi</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?php echo Router::url('dashboard'); ?>">Dashboard</a></li>
                                <li class="breadcrumb-item">Setting</li>
                                <li class="breadcrumb-item active">Pengaturan Aplikasi</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Identitas Sekolah</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?php echo Router::url('app-settings'); ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nama_sekolah" class="form-label">Nama Sekolah</label>
                                            <input type="text" class="form-control" id="nama_sekolah" name="nama_sekolah" 
                                                   value="<?php echo htmlspecialchars($settings['nama_sekolah'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="npsn" class="form-label">NPSN</label>
                                            <input type="text" class="form-control" id="npsn" name="npsn" 
                                                   value="<?php echo htmlspecialchars($settings['npsn'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?php echo htmlspecialchars($settings['alamat'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="no_telepon" class="form-label">No. Telepon</label>
                                            <input type="text" class="form-control" id="no_telepon" name="no_telepon" 
                                                   value="<?php echo htmlspecialchars($settings['no_telepon'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?php echo htmlspecialchars($settings['email'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="kepala_sekolah" class="form-label">Kepala Sekolah</label>
                                            <input type="text" class="form-control" id="kepala_sekolah" name="kepala_sekolah" 
                                                   value="<?php echo htmlspecialchars($settings['kepala_sekolah'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="bendahara" class="form-label">Bendahara</label>
                                            <input type="text" class="form-control" id="bendahara" name="bendahara" 
                                                   value="<?php echo htmlspecialchars($settings['bendahara'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save"></i> Simpan Pengaturan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
