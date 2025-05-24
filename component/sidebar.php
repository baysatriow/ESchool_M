<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
  <?php
  // Get current page from URL parameter
  $current_page = isset($_GET['pg']) ? $_GET['pg'] : 'beranda';
  ?>

  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link <?php echo ($current_page == 'beranda' || $current_page == '') ? '' : 'collapsed'; ?>" href=".">
        <i class="bi bi-house-door"></i> <!-- Ikon Beranda -->
        <span>Beranda</span>
      </a>
    </li><!-- End Dashboard Nav -->

    <!-- Start Zakat Data Page Nav -->
    <li class="nav-item">
      <a class="nav-link <?php echo ($current_page == 'dataZakat') ? '' : 'collapsed'; ?>" href="?pg=dataZakat">
        <i class="bi bi-clipboard-data"></i> <!-- Ikon Data Zakat -->
        <span>Data Zakat</span>
      </a>
    </li><!-- Zakat Data Page Nav -->

    <!-- Start Penyaluran Zakat Nav -->
    <li class="nav-item">
      <a class="nav-link <?php echo ($current_page == 'dataPenerima') ? '' : 'collapsed'; ?>" href="?pg=dataPenerima">
        <i class="bi bi-arrow-repeat"></i> <!-- Ikon Penyaluran Zakat -->
        <span>Penyaluran Zakat</span>
      </a>
    </li><!-- Penyaluran Zakat Nav -->

    <?php if ($userLogin['level'] == 1) {?>
    <!-- Start Pengaturan Zakat Nav -->
    <li class="nav-item">
      <a class="nav-link <?php echo ($current_page == 'settingZakat') ? '' : 'collapsed'; ?>" href="?pg=settingZakat">
        <i class="bi bi-gear"></i> <!-- Ikon Pengaturan Zakat -->
        <span>Pengaturan Zakat</span>
      </a>
    </li><!-- Pengaturan Zakat Nav -->

    <!-- Start Data Pengguna Nav -->
    <li class="nav-item">
      <a class="nav-link <?php echo ($current_page == 'data_akun') ? '' : 'collapsed'; ?>" href="?pg=data_akun">
        <i class="bi bi-person-circle"></i> <!-- Ikon Data Pengguna -->
        <span>Data Pengguna</span>
      </a>
    </li><!-- Data Pengguna Nav -->
    <?php }?>
    <!-- Start Logout Nav -->
    <li class="nav-item">
      <a class="nav-link collapsed" href="logout.php">
        <i class="bi bi-box-arrow-right"></i> <!-- Ikon Logout -->
        <span>Keluar</span>
      </a>
    </li><!-- End Logout Page Nav -->

  </ul>

</aside><!-- End Sidebar -->