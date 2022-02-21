<?php

/**
 * Plugin Name: Optimize WebP Images
 * Author: seojake
 * Version: 1.0.0
 */

require_once 'classes/OptimizeWebP.php';

//https://stackoverflow.com/questions/57757439/how-to-convert-png-file-to-webp-file

$optimize_image = new OptimizeWebP(12);
print_r($optimize_image->convert());
