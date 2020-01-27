<?php

namespace Drupal\college_schedule\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Timetable entity.
 *
 * @ConfigEntityType(
 *   id = "timetable",
 *   label = @Translation("Timetable"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\college_schedule\TimetableListBuilder",
 *     "form" = {
 *       "add" = "Drupal\college_schedule\Form\TimetableForm",
 *       "edit" = "Drupal\college_schedule\Form\TimetableForm",
 *       "delete" = "Drupal\college_schedule\Form\TimetableDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\college_schedule\TimetableHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "timetable",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/timetable/{timetable}",
 *     "add-form" = "/admin/structure/timetable/add",
 *     "edit-form" = "/admin/structure/timetable/{timetable}/edit",
 *     "delete-form" = "/admin/structure/timetable/{timetable}/delete",
 *     "collection" = "/admin/config/college-schedule/timetable"
 *   }
 * )
 */
class Timetable extends ConfigEntityBase implements TimetableInterface {

  /**
   * The Timetable ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Timetable label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Timetable label.
   *
   * @var string
   */
  protected $time;

  /**
   * Time table.
   *
   * @var array
   */
  protected $timing;

  /**
   * Return timing list.
   *
   * @return array
   *   Timing list
   */
  public function timing() {
    return $this->timing;
  }

  /**
   * Day length.
   *
   * @param int $last
   *   Last lesson number.
   *
   * @return string
   *   Duration
   */
  public function duration($last = 15) {
    return $this->timing[1]['start'] . '-' . $this->timing[$last]['end'];
  }

  /**
   * {@inheritdoc}
   */
  public function hourDuration($hourId) {
    if (empty($this->timing[$hourId])) {
      return NULL;
    }
    $times = $this->timing[$hourId];
    return $times['start'] . '-' . $times['end'];
  }

}
