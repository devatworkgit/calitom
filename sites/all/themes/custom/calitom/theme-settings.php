<?php
/**
 * @file
 * theme-settings.php
 *
 * Provides theme settings for Bootstrap based themes when admin theme is not.
 *
 * @see ./includes/settings.inc
 */


/**
 * Implements hook_form_FORM_ID_alter().
 */
function calitom_form_system_theme_settings_alter(&$form, $form_state, $form_id = NULL) {

  // Create vertical tabs for all Bootstrap related settings.
  $form['calitom'] = array(
    '#type' => 'vertical_tabs',
    '#attached' => array(
      'js'  => array(drupal_get_path('theme', 'bootstrap') . '/js/bootstrap.admin.js'),
    ),
    '#prefix' => '<h2><small>' . t('Calitom Settings') . '</small></h2>',
    '#weight' => -10,
  );
  
  $form['calitom']['calitom_citizen_account'] = array(
    '#type' => 'fieldset',
    '#title' => t('Citizen account'),
    '#group' => 'bootstrap1',
  );
  
  $form['calitom']['calitom_citizen_account']['calitom_citizen_account_display'] = array(
    '#type' => 'checkbox',
    '#title' => t('Display this button'),
    '#description' => t('Display this button on site.'),
    '#default_value' => theme_get_setting('calitom_citizen_account_display'),
  );
  
  $form['calitom']['calitom_citizen_account']['calitom_citizen_account_name'] = array(
    '#type' => 'textfield',
    '#title' => t('Button name'),
    '#description' => t('Button text'),
    '#default_value' => theme_get_setting('calitom_citizen_account_name'),
  );
  
  $form['calitom']['calitom_citizen_account']['calitom_citizen_account_path'] = array(
    '#type' => 'textfield',
    '#title' => t('Button path'),
    '#description' => t('Button path like "/user" or "http://www.calitom.fr"'),
    '#default_value' => theme_get_setting('calitom_citizen_account_path'),
  );
  
  $form['calitom']['calitom_background_image'] = array(
    '#type' => 'fieldset',
    '#title' => t('Background image'),
  );
  
  $form['calitom']['calitom_actualites_background_image'] = array(
    '#type' => 'fieldset',
    '#title' => t('News background image'),
  );
  
  $form['calitom']['calitom_page_background_image'] = array(
    '#type' => 'fieldset',
    '#title' => t('Page background image'),
  );
  
  $default_file_dir = 'public://template/images';
  $folder = file_prepare_directory($default_file_dir, FILE_CREATE_DIRECTORY);
  $settings_theme = $form_state['build_info']['args'][0];

  if ($folder) {
    $my_background_image = theme_get_setting('calitom_background_image');
    // BUG: Force file to be permanent.
    if (!empty($my_background_image)) {
      _fix_permanent_image('calitom_background_image', $settings_theme);
    }
    $form['calitom']['calitom_background_image']['calitom_background_image'] = array(
      '#type'               => 'managed_file',
      '#title'              => t('Background image'),
      '#description'        => t('Background image be displayed on front page'),
      '#default_value'      => $my_background_image,
      '#progress_message'   => t('Please wait...'),
      '#progress_indicator' => 'bar',
      '#upload_location'    => $default_file_dir,
      '#upload_validators'  => array(
        'file_validate_extensions' => array('gif png jpg jpeg'),
      ),
    );
    
    $my_actualites_background_image = theme_get_setting('calitom_actualites_background_image');
    if (!empty($my_actualites_background_image)) {
      _fix_permanent_image('calitom_actualites_background_image', $settings_theme);
    }
    $form['calitom']['calitom_actualites_background_image']['calitom_actualites_background_image'] = array(
      '#type'               => 'managed_file',
      '#title'              => t('News background image'),
      '#description'        => t('Background image to be displayed on news page'),
      '#default_value'      => $my_actualites_background_image,
      '#progress_message'   => t('Please wait...'),
      '#progress_indicator' => 'bar',
      '#upload_location'    => $default_file_dir,
      '#upload_validators'  => array(
        'file_validate_extensions' => array('gif png jpg jpeg'),
      ),
    );
    
    $my_page_background_image = theme_get_setting('calitom_page_background_image');
    if (!empty($my_page_background_image)) {
      _fix_permanent_image('calitom_page_background_image', $settings_theme);
    }
    $form['calitom']['calitom_page_background_image']['calitom_page_background_image'] = array(
      '#type'               => 'managed_file',
      '#title'              => t('Page background image'),
      '#description'        => t('Background image to be displayed on pages'),
      '#default_value'      => $my_page_background_image,
      '#progress_message'   => t('Please wait...'),
      '#progress_indicator' => 'bar',
      '#upload_location'    => $default_file_dir,
      '#upload_validators'  => array(
        'file_validate_extensions' => array('gif png jpg jpeg'),
      ),
    );
  }
}

function _fix_permanent_image($image, $theme) {
  $fid = theme_get_setting($image, $theme);
  if ($fid > 0) {
    $file = file_load($fid);
    if (is_object($file) && $file->status == 0) {
      $file->status = FILE_STATUS_PERMANENT;
      file_save($file);
      file_usage_add($file, $theme, 'theme', 1);
    }
  }
}
