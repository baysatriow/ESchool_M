<?php
switch ($pg) {
    case 'data_akun':
        if ($_SESSION['level'] == 1) {
            include "mod_akun/akun.php";
        } else {
            include "home.php";
        }
        break;
    case 'dataZakat':
        include "mod_data_zakat/zakat.php";
        break;
    case 'dataPenerima':
        include "mod_data_penerima/penerima.php";
        break;
    case 'settingZakat':
        if ($_SESSION['level'] == 1) {
            include "mod_setting_zakat/setting.php";
        } else {
            include "home.php";
        }
        break;
    default:
        include "home.php";
        break;
}
