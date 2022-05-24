<?php

namespace Drupal\commerce_price_periods\Plugin\Commerce\EntityTrait;

use Drupal\commerce\Plugin\Commerce\EntityTrait\EntityTraitBase;
use Drupal\commerce_price_periods\PricePeriodsTrait;

/**
 * Provides the "price_periods" trait.
 *
 * @CommerceEntityTrait(
 *   id = "price_periods",
 *   label = @Translation("Price periods"),
 *   entity_types = {"commerce_product_variation"}
 * )
 */
class PricePeriods extends EntityTraitBase {

  use PricePeriodsTrait;

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {
    return $this->getCustomBundleFieldDefinitions();
  }

}
