<?php

namespace Drupal\college_schedule_ui\Form;

use Drupal\college_schedule\Entity\GroupProgramInterface;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\SettingsCommand;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EventsForm.
 */
class EventsForm extends FormBase {

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\college_schedule_ui\ScheduleCalendarInterface definition.
   *
   * @var \Drupal\college_schedule_ui\ScheduleCalendarInterface
   */
  protected $calendar;

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
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->calendar = $container->get('college_schedule_ui.calendar');
    $instance->tempstorePrivate = $container->get('tempstore.private');
    $instance->dateFormatter = $container->get('date.formatter');
    $instance->scheduleBuilder = $container->get('college_schedule_ui.builder');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'events_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, GroupProgramInterface $group = NULL, DrupalDateTime $day = NULL) {
    $form['#prefix'] = '<div id="modal_events_form">';
    $form['#suffix'] = '</div>';

    $form['#title'] = $this->t('Group @group, schedule @date', [
      '@group' => $group->label(),
      '@date' => $this->dateFormatter->format($day->getTimestamp()),
    ]);
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $form['#attached']['library'][] = 'college_schedule_ui/form';
    $form['event'] = [
      '#type' => 'container',
      '#tree' => TRUE,
    ];
    $form['event']['group'] = [
      '#type' => 'hidden',
      '#value' => $group->id(),
      '#weight' => 9,
    ];
    $form['event']['date'] = [
      '#type' => 'hidden',
      '#value' => $day->format(DateTimeItemInterface::DATE_STORAGE_FORMAT),
      '#weight' => 9,
    ];
    $form['discipline_teacher'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => 'schedule-form-inline',
      ],
    ];
    $form['subgroup_location'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => 'schedule-form-inline',
      ],
    ];
    $form['discipline_teacher']['discipline'] = [
      '#type' => 'select',
      '#title' => $this->t('Discipline'),
      '#description' => $this->t('Discipline'),
      '#options' => $this->disciplineOptions($group),
      '#default_value' => NULL,
      '#required' => TRUE,
      '#weight' => 1,
    ];
    $discipline_id = NULL;
    $form['discipline_teacher']['teacher'] = [
      '#type' => 'select',
      '#title' => $this->t('Teacher'),
      '#options' => $this->teacherOptions($discipline_id),
      '#default_value' => NULL,
      '#description' => $this->t('Discipline'),
      '#required' => TRUE,
      '#weight' => 2,
    ];
    /* subgroup */
    $form['subgroup_location']['subgroup'] = [
      '#type' => 'select',
      '#title' => $this->t('Subgroup'),
      '#empty_option' => $this->t('- All -'),
      '#empty_value' => 'all',
      '#description' => $this->t('Subgroup'),
      '#options' => $this->subgroupOptions($discipline_id),
      '#default_value' => NULL,
      '#required' => FALSE,
      '#weight' => 4,
    ];

    $form['subgroup_location']['location'] = [
      '#type' => 'select',
      '#title' => $this->t('Location'),
      '#description' => $this->t('Location'),
      '#options' => $this->locationOptions($discipline_id),
      '#default_value' => NULL,
      '#required' => TRUE,
      '#weight' => 4,
    ];

    $form['subgroup_location']['hours'] = [
      '#type' => 'select',
      '#title' => $this->t('Hours'),
      '#multiple' => TRUE,
      '#options' => $this->hours(),
      '#default_value' => NULL,
      '#description' => $this->t('Hours'),
      '#required' => TRUE,
      '#weight' => 5,
    ];


    $form['actions']['#type'] = 'actions';
    $form['actions']['#weight'] = 10;
    $form['actions']['save'] = [
      '#type' => 'submit',
      '#name' => 'save',
      '#attributes' => [
        'class' => [
          'use-ajax',
        ],
      ],
      '#value' => $this->t('Add'),
      '#button_type' => 'primary',
      '#ajax' => [
        'callback' => '::saveCallback',
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
   * Save callback function.
   *
   * @param array $form
   *   Form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state object.
   */
  public function saveCallback(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $label = $form_state->getValue('label');
    if ($form_state->hasAnyErrors()) {
      $response->addCommand(new ReplaceCommand('#modal_events_form', $form));
    }
    else {
      $entry = $form_state->getValue('event');
      $group_id = $entry['group'];
      $entry['type'] = 'training';
      $entry['discipline'] = $form_state->getValue('discipline');
      $entry['teacher'] = $form_state->getValue('teacher');
      $entry['location'] = $form_state->getValue('location');
      $entry['subgroup'] = ($form_state->getValue('subgroup') != 'all') ? $form_state->getValue('subgroup') : NULL;

      $storage = $this->entityTypeManager->getStorage('schedule_event');
      $hours = $form_state->getValue('hours');
      dpm($hours ,' hours');
      dpm($entry ,' hours');
      foreach ($hours as $id => $hour) {
        /** @var \Drupal\college_schedule\Entity\EventInterface $event */
        $event = $storage->create($entry);
        $event->set('hour', $hour);
        $event->set('name', $form_state->getValue('discipline'));
        $event->save();
      }


      $monday = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATE_STORAGE_FORMAT, $entry['date'])->modify('monday this week');
      $build = $this->scheduleBuilder->build((int) $group_id, $monday->format(DateTimeItemInterface::DATE_STORAGE_FORMAT));
      $content = [
        '#markup' => $this->t('Label: @label Date: @monday @date1 @date2', [
          '@label' => $label,
          '@monday' => $monday->format(DateTimeItemInterface::DATE_STORAGE_FORMAT),
        ]),
      ];

      $response->addCommand(new HtmlCommand('.cs-board-js--schedule-area', $build));
      $response->addCommand(new OpenModalDialogCommand("Success!", $content, ['width' => 700]));
    }
    return $response;
  }

  /**
   * Helper.
   *
   * @param \Drupal\college_schedule\Entity\GroupProgramInterface|NULL $group
   *
   * @return array
   */
  private function disciplineOptions(GroupProgramInterface $group = NULL) {
    $options = [];
    if ($group instanceof GroupProgramInterface && !$group->get('disciplines')->isEmpty()) {
      foreach ($group->get('disciplines')->referencedEntities() as $item) {
        $options[$item->id()] = $item->label();
      }
    }
    return $options;
  }

  /**
   * Helper.
   *
   * @param int|NULL $discipline_id
   *
   * @return array
   */
  public function subgroupOptions(int $discipline_id = NULL) {
    $options = [];
    $options['1'] = $this->t('@num subgroup', ['@num' => 1]);
    $options['2'] = $this->t('@num subgroup', ['@num' => 2]);
    return $options;
  }

  /**
   * Helper.
   *
   * @param int|NULL $discipline_id
   *
   * @return array
   *   Options list.
   */
  public function locationOptions(int $discipline_id = NULL) {
    $options = [];
    try {
      $storage = $this->entityTypeManager->getStorage('event_location');
    }
    catch (PluginNotFoundException $e) {
      return $options;
    }
    catch (InvalidPluginDefinitionException $e) {
      return $options;
    }
    foreach ($storage->loadMultiple() as $item) {
      $options[$item->id()] = $item->label();
    }
    return $options;
  }

  /**
   * Helper.
   *
   * @todo Получить из производственного календаря расписание.
   */
  private function hours() {
    $options = [];
    $timetable = $this->calendar->getTimetable();
    foreach ($timetable->timing() as $hour => $times) {
      $options[$hour] = $hour . '. ' . $times['start'] . '-' . $times['end'];
    }
    unset($options['16']);
    unset($options['17']);
    return $options;
  }

  private function teacherOptions(int $discipline_id = NULL) {
    $options = [];
    $storage = $this->entityTypeManager->getStorage('teacher');
    foreach ($storage->loadMultiple() as $item) {
      $options[$item->id()] = $item->label();
    }
    return $options;
  }

}
