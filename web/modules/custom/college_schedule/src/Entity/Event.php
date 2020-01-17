<?php

namespace Drupal\college_schedule\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Event entity.
 *
 * @ingroup college_schedule
 *
 * @ContentEntityType(
 *   id = "schedule_event",
 *   label = @Translation("Event"),
 *   bundle_label = @Translation("Event type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\college_schedule\EventListBuilder",
 *     "views_data" = "Drupal\college_schedule\Entity\EventViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\college_schedule\Form\EventForm",
 *       "add" = "Drupal\college_schedule\Form\EventForm",
 *       "edit" = "Drupal\college_schedule\Form\EventForm",
 *       "delete" = "Drupal\college_schedule\Form\EventDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\college_schedule\EventHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\college_schedule\EventAccessControlHandler",
 *   },
 *   base_table = "schedule_event",
 *   translatable = FALSE,
 *   admin_permission = "administer event entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/schedule_event/{schedule_event}",
 *     "add-page" = "/admin/structure/schedule_event/add",
 *     "add-form" = "/admin/structure/schedule_event/add/{schedule_event_type}",
 *     "edit-form" = "/admin/structure/schedule_event/{schedule_event}/edit",
 *     "delete-form" = "/admin/structure/schedule_event/{schedule_event}/delete",
 *     "collection" = "/admin/structure/schedule_event",
 *   },
 *   bundle_entity_type = "schedule_event_type",
 *   field_ui_base_route = "entity.schedule_event_type.edit_form"
 * )
 */
class Event extends ContentEntityBase implements EventInterface {

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
      ->setDescription(t('The name of the Event entity.'))
      ->setSettings([
        'max_length' => 250,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
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

    $fields['status']->setDescription(t('A boolean indicating whether the Event is published.'))
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
