<div class="pagetitle">
  <h1>Dashboard</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href=".">Home</a></li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<!-- Begin Dashboard Section -->
<!-- Statistik Per Jenis Zakat -->
<section class="section">
  <div class="row">
    <!-- Data Zakat Fitrah -->
    <div class="col-12">
      <h4 class="mt-4 mb-3 text-muted">Data Zakat Fitrah</h4>
      <div class="row">
        <!-- Total Pembayar -->
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card info-card sales-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Pembayar</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-people"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT COUNT(DISTINCT id_pembayar) as total FROM tb_pembayaran WHERE jenis_zakat = 1";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo number_format($row['total'], 0, ',', '.');
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Orang</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Pembayaran Uang -->
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card info-card revenue-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Pembayaran Uang</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-cash"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT SUM(pembayaran_uang) as total FROM tb_pembayaran WHERE jenis_zakat = 1";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo 'Rp ' . number_format($row['total'] ?? 0, 0, ',', '.');
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Rupiah</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Pembayaran Beras -->
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card info-card success-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Pembayaran Beras</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-light">
                  <i class="bi bi-basket text-success"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT SUM(pembayaran_beras) as total FROM tb_pembayaran WHERE jenis_zakat = 1";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo number_format($row['total'] ?? 0, 1, ',', '.') . ' Kg';
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Kilogram</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Infaq -->
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card info-card primary-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Infaq</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                  <i class="bi bi-heart-fill"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT SUM(infaq_uang) as total_uang, SUM(infaq_beras) as total_beras FROM tb_pembayaran WHERE jenis_zakat = 1";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo 'Rp ' . number_format($row['total_uang'] ?? 0, 0, ',', '.') . ' & ' . number_format($row['total_beras'] ?? 0, 1, ',', '.') . ' Kg';
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Uang & Beras</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Zakat Fidyah -->
    <div class="col-12">
      <h4 class="mt-4 mb-3 text-muted">Data Zakat Fidyah</h4>
      <div class="row">
        <!-- Total Pembayar -->
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card info-card sales-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Pembayar</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-people"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT COUNT(DISTINCT id_pembayar) as total FROM tb_pembayaran WHERE jenis_zakat = 2";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo number_format($row['total'], 0, ',', '.');
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Orang</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Pembayaran Uang -->
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card info-card revenue-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Pembayaran Uang</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-cash"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT SUM(pembayaran_uang) as total FROM tb_pembayaran WHERE jenis_zakat = 2";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo 'Rp ' . number_format($row['total'] ?? 0, 0, ',', '.');
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Rupiah</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Pembayaran Beras -->
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card info-card success-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Pembayaran Beras</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-light">
                  <i class="bi bi-basket text-success"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT SUM(pembayaran_beras) as total FROM tb_pembayaran WHERE jenis_zakat = 2";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo number_format($row['total'] ?? 0, 1, ',', '.') . ' Kg';
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Kilogram</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Infaq -->
        <div class="col-lg-3 col-md-6 mb-4">
          <div class="card info-card primary-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Infaq</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                  <i class="bi bi-heart-fill"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT SUM(infaq_uang) as total_uang, SUM(infaq_beras) as total_beras FROM tb_pembayaran WHERE jenis_zakat = 2";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo 'Rp ' . number_format($row['total_uang'] ?? 0, 0, ',', '.') . ' & ' . number_format($row['total_beras'] ?? 0, 1, ',', '.') . ' Kg';
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Uang & Beras</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Zakat Maal -->
    <div class="col-12">
      <h4 class="mt-4 mb-3 text-muted">Data Zakat Maal</h4>
      <div class="row">
        <!-- Total Pembayar -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card info-card sales-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Pembayar</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-people"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT COUNT(DISTINCT id_pembayar) as total FROM tb_pembayaran WHERE jenis_zakat = 3";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo number_format($row['total'], 0, ',', '.');
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Orang</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Pembayaran Uang -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card info-card revenue-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Pembayaran Uang</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-cash"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT SUM(pembayaran_uang) as total FROM tb_pembayaran WHERE jenis_zakat = 3";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo 'Rp ' . number_format($row['total'] ?? 0, 0, ',', '.');
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Rupiah</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Infaq -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card info-card primary-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Infaq</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                  <i class="bi bi-heart-fill"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT SUM(infaq_uang) as total FROM tb_pembayaran WHERE jenis_zakat = 3";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo 'Rp ' . number_format($row['total'] ?? 0, 0, ',', '.');
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Rupiah</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Infaq -->
    <div class="col-12">
      <h4 class="mt-4 mb-3 text-muted">Data Infaq</h4>
      <div class="row">
        <!-- Total Pembayar -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card info-card sales-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Pembayar</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-people"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT COUNT(DISTINCT id_pembayar) as total FROM tb_pembayaran WHERE jenis_zakat = 4";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo number_format($row['total'], 0, ',', '.');
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Orang</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Infaq Uang -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card info-card revenue-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Infaq Uang</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-cash"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT SUM(infaq_uang) as total FROM tb_pembayaran WHERE jenis_zakat = 4";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo 'Rp ' . number_format($row['total'] ?? 0, 0, ',', '.');
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Rupiah</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Infaq Beras -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card info-card success-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Infaq Beras</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-light">
                  <i class="bi bi-basket text-success"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT SUM(infaq_beras) as total FROM tb_pembayaran WHERE jenis_zakat = 4";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo number_format($row['total'] ?? 0, 1, ',', '.') . ' Kg';
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Kilogram</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Keseluruhan Data Zakat -->
    <div class="col-12">
      <h4 class="mt-4 mb-3 text-muted">Total Keseluruhan Data Zakat</h4>
      <div class="row">
        <!-- Total Pembayar Zakat -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card info-card sales-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Pembayar Zakat</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-people"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT COUNT(DISTINCT id_pembayar) as total FROM tb_pembayaran WHERE jenis_zakat IN (1, 2, 3)";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo number_format($row['total'], 0, ',', '.');
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Orang</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Pemberian Uang Zakat -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card info-card revenue-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Pemberian Uang Zakat</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-cash"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT SUM(pembayaran_uang) as total_fitrah_fidyah
                              FROM tb_pembayaran 
                              WHERE jenis_zakat IN (1, 2, 3)";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    $total_uang = ($row['total_fitrah_fidyah'] ?? 0);
                    echo 'Rp ' . number_format($total_uang, 0, ',', '.');
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Rupiah</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Pemberian Beras Zakat -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card info-card success-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Pemberian Beras Zakat</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-light">
                  <i class="bi bi-basket text-success"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT SUM(pembayaran_beras) as total FROM tb_pembayaran WHERE jenis_zakat IN (1, 2)";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo number_format($row['total'] ?? 0, 1, ',', '.') . ' Kg';
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Kilogram</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Infaq Uang -->
        <div class="col-lg-6 col-md-6 mb-4">
          <div class="card info-card primary-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Infaq Uang</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center" style="background-color: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                  <i class="bi bi-heart-fill"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT SUM(infaq_uang) as total FROM tb_pembayaran";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo 'Rp ' . number_format($row['total'] ?? 0, 0, ',', '.');
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Rupiah</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Infaq Beras -->
        <div class="col-lg-6 col-md-6 mb-4">
          <div class="card info-card success-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Infaq Beras</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success-light">
                  <i class="bi bi-basket text-success"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT SUM(infaq_beras) as total FROM tb_pembayaran";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo number_format($row['total'] ?? 0, 1, ',', '.') . ' Kg';
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Kilogram</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Penyaluran Zakat -->
    <div class="col-12">
      <h4 class="mt-4 mb-3 text-muted">Penyaluran Zakat</h4>
      <div class="row">
        <!-- Total Penerima Zakat -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card info-card customers-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Penerima Zakat</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                  <i class="bi bi-person-check"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT COUNT(DISTINCT id_penerima) as total FROM tb_penerima";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo number_format($row['total'], 0, ',', '.');
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Orang</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Penyaluran Uang -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card info-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Penyaluran Uang</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success">
                  <i class="bi bi-cash-coin text-white"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT SUM(jumlah_penerimaan_uang) as total FROM tb_penerimaan";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo 'Rp ' . number_format($row['total'] ?? 0, 0, ',', '.');
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Rupiah</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Penyaluran Beras -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card info-card h-100">
            <div class="card-body">
              <h5 class="card-title">Total Penyaluran Beras</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-success">
                  <i class="bi bi-basket2 text-white"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    $query = "SELECT SUM(jumlah_penerimaan_beras) as total FROM tb_penerimaan";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_assoc($result);
                    echo number_format($row['total'] ?? 0, 1, ',', '.') . ' Kg';
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Kilogram</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Saldo Zakat -->
    <div class="col-12">
      <h4 class="mt-4 mb-3 text-muted">Saldo Zakat</h4>
      <div class="row">
        <!-- Saldo Uang Zakat -->
        <div class="col-lg-6 col-md-6 mb-4">
          <div class="card info-card h-100">
            <div class="card-body">
              <h5 class="card-title">Saldo Uang Zakat & Infaq</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning">
                  <i class="bi bi-wallet2 text-white"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    // Menghitung selisih antara pembayaran dan penyaluran uang
                    $query_masuk_fitrah_fidyah = "SELECT COALESCE(SUM(pembayaran_uang), 0) as total FROM tb_pembayaran WHERE jenis_zakat IN (1, 2)";
                    $result_masuk_fitrah_fidyah = mysqli_query($koneksi, $query_masuk_fitrah_fidyah);
                    $row_masuk_fitrah_fidyah = mysqli_fetch_assoc($result_masuk_fitrah_fidyah);
                    
                    $query_masuk_maal = "SELECT COALESCE(SUM(pembayaran_uang), 0) as total FROM tb_pembayaran WHERE jenis_zakat = 3";
                    $result_masuk_maal = mysqli_query($koneksi, $query_masuk_maal);
                    $row_masuk_maal = mysqli_fetch_assoc($result_masuk_maal);
                    
                    $query_masuk_infaq = "SELECT COALESCE(SUM(infaq_uang), 0) as total FROM tb_pembayaran";
                    $result_masuk_infaq = mysqli_query($koneksi, $query_masuk_infaq);
                    $row_masuk_infaq = mysqli_fetch_assoc($result_masuk_infaq);
                    
                    $total_masuk_uang = $row_masuk_fitrah_fidyah['total'] + $row_masuk_maal['total'] + $row_masuk_infaq['total'];
                    
                    $query_keluar = "SELECT COALESCE(SUM(jumlah_penerimaan_uang), 0) as total FROM tb_penerimaan";
                    $result_keluar = mysqli_query($koneksi, $query_keluar);
                    $row_keluar = mysqli_fetch_assoc($result_keluar);
                    
                    $saldo_uang = $total_masuk_uang - $row_keluar['total'];
                    echo 'Rp ' . number_format($saldo_uang, 0, ',', '.');
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Rupiah</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Saldo Beras Zakat -->
        <div class="col-lg-6 col-md-6 mb-4">
          <div class="card info-card h-100">
            <div class="card-body">
              <h5 class="card-title">Saldo Beras Zakat & Infaq</h5>
              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-warning">
                  <i class="bi bi-box2 text-white"></i>
                </div>
                <div class="ps-3">
                  <h6>
                    <?php
                    // Menghitung selisih antara pembayaran dan penyaluran beras
                    $query_masuk_beras = "SELECT COALESCE(SUM(pembayaran_beras), 0) as total_pembayaran, 
                                                 COALESCE(SUM(infaq_beras), 0) as total_infaq 
                                         FROM tb_pembayaran";
                    $result_masuk_beras = mysqli_query($koneksi, $query_masuk_beras);
                    $row_masuk_beras = mysqli_fetch_assoc($result_masuk_beras);
                    $total_masuk_beras = $row_masuk_beras['total_pembayaran'] + $row_masuk_beras['total_infaq'];
                    
                    $query_keluar_beras = "SELECT COALESCE(SUM(jumlah_penerimaan_beras), 0) as total FROM tb_penerimaan";
                    $result_keluar_beras = mysqli_query($koneksi, $query_keluar_beras);
                    $row_keluar_beras = mysqli_fetch_assoc($result_keluar_beras);
                    
                    $saldo_beras = $total_masuk_beras - $row_keluar_beras['total'];
                    echo number_format($saldo_beras, 1, ',', '.') . ' Kg';
                    ?>
                  </h6>
                  <span class="text-muted small pt-2">Kilogram</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- End Dashboard Section -->