<?php
class ExcelExporter {
    
    public function exportToExcel($data, $filename) {
        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');
        
        // Start output buffering
        ob_start();
        
        // Create Excel content with better formatting
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        echo '<meta name="ProgId" content="Excel.Sheet">';
        echo '<meta name="Generator" content="Microsoft Excel 11">';
        echo '<style>';
        echo 'table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }';
        echo 'th, td { border: 1px solid #000; padding: 8px; text-align: left; vertical-align: top; }';
        echo 'th { background-color: #f0f0f0; font-weight: bold; text-align: center; }';
        echo '.number { text-align: right; }';
        echo '.center { text-align: center; }';
        echo '.header { background-color: #d9d9d9; font-weight: bold; font-size: 14px; text-align: center; }';
        echo '.subheader { background-color: #e6e6e6; font-weight: bold; font-size: 12px; }';
        echo '.total { background-color: #ffff99; font-weight: bold; }';
        echo '.currency { text-align: right; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        echo '<table>';
        
        $isHeaderSection = true;
        $isTableHeader = false;
        
        foreach ($data as $rowIndex => $row) {
            echo '<tr>';
            
            if (is_array($row)) {
                // Check if this is a section header
                if (count($row) == 1 && !empty($row[0]) && 
                    (strpos($row[0], 'LAPORAN') !== false || 
                     strpos($row[0], 'DETAIL') !== false || 
                     strpos($row[0], 'REKAP') !== false ||
                     strpos($row[0], 'RINGKASAN') !== false)) {
                    echo '<td colspan="15" class="header">' . htmlspecialchars($row[0]) . '</td>';
                }
                // Check if this is a table header row
                elseif (in_array('No', $row) || in_array('Tanggal', $row) || 
                        (isset($row[0]) && $row[0] === 'No')) {
                    foreach ($row as $cell) {
                        echo '<th>' . htmlspecialchars($cell) . '</th>';
                    }
                }
                // Regular data row
                else {
                    foreach ($row as $cellIndex => $cell) {
                        $class = '';
                        
                        // Format currency values
                        if (is_numeric($cell) && $cell > 1000) {
                            $cell = 'Rp ' . number_format($cell, 0, ',', '.');
                            $class = 'currency';
                        }
                        // Format regular numbers
                        elseif (is_numeric($cell) && !is_string($cell)) {
                            $class = 'number';
                        }
                        // Check for total rows
                        elseif (stripos($cell, 'TOTAL') !== false) {
                            $class = 'total';
                        }
                        
                        echo '<td class="' . $class . '">' . htmlspecialchars($cell) . '</td>';
                    }
                }
            } else {
                // Single cell row (like empty rows or section breaks)
                if (empty($row)) {
                    echo '<td colspan="15">&nbsp;</td>';
                } else {
                    echo '<td colspan="15" class="subheader">' . htmlspecialchars($row) . '</td>';
                }
            }
            echo '</tr>';
        }
        
        echo '</table>';
        echo '</body>';
        echo '</html>';
        
        // Get content and clean buffer
        $content = ob_get_clean();
        
        // Output the content
        echo $content;
        exit;
    }
    
    public function exportArrayToExcel($data, $headers, $filename) {
        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');
        
        // Start output buffering
        ob_start();
        
        // Create Excel content
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        echo '<meta name="ProgId" content="Excel.Sheet">';
        echo '<meta name="Generator" content="Microsoft Excel 11">';
        echo '<style>';
        echo 'table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }';
        echo 'th, td { border: 1px solid #000; padding: 8px; text-align: left; }';
        echo 'th { background-color: #f0f0f0; font-weight: bold; text-align: center; }';
        echo '.number { text-align: right; }';
        echo '.center { text-align: center; }';
        echo '.currency { text-align: right; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        echo '<table>';
        
        // Add headers
        if (!empty($headers)) {
            echo '<tr>';
            foreach ($headers as $header) {
                echo '<th>' . htmlspecialchars($header) . '</th>';
            }
            echo '</tr>';
        }
        
        // Add data
        foreach ($data as $row) {
            echo '<tr>';
            foreach ($row as $cell) {
                $class = '';
                
                // Format currency values
                if (is_numeric($cell) && $cell > 1000) {
                    $cell = 'Rp ' . number_format($cell, 0, ',', '.');
                    $class = 'currency';
                }
                // Format regular numbers
                elseif (is_numeric($cell) && !is_string($cell)) {
                    $class = 'number';
                }
                
                echo '<td class="' . $class . '">' . htmlspecialchars($cell) . '</td>';
            }
            echo '</tr>';
        }
        
        echo '</table>';
        echo '</body>';
        echo '</html>';
        
        // Get content and clean buffer
        $content = ob_get_clean();
        
        // Output the content
        echo $content;
        exit;
    }
}
