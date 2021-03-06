<?php

namespace Drupal\college_schedule\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class EventTypeForm.
 */
class EventTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $schedule_event_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $schedule_event_type->label(),
      '#description' => $this->t("Label for the Event type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $schedule_event_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\college_schedule\Entity\EventType::load',
      ],
      '#disabled' => !$schedule_event_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $schedule_event_type = $this->entity;
    $status = $schedule_event_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Event type.', [
          '%label' => $schedule_event_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Event type.', [
          '%label' => $schedule_event_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($schedule_event_type->toUrl('collection'));
  }

}
