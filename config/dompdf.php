<?php

return [

    /*
    |--------------------------------------------------------------------------
    | DOMPDF Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure the options for the DomPDF library. These settings
    | allow you to customize the PDF generation process, including font paths,
    | paper size, and image handling.
    |
    */

    'show_warnings' => false, // Set to true to display warnings during rendering

    'orientation' => 'portrait', // Options: 'portrait' or 'landscape'

    'paper_size' => 'A4', // Options: 'A4', 'letter', etc., or array([width, height]) in mm

    'paper_orientation' => 'portrait', // Deprecated, use 'orientation' instead

    'default_font' => 'serif', // Default font family (e.g., 'Helvetica', 'Times', 'Arial')

    'dpi' => 96, // Dots per inch for rendering

    'font_cache' => storage_path('fonts'), // Path to store font cache files

    'font_dir' => storage_path('fonts'), // Directory for custom fonts (if any)

    'temp_dir' => sys_get_temp_dir(), // Temporary directory for processing

    'chroot' => realpath(base_path()), // Root directory for file access (security)

    'log_output_file' => null, // Path to log file (null to disable logging)

    'default_media_type' => 'screen', // Media type for CSS (e.g., 'screen', 'print')

    'is_remote_enabled' => false, // Allow remote resources (e.g., images from URLs)

    'is_html5_parser_enabled' => true, // Use HTML5 parser (recommended)

    'isPhpEnabled' => true, // Enable PHP in templates

    'isJavascriptEnabled' => false, // Enable JavaScript (not recommended for security)

    'isFontSubsettingEnabled' => false, // Enable font subsetting (reduces file size)

    'options' => [
        'debugCss' => false, // Debug CSS issues
        'debugLayout' => false, // Debug layout issues
        'debugPng' => false, // Debug PNG images
    ],

];
