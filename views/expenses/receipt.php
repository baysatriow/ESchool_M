<?php
$title = "Kuitansi Pengeluaran - " . htmlspecialchars($expense['no_bukti']);

// Function to convert number to Indonesian words
function terbilang($angka) {
    $angka = abs($angka);
    $baca = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $terbilang = "";

    if ($angka < 12) {
        $terbilang = " " . $baca[$angka];
    } else if ($angka < 20) {
        $terbilang = terbilang($angka - 10) . " belas";
    } else if ($angka < 100) {
        $terbilang = terbilang(intval($angka / 10)) . " puluh" . terbilang($angka % 10);
    } else if ($angka < 200) {
        $terbilang = " seratus" . terbilang($angka - 100);
    } else if ($angka < 1000) {
        $terbilang = terbilang(intval($angka / 100)) . " ratus" . terbilang($angka % 100);
    } else if ($angka < 2000) {
        $terbilang = " seribu" . terbilang($angka - 1000);
    } else if ($angka < 1000000) {
        $terbilang = terbilang(intval($angka / 1000)) . " ribu" . terbilang($angka % 1000);
    } else if ($angka < 1000000000) {
        $terbilang = terbilang(intval($angka / 1000000)) . " juta" . terbilang($angka % 1000000);
    } else if ($angka < 1000000000000) {
        $terbilang = terbilang(intval($angka / 1000000000)) . " milyar" . terbilang($angka % 1000000000);
    }

    return $terbilang;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* General styles for both screen and print */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .receipt-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e9ecef; /* Added for consistency */
        }
        .receipt-header {
            text-align: center;
            border-bottom: 3px solid #dc3545; /* Red */
            padding: 30px 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        .school-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            background: #dc3545; /* Red */
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            box-shadow: 0 4px 8px rgba(220,53,69,0.3);
        }
        .receipt-title {
            font-size: 28px;
            font-weight: bold;
            color: #dc3545; /* Red */
            margin: 15px 0 10px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .school-name {
            font-size: 18px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        .school-address {
            color: #6c757d;
            font-size: 14px;
            line-height: 1.4;
        }
        .receipt-number {
            background: #dc3545; /* Red */
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 15px;
            font-weight: 600;
        }
        .info-section {
            padding: 30px;
        }
        .info-table td {
            padding: 10px 15px;
            border: none;
            vertical-align: top;
        }
        .info-table .label {
            font-weight: 600;
            width: 150px;
            color: #495057;
        }
        .info-table .value {
            color: #212529;
        }
        .expense-details {
            margin: 20px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px;
            border-radius: 8px;
            border-left: 5px solid #dc3545; /* Red */
        }
        .total-section {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); /* Red gradient */
            color: white;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
            text-align: center;
        }
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .terbilang-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #dee2e6;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
            border-left: 5px solid #dc3545; /* Red */
        }
        .terbilang-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        .terbilang-text {
            font-style: italic;
            color: #dc3545; /* Red */
            font-size: 16px;
            font-weight: 500;
        }
        .signature-section {
            margin-top: 40px;
            padding: 0 30px;
        }
        .signature-box {
            text-align: center;
            min-height: 80px;
            border-bottom: 2px solid #495057;
            margin-bottom: 15px;
            position: relative;
        }
        .signature-box::after {
            content: "(Tanda Tangan)";
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            color: #6c757d;
        }
        .signature-name {
            font-weight: 600;
            color: #495057;
            margin-top: 10px;
        }
        .footer-note {
            margin-top: 40px;
            padding: 25px 30px;
            border-top: 2px solid #dee2e6;
            background: #f8f9fa;
        }
        .footer-note h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .footer-note ul {
            color: #6c757d;
            font-size: 14px;
        }
        .footer-note ul li {
            margin-bottom: 5px;
        }
        .print-info {
            text-align: center;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 12px;
        }
        .btn-print {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); /* Red gradient */
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(220,53,69,0.3);
            transition: all 0.3s ease;
        }
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(220,53,69,0.4);
        }
        .btn-back {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(108,117,125,0.3);
        }
        .status-badge {
            background: #dc3545; /* Red */
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        /* --- PRINT-SPECIFIC STYLES --- */
        @media print {
            /* Hide non-essential elements for print */
            .no-print {
                display: none !important;
            }

            /* Ensure elements intended to be block on print are */
            .print-only {
                display: block !important;
            }

            /* Optimize receipt container for print */
            .receipt-container {
                max-width: 800px !important;
                margin: 0 auto !important; /* Center with no top/bottom margin */
                box-shadow: none !important; /* Remove shadow */
                border: none !important; /* Remove outer border */
            }

            /* Ensure colors and backgrounds are printed */
            .receipt-header,
            .school-logo,
            .receipt-title,
            .receipt-number,
            .expense-details, /* Include this for its background/border-left */
            .total-section,
            .terbilang-box,
            .terbilang-text,
            .status-badge {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Adjust page margins for more content space */
            @page {
                margin: 0.8cm !important; /* Reduced margins */
                size: A4 portrait;
            }

            /* General font size adjustment for compactness in print */
            body { font-size: 13px; }
            .receipt-title { font-size: 26px !important; }
            .school-name { font-size: 17px !important; }
            .school-address { font-size: 13px !important; }
            .receipt-number { font-size: 13px !important; padding: 6px 15px !important; }

            /* Spacing adjustments for compactness */
            .info-section { padding: 25px !important; }
            .info-table td { padding: 8px 12px !important; }
            .expense-details { margin: 15px 0 !important; padding: 15px !important;} /* Reduced margins/paddings */
            .expense-details h5 { font-size: 16px !important; margin-bottom: 8px !important; }
            .expense-details p { font-size: 13px !important; }
            .total-section { padding: 15px !important; margin: 20px 0 !important; } /* Reduced margins/paddings */
            .total-amount { font-size: 22px !important; margin-bottom: 5px !important;}
            .terbilang-box { padding: 15px !important; margin: 20px 0 !important; }
            
            /* --- REVISED PAGE BREAK CONTROL & FLEXBOX FOR SIGNATURES --- */
            
            /* Keep logical content blocks together, avoiding breaks inside them */
            .info-section,
            .expense-details,
            .total-section,
            .terbilang-box,
            .signature-section, /* Entire signature block should ideally stay together */
            .footer-note { /* Entire footer block should ideally stay together */
                page-break-inside: avoid !important;
            }

            /* Forcing flexbox on the signature row to keep them side-by-side */
            .signature-section .row {
                display: flex !important;
                flex-wrap: nowrap !important; /* Prevent wrapping */
                justify-content: space-around !important;
                width: 100% !important;
                margin-top: 25px !important; /* Adjust if needed to pull it up */
            }

            .signature-section .col-md-6 {
                flex: 1 0 50% !important; /* Distribute space evenly, don't shrink below 50% */
                max-width: 50% !important; /* Ensure they don't grow beyond 50% */
            }
            .signature-box { min-height: 70px !important; margin-bottom: 10px !important; }
            .signature-box::after { bottom: -20px !important; font-size: 11px !important; }
            .signature-name { font-size: 13px !important; margin-top: 5px !important;}
            p strong { font-size: 13px !important; } /* Adjust font size of "Yang Menyerahkan" / "Mengetahui" */


            /* Specific control for footer to avoid being orphaned */
            .footer-note {
                page-break-before: avoid !important; /* Try not to break *before* the footer */
                margin-top: 20px !important; /* Reduced margin to bring it up */
                padding: 15px 25px !important; /* Reduced padding */
                orphans: 2; /* Minimum lines at bottom of a page */
                widows: 2;  /* Minimum lines at top of a new page */
            }
            .footer-note h6 { margin-bottom: 8px !important; font-size: 14px !important;}
            .footer-note ul { font-size: 13px !important; margin-bottom: 10px !important;}
            .footer-note ul li { margin-bottom: 2px !important; }
            .print-info { padding-top: 8px !important; font-size: 11px !important; }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <div class="school-logo">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <h1 class="receipt-title">KUITANSI PENGELUARAN</h1>
            <div class="school-name">
                <?= htmlspecialchars($school['nama_sekolah'] ?? 'SEKOLAH CONTOH') ?>
            </div>
            <div class="school-address">
                <?= htmlspecialchars($school['alamat'] ?? 'Jl. Pendidikan No. 123, Kota Contoh, Provinsi Contoh') ?><br>
                <?php if (!empty($school['no_telepon'])): ?>
                    Telp: <?= htmlspecialchars($school['no_telepon']) ?>
                <?php endif; ?>
                <?php if (!empty($school['email'])): ?>
                    <?= !empty($school['no_telepon']) ? ' | ' : '' ?>Email: <?= htmlspecialchars($school['email']) ?>
                <?php endif; ?>
            </div>
            <div class="receipt-number">
                No. Bukti: <?= htmlspecialchars($expense['no_bukti']) ?>
            </div>
        </div>

        <div class="info-section">
            <div class="row">
                <div class="col-md-6">
                    <table class="table info-table">
                        <tr>
                            <td class="label">Tanggal</td>
                            <td class="value">: <?= date('d F Y', strtotime($expense['tanggal'])) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Kategori</td>
                            <td class="value">: <?= htmlspecialchars($expense['nama_kategori'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="label">No. Bukti</td>
                            <td class="value">: <?= htmlspecialchars($expense['no_bukti']) ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table info-table">
                        <tr>
                            <td class="label">Dibuat Oleh</td>
                            <td class="value">: <?= htmlspecialchars($expense['created_by'] ?? 'System') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Waktu Input</td>
                            <td class="value">: <?= htmlspecialchars($expense['waktu_transaksi'] ?? date('d F Y H:i:s')) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Status</td>
                            <td class="value">: <span class="status-badge">VALID</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="px-4">
            <div class="expense-details">
                <h5 class="mb-3"><i class="fas fa-info-circle mr-2"></i>Detail Pengeluaran</h5>
                <p><strong>Keterangan:</strong></p>
                <p class="mb-0"><?= htmlspecialchars($expense['keterangan']) ?></p>
            </div>
        </div>

        <div class="px-4">
            <div class="total-section">
                <div class="total-amount">
                    TOTAL PENGELUARAN: Rp <?= number_format($expense['nominal'], 0, ',', '.') ?>
                </div>
            </div>
        </div>

        <div class="px-4">
            <div class="terbilang-box">
                <div class="terbilang-label">Terbilang:</div>
                <div class="terbilang-text"><?= ucwords(trim(terbilang($expense['nominal']))) ?> rupiah</div>
            </div>
        </div>

        <div class="signature-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="text-center">
                        <p><strong>Yang Menyerahkan,</strong></p>
                        <div class="signature-box"></div>
                        <p class="signature-name"><?= htmlspecialchars($expense['created_by'] ?? 'Petugas Keuangan') ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-center">
                        <p><strong>Mengetahui,</strong></p>
                        <div class="signature-box"></div>
                        <p class="signature-name">Kepala Sekolah</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-note">
            <h6><i class="fas fa-info-circle mr-2"></i>Catatan Penting:</h6>
            <ul class="mb-3">
                <li>Kuitansi ini adalah bukti sah pengeluaran yang telah diverifikasi</li>
                <li>Harap simpan kuitansi ini dengan baik sebagai arsip</li>
                <li>Untuk informasi lebih lanjut hubungi bagian keuangan sekolah</li>
                <li>Dokumen ini dicetak secara otomatis oleh sistem</li>
            </ul>
            
            <div class="print-info">
                <small>
                    <i class="fas fa-calendar mr-1"></i>Dicetak pada: <?= date('d F Y H:i:s') ?> | 
                    <i class="fas fa-desktop mr-1"></i>Sistem Informasi Sekolah v2.0
                </small>
            </div>
        </div>

        <div class="text-center py-4 no-print">
            <button onclick="window.print()" class="btn btn-danger btn-print mr-3">
                <i class="fas fa-print mr-2"></i>Cetak Kuitansi
            </button>
            <a href="<?= Router::url('expenses') ?>" class="btn btn-secondary btn-back">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <script>
        // Auto focus untuk print
        document.addEventListener('DOMContentLoaded', function() {
            // Keyboard shortcut untuk print (Ctrl+P)
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'p') {
                    e.preventDefault();
                    window.print();
                }
            });
        });
    </script>
</body>
</html>