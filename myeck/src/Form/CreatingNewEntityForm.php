<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 04.01.2016
 * Time: 10:16
 */

namespace Drupal\myeck\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\FieldStorageConfigInterface;
use Drupal\field_ui\FieldUI;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\Null;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;

use Drupal\myeck\Controller\MyeckCreatingNewEntityController;


class CreatingNewEntityForm extends FormBase {

  /**
   * The field type plugin manager.
   */
  protected $fieldTypePluginManager;

  /**
   * The list of type field
   */
  protected $listFieldType;

  /**
   * Widgets for The field type plugin.
   */
  protected $fieldWidgetPluginManager;

  /**
   * Formatters for The field type plugin.
   */
  protected $fieldFormatterPluginManager;

  /**
   * Constructs a new FieldStorageAddForm object.
   *
   */
//  public function __construct(FieldTypePluginManagerInterface $field_type_plugin_manager) {
  public function __construct() {
    $this->fieldTypePluginManager = \Drupal::service('plugin.manager.field.field_type');
    $this->fieldWidgetPluginManager = \Drupal::service('plugin.manager.field.widget');
    $this->fieldFormatterPluginManager = \Drupal::service('plugin.manager.field.formatter');
  }

  /**
   * return List of exists Type of Field
   */
  protected function getListTypeField() {
    if (empty($this->listFieldType)) {
      $field_type_options = array();
      $getUiDefinitions = $this->fieldTypePluginManager->getUiDefinitions();
      foreach ($this->fieldTypePluginManager->getGroupedDefinitions($getUiDefinitions) as $category => $field_types) {
        foreach ($field_types as $name => $field_type) {
          $field_type_options[$category][$name] = $field_type['label'];
        }
      }
      $this->listFieldType = $field_type_options;
    }
    return $this->listFieldType;
  }

  /**
   * return settings form for Type of Field
   */
  protected function getTypeFieldSettingsForm($field_type, $prefix = '') {
    $StorageSettings = $this->fieldTypePluginManager->getDefaultStorageSettings($field_type);
    if ($StorageSettings) {
      $element = array();
      foreach ($StorageSettings as $key => $value) {
        if (in_array($key, array('is_ascii', 'case_sensitive'))) {
          continue;
        }
        $element[$prefix . $key] = array(
          '#type' => is_bool($value) ? 'checkbox' : 'textfield',
          '#title' => $key,
          '#default_value' => $value,
        );
      }
      return $element;
    }
    else {
      return array();
    }

  }

  /**
   * Returns an array of formatter options for a field type.
   *
   * @param string|null $field_type
   *   (optional) The name of a field type, or NULL to retrieve all formatters.
   *
   * @return array
   *   If no field type is provided, returns a nested array of all formatters,
   *   keyed by field type.
   */
  protected function getListFormatterField($field_type = NULL) {
    $formatter_options = $this->fieldFormatterPluginManager->getOptions($field_type);
    return $formatter_options;
  }

  /**
   * Returns an array of widget type options for a field type.
   *
   * @param string|null $field_type
   *   (optional) The name of a field type, or NULL to retrieve all widget
   *   options. Defaults to NULL.
   *
   * @return array
   *   If no field type is provided, returns a nested array of all widget types,
   *   keyed by field type human name.
   */
  protected function getListWidgetField($field_type = NULL) {
    $widget_options = $this->fieldWidgetPluginManager->getOptions($field_type);
    return $widget_options;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'creating_new_entity_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['module']['module_name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Module name'),
      '#required' => TRUE,
      '#size' => 50,
      '#default_value' => '',
      '#maxlength' => 255,
    );
    $form['module']['module_id'] = array(
      '#type' => 'machine_name',
      '#maxlength' => 128,
      '#maxlength' => FieldStorageConfig::NAME_MAX_LENGTH - 10,
      '#required' => FALSE,
      '#machine_name' => array(
        'source' => array('module', 'module_name'),
        'exists' => '\Drupal\myeck\Form\CreatingNewEntityForm::getExistsName',
      ),
      '#description' => $this->t('A unique machine-readable name for this module.'),
    );
    $form['module']['module_description'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Module description'),
      '#required' => TRUE,
      '#default_value' => '',
      '#maxlength' => 255,
    );
    $form['entity']['entity_name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Entity name'),
      '#required' => TRUE,
      '#size' => 50,
      '#default_value' => '',
      '#maxlength' => 255,
    );
    $form['entity']['entity_machine_name'] = array(
      '#type' => 'machine_name',
      '#maxlength' => 128,
      '#maxlength' => FieldStorageConfig::NAME_MAX_LENGTH - 10,
      '#required' => FALSE,
      '#machine_name' => array(
        'source' => array('entity', 'entity_name'),
        'exists' => '\Drupal\myeck\Form\CreatingNewEntityForm::getExistsName',
      ),
      '#description' => $this->t('A unique machine-readable name for this module.'),
    );
    $form['entity']['entity_id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Entity id'),
      '#required' => TRUE,
      '#size' => 10,
      '#default_value' => 'id',
      '#maxlength' => 10,
    );
    $form['standard_fields'] = array(
      '#type' => 'fieldset',
      '#title' => t('add standard fields'),
    );
    $form['standard_fields']['created'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Add created entity'),
    );
    $form['standard_fields']['created_show'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('show created in lists'),
      '#attributes' => array('class' => array('left-m1')),
      '#states' => array(
        'visible' => array(
          ':input[name="created"]' => array('checked' => TRUE),
        ),
      ),
    );
    $form['standard_fields']['changed'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Add changed entity'),
    );
    $form['standard_fields']['changed_show'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('show changed in lists'),
      '#attributes' => array('class' => array('left-m1')),
      '#states' => array(
        'visible' => array(
          ':input[name="changed"]' => array('checked' => TRUE),
        ),
      ),
    );
    $form['standard_fields']['language'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Add language entity'),
    );
    $form['standard_fields']['language_show'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('show language in list'),
      '#attributes' => array('class' => array('left-m1')),
      '#states' => array(
        'visible' => array(
          ':input[name="language"]' => array('checked' => TRUE),
        ),
      ),
    );
    $form['standard_fields']['uuid'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Add uuid'),
    );
    $form['standard_fields']['user_id'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Add user_id'),
    );
    $form['standard_fields']['user_show'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('show user in list'),
      '#attributes' => array('class' => array('left-m1')),
      '#states' => array(
        'visible' => array(
          ':input[name="user_id"]' => array('checked' => TRUE),
        ),
      ),
    );
    // Gather valid field types.
    $TriggeringElement = $form_state->getTriggeringElement();
    if (!empty($TriggeringElement['#name'])) {
      if (strpos($TriggeringElement['#name'], '_type_')) {
        $nom = str_replace('storage_type_', '', $TriggeringElement['#name']);
      }
      else {
        $nom = 0;
      }
    }
    else {
      $nom = 0;
    }

    // Initialize the counter if it hasn't been set.
    if (empty($form_state->getValue('count_field'))) {
      $form['count_field'] = array('#type' => 'hidden', '#value' => 1);
      $count_field = 1;
    }
    else {
      $count_field = $form_state->getValue('count_field');
      if (!empty($TriggeringElement['#name']) && $TriggeringElement['#parents'][0] == 'add_field') {
        $count_field += 1;
      }
      $form['count_field'] = array(
        '#type' => 'hidden',
        '#value' => $count_field
      );
    }

    for ($x = 0; $x++ < $count_field;) {
      $field_type = $form_state->getValue('storage_type_' . $x);
      $variable = array(
        'name_group' => 'field_' . $x,
        'field_type' => $nom != $x ? $field_type : $TriggeringElement['#value'],
        'nom_field' => $x,
      );

      $form += $this->itemElement($variable);
    }

    $form['field_' . $count_field]['#suffix'] = '<div id="wraper-myeck-add-fields"></div>';

//    $form['actions']['#type'] = 'actions';
    $form['actions'] = array(
      '#type' => 'actions',
      '#states' => array(
        'visible' => array(
          ':input[name="module_name"]' => array('empty' => FALSE),
          ':input[name="entity_name"]' => array('empty' => FALSE),
          ':input[name="entity_id"]' => array('empty' => FALSE),
        ),
      ),
    );
    $form['actions']['add_field'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Add field'),
      '#submit' => array(array($this, 'addFieldSubmit')),
      '#ajax' => [
        'callback' => '\Drupal\myeck\Form\CreatingNewEntityForm::addFieldCallback',
        'wrapper' => 'wraper-myeck-add-fields',
        'progress' => array(
          'type' => 'throbber',
          'message' => t('add New Field...'),
        ),
      ],
    );
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    );
    $form['#attached']['library'][] = 'myeck/form-creating-entity';
    return $form;
  }

  public function itemElement(array $variable) {
    $name_group = $variable['name_group'];
    $nom = $variable['nom_field'];
    $form[$name_group] = array(
      '#type' => 'fieldset',
      '#title' => t('field information ' . $nom),
      '#attributes' => array('class' => array('fieldset-no-legend')),
    );

    $form[$name_group]['label_' . $nom] = array(
      '#type' => 'textfield',
      '#title' => $this->t('field name'),
//      '#required' => TRUE,
      '#size' => 32,
      '#default_value' => '',
      '#maxlength' => 255,
    );
    $form[$name_group]['id_' . $nom] = array(
      '#type' => 'machine_name',
      '#maxlength' => 128,
      '#maxlength' => FieldStorageConfig::NAME_MAX_LENGTH - 10,
      '#required' => FALSE,
      '#machine_name' => array(
        'source' => array($name_group, 'label_' . $nom),
        'exists' => '\Drupal\myeck\Form\CreatingNewEntityForm::getExistsName',
      ),
      '#description' => $this->t('A unique machine-readable name for this field.'),
    );

    $form[$name_group]['field_set_key_' . $nom] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Set entity key'),
    );
    if ($nom == 1) {
      $form[$name_group]['field_set_label_' . $nom] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Set as entity label'),
      );
      $form[$name_group]['field_set_key_' . $nom]['#states'] = array(
        'checked' => array(
          ':input[name="field_set_label_' . $nom . '"]' => array('checked' => TRUE),
        ),
      );
    }

    $form[$name_group]['storage_type_' . $nom] = array(
      '#type' => 'select',
      '#title' => $this->t('Type of field'),
      '#options' => $this->getListTypeField(),
      '#empty_option' => $this->t('- Select a field type -'),
      '#ajax' => [
        'callback' => '\Drupal\myeck\Form\CreatingNewEntityForm::updateFormatterWidgetField',
        //      'wrapper' => 'wraper-'.$name_group.'-widget-formatter',
        'event' => 'change',
        'progress' => array(
          'type' => 'throbber',
          'message' => t('update Formatter and Widget Field...'),
        ),
      ],
    );
    $form[$name_group]['group_widget_formatter_' . $nom] = $this->subItemElement($nom, $variable['field_type']);

    return $form;

  }

  public function subItemElement($nom, $type_field = NULL) {
    $group_element = array(
      '#prefix' => '<div id="wraper-field-' . $nom . '-widget-formatter">',
      '#suffix' => '</div>',
    );
    if ($type_field) {
      $group_element['settings_field'] = $this->getTypeFieldSettingsForm($type_field, 'settings_field_' . $nom . '_');
      if ($group_element['settings_field']) {
        $group_element['settings_field'] += array(
          '#type' => 'fieldset',
          '#title' => t('settings field'),
        );
      }
      $group_element['view'] = array(
        '#prefix' => '<div class="wraper-field-view-settings">',
        '#suffix' => '</div>',
      );
      $group_element['view']['view_enable_' . $nom] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Add in the manager views'),
      );
      $group_element['view']['view_formatter_' . $nom] = $this->getFormElementWidgetFormatterSelect('formatter', 'view_enable_' . $nom, $type_field);
      //     $group_element['view_formatter_' . $nom]['view_formatter_settings_' . $nom] = '<div id="wraper-settings-formatter-'.$nom.'"></div>';

      $group_element['view']['show_field_list_' . $nom] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('show field in lists'),
        '#states' => array(
          'visible' => array(
            ':input[name="view_enable_' . $nom . '"]' => array('checked' => TRUE),
          ),
        ),
      );
      $group_element['form'] = array(
        '#prefix' => '<div class="wraper-field-form-settings">',
        '#suffix' => '</div>',
      );
      $group_element['form']['form_enable_' . $nom] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('Add in the manager form'),
      );
      $group_element['form']['form_widget_' . $nom] = $this->getFormElementWidgetFormatterSelect('widget', 'form_enable_' . $nom, $type_field);
    }
    return $group_element;
  }

  protected function getFormElementWidgetFormatterSelect($type, $name_states, $type_field = NULL) {
    if (empty($type_field)) {
      $options = array();
    }
    else {
      switch ($type) {
        case 'widget':
          $options = $this->getListWidgetField($type_field);
          break;
        case 'formatter':
          $options = $this->getListFormatterField($type_field);
          break;
        default:
          $options = array();
          break;
      }

    }
    if ($options) {
      $element = array(
        '#type' => 'select',
        '#title' => $this->t($type . ' of field'),
        '#options' => $options,
        '#empty_option' => $this->t("- Select a field $type -"),
        '#title_display' => 'invisible',
        '#states' => array(
          'visible' => array(
            ':input[name="' . $name_states . '"]' => array('checked' => TRUE),
          ),
        ),

      );

      return $element;
    }
    else {
      return FALSE;
    }
  }

  public function updateFormatterWidgetField(array &$form, FormStateInterface $form_state) {
    $TriggeringElement = $form_state->getTriggeringElement();
    if (!empty($TriggeringElement['#name'])) {
      $nom = str_replace('storage_type_', '', $TriggeringElement['#name']);
      $form_element = $form['field_' . $nom]['group_widget_formatter_' . $nom];
      $output = drupal_render($form_element);

      $response = new AjaxResponse();
      $response->addCommand(new ReplaceCommand(
        '#wraper-field-' . $nom . '-widget-formatter',
        $output
      ));
      return $response;

    }
  }

  public function addFieldSubmit(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild();
  }

  public function addFieldCallback(array &$form, FormStateInterface $form_state) {
    $count_field = $form['count_field']['#value'];
    if ((integer) $count_field > 1) {
      return $form['field_' . $count_field];
    }
    else {
      return '<div id="wraper-myeck-add-fields"></div>';
    }

  }

  public static function getExistsName($id = '') {
    $dd = 5;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
//    if (strlen($form_state->getValue('phone_number')) < 3) {
//      $form_state->setErrorByName('phone_number', $this->t('The phone number is too short. Please enter a full phone number.'));
//    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
//    drupal_set_message($this->t('Your phone number is @number', array('@number' => $form_state->getValue('phone_number'))));
    $values = $form_state->getValues();
    $new_entity = new MyeckCreatingNewEntityController();
    $archive = $new_entity->generate($values);
    // Redirect to the archive file download.
    $session = \Drupal::request()->getSession();
    if (isset($session)) {
      $session->set('myeck_new_entity_download', $archive['archiveName']);
    }
    $form_state->setRedirect('myeck.new_entity_download');
  }


}