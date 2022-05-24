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
    // Get timestamp of the request time.
    $request_time = $this->time->getRequestTime();

    // Prepare current time from the request timestamp.
    $current_time = new \DateTime();
    $current_time->setTimestamp($request_time);

    // Prepare a start time for the day.
    $day_start = clone $current_time;
    $day_start->setTime(0, 0, 0, 0);

    // Check difference between date times.
    $diff = $current_time->diff($day_start);

    // Retrieve period of the day according to the diff.
    $periods = $this->info();
    $period = array_splice($periods, intdiv($diff->h, 8), 1);
    return array_key_first($period);
  }

}
