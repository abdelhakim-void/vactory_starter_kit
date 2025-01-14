<?php

namespace Drupal\vactory_search_overlay\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a "Vactory Search Overlay Block 1" block.
 *
 * @Block(
 *     id = "vactory_search_overlay_block1",
 *     admin_label = @Translation("Vactory Search Overlay V1"),
 *     category = @Translation("Search")
 * )
 */
class VactorySearchOverlayBlock1 extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    return [
      '#theme' => 'block_vactory_search_overlay_variant1',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return Cache::PERMANENT;
  }

}
