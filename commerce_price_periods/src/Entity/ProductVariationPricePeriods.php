<?php

namespace Drupal\commerce_price_periods\Entity;

use Drupal\commerce_price_periods\PricePeriodsServiceInterface;
use Drupal\commerce_price_periods\PricePeriodsTrait;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Override getPrice() method of the entity.
 */
class ProductVariationPricePeriods extends ProductVariation {

  use PricePeriodsTrait;

  /**
   * The price periods service.
   *
   * @var \Drupal\commerce_price_periods\PricePeriodsServiceInterface
   */
  protected PricePeriodsServiceInterface $pricePeriodsService;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $values, $entity_type, $bundle = FALSE, $translations = []) {
    $this->pricePeriodsService = \Drupal::service('commerce_price_periods.price_periods');
    parent::__construct($values, $entity_type, $bundle, $translations);
  }

  /**
   * {@inheritdoc}
   */
  public function getPrice() {
    $field_name = $this->pricePeriodsService->getCurrentPeriod();
    if ($this->hasField($field_name) && !$this->get($field_name)->isEmpty()) {
      return $this->get($field_name)->first()->toPrice();
    }
    elseif (!$this->get('price')->isEmpty()) {
      return $this->get('price')->first()->toPrice();
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += static::getCustomFieldDefinitions();
    return $fields;
  }

}
