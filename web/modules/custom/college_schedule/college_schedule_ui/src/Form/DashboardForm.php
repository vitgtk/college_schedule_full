<?php

namespace Drupal\college_schedule_ui\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\SettingsCommand;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
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
    $form['select_container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['container-inline'],
      ],
    ];
    $form['select_container']['group_program'] = [
      '#type' => 'select',
      '#options' => [
        '1' => '12о',
        '2' => '22о',
        '3' => '32о',
      ],
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
    dpm($this->dateFormatter->format($weekTimestamp, 'custom', 'r'));
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

    $items = [];
    for ($i = 1; $i < 16; $i++) {
      $items[] = [
        'event_id' => 0,
        'label' => 'Empty',
        'day_id' => $i,
        'time' => '08:00-08:45',
      ];
    }
    //dpm($items);
    // data-schedule-event-number schedule-event-item
    foreach ($data as $date_storage => $item) {

      $element_key = 'day' . $date_storage;
      $build['content'][$element_key] = [
        '#type' => 'schedule_day',
        '#content' => $date_storage,
        '#date' => $date_storage,
        '#items' => $items,
      ];
    }

    $response->addCommand(new HtmlCommand('.schedule-area', $build));

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
    for ($i = 0; $i < $count; $i++) {
      $key = $date->format(DateTimeItemInterface::DATE_STORAGE_FORMAT);
      $data[$key] = $date->format('r');
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

}
