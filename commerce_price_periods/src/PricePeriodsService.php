<?php

namespace Drupal\commerce_price_periods;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Service actions around price periods.
 */
class PricePeriodsService implements PricePeriodsServiceInterface {

  use StringTranslationTrait;
  use PricePeriodsTrait;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected TimeInterface $time;

  /**
   * Constructs a new PricePeriodsGetter.
   *
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   */
  public function __construct(TimeInterface $time) {
    $this->time = $time;
  }

  /**
   * {@inheritDoc}
   */
  public function getCurrentPeriod(): ?string {
    $current_time = $this->time->getRequestTime();
    $start_of_period = strtotime('today', $current_time);
    $eight_hours = 3600 * 8;

    foreach ($this->info() as $period => $info) {
      if ($start_of_period <= $current_time && $current_time < $start_of_period + $eight_hours) {
        return $period;
      }
      $start_of_period += $eight_hours;
    }

    return NULL;
  }

}
