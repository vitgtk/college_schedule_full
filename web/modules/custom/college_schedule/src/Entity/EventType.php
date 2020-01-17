<?php

namespace Drupal\college_schedule\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Event type entity.
 *
 * @ConfigEntityType(
 *   id = "schedule_event_type",
 *   label = @Translation("Event type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\college_schedule\EventTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\college_schedule\Form\EventTypeForm",
 *       "edit" = "Drupal\college_schedule\Form\EventTypeForm",
 *       "delete" = "Drupal\college_schedule\Form\EventTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\college_schedule\EventTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "schedule_event_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "schedule_event",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/college-schedule/schedule_event_type/{schedule_event_type}",
 *     "add-form" = "/admin/config/college-schedule/schedule_event_type/add",
 *     "edit-form" = "/admin/config/college-schedule/schedule_event_type/{schedule_event_type}/edit",
 *     "delete-form" = "/admin/config/college-schedule/schedule_event_type/{schedule_event_type}/delete",
 *     "collection" = "/admin/config/college-schedule/schedule_event_type"
 *   }
 * )
 */
class EventType extends ConfigEntityBundleBase implements EventTypeInterface {

  /**
   * The Event type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Event type label.
   *
   * @var string
   */
  protected $label;

}
