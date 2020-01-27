<?php

namespace Drupal\college_schedule_ui\Controller;

use Drupal\college_schedule\Entity\Event;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * Class DashboardController.
 */
class DashboardController extends ControllerBase {

  /**
   * Hello.
   *
   * @return array
   *   Return Hello string.
   */
  public function hello($name) {
    $build['#attached']['library'][] = 'core/drupal.dialog.ajax';


    $events = Event::loadMultiple();

    foreach ($events as $id => $event) {
      $event->set('group_id', 1);
      $event->save();
    }

    $build['m'] = [
      '#markup' => '3',
    ];


    dpm($build);
    return $build;
  }

}
