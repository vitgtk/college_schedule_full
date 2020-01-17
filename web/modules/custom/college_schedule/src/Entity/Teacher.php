<?php

namespace Drupal\college_schedule\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Teacher entity.
 *
 * @ingroup college_schedule
 *
 * @ContentEntityType(
 *   id = "teacher",
 *   label = @Translation("Teacher"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\college_schedule\TeacherListBuilder",
 *     "views_data" = "Drupal\college_schedule\Entity\TeacherViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\college_schedule\Form\TeacherForm",
 *       "add" = "Drupal\college_schedule\Form\TeacherForm",
 *       "edit" = "Drupal\college_schedule\Form\TeacherForm",
 *       "delete" = "Drupal\college_schedule\Form\TeacherDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\college_schedule\TeacherHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\college_schedule\TeacherAccessControlHandler",
 *   },
 *   base_table = "teacher",
 *   translatable = FALSE,
 *   admin_permission = "administer teacher entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/teacher/{teacher}",
 *     "add-form" = "/admin/structure/teacher/add",
 *     "edit-form" = "/admin/structure/teacher/{teacher}/edit",
 *     "delete-form" = "/admin/structure/teacher/{teacher}/delete",
 *     "collection" = "/admin/structure/teacher",
 *   },
 *   field_ui_base_route = "teacher.settings"
 * )
 */
class Teacher extends ContentEntityBase implements TeacherInterface {

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
      ->setDescription(t('The name of the Teacher entity.'))
      ->setSettings([
        'max_length' => 200,
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

    $fields['status']->setDescription(t('A boolean indicating whether the Teacher is published.'))
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
