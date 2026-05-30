<?php
/**
 * Test AJAX direct
 */

// Simuler une requête AJAX
$_GET['id'] = 19; // ID du rapport qui existe

// Capturer la sortie de get_report.php
ob_start();
include 'get_report.php';
$output = ob_get_clean();

echo "=== TEST AJAX GET_REPORT ===\n";
echo "Output: " . $output . "\n";
echo "=== FIN TEST ===\n";
?>