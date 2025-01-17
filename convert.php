<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['phar_file'])) {
    $uploadedFile = $_FILES['phar_file'];
    $uploadDir = 'uploads/';
    $outputDir = 'outputs/';
    
    // Ensure upload and output directories exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    // Handle file upload
    $filePath = $uploadDir . basename($uploadedFile['name']);
    if (move_uploaded_file($uploadedFile['tmp_name'], $filePath)) {
        try {
            // Convert PHAR to ZIP
            $phar = new Phar($filePath);
            $zipPath = $outputDir . pathinfo($uploadedFile['name'], PATHINFO_FILENAME) . '.zip';
            $phar->convertToData(Phar::ZIP, Phar::NONE)->compressFiles(Phar::NONE);
            
            // Save as ZIP
            copy($filePath, $zipPath);
            
            echo "<p>Conversion successful!</p>";
            echo "<a class='download-link' href='$zipPath' download>Download ZIP File</a>";
        } catch (Exception $e) {
            echo "<p>Error during conversion: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>Error uploading file.</p>";
    }
} else {
    echo "<p>No file uploaded or invalid request.</p>";
}
?>
