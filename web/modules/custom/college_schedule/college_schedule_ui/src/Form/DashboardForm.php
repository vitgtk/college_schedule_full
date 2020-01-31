<?php

namespace Drupal\college_schedule_ui\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
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
   * Drupal\college_schedule_ui\ScheduleBuilderInterface definition.
   *
   * @var \Drupal\college_schedule_ui\ScheduleBuilderInterface
   */
  protected $scheduleBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->configFactory = $container->get('config.factory');
    $instance->tempstorePrivate = $container->get('tempstore.private');
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->scheduleBuilder = $container->get('college_schedule_ui.builder');
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

    $form['#attributes']['class'][] = 'cs-board--dashboard';
    /* Filter and buttons */
    $form['select_container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['cs-board--controls', 'container-inline'],
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
      '#default_value' => $this->monday()->format(DateTimeItemInterface::DATE_STORAGE_FORMAT),
    ];

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
    $form['select_container']['actions']['lunch'] = [
      '#type' => 'submit',
      '#name' => 'lunch',
      '#value' => $this->t('Add lunch'),
      '#button_type' => 'primary',
      '#ajax' => [
        'callback' => '::lunchCallback',
      ],
    ];

    /* Not used */
    $form['select_container']['actions']['add_container'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'actions--add-container',
        'class' => ['actions--add-container'],
      ],
    ];

    /* Schedule area */
    $form['schedule_area'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['cs-board-js--schedule-area', 'cs-board--schedule-area'],
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
  public function submitForm(array &$form, FormStateInterface $form_state) {}

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

    $group_program = (int) $form_state->getValue('group_program');
    $week = $form_state->getValue('week');

    $saturday = (bool) $form_state->getValue('saturday');

    $build = $this->scheduleBuilder->build($group_program, $week, $saturday);

    $response->addCommand(new HtmlCommand('.cs-board-js--schedule-area', $build));

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
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function lunchCallback(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $group_program = (int) $form_state->getValue('group_program');
    $week = $form_state->getValue('week');
    $saturday = (bool) $form_state->getValue('saturday');
    /** @var \Drupal\college_schedule_api\EditorInterface $editor */
    $editor = \Drupal::service('college_schedule_api.editor');

    $editor->addLunch($group_program, $week);
    $build = $this->scheduleBuilder->build($group_program, $week, $saturday);

    $response->addCommand(new HtmlCommand('.cs-board-js--schedule-area', $build));
  }

  /**
   * Helper function.
   *
   * @param int $recurrences
   *   Count recurrences.
   *
   * @return array
   *   Options list.
   */
  private function weekList(int $recurrences = 10) {
    $options = [];
    try {
      $startMonday = new \DateTime('Monday noon this week');
      $startMonday->modify('-1 week');
      $interval = new \DateInterval('P7D');
      $weeks = new \DatePeriod($startMonday, $interval, $recurrences);
      foreach ($weeks as $week) {
        $key = $week->format(DateTimeItemInterface::DATE_STORAGE_FORMAT);
        $options[$key] = $this->dateFormatter->format($week->format('U'), 'college_schedule_ui');
      }
    }
    catch (\Exception $e) {
      $this->logger('college_schedule_ui')->warning($e->getMessage());
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
    return new DrupalDateTime('Monday noon this week');
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
