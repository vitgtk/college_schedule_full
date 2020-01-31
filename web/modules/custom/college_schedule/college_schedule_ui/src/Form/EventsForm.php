<?php

namespace Drupal\college_schedule_ui\Form;

use Drupal\college_schedule\Entity\DisciplineInterface;
use Drupal\college_schedule\Entity\GroupProgramInterface;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
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
   * Drupal\college_schedule_api\ScheduleCalendarInterface definition.
   *
   * @var \Drupal\college_schedule_api\ScheduleCalendarInterface
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
    $instance->calendar = $container->get('college_schedule_api.calendar');
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
  public function buildForm(array $form, FormStateInterface $form_state, GroupProgramInterface $group = NULL, DrupalDateTime $day = NULL, int $hour = NULL) {
    $form['#prefix'] = '<div id="modal_events_form">';
    $form['#suffix'] = '</div>';

    $is_all = (bool) $form_state->getValue('all_options');
    $discipline_default = $form_state->getValue('default') ?? NULL;
    $form['#title'] = $this->t('Group @group, schedule @date', [
      '@group' => $group->label(),
      '@date' => $this->dateFormatter->format($day->getTimestamp()),
    ]);
    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
    $form['#attached']['library'][] = 'college_schedule_ui/form';

    $form['event'] = [
      '#type' => 'container',
     // '#tree' => TRUE,
      '#attributes' => [
        'class' => 'schedule-form-inline',
      ],
    ];
    $form['event']['group_id'] = [
      '#type' => 'hidden',
      '#value' => $group->id(),
      '#weight' => -10,
    ];
    $form['event']['date'] = [
      '#type' => 'hidden',
      '#value' => $day->format(DateTimeItemInterface::DATE_STORAGE_FORMAT),
      '#weight' => -10,
    ];
    $form['event']['discipline'] = [
      '#type' => 'select',
      '#title' => $this->t('Discipline'),
      '#description' => $this->t('Discipline'),
      '#options' => $this->disciplineOptions($group),
      '#default_value' => $discipline_default,
      '#required' => TRUE,
      '#weight' => 1,
      '#ajax' => [
        'event' => 'change',
        'callback' => '::reloadCallback',
      ],
    ];
    $discipline_id = NULL;
    $form['event']['teacher'] = [
      '#type' => 'select',
      '#title' => $this->t('Teacher'),
      '#options' => $this->teacherOptions($is_all, $discipline_default),
      '#default_value' => NULL,
      '#description' => $this->t('Discipline'),
      '#required' => TRUE,
      '#weight' => 2,
    ];
    /* subgroup */
    $form['event']['subgroup'] = [
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

    $form['event']['location'] = [
      '#type' => 'select',
      '#title' => $this->t('Location'),
      '#description' => $this->t('Location'),
      '#options' => $this->locationOptions($is_all, $discipline_id),
      '#default_value' => NULL,
      '#required' => TRUE,
      '#weight' => 4,
    ];
    /** @var \Drupal\college_schedule_api\ScheduleDataInterface $data */
    $data = \Drupal::service('college_schedule_api.data');
    $data->getNotFreeHours($group->id(), $day->format(DATETIME_DATE_STORAGE_FORMAT));

    $default_hours = ($hour === NULL) ? NULL : $hour;
    $form['event']['hours'] = [
      '#type' => 'select',
      '#title' => $this->t('Hours'),
      '#multiple' => TRUE,
      '#options' => $this->hours(),
      '#default_value' => $default_hours,
      '#description' => $this->t('Hours'),
      '#required' => TRUE,
      '#weight' => 5,
      '#size' => 2,
    ];

    $form['all_options'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('All options'),
      '#description' => $this->t('All options to choose'),
      '#default_value' => FALSE,
      '#weight' => 9,
      '#ajax' => [
        'event' => 'change',
        'callback' => '::reloadCallback',
      ],
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
   * Reload callback.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function reloadCallback(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $is_all = (bool) $form_state->getValue('all_options');
    $discipline = $form_state->getValue('discipline');

    $form['event']['teacher']['#options'] = $this->teacherOptions($is_all, $discipline);
    $form['event']['location']['#options'] = $this->locationOptions($is_all, $discipline);

    $response->addCommand(new ReplaceCommand('#modal_events_form', $form));
    return $response;
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

      $group_id = $form_state->getValue('group_id');
      $entry = [
        'group_id' => $group_id,
        'date' => $form_state->getValue('date'),
      ];
      $entry['type'] = 'training';
      $entry['discipline'] = $form_state->getValue('discipline');
      $entry['teacher'] = $form_state->getValue('teacher');
      $entry['location'] = $form_state->getValue('location');
      $entry['subgroup'] = ($form_state->getValue('subgroup') != 'all') ? $form_state->getValue('subgroup') : NULL;

      $storage = $this->entityTypeManager->getStorage('schedule_event');
      $hours = $form_state->getValue('hours');

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
   * @param bool $is_all
   * @param int|NULL $discipline_id
   *
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function teacherOptions(bool $is_all = FALSE, int $discipline_id = NULL) {
    $options = [];

    if ($discipline_id) {
      $discipline = $this->entityTypeManager->getStorage('discipline')->load($discipline_id);
      $label = (string) $this->t('- Teachers of the discipline @discipline -', [
        '@discipline' => $discipline->label(),
      ]);
      \Drupal::messenger()->addStatus($label);
      if ($discipline instanceof DisciplineInterface && !$discipline->get('teachers')->isEmpty()) {
        foreach ($discipline->get('teachers')->referencedEntities() as $item) {
          $options[$label][$item->id()] = $item->label();
        }
      }
    }

    if ($is_all || !$discipline_id) {
      $label = (string) $this->t('- All teachers -');
      $storage = $this->entityTypeManager->getStorage('teacher');
      foreach ($storage->loadMultiple() as $item) {
        $options[$label][$item->id()] = $item->label();
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
  private function subgroupOptions(int $discipline_id = NULL) {
    $options = [];
    $options['1'] = $this->t('@num subgroup', ['@num' => 1]);
    $options['2'] = $this->t('@num subgroup', ['@num' => 2]);
    return $options;
  }

  /**
   * Helper.
   *
   * @param bool $is_all
   * @param int|NULL $discipline_id
   *
   * @return array
   *   Options list.
   */
  private function locationOptions(bool $is_all = FALSE, int $discipline_id = NULL) {
    $options = [];


    if ($discipline_id) {
      $discipline = $this->entityTypeManager->getStorage('discipline')->load($discipline_id);
      $label = (string) $this->t('- Locations for the discipline @discipline -', [
        '@discipline' => $discipline->label(),
      ]);
      if ($discipline instanceof DisciplineInterface && !$discipline->get('locations')->isEmpty()) {
        foreach ($discipline->get('locations')->referencedEntities() as $item) {
          $options[$label][$item->id()] = $item->label();
        }
      }
    }

    if ($is_all || !$discipline_id) {
      $label = (string) $this->t('- All locations -');
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
        $options[$label][$item->id()] = $item->label();
      }
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

}
