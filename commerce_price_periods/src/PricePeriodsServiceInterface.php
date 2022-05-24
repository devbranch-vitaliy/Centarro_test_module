<?php

namespace Drupal\commerce_price_periods;

/**
 * Service actions around price periods.
 */
interface PricePeriodsServiceInterface {

  /**
   * Retrieve the current period.
   *
   * @return string|null
   *   The name of the current period.
   */
  public function getCurrentPeriod(): ?string;

}
