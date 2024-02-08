<?php

namespace Drupal\resumeslackupdate\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Settings form for the module.
 */
class SettingsForm extends ConfigFormBase {
  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;
  /**
   * Configuration settings.
   *
   * @var string
   */
  const SETTINGS = 'resumeslackupdate.module.settings';

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get("entity_type.manager")
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return "resumeslackupdate.module.settingsform";
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $query = $this->entityTypeManager->getStorage("taxonomy_term")->getQuery();
    $term_tids = $query->accessCheck(FALSE)->condition("vid", "skills")->execute();
    $config = $this->config(static::SETTINGS);

    $form["select_skill"] = [
      '#type' => 'fieldset',
      '#title' => 'Select the skills',
      '#description' => $this->t("Based on the selected skills resume will be sent to slack channel"),
    ];
    foreach ($term_tids as $value) {
      /** @var \Drupal\taxonomy\Entity\Term */
      $term = $this->entityTypeManager->getStorage("taxonomy_term")->load($value);
      $form["select_skill"]["select_$value"] = [
        '#type' => 'checkbox',
        '#title' => $term->get('name')->getString(),
        '#default_value' => $config->get('selected_skills')["select_$value"] ?? 0,
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $values = $form_state->getValues();
    $selected_values = array_filter(
      $values,
      fn($v, $k) => str_starts_with($k, "select_"),
      ARRAY_FILTER_USE_BOTH
    );
    $this->config(static::SETTINGS)
      ->set('selected_skills', $selected_values)
      ->save();

    parent::submitForm($form, $form_state);
  }

}
