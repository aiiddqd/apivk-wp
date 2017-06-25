<?php
/*
Plugin Name: API VKontakte for WordPress
Version: 0.3
Plugin URI: https://wpcraft.ru/product/apivk-wp/
Description: Обертка для ВКонтакте API, которая позволяет упростить интеграцию сайта и социальной сети
Author: WPCraft
Author URI: https://wpcraft.ru/
*/

require_once 'inc/class-apivk-settings.php';

class API_VK_WP
{

  function __construct() {
    # code...
  }

  public function method($method, $args = []){

    try {

      if(empty($method)){
        throw new Exception("Нужно указать метод");
      }

      $url = 'https://api.vk.com/method/'.$method;
      $url = add_query_arg($args, $url);
      $url = add_query_arg('access_token', get_option('apivk_access_token'), $url);

      $request = wp_remote_get($url);
      $response = wp_remote_retrieve_body( $request );
      $response = json_decode( $response );

      if(isset($response->error)){
        $msg = sprintf('<p>Код ошибки: %s, детали: %s</p>', $response->error->error_code, $response->error->error_msg);
        throw new Exception($msg);
      } else {
        return $response;
      }

    } catch (Exception $e) {
      return new WP_Error('fail', $e->getMessage());
    }
  }


}


// $GLOBALS['apivk'] = new API_VK_WP;
