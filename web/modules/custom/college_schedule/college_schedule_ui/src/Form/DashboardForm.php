<?php

namespace Drupal\college_schedule_ui\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\SettingsCommand;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DashboardForm.
 */
class DashboardForm extends FormBase {

  /**
   * Week format for options key.
   */
  const WEEK_KEY_FORMAT = 'U';

  const WEEK_LABEL_FORMAT = 'W: d.m.Y r';
  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Drupal\Core\TempStore\PrivateTempStoreFactory definition.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStoreFactory
   */
  protected $tempstorePrivate;

  /**
   * Drupal\Core\Datetime\DateFormatterInterface definition.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->configFactory = $container->get('config.factory');
    $instance->tempstorePrivate = $container->get('tempstore.private');
    $instance->dateFormatter = $container->get('date.formatter');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'college_schedule_dashboard';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#attached']['library'][] = 'college_schedule_ui/dashboard';
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $form['select_container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['container-inline'],
      ],
    ];
    $form['select_container']['group_program'] = [
      '#type' => 'select',
      '#options' => $this->groupProgramOptions(),
      '#title' => $this->t('Group'),
      '#weight' => '0',
    ];
    $form['select_container']['week'] = [
      '#type' => 'select',
      '#options' => $this->weekList(),
      '#title' => $this->t('Week'),
      '#weight' => '0',
      '#default_value' => $this->monday()->format(self::WEEK_KEY_FORMAT),
    ];
    // Saturday
    $form['select_container']['saturday'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Saturday'),
      '#weight' => '5',
      '#default_value' => FALSE,
    ];
    $form['select_container']['actions']['#type'] = 'actions';
    $form['select_container']['actions']['load'] = [
      '#type' => 'submit',
      '#name' => 'load',
      '#value' => $this->t('Load'),
      '#ajax' => [
        'callback' => '::loadCallback',
      ],
    ];
    $form['select_container']['actions']['save'] = [
      '#type' => 'submit',
      '#name' => 'save',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
      '#ajax' => [
        'callback' => '::saveCallback',
      ],
    ];

    $form['select_container']['actions']['add_container'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'actions--add-container',
        'class' => ['actions--add-container'],
      ],
    ];

    $form['schedule_area'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['schedule-area'],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    $weekTimestamp = $form_state->getValue('week');
    //dpm($this->dateFormatter->format($weekTimestamp, 'custom', 'r'));
  }

  /**
   * Load callback function.
   *
   * @param array $form
   *   Form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state object.
   */
  public function loadCallback(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $group_program = $form_state->getValue('group_program');
    $week = $form_state->getValue('week');
    $saturday = $form_state->getValue('saturday');
    $data = $this->loadSchedule($group_program, $week, $saturday);
    $response->addCommand(new SettingsCommand(['college_schedule' => $data]));

    $date = DrupalDateTime::createFromTimestamp($week);
    $build['content'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'schedule-area' . $week,
        'data-week' => $date->format(DateTimeItemInterface::DATE_STORAGE_FORMAT),
        'data-group' => $group_program,
        'data-saturday' => $saturday,
        'class' => ['schedule-area-days'],
      ],
    ];
    if ($saturday) {
      $build['content']['#attributes']['class'][] = 'schedule-area-days--saturday-on';
    }


    foreach ($data as $date_storage => $itemsByHour) {

      $element_key = 'day' . $date_storage;
      $build['content'][$element_key] = [
        '#type' => 'schedule_day',
        '#content' => $date_storage,
        '#date' => $date_storage,
        '#items' => $itemsByHour, /* events group by hour */
      ];
    }

    $response->addCommand(new HtmlCommand('.schedule-area', $build));
    // $response->addCommand(new HtmlCommand('.actions--add-container', $this->addLink($group_program, $week)));

    return $response;
  }

  /**
   * Save callback function.
   *
   * @param array $form
   *   Form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state object.
   */
  public function saveCallback(array &$form, FormStateInterface $form_state) {}

  /**
   * Add callback function.
   *
   * @param array $form
   *   Form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state object.
   *
   * @deprecated
   */
  public function addCallback(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $group_program = $form_state->getValue('group_program');
    $week = $form_state->getValue('week');
    $saturday = $form_state->getValue('saturday');
    $data = $this->loadSchedule($group_program, $week, $saturday);
    $response->addCommand(new SettingsCommand(['college_schedule' => $data]));
    $title = $this->t('Add event');
    $content = \Drupal::formBuilder()->getForm('\Drupal\college_schedule_ui\Form\EventsForm');

    $dialog_options = [
      'minHeight' => 200,
      'resizable' => TRUE,
    ];
    $settings = [];
    $response->addCommand(new OpenModalDialogCommand($title, $content, $dialog_options, $settings));
    return $response;
  }

  /**
   * Get schedule data.
   *
   * @param $group_program
   * @param $week
   * @param bool $saturday
   *
   * @return array
   */
  private function loadSchedule($group_program, $week, $saturday = FALSE) {
    $count = $saturday ? 6 : 5;
    $data = [];
    $date = DrupalDateTime::createFromTimestamp($week);
    /** @var \Drupal\college_schedule_ui\ScheduleDataInterface $scheduleStorage */
    $scheduleStorage = \Drupal::service('college_schedule_ui.schedule_data');
    $entities = $scheduleStorage->load($group_program, $week, $saturday);
    for ($i = 0; $i < $count; $i++) {
      $key = $date->format(DateTimeItemInterface::DATE_STORAGE_FORMAT);
      $data[$key] = $entities[$key];
      $date->modify('+ 24 hours');
    }

    return $data;
  }

  /**
   * Helper function.
   *
   * @return array
   *   Options list.
   */
  private function weekList() {
    $options = [];
    $date = $this->monday();
    $date->modify('- 1 week');

    for ($i = 1; $i < 6; $i++) {
      $key = $date->format(self::WEEK_KEY_FORMAT);
      $options[$key] = $this->dateFormatter->format($date->format('U'), 'college_schedule_ui');
      $date->modify('+ 1 week');
    }
    return $options;
  }

  /**
   * Current week Monday.
   *
   * @return \Drupal\Core\Datetime\DrupalDateTime
   *   Monday
   */
  private function monday() {
    return new DrupalDateTime('Monday noon -1 week');
  }

  /**
   * Helper function.
   *
   * @param null|int $group
   *   Grout ID.
   * @param null|int $week
   *   Week.
   *
   * @return array
   *   Link render array
   */
  private function addLink($group = NULL, $week = NULL) {
    $build['content'] = [
      '#type' => 'link',
      '#title' => $this->t('Add'),
      '#url' => Url::fromRoute('college_schedule_ui.events_form', [
        'group' => $group,
        'week' => $week,
      ]),
      '#options' => [
        'attributes' => [
          'class' => ['use-ajax', 'button', 'button--danger'],
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

  private function groupProgramOptions() {
    $departmentStorage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $departments = $departmentStorage->loadByProperties(['vid' => 'department']);
    /** @var \Drupal\Core\Entity\EntityStorageInterface $storage */
    $storage = \Drupal::entityTypeManager()->getStorage('group_program');
    $options = [];
    $items = $storage->loadMultiple();


    foreach ($items as $id => $group_program) {
      $options[$id] = $group_program->label();
    }

    $group_options = [];
    foreach ($departments as $department) {
      if ($groups = $storage->loadByProperties(['department' => $department->id()])) {
        $options = [];
        foreach ($groups as $id => $group_program) {
          $options[$id] = $group_program->label();
        }
        $group_options[$department->label()] = $options;
      }

    }

    return $group_options;
  }

}
