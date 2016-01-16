<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 11.01.2016
 * Time: 23:39
 */

namespace Drupal\myeck\Controller;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Archiver\ArchiveTar;


class MyeckCreatingNewEntityController {

  /**
   * The filename being written.
   *
   * @var string
   */
  protected $archiveName;


  /**
   * Registers a successful package or profile archive operation.
   *
   * @param array &$return
   *   The return value, passed by reference.
   * @param array $package
   *   The package or profile.
   */
  protected function success(array &$return, array $package) {
    $type = 'module';
    $return[] = [
      'success' => TRUE,
      // Archive writing doesn't merit a message, and if done through the UI
      // would appear on the subsequent page load.
      'display' => FALSE,
      'message' => '@type @package written to archive.',
      'variables' => [
        '@type' => $type,
        '@package' => $package['name']
      ],
    ];
  }

  /**
   * Registers a failed package or profile archive operation.
   *
   * @param array &$return
   *   The return value, passed by reference.
   * @param array $package
   *   The package or profile.
   * @param \Exception $exception
   *   The exception object.
   * @param string $message
   *   Error message when there isn't an Exception object.
   */
  protected function failure(array &$return, array $package, \Exception $exception, $message = '') {
    $type = 'module';
    $return[] = [
      'success' => FALSE,
      // Archive writing doesn't merit a message, and if done through the UI
      // would appear on the subsequent page load.
      'display' => FALSE,
      'message' => '@type @package not written to archive. Error: @error.',
      'variables' => [
        '@type' => $type,
        '@package' => $package['name'],
        '@error' => isset($exception) ? $exception->getMessage() : $message,
      ],
    ];
  }


  /**
   * Writes a file to the file system, creating its directory as needed.
   *
   * @param string $directory
   *   The extension's directory.
   * @param array $file
   *   Array with the following keys:
   *   - 'filename': the name of the file.
   *   - 'subdirectory': any subdirectory of the file within the extension
   *      directory.
   *   - 'string': the contents of the file.
   * @param ArchiveTar $archiver
   *   The archiver.
   *
   * @throws Exception
   */
  protected function generateFile($directory, array $file, ArchiveTar $archiver) {
    $filename = $directory;
    if (!empty($file['subdirectory'])) {
      $filename .= '/' . $file['subdirectory'];
    }
    $filename .= '/' . $file['filename'];
    // Set the mode to 0644 rather than the default of 0600.
    if ($archiver->addString($filename, $file['string'], FALSE, ['mode' => 0644]) === FALSE) {
      throw new \Exception($this->t('Failed to archive file @filename.', ['@filename' => $file['filename']]));
    }
  }

  /**
   * Writes a package or profile's files to an archive.
   *
   * @param array &$return
   *   The return value, passed by reference.
   * @param array $package
   *   The package or profile.
   * @param ArchiveTar $archiver
   *   The archiver.
   */
  protected function generatePackage(array &$return, array $package, ArchiveTar $archiver) {
    $success = TRUE;
    foreach ($package['files'] as $file) {
      try {
        $this->generateFile($package['directory'], $file, $archiver);
      }
      catch (\Exception $exception) {
        $this->failure($return, $package, $exception);
        $success = FALSE;
        break;
      }
    }
    if ($success) {
      $this->success($return, $package);
    }
  }

  protected function generateYmlfile($name, $values) {
    $file_info = array();
    switch ($name) {
      case 'info':
        $file_info['filename'] = $values['module_id'] . '.info.yml';
        $file_info['subdirectory'] = NULL;
        $file_text = array(
          'name' => $values['module_name'],
          'type' => 'module',
          'description' => $values['module_description'],
          'package' => 'MyModule',
          'core' => '8.x',
          'version' => '8.1.dev',
          'dependencies' => array('myeck'),
        );
        break;
      case 'links_action':
        $file_info['filename'] = $values['module_id'] . '.links.action.yml';
        $file_info['subdirectory'] = NULL;
        $file_text = '# All action links for this module';
        break;
      case 'links_menu':
        $file_info['filename'] = $values['module_id'] . '.links.menu.yml';
        $file_info['subdirectory'] = NULL;
        $file_text = '# In combination with the routing file, this replaces hook_menu for the module.';
        break;
      case 'links_task':
        $file_info['filename'] = $values['module_id'] . '.links.task.yml';
        $file_info['subdirectory'] = NULL;
        $file_text = '# The "Settings" tab will appear on the entity settings page and other tabs.';
        break;
      case 'permissions':
        $file_info['filename'] = $values['module_id'] . '.permissions.yml';
        $file_info['subdirectory'] = NULL;
        $file_text = '# this file for extra  permissions ';
        break;
      case 'routing':
        $file_info['filename'] = $values['module_id'] . '.routing.yml';
        $file_info['subdirectory'] = NULL;
        $file_text = '# this file for extra routings';
        break;
    }

    $file_info['string'] = is_array($file_text) ? Yaml::encode($file_text) : $file_text;
    return $file_info;
  }

  protected function generatePhpfile($name, $values) {
    $file_info = array();
    $name_file = str_replace('Entity', '', $name);

    $file_info['filename'] = $values['entity_name_class'] . $name_file . '.php';
    switch ($name) {
      case 'Entity':
      case 'EntityViewsData':
        $file_info['subdirectory'] = 'src/Entity';
        break;

      case 'EntityDeleteForm':
      case 'EntityForm':
      case 'EntitySettingsForm':
        $file_info['subdirectory'] = 'src/Form';
        break;
      case 'EntityInterface':
      case 'EntityAccessControlHandler':
        $file_info['subdirectory'] = 'src';
        break;
      case 'EntityListBuilder':
        $file_info['subdirectory'] = 'src/Entity';
        break;
      case 'install':
        $file_info['subdirectory'] = NULL;
        $file_info['filename'] = $values['module_id'] . '.install';
    }
    $build_file = array(
      '#theme' => 'Myeck_skelet_' . $name,
      '#values' => $values,
    );
    $file_info['string'] = \Drupal::service('renderer')->render($build_file);
    return $file_info;
  }


  public function generate(array $values) {
    $this->prepareValues($values);
    $filename = $values['module_id'];
    $package = array(
      'directory' => $filename,
    );
    $lists_yml_file = array(
      'info',
      'links_action',
      'links_menu',
      'links_task',
      'permissions',
      'routing'
    );
    foreach ($lists_yml_file as $name_yml_file) {
      $package['files'][] = $this->generateYmlfile($name_yml_file, $values);
    }
    $lists_php_file = array(
      'Entity',
      'EntityAccessControlHandler',
      'EntityForm',
      'EntityInterface',
      'EntityListBuilder',
      'EntitySettingsForm',
      'EntityViewsData',
      'install'
    ); //'EntityDeleteForm',
    foreach ($lists_php_file as $name_php_file) {
      $package['files'][] = $this->generatePhpfile($name_php_file, $values);
    }
    $return_messages = [];

    $this->archiveName = $filename . '.tar.gz';
    $archive_name = 'temporary://' . $this->archiveName;
    if (file_exists($archive_name)) {
      file_unmanaged_delete($archive_name);
    }

    $archiver = new ArchiveTar($archive_name);
    $this->generatePackage($return_messages, $package, $archiver);

    return array(
      'archiveName' => $this->archiveName,
      'messages' => $return_messages,
    );
  }

  protected function prepareValues(&$values) {
    $tmp = str_replace('_', ' ', $values['entity_machine_name']);
    $tmp = ucwords($tmp);
    $values['entity_name_class'] = str_replace(' ', '', $tmp);
    $values['entity_label'] = empty($values['field_set_label_1']) ? '' : $values['id_1'];
    $show_fields = [];
    $created_field = [];
    $set_key = [];
    $count_field = (integer) $values['count_field'];
    $settings_field = array();
    foreach ($values as $name_val => $value) {
      if (strpos($name_val, 'settings_field_') === 0) {
        $tmp = str_replace('settings_field_', '', $name_val);
        $tmp_array = explode('_', $tmp, 2);
        $settings_field[$tmp_array[0]][$tmp_array[1]] = $value;
      }
    }

    for ($x = 0; $x++ < $count_field;) {
      $created_field[$values['id_' . $x]]['machine_name'] = $values['id_' . $x];
      $created_field[$values['id_' . $x]]['label'] = $values['label_' . $x];
      $created_field[$values['id_' . $x]]['storage_type'] = $values['storage_type_' . $x];
      $created_field[$values['id_' . $x]]['view_enable'] = empty($values['view_enable_' . $x]) ? '' : $values['view_enable_' . $x];
      $created_field[$values['id_' . $x]]['view_formatter'] = empty($values['view_formatter_' . $x]) ? '' : $values['view_formatter_' . $x];
      $created_field[$values['id_' . $x]]['form_enable'] = empty($values['form_enable_' . $x]) ? '' : $values['form_enable_' . $x];
      $created_field[$values['id_' . $x]]['form_widget'] = empty($values['form_widget_' . $x]) ? '' : $values['form_widget_' . $x];
      $created_field[$values['id_' . $x]]['settings'] = empty($settings_field[$x]) ? '' : $settings_field[$x];
      if ($values['field_set_key_' . $x]) {
        if ($x == 1 && $values['field_set_label_1']) {
          $set_key['label'] = $values['id_' . $x];
        }
        else {
          $set_key[$values['id_' . $x]] = $values['id_' . $x];
        }
      }
      if ($values['show_field_list_' . $x]) {
        $show_fields[$values['id_' . $x]] = $values['label_' . $x];
      }
    }
    if ($values['user_id']) {
      $set_key['uid'] = 'user_id';
      if ($values['user_show']) {
        $show_fields['user_id'] = 'User';
      }
    }
    if ($values['created_show']) {
      $show_fields['created'] = 'Created';
    }
    if ($values['changed_show']) {
      $show_fields['changed'] = 'Changed';
    }
    if ($values['language']) {
      $set_key['langcode'] = 'langcode';
      if ($values['language_show']) {
        $show_fields['langcode'] = 'langcode';
      }
    }
    if ($values['uuid']) {
      $set_key['uuid'] = 'uuid';
    }
    $values['show_fields'] = $show_fields;
    $values['set_key'] = $set_key;
    $values['created_fields'] = $created_field;
  }

}