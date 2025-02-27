<?php
// Check if a file parameter is provided
if (!isset($_GET['file']) || empty($_GET['file'])) {
    http_response_code(400);
    exit("File parameter is missing.");
}

// Use basename to sanitize the file name (prevents directory traversal)
$file = basename($_GET['file']);

// Define the full path to the file in the uploads folder.
// Ensure this folder is correct relative to your public folder.
$filepath = __DIR__ . '/../uploads/' . $file;

if (!file_exists($filepath)) {
    http_response_code(404);
    // exit("File not found. Path: " . $filepath);
    exit("File not found.");
}

// Set headers for file download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));

// Read and output the file
readfile($filepath);
exit;
