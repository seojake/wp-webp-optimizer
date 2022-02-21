<?php

/**
 * Plugin Name: Optimize WebP Images
 * Author: seojake
 * Version: 1.0.0
 */

require_once 'classes/OptimizeWebP.php';

$optimize_image = new OptimizeWebP(12);
print_r($optimize_image->convert());
