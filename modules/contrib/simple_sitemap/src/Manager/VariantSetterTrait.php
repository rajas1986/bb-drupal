<?php

namespace Drupal\simple_sitemap\Manager;

use Drupal\simple_sitemap\Entity\SimpleSitemap;

/**
 * Provides a helper to setting/getting variants.
 */
trait VariantSetterTrait {

  /**
   * The currently set variants.
   *
   * @var array
   */
  protected $variants;

  /**
   * Sets the variants.
   *
   * @param array|string|true|null $variants
   *   array: Array of variants to be set.
   *   string: A particular variant to be set.
   *   null: All existing variants will be set.
   *
   * @return $this
   *
   * @todo Check if variants exist and throw exception.
   * @todo Instead of array_keys(loadMultiple()) maybe a quicker entity query to get simple_sitemap IDs (variants)?
   */
  public function setVariants($variants = NULL) {
    if ($variants === NULL) {
      $this->variants = array_keys(SimpleSitemap::loadMultiple());
    }
    else {
      $this->variants = (array) $variants;
    }

    return $this;
  }

  /**
   * Gets the currently set variants, or all variants if none are set.
   *
   * @return array
   *   The currently set variants, or all variants if none are set.
   */
  protected function getVariants(): array {
    if (NULL === $this->variants) {
      $this->setVariants();
    }

    return $this->variants;
  }

}
