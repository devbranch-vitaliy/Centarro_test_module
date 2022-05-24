<?php

namespace Drupal\commerce_price_periods\Entity\Render;

use Drupal\commerce_price_periods\Plugin\views\field\ProductVariationPriceByPeriod;
use Drupal\commerce_price_periods\PricePeriodsServiceInterface;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\views\Entity\Render\EntityFieldRenderer;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;

/**
 * Override default Entity field render.
 */
class EntityFieldPricePeriodsRenderer extends EntityFieldRenderer {

  /**
   * The price periods service.
   *
   * @var \Drupal\commerce_price_periods\PricePeriodsServiceInterface
   */
  protected PricePeriodsServiceInterface $pricePeriodsService;

  /**
   * Constructs an EntityFieldRenderer object.
   *
   * @param \Drupal\views\ViewExecutable $view
   *   The view whose fields are being rendered.
   * @param string $relationship
   *   The relationship to be handled.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   * @param \Drupal\commerce_price_periods\PricePeriodsServiceInterface $pricePeriodsService
   *   The price periods service.
   */
  public function __construct(ViewExecutable $view, $relationship, LanguageManagerInterface $language_manager, EntityTypeInterface $entity_type, EntityTypeManagerInterface $entity_type_manager, EntityRepositoryInterface $entity_repository, PricePeriodsServiceInterface $pricePeriodsService) {
    parent::__construct($view, $relationship, $language_manager, $entity_type, $entity_type_manager, $entity_repository);
    $this->pricePeriodsService = $pricePeriodsService;
  }

  /**
   * {@inheritDoc}
   */
  protected function buildFields(array $values) {
    $build = [];

    if ($values && ($field_ids = $this->getRenderableFieldIds())) {
      $entity_type_id = $this->getEntityTypeId();

      // Collect the entities for the relationship, fetch the right translation,
      // and group by bundle. For each result row, the corresponding entity can
      // be obtained from any of the fields handlers, so we arbitrarily use the
      // first one.
      $entities_by_bundles = [];
      $field = $this->view->field[current($field_ids)];
      foreach ($values as $result_row) {
        if ($entity = $field->getEntity($result_row)) {
          $entities_by_bundles[$entity->bundle()][$result_row->index] = $this->getEntityTranslation($entity, $result_row);
        }
      }

      // Determine unique sets of fields that can be processed by the same
      // display. Fields that appear several times in the View open additional
      // "overflow" displays.
      $display_sets = [];
      foreach ($field_ids as $field_id) {
        $field = $this->view->field[$field_id];
        $field_name = $field->definition['field_name'];
        $index = 0;
        while (isset($display_sets[$index]['field_names'][$field_name])) {
          $index++;
        }
        $display_sets[$index]['field_names'][$field_name] = $field;
        $display_sets[$index]['field_ids'][$field_id] = $field;
      }

      // For each set of fields, build the output by bundle.
      foreach ($display_sets as $display_fields) {
        foreach ($entities_by_bundles as $bundle => $bundle_entities) {
          // Create the display, and configure the field display options.
          $display = EntityViewDisplay::create([
            'targetEntityType' => $entity_type_id,
            'bundle' => $bundle,
            'status' => TRUE,
          ]);
          foreach ($display_fields['field_ids'] as $field) {
            $display->setComponent($field->definition['field_name'], [
              'type' => $field->options['type'],
              'settings' => $field->options['settings'],
            ]);
            // Retrieve all related fields.
            if ($field instanceof ProductVariationPriceByPeriod) {
              foreach ($this->pricePeriodsService::info() as $period => $info) {
                $display->setComponent($period, [
                  'type' => $field->options['type'],
                  'settings' => $field->options['settings'],
                ]);
              }
            }
          }
          // Let the display build the render array for the entities.
          $display_build = $display->buildMultiple($bundle_entities);
          // Collect the field render arrays and index them using our internal
          // row indexes and field IDs.
          foreach ($display_build as $row_index => $entity_build) {
            foreach ($display_fields['field_ids'] as $field_id => $field) {
              $field_name = $field->definition['field_name'];

              // Check a specific case of the price periods.
              if ($field instanceof ProductVariationPriceByPeriod) {
                $period = $this->pricePeriodsService->getCurrentPeriod();
                $entity = $this->getEntity($values[$row_index]);
                if ($entity->hasField($period) && !$entity->get($period)->isEmpty()) {
                  $field_name = $period;
                }
              }

              $build[$row_index][$field_id] = !empty($entity_build[$field_name]) ? $entity_build[$field_name] : [];
            }
          }
        }
      }
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntity(ResultRow $values) {
    if (!isset($this->relationship)) {
      return $values->_entity;
    }
    elseif (isset($values->_relationship_entities[$this->relationship])) {
      return $values->_relationship_entities[$this->relationship];
    }
  }

}
