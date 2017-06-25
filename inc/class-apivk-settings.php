<?php

/**
* Settings for API VK
*/

class API_VK_WP_Settings
{

  function __construct() {
    add_action('admin_menu', function(){
			add_options_page(
				$page_title = 'VK API',
				$menu_title = 'VK API',
				$capability = 'manage_options',
				$menu_slug = 'vkapi',
				$function = array($this, 'settings_ui')
			);
		});

    add_action( 'admin_init', array($this, 'settings_init') );
  }

  function settings_init(){

    add_settings_section(
			$name = 'vkapi_settings_section_main',
			$title = 'Основные настройки',
			$callback = array($this, 'display_vkapi_settings_section_main'),
			$page = 'vkapi'
		);

    register_setting(
			$option_group = 'vkapi',
			$option_name = 'vkapi_app_id'
		);
		add_settings_field(
			$id = 'vkapi_app_id',
			$title = 'ID приложения',
			$callback = [$this, 'display_vkapi_app_id'],
			$page = 'vkapi',
			$section = 'vkapi_settings_section_main'
		);

    register_setting(
			$option_group = 'vkapi',
			$option_name = 'vkapi_key_secret'
		);
		add_settings_field(
			$id = 'vkapi_key_secret',
			$title = 'Защищённый ключ',
			$callback = [$this, 'display_vkapi_key_secret'],
			$page = 'vkapi',
			$section = 'vkapi_settings_section_main'
		);

    register_setting(
			$option_group = 'vkapi',
			$option_name = 'vkapi_key_access'
		);
		add_settings_field(
			$id = 'vkapi_key_access',
			$title = 'Сервисный ключ доступа',
			$callback = [$this, 'display_vkapi_key_access'],
			$page = 'vkapi',
			$section = 'vkapi_settings_section_main'
		);

    register_setting(
			$option_group = 'vkapi',
			$option_name = 'vkapi_scope'
		);
		add_settings_field(
			$id = 'vkapi_scope',
			$title = 'Охват методов',
			$callback = [$this, 'display_vkapi_scope'],
			$page = 'vkapi',
			$section = 'vkapi_settings_section_main'
		);


  }

  function display_vkapi_scope(){
    $name = 'vkapi_scope';
    printf('<input type="text" id="%1$s" name="%1$s" value="%2$s" size="55"  />', $name, get_option($name) );
    echo '<p>Указать группы доступа через запятую с маленькой буквы. Список тут <a href="https://vk.com/dev/methods" target="_blank">Список методов</a></p>';
  }

  function display_vkapi_app_id(){
    $name = 'vkapi_app_id';
    printf('<input type="text" id="%1$s" name="%1$s" value="%2$s" size="55"  />', $name, get_option($name) );
  }
  function display_vkapi_key_secret(){
    $name = 'vkapi_key_secret';
    printf('<input type="text" id="%1$s" name="%1$s" value="%2$s" size="55"  />', $name, get_option($name) );
  }
  function display_vkapi_key_access(){
    $name = 'vkapi_key_access';
    printf('<input type="text" id="%1$s" name="%1$s" value="%2$s" size="55"  />', $name, get_option($name) );
  }
  function display_vkapi_settings_section_main(){
    ?>
    <p>Параметры для настройки интеграции можно получить на специальной странице: <a href="https://vk.com/apps?act=manage" target="_blank">https://vk.com/apps?act=manage</a></p>
    <hr>
    <?php
  }
  function settings_ui(){
    ?>
    <div class="wrap">
        <h1>VK API - настройка интеграции ВКонтакте и WP</h1>
        <form action="options.php" method="POST">
            <?php settings_fields( 'vkapi' ); ?>
            <?php do_settings_sections( 'vkapi' ); ?>
            <?php submit_button(); ?>
        </form>
        <div class="get_access_code">
          <?php
            printf('<p>Токен доступа: %s</p>', get_option('apivk_access_token', "Отсутствует"));
            $this->get_access_tocken();
          ?>
        </div>
    </div>
    <?php
  }

  function get_access_tocken(){

    $client_id = get_option('vkapi_app_id');
    $redirect_uri = esc_url_raw(admin_url('options-general.php?page=vkapi'));
    $scope = get_option('vkapi_scope', 'database');
    $v = '5.65';

    $url = sprintf(
      'https://oauth.vk.com/authorize?client_id=%s&display=page&redirect_uri=%s&scope=%s&response_type=code&v=%s',
      $client_id,
      $redirect_uri,
      $scope,
      $v
    );

    if( empty($_GET['code'])){

      printf('<a href="%s" target="_blank" class="button">Получить ключи доступа</a>', apply_filters('vkapi_oauth_url', $url));

    } else {

      $code = sanitize_text_field($_GET['code']);

      $client_secret = get_option('vkapi_key_secret');

      $url = sprintf(
        'https://oauth.vk.com/access_token?client_id=%s&client_secret=%s&redirect_uri=%s&code=%s',
        $client_id,
        $client_secret,
        $redirect_uri,
        $code
      );

      $request = wp_remote_get($url);
      $response = wp_remote_retrieve_body( $request );

      $response = json_decode( $response );
      if( ! empty($response->access_token)){
        update_option('apivk_access_token', $response->access_token);
      }


    }
  }
}
$GLOBALS['apivk_settings'] = new API_VK_WP_Settings;
