<?php

/**
 * Plugin Name: Optimize WebP Images
 * Description: Convert PNGs and JPEGs to WebP for image optimization.
 * Author: seojake
 * Author URI: https://github.com/seojake
 * Version: 1.0.0
 */

require_once 'OptimizeWebP.php';

// Disable image scaling
add_filter('big_image_size_threshold', '__return_false');

// When a file is being uploaded, automatically optimize it
// add_action('add_attachment', 'optimizewebp__uploaded_image', 10, 1);
function owebp_optimize_uploaded_image($id)
{
  $optimizer = new OptimizeWebP($id);
  $optimizer->optimize();
}

// Optimize all images that haven't been already
function optimizewebp_all()
{
  $images = new WP_Query([
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'post_mime_type' => [
      'image/png',
      'image/jpg',
      'image/jpeg'
    ],
    'posts_per_page' => -1
  ]);

  if (!empty($images->posts)) {
    foreach ($images->posts as $image) {
      $optimizer = new OptimizeWebP($image->ID);
      $optimizer->optimize();
    }
  }
}
