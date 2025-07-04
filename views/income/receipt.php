<?php
$title = "Kuitansi Pendapatan - " . htmlspecialchars($income['no_bukti']);

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
            border: 1px solid #e9ecef;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 3px solid #28a745;
            padding: 30px 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        .school-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            box-shadow: 0 4px 8px rgba(40,167,69,0.3);
        }
        .receipt-title {
            font-size: 28px;
            font-weight: bold;
            color: #28a745;
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
            background: #28a745;
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
        .income-table {
            margin: 20px 0;
        }
        .income-table th {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            font-weight: 600;
            text-align: center;
            padding: 15px 10px;
            border: none;
        }
        .income-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }
        .total-row {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            font-weight: bold;
        }
        .total-row td {
            border: none;
            padding: 15px 10px;
        }
        .terbilang-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #dee2e6;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
            border-left: 5px solid #28a745;
        }
        .terbilang-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        .terbilang-text {
            font-style: italic;
            color: #28a745;
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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(40,167,69,0.3);
            transition: all 0.3s ease;
        }
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(40,167,69,0.4);
        }
        .btn-back {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(108,117,125,0.3);
        }
        .status-badge {
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Print-specific styles */
        @media print {
            /* Hide non-essential elements for print */
            .no-print {
                display: none !important;
            }

            /* Ensure elements intended to be block on print are */
            .print-only {
                display: block !important;
            }

            /* Maintain the receipt container's screen appearance */
            .receipt-container {
                max-width: 800px !important;
                margin: 0 auto !important; /* Changed: Reduced top/bottom margin for printing */
                box-shadow: none !important; /* Removed shadow for print to save ink */
                border: none !important; /* Removed outer border for cleaner print */
            }

            /* Ensure colors and backgrounds are printed */
            .receipt-header,
            .school-logo,
            .receipt-title,
            .receipt-number,
            .income-table th,
            .total-row,
            .terbilang-box,
            .terbilang-text,
            .status-badge {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Adjust page margins */
            @page {
                margin: 0.8cm !important; /* Slightly reduced page margins for more content */
                size: A4 portrait;
            }

            /* --- REVISED PAGE BREAK CONTROL --- */
            /* Allow page breaks generally, but prevent *inside* critical, small blocks */
            /* Default for page-break-before/after is auto, let's rely on that for most cases */

            /* Prevent signature elements from breaking across pages */
            .signature-section {
                page-break-inside: avoid !important;
                page-break-before: auto !important; /* Ensure it doesn't force a break unnecessarily */
                margin-top: 30px !important; /* Slightly reduce margin-top for print */
            }
            .signature-section .row {
                display: flex !important;
                flex-wrap: nowrap !important;
                justify-content: space-around !important;
                width: 100% !important;
            }
            .signature-section .col-md-6 {
                flex: 1 0 50% !important;
                max-width: 50% !important;
            }

            /* Prevent footer content from breaking and ensure it stays with signatures if possible */
            .footer-note {
                page-break-inside: avoid !important;
                page-break-before: avoid !important; /* Try hard not to break before the footer */
                margin-top: 25px !important; /* Slightly reduced margin-top for print */
                orphans: 2; /* Min lines on bottom of page */
                widows: 2;  /* Min lines on top of next page */
            }
            .footer-note h6 {
                margin-bottom: 10px !important; /* Slightly reduced margin for h6 */
            }
            .footer-note ul {
                padding-left: 15px !important; /* Adjust if bullets look off */
            }
            .footer-note ul li {
                margin-bottom: 3px !important; /* Reduced margin between list items */
            }


            /* Ensure main content sections also try to stay together */
            .info-section,
            .income-table,
            .terbilang-box {
                page-break-inside: avoid !important;
            }

            /* General font size for print - often a good compromise */
            body { font-size: 13px; } /* Slightly smaller for compactness, adjust as needed */
            .receipt-title { font-size: 26px !important; } /* A bit smaller */
            .school-name { font-size: 17px !important; }
            .school-address { font-size: 13px !important; }
            .receipt-number { font-size: 13px !important; padding: 6px 15px !important; }
            .info-table td { padding: 8px 12px !important; }
            .income-table th, .income-table td { padding: 10px !important; }
            .terbilang-box { padding: 15px !important; margin: 20px 0 !important; }
            .signature-box { min-height: 70px !important; margin-bottom: 10px !important; }
            .signature-box::after { bottom: -20px !important; }
            .print-info { padding-top: 10px !important; }

            /* Remove default browser header/footer (URLs, page numbers) - sometimes helps */
            /* @page { margin: 0; } not always ideal for content but removes browser headers */
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <div class="school-logo">
                <i class="fas fa-coins"></i>
            </div>
            <h1 class="receipt-title">KUITANSI PENDAPATAN</h1>
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
                No. Bukti: <?= htmlspecialchars($income['no_bukti']) ?>
            </div>
        </div>

        <div class="info-section">
            <div class="row">
                <div class="col-md-6">
                    <table class="table info-table">
                        <tr>
                            <td class="label">Tanggal</td>
                            <td class="value">: <?= date('d F Y', strtotime($income['tanggal'])) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Kategori</td>
                            <td class="value">: <?= htmlspecialchars($income['nama_kategori'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Keterangan</td>
                            <td class="value">: <?= htmlspecialchars($income['keterangan']) ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table info-table">
                        <tr>
                            <td class="label">Petugas</td>
                            <td class="value">: <?= htmlspecialchars($income['created_by'] ?? 'System') ?></td>
                        </tr>
                        <tr>
                            <td class="label">Waktu Input</td>
                            <td class="value">: <?= htmlspecialchars($income['waktu_transaksi'] ?? date('d M Y H:i:s')) ?></td>
                        </tr>
                        <tr>
                            <td class="label">Status</td>
                            <td class="value">: <span class="status-badge">DITERIMA</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="income-table px-4">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Keterangan</th>
                        <th width="200">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">1</td>
                        <td>
                            <strong><?= htmlspecialchars($income['nama_kategori'] ?? 'Pendapatan') ?></strong>
                            <br><small class="text-muted"><?= htmlspecialchars($income['keterangan']) ?></small>
                        </td>
                        <td class="text-right">Rp <?= number_format($income['nominal'], 0, ',', '.') ?></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="2" class="text-center"><strong>TOTAL PENDAPATAN</strong></td>
                        <td class="text-right"><strong>Rp <?= number_format($income['nominal'], 0, ',', '.') ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="px-4">
            <div class="terbilang-box">
                <div class="terbilang-label">Terbilang:</div>
                <div class="terbilang-text"><?= ucwords(trim(terbilang($income['nominal']))) ?> rupiah</div>
            </div>
        </div>

        <div class="signature-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="text-center">
                        <p><strong>Yang Menerima,</strong></p>
                        <div class="signature-box"></div>
                        <p class="signature-name"><?= htmlspecialchars($income['created_by'] ?? 'Bendahara') ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-center">
                        <p><strong>Yang Menyerahkan,</strong></p>
                        <div class="signature-box"></div>
                        <p class="signature-name">_____________________</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-note">
            <h6><i class="fas fa-info-circle mr-2"></i>Catatan Penting:</h6>
            <ul class="mb-3">
                <li>Kuitansi ini adalah bukti sah penerimaan pendapatan yang telah diverifikasi</li>
                <li>Harap simpan kuitansi ini dengan baik sebagai arsip</li>
                <li>Untuk informasi lebih lanjut hubungi bagian keuangan sekolah</li>
                <li>Pendapatan telah dicatat dalam sistem keuangan sekolah</li>
            </ul>
            
            <div class="print-info">
                <small>
                    <i class="fas fa-calendar mr-1"></i>Dicetak pada: <?= date('d F Y H:i:s') ?> | 
                    <i class="fas fa-desktop mr-1"></i>Sistem Keuangan Sekolah v1.0
                </small>
            </div>
        </div>

        <div class="text-center py-4 no-print">
            <button onclick="window.print()" class="btn btn-success btn-print mr-3">
                <i class="fas fa-print mr-2"></i>Cetak Kuitansi
            </button>
            <a href="<?= Router::url('income') ?>" class="btn btn-secondary btn-back">
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

        // Fungsi untuk download PDF (opsional)
        function downloadPDF() {
            window.print();
        }
    </script>
</body>
</html>