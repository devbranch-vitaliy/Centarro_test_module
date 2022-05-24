<?php

namespace Drupal\commerce_price_periods\Plugin\Commerce\Condition;

use Drupal\commerce\Plugin\Commerce\Condition\ConditionBase;
use Drupal\commerce_price_periods\PricePeriodsServiceInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the day period condition for orders.
 *
 * @CommerceCondition(
 *   id = "day_period",
 *   label = @Translation("Day period"),
 *   category = @Translation("Order"),
 *   entity_type = "commerce_order",
 *   weight = 0,
 * )
 */
class DayPeriod extends ConditionBase implements ContainerFactoryPluginInterface {

  /**
   * The price list repository.
   *
   * @var \Drupal\commerce_price_periods\PricePeriodsServiceInterface
   */
  protected PricePeriodsServiceInterface $pricePeriodsService;

  /**
   * Constructs a DayPeriod object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\commerce_price_periods\PricePeriodsServiceInterface $pricePeriodsService
   *   The price periods service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PricePeriodsServiceInterface $pricePeriodsService) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->pricePeriodsService = $pricePeriodsService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('commerce_price_periods.price_periods'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'periods' => [],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $periods = $this->pricePeriodsService::info();
    $options = array_combine(array_keys($periods), array_column($periods, 'title'));
    $form['periods'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Day periods'),
      '#default_value' => $this->configuration['periods'],
      '#options' => $options,
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $values = $form_state->getValue($form['#parents']);
    $this->configuration['periods'] = array_filter($values['periods']);
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate(EntityInterface $entity) {
    $this->assertEntity($entity);
    return isset($this->configuration['periods'][$this->pricePeriodsService->getCurrentPeriod()]);
  }

}
