<?php

namespace Drupal\college_schedule_ui\Controller;

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
    $build['open_modal'] = [
      '#type' => 'link',
      '#title' => new FormattableMarkup('Open node @nid in modal!', ['@nid' => 1]),
      '#url' => Url::fromRoute('college_schedule_ui.events_form'),
      '#options' => [
        'attributes' => [
          'class' => ['use-ajax'],
          'data-dialog-type' => 'modal',
          'data-dialog-options' => Json::encode([
            'width' => 700,
          ]),
        ],
      ],
      '#attached' => ['library' => ['core/drupal.dialog.ajax']],
    ];
    return $build;
  }

}
