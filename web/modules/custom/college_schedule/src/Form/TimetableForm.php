<?php

namespace Drupal\college_schedule\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class TimetableForm.
 */
class TimetableForm extends EntityForm {

  /**
   * Time validation pattern.
   */
  const TIME_PATTERN = '([01]?[0-9]|2[0-3]):[0-5][0-9]';

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\college_schedule\Entity\TimetableInterface $timetable */
    $timetable = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $timetable->label(),
      '#description' => $this->t("Label for the Timetable."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $timetable->id(),
      '#machine_name' => [
        'exists' => '\Drupal\college_schedule\Entity\Timetable::load',
      ],
      '#disabled' => !$timetable->isNew(),
    ];

    $timing = $timetable->timing();
    $form['timing'] = [
      '#type' => 'details',
      '#title' => $this->t('Timing'),
      '#tree' => TRUE,
    ];
    for ($i = 1; $i < 18; $i++) {
      $required = ($i < 16) ?? FALSE;
      $form['timing'][$i] = [
        '#type' => 'details',
        '#title' => $this->t('Time @num', ['@num' => $i]),
        '#open' => TRUE,
        '#attributes' => [
          'class' => ['container-inline'],
        ],
      ];
      $form['timing'][$i]['start'] = [
        '#type' => 'textfield',
        '#maxlength' => 5,
        '#size' => 15,
        '#pattern' => self::TIME_PATTERN,
        '#required' => $required,
        '#title' => $this->t('Start time'),
        '#default_value' => $timing[$i]['start'] ?? '',
      ];
      $form['timing'][$i]['end'] = [
        '#type' => 'textfield',
        '#maxlength' => 5,
        '#size' => 15,
        '#pattern' => self::TIME_PATTERN,
        '#required' => $required,
        '#title' => $this->t('End time'),
        '#default_value' => $timing[$i]['end'] ?? '',
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $timetable = $this->entity;
    $status = $timetable->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Timetable.', [
          '%label' => $timetable->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Timetable.', [
          '%label' => $timetable->label(),
        ]));
    }
    $form_state->setRedirectUrl($timetable->toUrl('collection'));
  }

}
