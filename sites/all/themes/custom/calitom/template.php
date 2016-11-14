<?php
/**
 * @file
 * The primary PHP file for this theme.
 */

 /**
 * Google fonts
 */
drupal_add_html_head_link(
  array(
    'rel' => 'stylesheet',
    'href' => 'https://fonts.googleapis.com/css?family=Lobster&subset=latin,latin-ext',
    'type' => 'text/css',
  )
);

/**
 * Pre-processes variables for the "page" theme hook.
 *
 * See template for list of available variables.
 *
 * @see page.tpl.php
 *
 * @ingroup theme_preprocess
*/
function calitom_preprocess_page(&$variables) {

  $variables['citizen_account_display'] = theme_get_setting('calitom_citizen_account_display');
  $variables['citizen_account_name'] = theme_get_setting('calitom_citizen_account_name');
  $variables['citizen_account_path'] = theme_get_setting('calitom_citizen_account_path');

  if(!empty(theme_get_setting('calitom_background_image'))){
    $background_image = theme_get_setting('calitom_background_image');
    $variables['background_image'] = file_create_url(file_load($background_image)->uri);
  } else {
    $variables['background_image']= 0;
  }
  if(!empty(theme_get_setting('calitom_actualites_background_image'))){
    $actualites_background_image = theme_get_setting('calitom_actualites_background_image');
    $variables['actualites_background_image'] = file_create_url(file_load($actualites_background_image)->uri);
  } else {
    $variables['actualites_background_image']= 0;
  }
  if(!empty(theme_get_setting('calitom_page_background_image'))){
    $page_background_image = theme_get_setting('calitom_page_background_image');
    $variables['page_background_image'] = file_create_url(file_load($page_background_image)->uri);
  } else {
    $variables['page_background_image'] = 0;
  }
  
  if(isset($variables['node'])) {
    if($variables['node']->type == 'page') {
      if(isset($variables['node']->field_image['und'][0]['uri']) && !empty($variables['node']->field_image['und'][0]['uri'])) {
        $page_background_image = $variables['node']->field_image['und'][0]['uri'];
        $variables['page_background_image'] = file_create_url($page_background_image);
      }
    }
    if(isset($variables['node']->field_liens_utiles['und']) && !empty($variables['node']->field_liens_utiles['und'])) {
      $block = _block_get_renderable_array(_block_render_blocks(array(block_load('views', 'page_components-block'))));
      $variables['page']['sidebar_second'][] = $block;
    }
    if(isset($variables['node']->field_blocs['und']) && !empty($variables['node']->field_blocs['und'])) {
      $block = _block_get_renderable_array(_block_render_blocks(array(block_load('views', 'page_components-block_1'))));
      $variables['page']['sidebar_second'][] = $block;
    }
    if(isset($variables['node']->field_blocs_afficher['und']) && in_array(array('value' => 1), $variables['node']->field_blocs_afficher['und'])) {
      $block = _block_get_renderable_array(_block_render_blocks(array(block_load('views', 'documents-block'))));
      $variables['page']['sidebar_second'][] = $block;
    }
    
    $block = _block_get_renderable_array(_block_render_blocks(array(block_load('text_resize', '0'))));
    $variables['page']['uppertitle'][] = $block;
    $block = _block_get_renderable_array(_block_render_blocks(array(block_load('sharethis', 'sharethis_block'))));
    $variables['page']['uppertitle'][] = $block;
    $block = _block_get_renderable_array(_block_render_blocks(array(block_load('views', 'page_components-block_2'))));
    $variables['page']['uppertitle'][] = $block;
  }
  
  //Recheck sidebars beacause we rebuild it
  if (!empty($variables['page']['sidebar_first']) && !empty($variables['page']['sidebar_second'])) {
    $variables['content_column_class'] = ' class="col-sm-6"';
  }
  elseif (!empty($variables['page']['sidebar_first']) || !empty($variables['page']['sidebar_second'])) {
    $variables['content_column_class'] = ' class="col-sm-9"';
  }
  else {
    $variables['content_column_class'] = ' class="col-sm-12"';
  }
  
  //page--type--content-type-name.tpl.php
  if (isset($variables['node']->type)) {
    $variables['theme_hook_suggestions'][] = 'page__type__' . $variables['node']->type;
  }
  
}

/**
 * Returns HTML for a menu link and submenu.
 *
 * @param array $variables
 *   An associative array containing:
 *   - element: Structured array data for a menu link.
 *
 * @return string
 *   The constructed HTML.
 *
 * @see theme_menu_link()
 *
 * @ingroup theme_functions
 */
function calitom_menu_link(array $variables) {
  $element = $variables['element'];
  $sub_menu = '';
  if ($element['#below']) {
    // Prevent dropdown functions from being added to management menu so it
    // does not affect the navbar module.
    if (($element['#original_link']['menu_name'] == 'management') && (module_exists('navbar'))) {
      $sub_menu = drupal_render($element['#below']);
    }
    elseif ((!empty($element['#original_link']['depth'])) && ($element['#original_link']['depth'] >= 1)) {
      // Add our own wrapper.
      unset($element['#below']['#theme_wrappers']);
      if ($element['#original_link']['depth'] == 1) {
        $sub_menu = '<ul class="dropdown-menu"><h2 class="text-uppercase">' . $element['#title'] .'</h2>' . drupal_render($element['#below']) . '</ul>';
      } else {
        $sub_menu = '<ul class="dropdown-menu">' . drupal_render($element['#below']) . '</ul>';
      }
      
      // Generate as standard dropdown.
      if ($element['#original_link']['depth'] > 1) {
        $element['#title'] .= ' <span class="caret">￫</span>';
      }
      else {
        $element['#title'] .= ' <span class="caret"></span>';
      }
      $element['#attributes']['class'][] = 'dropdown';
      $element['#localized_options']['html'] = TRUE;

      // Set dropdown trigger element to # to prevent inadvertant page loading
      // when a submenu link is clicked.
      $element['#localized_options']['attributes']['data-target'] = '#';
      $element['#localized_options']['attributes']['class'][] = 'dropdown-toggle';
      //$element['#localized_options']['attributes']['data-toggle'] = 'dropdown';
    }
  }
  // On primary navigation menu, class 'active' is not set on active menu item.
  // @see https://drupal.org/node/1896674
  if (($element['#href'] == $_GET['q'] || ($element['#href'] == '<front>' && drupal_is_front_page())) && (empty($element['#localized_options']['language']))) {
    $element['#attributes']['class'][] = 'active';
  }
  $output = l($element['#title'], $element['#href'], $element['#localized_options']);
  
  if(isset($element['#localized_options']['attributes']['title']) && !empty($element['#localized_options']['attributes']['title'])) {
    $output = $output . '<span class="sublink">' . $element['#localized_options']['attributes']['title'] . '</span>';
  }
  
  return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
}

//Search form placeholder
function calitom_form_alter(&$form, &$form_state, $form_id) {
  if ($form_id == 'simplenews_block_form_8'){
    $form['mail']['#title_display'] = 'invisible';
    $form['mail']['#attributes']['placeholder'] = 'Email';
    $form['submit']['#value'] = '￫';
  }
}

/**
 * Implements template_form_element_labels(&$variables).
 * Changing bootstrap radios and checkbox-es to be styled...
 */
function calitom_form_element_label(&$variables) {
  $element = $variables['element'];
  $output = '';
  $output2 = '';
  $title = isset($element['#title']) ? filter_xss_admin($element['#title']) . ' ' : '';
  if ($title && ($required = !empty($element['#required']) ? theme('form_required_marker', array('element' => $element)) : '')) {
    $title .= $required;
  }
  $display = isset($element['#title_display']) ? $element['#title_display'] : 'before';
  $type = !empty($element['#type']) ? $element['#type'] : FALSE;
  $checkbox = $type && $type === 'checkbox';
  $radio = $type && $type === 'radio';
  if (!$checkbox && !$radio && ($display === 'none' || !$title)) {
    return '';
  }
  $attributes = &_bootstrap_get_attributes($element, 'label_attributes');
  $attributes['class'][] = 'control-label';
  if (!empty($element['#id'])) {
    $attributes['for'] = $element['#id'];
  }
  if ($checkbox || $radio) {
    if ($display === 'before') {
      $output .= $title;
    }
    elseif ($display === 'none' || $display === 'invisible') {
      $output .= '<span class="element-invisible">' . $title . '</span>';
    }
    if (!empty($element['#children'])) {
      $output2 .= $element['#children'];
    }
    if ($display === 'after') {
      $output .= $title;
    }
  }
  else {
    if ($display === 'invisible') {
      $attributes['class'][] = 'element-invisible';
    }
    $output .= $title;
  }
  return $output2 . '<label' . drupal_attributes($attributes) . '>' . $output . "</label>\n";
}