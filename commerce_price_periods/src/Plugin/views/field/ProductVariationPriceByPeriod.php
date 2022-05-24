<?php

namespace Drupal\commerce_price_periods\Plugin\views\field;

use Drupal\commerce_price_periods\Entity\Render\EntityFieldPricePeriodsRenderer;
use Drupal\commerce_price_periods\PricePeriodsServiceInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\Field\FormatterPluginManager;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\views\Plugin\views\field\EntityField;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Field handler to present a link to view a product variation.
 *
 * @ViewsField("price_by_period")
 */
class ProductVariationPriceByPeriod extends EntityField {

  /**
   * Default field name.
   *
   * @var string
   */
  protected string $defaultField = 'price';

  /**
   * Static cache for ::getEntityFieldRenderer().
   *
   * @var \Drupal\commerce_price_periods\Entity\Render\EntityFieldPricePeriodsRenderer
   */
  protected $entityFieldRenderer;

  /**
   * The price list repository.
   *
   * @var \Drupal\commerce_price_periods\PricePeriodsServiceInterface
   */
  protected PricePeriodsServiceInterface $pricePeriodsService;

  /**
   * Constructs a ProductVariationPriceByPeriod object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Field\FormatterPluginManager $formatter_plugin_manager
   *   The field formatter plugin manager.
   * @param \Drupal\Core\Field\FieldTypePluginManagerInterface $field_type_plugin_manager
   *   The field plugin type manager.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   * @param \Drupal\commerce_price_periods\PricePeriodsServiceInterface $pricePeriodsService
   *   The price periods service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, FormatterPluginManager $formatter_plugin_manager, FieldTypePluginManagerInterface $field_type_plugin_manager, LanguageManagerInterface $language_manager, RendererInterface $renderer, EntityRepositoryInterface $entity_repository, EntityFieldManagerInterface $entity_field_manager, PricePeriodsServiceInterface $pricePeriodsService) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager, $formatter_plugin_manager, $field_type_plugin_manager, $language_manager, $renderer, $entity_repository, $entity_field_manager);
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
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.field.formatter'),
      $container->get('plugin.manager.field.field_type'),
      $container->get('language_manager'),
      $container->get('renderer'),
      $container->get('entity.repository'),
      $container->get('entity_field.manager'),
      $container->get('commerce_price_periods.price_periods')
    );
  }

  /**
   * Returns the entity field renderer.
   *
   * @return \Drupal\commerce_price_periods\Entity\Render\EntityFieldPricePeriodsRenderer
   *   The entity field renderer.
   */
  protected function getEntityFieldRenderer() {
    if (!isset($this->entityFieldRenderer)) {
      // This can be invoked during field handler initialization in which case
      // view fields are not set yet.
      if (!empty($this->view->field)) {
        foreach ($this->view->field as $field) {
          // An entity field renderer can handle only a single relationship.
          if ($field instanceof ProductVariationPriceByPeriod && isset($field->entityFieldRenderer)) {
            $this->entityFieldRenderer = $field->entityFieldRenderer;
            break;
          }
        }
      }
      if (!isset($this->entityFieldRenderer)) {
        $entity_type = $this->entityTypeManager->getDefinition($this->getEntityType());
        $this->entityFieldRenderer = new EntityFieldPricePeriodsRenderer($this->view, $this->relationship, $this->languageManager, $entity_type, $this->entityTypeManager, $this->entityRepository, $this->pricePeriodsService);
      }
    }
    return $this->entityFieldRenderer;
  }

}
