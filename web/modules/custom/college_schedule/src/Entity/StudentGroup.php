<?php

namespace Drupal\college_schedule\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Student group entity.
 *
 * @ingroup college_schedule
 *
 * @ContentEntityType(
 *   id = "student_group",
 *   label = @Translation("Student group"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\college_schedule\StudentGroupListBuilder",
 *     "views_data" = "Drupal\college_schedule\Entity\StudentGroupViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\college_schedule\Form\StudentGroupForm",
 *       "add" = "Drupal\college_schedule\Form\StudentGroupForm",
 *       "edit" = "Drupal\college_schedule\Form\StudentGroupForm",
 *       "delete" = "Drupal\college_schedule\Form\StudentGroupDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\college_schedule\StudentGroupHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\college_schedule\StudentGroupAccessControlHandler",
 *   },
 *   base_table = "student_group",
 *   translatable = FALSE,
 *   admin_permission = "administer student group entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/student_group/{student_group}",
 *     "add-form" = "/admin/structure/student_group/add",
 *     "edit-form" = "/admin/structure/student_group/{student_group}/edit",
 *     "delete-form" = "/admin/structure/student_group/{student_group}/delete",
 *     "collection" = "/admin/structure/student_group",
 *   },
 *   field_ui_base_route = "student_group.settings"
 * )
 */
class StudentGroup extends ContentEntityBase implements StudentGroupInterface {

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
      ->setDescription(t('The name of the Student group entity.'))
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

    $fields['status']->setDescription(t('A boolean indicating whether the Student group is published.'))
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
