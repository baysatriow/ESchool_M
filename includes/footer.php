<?php
// Helper function untuk asset path
if (!function_exists('asset_path_footer')) {
    function asset_path_footer($path) {
        $script_dir = dirname($_SERVER['SCRIPT_NAME']);
        $request_uri = $_SERVER['REQUEST_URI'];
        
        // Hitung berapa level naik yang dibutuhkan
        $levels = substr_count(trim(str_replace($script_dir, '', $request_uri), '/'), '/');
        $prefix = str_repeat('../', $levels-1);
        
        return $prefix . $path;
    }
}
?>

<footer class="footer">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <script>document.write(new Date().getFullYear())</script> © ESchool_M.
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end d-none d-sm-block">
                            Crafted with <i class="mdi mdi-heart text-danger"></i> by <a href="#" target="_blank" class="text-muted">Abdimas Tel-U 2025</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

<div class="custom-setting bg-primary pe-0 d-flex flex-column rounded-start">
    <button type="button" class="btn btn-wide border-0 text-white fs-20 avatar-sm rounded-end-0" id="light-dark-mode">
        <i class="mdi mdi-brightness-7 align-middle"></i>
        <i class="mdi mdi-white-balance-sunny align-middle"></i>
    </button>
    <button type="button" class="btn btn-wide border-0 text-white fs-20 avatar-sm" data-toggle="fullscreen">
        <i class="mdi mdi-arrow-expand-all align-middle"></i>
    </button>
    <button type="button" class="btn btn-wide border-0 text-white fs-16 avatar-sm" id="layout-dir-btn">
        <span>RTL</span>
    </button>
</div>

<script src="<?php echo asset_path_footer('assets/libs/jquery/jquery.min.js'); ?>"></script>
<script src="<?php echo asset_path_footer('assets/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="<?php echo asset_path_footer('assets/libs/metismenu/metisMenu.min.js'); ?>"></script>
<script src="<?php echo asset_path_footer('assets/libs/simplebar/simplebar.min.js'); ?>"></script>
<script src="<?php echo asset_path_footer('assets/libs/node-waves/waves.min.js'); ?>"></script>
<script src="<?php echo asset_path_footer('assets/js/app.js'); ?>"></script>
<script src="<?php echo asset_path_footer('assets/js/common.js'); ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<!-- iziToast JS -->
<script src="assets/libs/izitoast/js/iziToast.min.js"></script>

<?php if (isset($additional_js)): ?>
    <?php foreach ($additional_js as $js): ?>
        <script src="<?php echo $js; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Flash Messages with iziToast -->
<script>
<?php if (Session::hasFlash('success')): ?>
    iziToast.success({
        title: 'Berhasil!',
        message: '<?php echo Session::getFlash('success'); ?>',
        position: 'topRight'
    });
<?php endif; ?>

<?php if (Session::hasFlash('error')): ?>
    iziToast.error({
        title: 'Error!',
        message: '<?php echo Session::getFlash('error'); ?>',
        position: 'topRight'
    });
<?php endif; ?>

<?php if (Session::hasFlash('warning')): ?>
    iziToast.warning({
        title: 'Peringatan!',
        message: '<?php echo Session::getFlash('warning'); ?>',
        position: 'topRight'
    });
<?php endif; ?>

<?php if (Session::hasFlash('info')): ?>
    iziToast.info({
        title: 'Info!',
        message: '<?php echo Session::getFlash('info'); ?>',
        position: 'topRight'
    });
<?php endif; ?>
</script>

<?php if (isset($custom_js)): ?>
    <script>
        <?php echo $custom_js; ?>
    </script>
<?php endif; ?>

</body>
</html>
