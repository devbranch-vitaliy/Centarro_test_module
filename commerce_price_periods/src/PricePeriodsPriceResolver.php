<?php

namespace Drupal\commerce_price_periods;

use Drupal\commerce\Context;
use Drupal\commerce\PurchasableEntityInterface;
use Drupal\commerce_price\Resolver\PriceResolverInterface;

/**
 * Custom price resolve according to the price periods.
 */
class PricePeriodsPriceResolver implements PriceResolverInterface {

  /**
   * The price list repository.
   *
   * @var \Drupal\commerce_price_periods\PricePeriodsServiceInterface
   */
  protected PricePeriodsServiceInterface $pricePeriodsService;

  /**
   * Constructs a new PricePeriodsPriceResolver.
   *
   * @param \Drupal\commerce_price_periods\PricePeriodsServiceInterface $pricePeriodsService
   *   The price periods service.
   */
  public function __construct(PricePeriodsServiceInterface $pricePeriodsService) {
    $this->pricePeriodsService = $pricePeriodsService;
  }

  /**
   * {@inheritdoc}
   */
  public function resolve(PurchasableEntityInterface $entity, $quantity, Context $context) {
    $field_name = $this->pricePeriodsService->getCurrentPeriod();
    if ($entity->hasField($field_name) && !$entity->get($field_name)->isEmpty()) {
      return $entity->get($field_name)->first()->toPrice();
    }
  }

}
