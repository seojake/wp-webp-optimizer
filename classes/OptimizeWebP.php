<?php

if (!defined('ABSPATH')) exit;

if (!class_exists('OptimizeWebP')) {
  class OptimizeWebP
  {
    private $id;
    private $file;
    public $meta;
    private $basedir;
    private $dir;

    public function __construct($id)
    {
      $this->id = $id;
      $this->file = get_post_meta($id, '_wp_attached_file', true);
      $this->meta = get_post_meta($id, '_wp_attachment_metadata', true);

      // Add the month and year to the $subdir variable
      $upload_dir = wp_upload_dir();
      $base_dir = trailingslashit($upload_dir['basedir']);
      $this->basedir = $base_dir;
      if (isset($this->meta['file'])) {
        foreach (explode('/', '/' . $this->meta['file']) as $filename_part) {
          if (is_numeric($filename_part)) {
            $base_dir .= trailingslashit($filename_part);
          }
        }
        $this->dir = $base_dir;
      }
    }

    public function convert()
    {
      // The variable sized images
      if (isset($this->meta['sizes'])) {
        foreach ($this->meta['sizes'] as $key => $img) {
          $file = $this->dir . $img['file'];
          $webp = $this->dirty_convert($file, $img['mime-type']);
          if ($webp) {
            unlink($file);
            $this->meta['sizes'][$key]['mime-type'] = 'image/webp';
            $this->meta['sizes'][$key]['file'] = str_replace(['.jpg', '.jpeg'], '.webp', $img['file']);
          }
        }
      }

      // The original image
      if (!empty($this->file) && file_exists($this->basedir . $this->file) && $this->contains($this->basedir . $this->file, ['.jpg', '.jpeg', '.png'])) {
        $file = $this->basedir . $this->file;
        $webp = $this->dirty_convert($file, mime_content_type($file));
        if ($webp) {
          unlink($file);
        }
      }

      $this->file = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $this->file);
      $this->meta['file'] = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $this->meta['file']);

      update_post_meta($this->id, '_wp_attached_file', $this->file);
      update_post_meta($this->id, '_wp_attachment_metadata', $this->meta);
    }

    private function dirty_convert($file, $mime_type)
    {
      if (file_exists($file) && $this->contains($file, ['.jpg', '.jpeg', '.png'])) {
        $new_img = '';
        switch ($mime_type) {
          case 'image/jpeg':
            $new_img = @imagecreatefromjpeg($file);
            $webp = @imagewebp($new_img, str_replace(['.jpg', '.jpeg'], '.webp', $file), 80);
            break;

          case 'image/png':
            $new_img = @imagecreatefrompng($file);
            @imagepalettetotruecolor($new_img);
            @imageAlphaBlending($new_img, true);
            @imageSaveAlpha($new_img, true);
            $webp = @imagewebp($new_img, str_replace('.png', '.webp', $file), 80);
            break;
        }

        if (isset($webp) && $webp) {
          imagedestroy($new_img);
          return true;
        }
      }

      return false;
    }

    private function contains($str, array $arr)
    {
      foreach ($arr as $a) {
        if (stripos($str, $a) !== false) return true;
      }
      return false;
    }
  }
}
