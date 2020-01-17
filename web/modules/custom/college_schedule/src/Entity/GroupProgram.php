<?php

namespace Drupal\college_schedule\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Group program entity.
 *
 * @ingroup college_schedule
 *
 * @ContentEntityType(
 *   id = "group_program",
 *   label = @Translation("Group program"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\college_schedule\GroupProgramListBuilder",
 *     "views_data" = "Drupal\college_schedule\Entity\GroupProgramViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\college_schedule\Form\GroupProgramForm",
 *       "add" = "Drupal\college_schedule\Form\GroupProgramForm",
 *       "edit" = "Drupal\college_schedule\Form\GroupProgramForm",
 *       "delete" = "Drupal\college_schedule\Form\GroupProgramDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\college_schedule\GroupProgramHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\college_schedule\GroupProgramAccessControlHandler",
 *   },
 *   base_table = "group_program",
 *   translatable = FALSE,
 *   admin_permission = "administer group program entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/group_program/{group_program}",
 *     "add-form" = "/admin/structure/group_program/add",
 *     "edit-form" = "/admin/structure/group_program/{group_program}/edit",
 *     "delete-form" = "/admin/structure/group_program/{group_program}/delete",
 *     "collection" = "/admin/structure/group_program",
 *   },
 *   field_ui_base_route = "group_program.settings"
 * )
 */
class GroupProgram extends ContentEntityBase implements GroupProgramInterface {

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
      ->setDescription(t('The name of the Group program entity.'))
      ->setSettings([
        'max_length' => 50,
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

    $fields['status']->setDescription(t('A boolean indicating whether the Group program is published.'))
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
