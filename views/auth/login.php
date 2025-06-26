<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Login | ESchool_M - SD IT Ahsanul Fikri</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Aplikasi Keuangan Berbasis Website SD IT Ahsanul Fikri" name="description" />
    <meta content="Abdimas Tel-U 2025" name="author" />
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    
    <script src="assets/js/pages/layout.js"></script>
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/simplebar/simplebar.min.css" rel="stylesheet">
    <!-- iziToast CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="container-fluid authentication-bg overflow-hidden">
        <div class="bg-overlay"></div>
        <div class="row align-items-center justify-content-center min-vh-100">
            <div class="col-10 col-md-6 col-lg-4 col-xxl-3">
                <div class="card mb-0">
                    <div class="card-body">
                        <div class="text-center">
                            <a href="<?php echo Router::url('dashboard'); ?>" class="logo-dark">
                                <img src="assets/images/logo-dark.png" alt="" height="70" class="auth-logo logo-dark mx-auto">
                            </a>
                            <a href="<?php echo Router::url(path: 'dashboard'); ?>" class="logo-light">
                                <img src="assets/images/logo-light.png" alt="" height="70" class="auth-logo logo-light mx-auto">
                            </a>
                            
                            <h4 class="mt-4">Selamat Datang di ESchool_M!</h4>
                            <p class="text-muted">Masuk untuk melanjutkan ke dashboard.</p>
                        </div>

                        <div class="p-2 mt-5">
                            <form method="POST" action="<?php echo Router::url('login'); ?>">
                                <div class="input-group auth-form-group-custom mb-3">
                                    <span class="input-group-text bg-primary bg-opacity-10 fs-16" id="basic-addon1">
                                        <i class="mdi mdi-account-outline auti-custom-input-icon"></i>
                                    </span>
                                    <input type="text" name="username" class="form-control" placeholder="Masukkan Username" 
                                           aria-label="Username" aria-describedby="basic-addon1" required>
                                </div>

                                <div class="input-group auth-form-group-custom mb-3">
                                    <span class="input-group-text bg-primary bg-opacity-10 fs-16" id="basic-addon2">
                                        <i class="mdi mdi-lock-outline auti-custom-input-icon"></i>
                                    </span>
                                    <input type="password" name="password" class="form-control" placeholder="Masukkan Password" 
                                           aria-label="Password" aria-describedby="basic-addon2" required>
                                </div>

                                <div class="mb-sm-5">
                                    <div class="form-check float-sm-start">
                                        <input type="checkbox" class="form-check-input" id="customControlInline" name="remember">
                                        <label class="form-check-label" for="customControlInline">Ingat saya</label>
                                    </div>
                                </div>

                                <div class="pt-3 text-center">
                                    <button class="btn btn-primary w-xl waves-effect waves-light" type="submit">Masuk</button>
                                </div>
                            </form>
                        </div>

                        <div class="mt-5 text-center">
                            <p>©
                                <script>document.write(new Date().getFullYear())</script> 
                                Crafted with <i class="mdi mdi-heart text-danger"></i> by Abdimas Tel-U 2025
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <!-- iziToast JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script src="assets/js/app.js"></script>
    
    <?php if (isset($error)): ?>
    <script>
        iziToast.error({
            title: 'Login Gagal!',
            message: '<?php echo $error; ?>',
            position: 'topRight'
        });
    </script>
    <?php endif; ?>

    <?php if (Session::hasFlash('info')): ?>
    <script>
    iziToast.info({
        title: 'Info!',
        message: '<?php echo Session::getFlash('info'); ?>',
        position: 'topRight'
    });
    </script>
    <?php endif; ?>
</body>
</html>
