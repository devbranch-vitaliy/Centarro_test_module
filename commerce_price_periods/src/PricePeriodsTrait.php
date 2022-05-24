<?php

namespace Drupal\commerce_price_periods;

use Drupal\entity\BundleFieldDefinition;

/**
 * Provides a trait for Commerce price periods.
 */
trait PricePeriodsTrait {

  /**
   * Retrieve data about allowed periods.
   *
   * @return array
   *   The info about price periods.
   */
  public function info(): array {
    return [
      'morning' => [
        'title' => t('Morning price: 00:00 - 08:00'),
        'description' => t('Active time: 00:00 - 08:00'),
      ],
      'midday' => [
        'title' => t('Midday price: 08:00 - 16:00'),
        'description' => t('Active time: 08:00 - 16:00'),
      ],
      'evening' => [
        'title' => t('Evening price: 16:00 - 00:00'),
        'description' => t('Active time: 16:00 - 00:00'),
      ],
    ];
  }

  /**
   * Retrieve field for price periods.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition[]
   *   Field definitions.
   */
  public function getCustomBundleFieldDefinitions(): array {
    $fields = [];
    foreach ($this->info() as $period => $info) {
      $fields[$period] = BundleFieldDefinition::create('commerce_price')
        ->setLabel($info['title'])
        ->setDescription($info['description'])
        ->setDisplayOptions('view', [
          'label' => 'above',
          'type' => 'commerce_price_default',
          'weight' => 0,
        ])
        ->setDisplayOptions('form', [
          'type' => 'commerce_price_default',
          'weight' => 0,
        ])
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE);
    }
    return $fields;
  }

}
