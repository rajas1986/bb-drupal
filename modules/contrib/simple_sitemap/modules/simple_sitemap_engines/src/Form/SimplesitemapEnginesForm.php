<?php

namespace Drupal\simple_sitemap_engines\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\simple_sitemap\Entity\SimpleSitemap;
use Drupal\simple_sitemap\Form\FormHelper;
use Drupal\simple_sitemap_engines\Entity\SimpleSitemapEngine;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form for managing search engine submission settings.
 */
class SimplesitemapEnginesForm extends ConfigFormBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * SimplesitemapEnginesForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager, DateFormatter $date_formatter) {
    parent::__construct($config_factory);
    $this->entityTypeManager = $entity_type_manager;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'simple_sitemap_engines_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['simple_sitemap_engines.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('simple_sitemap_engines.settings');

    $form['#tree'] = TRUE;

    $form['settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('General submission settings'),
      '#prefix' => FormHelper::getDonationText(),
    ];

    $form['settings']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Submit the sitemap to search engines'),
      '#description' => $this->t("This enables/disables sitemap submitting; don't forget to choose sitemaps below."),
      '#default_value' => $config->get('enabled'),
    ];

    $form['settings']['submission_interval'] = [
      '#type' => 'select',
      '#title' => $this->t('Submission interval'),
      '#options' => FormHelper::getCronIntervalOptions(),
      '#default_value' => $config->get('submission_interval'),
      '#states' => [
        'visible' => [':input[name="settings[enabled]"]' => ['checked' => TRUE]],
      ],
    ];

    $form['engines'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Sitemap specific settings'),
      '#markup' => '<div class="description">' . $this->t('Choose which sitemaps are to be submitted to which search engines.<br>Sitemaps can be configured <a href="@url">here</a>.', ['@url' => $GLOBALS['base_url'] . '/admin/config/search/simplesitemap']) . '</div>',
    ];

    $engines = SimpleSitemapEngine::loadMultiple();
    foreach ($engines as $engine_id => $engine) {
      $form['engines'][$engine_id] = [
        '#type' => 'details',
        '#title' => $engine->label(),
        '#open' => !empty($engine->sitemap_variants) || count($engines) === 1,
      ];
      $form['engines'][$engine_id]['variants'] = [
        '#type' => 'select',
        '#title' => $this->t('Sitemaps'),
        '#options' => array_map(
          function ($sitemap) {
            return $sitemap->label();
          },
          SimpleSitemap::loadMultiple()
        ),
        '#default_value' => $engine->sitemap_variants,
        '#multiple' => TRUE,
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    foreach (SimpleSitemapEngine::loadMultiple() as $engine_id => $engine) {
      if (!empty($values = $form_state->getValue([
        'engines',
        $engine_id,
        'variants',
      ]))) {
        $submit = TRUE;
      }
      $engine->sitemap_variants = $values;
      $engine->save();
    }

    $config = $this->config('simple_sitemap_engines.settings');

    $enabled = (bool) $form_state->getValue(['settings', 'enabled']);
    $config->set('enabled', $enabled);
    $config->set('submission_interval', $form_state->getValue([
      'settings',
      'submission_interval',
    ]));
    $config->save();

    if ($enabled && empty($submit)) {
      $this->messenger()->addWarning($this->t('No sitemaps have been selected for submission.'));
    }

    parent::submitForm($form, $form_state);
  }

}
