<?php

/**
 * Plugin Name: Optimize WebP Images
 * Author: seojake
 * Version: 1.0.0
 */

require_once 'OptimizeWebP.php';

// Disable image scaling
add_filter('big_image_size_threshold', '__return_false');

// When a file is being uploaded, automatically optimize it
add_action('add_attachment', 'owebp_optimize_uploaded_image', 10, 1);
function owebp_optimize_uploaded_image($id)
{
  $optimizer = new OptimizeWebP($id);
  $optimizer->optimize();
}

// Add a admin page to the tools page
