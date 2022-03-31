<?php

namespace Drupal\vactory_cross_content\Field;

use Drupal\block\Entity\Block;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;
use Drupal\node\Entity\NodeType;

/**
 * Defines a vcc list class for better normalization targeting.
 */
class InternalVCCFieldItemList extends FieldItemList {

  use ComputedItemListTrait;

  /**
   * {@inheritdoc}
   */
  protected function computeValue() {
    $entity = $this->getEntity();
    $entity_type = $entity->getEntityTypeId();
    $value = [];
    /** @var \Drupal\node\NodeTypeInterface $type */
    $type = NodeType::load($entity->bundle());
    $isEnabled = $type->getThirdPartySetting('vactory_cross_content', 'enabling', '');
    if ($entity_type !== 'node' || !$isEnabled) {
      return $value;
    }
    $value = [
      'ids' => [],
      'more_link' => '',
      'more_link_label' => '',
      'display_mode' => '',
      'limit' => 0,
    ];
    if (\Drupal::moduleHandler()->moduleExists('vactory_decoupled')) {
      // Get vcc blocks here.
      $banner_plugin_filter = [
        'operator' => 'IN',
        'plugins' => ['vactory_cross_content'],
      ];
      $blocks = \Drupal::service('vactory_decoupled.blocksManager')->getBlocksByNode($entity->id(), $banner_plugin_filter);
      $block_info = reset($blocks);
      if (!empty($block_info)) {
        $block = Block::load($block_info['id']);
        if (!empty($block)) {
          $configuration = $block->get('settings');
          $title = $configuration['title'];
          $nbr = $configuration['nombre_elements'] ?? $type->getThirdPartySetting('vactory_cross_content', 'nombre_elements', 3);
          $more_link = $configuration['more_link'] ?? $type->getThirdPartySetting('vactory_cross_content', 'more_link', '');
          $more_link_label = $configuration['more_link_label'] ?? $type->getThirdPartySetting('vactory_cross_content', 'more_link_label', '');
          $display_mode = $configuration['display_mode'];
          $value['title'] = $title;
          $value['more_link'] = $more_link;
          $value['more_link_label'] = $more_link_label;
          $value['limit'] = $nbr;
          $value['display_mode'] = $display_mode;
          $view = \Drupal::service('vactory_cross_content.manager')->getCrossContentView($type, $entity, $configuration);
          if (!empty($view) && is_object($view)) {
            $view->execute();
            if (!empty($view->result)) {
              $nids = array_map(function ($row) {
                return $row->nid;
              }, $view->result);
              $value['ids'] = $nids;
            }
          }
        }
      }
    }
    $this->list[0] = $this->createItem(0, $value);
  }
}