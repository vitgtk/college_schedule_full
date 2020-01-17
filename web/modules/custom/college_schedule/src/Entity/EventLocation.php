<?php

namespace Drupal\college_schedule\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Location entity.
 *
 * @ingroup college_schedule
 *
 * @ContentEntityType(
 *   id = "event_location",
 *   label = @Translation("Location"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\college_schedule\EventLocationListBuilder",
 *     "views_data" = "Drupal\college_schedule\Entity\EventLocationViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\college_schedule\Form\EventLocationForm",
 *       "add" = "Drupal\college_schedule\Form\EventLocationForm",
 *       "edit" = "Drupal\college_schedule\Form\EventLocationForm",
 *       "delete" = "Drupal\college_schedule\Form\EventLocationDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\college_schedule\EventLocationHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\college_schedule\EventLocationAccessControlHandler",
 *   },
 *   base_table = "event_location",
 *   translatable = FALSE,
 *   admin_permission = "administer location entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/event_location/{event_location}",
 *     "add-form" = "/admin/structure/event_location/add",
 *     "edit-form" = "/admin/structure/event_location/{event_location}/edit",
 *     "delete-form" = "/admin/structure/event_location/{event_location}/delete",
 *     "collection" = "/admin/structure/event_location",
 *   },
 *   field_ui_base_route = "event_location.settings"
 * )
 */
class EventLocation extends ContentEntityBase implements EventLocationInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Location entity.'))
      ->setSettings([
        'max_length' => 100,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Location is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
