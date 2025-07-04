<?php
// Helper function untuk asset path
if (!function_exists('asset_path_header')) {
    function asset_path_header($path) {
        $script_dir = dirname($_SERVER['SCRIPT_NAME']);
        $request_uri = $_SERVER['REQUEST_URI'];
        
        // Hitung berapa level naik yang dibutuhkan
        $levels = substr_count(trim(str_replace($script_dir, '', $request_uri), '/'), '/');
        $prefix = str_repeat('../', $levels-1);
        
        return $prefix . $path;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?php echo $page_title ?? 'ESchool_M'; ?> | SD IT Ahsanul Fikri</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Aplikasi Keuangan Berbasis Website SD IT Ahsanul Fikri" name="description" />
    <meta content="Abdimas Tel-U 2025" name="author" />
    <link rel="shortcut icon" href="assets/images/favicon.png">
    <link href="<?php echo asset_path_header('assets/libs/select2/css/select2.min.css'); ?>" rel="stylesheet" type="text/css">
    
    <script src="<?php echo asset_path_header('assets/js/pages/layout.js'); ?>"></script>
    <link href="<?php echo asset_path_header('assets/css/bootstrap.min.css'); ?>" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="<?php echo asset_path_header('assets/css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo asset_path_header('assets/libs/simplebar/simplebar.min.css'); ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <!-- iziToast CSS -->
    <link href="<?php echo asset_path_header('assets/libs/izitoast/css/iziToast.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo asset_path_header('assets/css/app.min.css'); ?>" id="app-style" rel="stylesheet" type="text/css" />

    <?php if (isset($additional_css)): ?>
    <link href="<?php echo asset_path_header('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo asset_path_header('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'); ?>" rel="stylesheet" type="text/css" />
    <?php endif; ?>
</head>
<body>
<div id="layout-wrapper">
    <header id="page-topbar">
        <div class="navbar-header">
            <div class="navbar-logo-box">
                <a href="<?php echo Router::url('dashboard'); ?>" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="<?php echo asset_path_header('assets/images/logo-sm.png'); ?>" alt="logo-sm-dark" height="52">
                    </span>
                    <span class="logo-lg">
                        <img src="<?php echo asset_path_header('assets/images/logo-dark.png'); ?>" alt="logo-dark" height="50">
                    </span>
                </a>
                <a href="<?php echo Router::url('dashboard'); ?>" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="<?php echo asset_path_header('assets/images/logo-sm.png'); ?>" alt="logo-sm-light" height="52">
                    </span>
                    <span class="logo-lg">
                        <img src="<?php echo asset_path_header('assets/images/logo-light.png'); ?>" alt="logo-light" height="50">
                    </span>
                </a>
                <button type="button" class="btn btn-sm top-icon sidebar-btn" id="sidebar-btn">
                    <i class="mdi mdi-menu-open align-middle fs-19"></i>
                </button>
            </div>
            
            <div class="d-flex justify-content-between menu-sm px-3 ms-auto">
                <div class="d-flex align-items-center gap-2"></div>
                <div class="d-flex align-items-center gap-2">
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn btn-sm top-icon p-0" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded avatar-2xs p-0" src="<?php echo asset_path_header('assets/images/users/avatar-6.png'); ?>" alt="Header Avatar">
                        </button>
                        <div class="dropdown-menu dropdown-menu-wide dropdown-menu-end dropdown-menu-animated overflow-hidden py-0">
                            <div class="card border-0">
                                <div class="card-header bg-primary rounded-0">
                                    <div class="rich-list-item w-100 p-0">
                                        <div class="rich-list-prepend">
                                            <div class="avatar avatar-label-light avatar-circle">
                                                <div class="avatar-display"><i class="fa fa-user-alt"></i></div>
                                            </div>
                                        </div>
                                        <div class="rich-list-content">
                                            <h3 class="rich-list-title text-white"><?php echo Session::get('user_name', 'User'); ?></h3>
                                            <span class="rich-list-subtitle text-white"><?php echo Session::get('user_role', 'user'); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="grid-nav grid-nav-flush grid-nav-action grid-nav-no-rounded">
                                        <div class="grid-nav-row">
                                            <a href="<?php echo Router::url('logout'); ?>" class="grid-nav-item">
                                                <div class="grid-nav-icon"><i class="fas fa-sign-out-alt"></i></div> <span class="grid-nav-content">Keluar Aplikasi</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="card-footer card-footer-bordered rounded-0">
                                    <a href="<?php echo Router::url('logout'); ?>" class="btn btn-label-danger">Sign out</a>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
