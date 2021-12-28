<?php

declare(strict_types = 1);

namespace Drupal\animated_gif;

/**
 * Utilities for Animated Gif module.
 */
class AnimatedGifUtility {

  /**
   * Check if a gif image is animated.
   *
   * @param string $fileUri
   *   The uri file.
   *
   * @return bool
   *   Return true if file contains multiple "frames".
   *
   * @SuppressWarnings(PHPMD.ErrorControlOperator)
   */
  public static function isAnAnimatedGif(string $fileUri): bool {
    $fopen = @fopen($fileUri, 'rb');
    if (!$fopen) {
      return FALSE;
    }
    $count = 0;
    // An animated gif contains multiple "frames", with each frame having a
    // header made up of:
    // * a static 4-byte sequence (\x00\x21\xF9\x04)
    // * 4 variable bytes
    // * a static 2-byte sequence (\x00\x2C)
    // We read through the file til we reach the end of the file, or we've found
    // at least 2 frame headers.
    while (!feof($fopen) && $count < 2) {
      // Read 100kb at a time.
      $chunk = fread($fopen, 1024 * 100);
      $count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00[\x2C\x21]#s', (string) $chunk);
    }

    fclose($fopen);
    return $count > 1;
  }

}
