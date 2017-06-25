# apivk-wp
API VKontakte for WordPress

Обертка API ВКонтакте как плагин для WordPress

# Пример


```
<?php

/**
 * Example
 */
class WPSS_Cities
{

  function __construct() {
    add_action('admin_menu', function(){
            add_management_page(
                $page_title = 'Import cities',
                $menu_title = 'Import cities',
                $capability = 'manage_options',
                $menu_slug = 'apivk-cities',
                $function = [$this, 'apivk_import_cities']
            );
        });

  }


  function apivk_import_cities() {
    ?>
    <h1>Import cities</h1>
    <?php
    $vk = new API_VK_WP();

    $args = [
      'country_id' => 1,
      // 'need_all' => 1
    ];
    $data = $vk->method('database.getCities', $args);

    echo '<pre>';
    var_dump($data->response);
    echo '</pre>';

  }

}

if(class_exists('API_VK_WP')){
  new WPSS_Cities;
}
```
