<?php

function api_photo_post($request) {
  $user = wp_get_current_user();
  $user_id = $user->ID;

  // Verifica se o usuário está logado
  if(user_id === 0) {
    $response = new WP_Error('error', 'Usuário não possui permissão.', ['status' => 401]);
    return rest_ensure_response($response);
  }

   // Obtém campos do pedido
  $nome = sanitize_text_field($request['nome']); 
  $peso = sanitize_text_field($request['peso']); 
  $idade = sanitize_text_field($request['idade']); 
  $files = $request->get_file_params();

  // Verifica se todos os campos obrigatórios estão preenchidos
  if(empty($nome)|| empty($peso)|| empty($idade) || empty($files) ){
    $response = new WP_Error('error', 'Dados incompletos.', ['status' => 422]);
    return rest_ensure_response($response);
  }

  // Insere o post
  $response = [
    'post_author' => $user_id,
    'post_type' => 'post',
    'post_status' => 'publish',
    'post_title' => $nome,
    'post_content' => $nome,
    'files' => $files,
    'meta_input' => [
      'peso' => $peso,
      'idade' => $idade,
      'acessos' => 0,
    ],
  ];
  $post_id = wp_insert_post($response);

  // Lida com o upload da imagem
  require_once ABSPATH . 'wp-admin/includes/image.php';
  require_once ABSPATH . 'wp-admin/includes/file.php';
  require_once ABSPATH . 'wp-admin/includes/media.php';

  $photo_id = media_handle_upload('img', $post_id);
  update_post_meta($post_id, 'img', $photo_id);

  // Retorna a resposta
  return rest_ensure_response($response);
}

function register_api_photo_post() {
  register_rest_route('api', '/photo', [
    'methods' => WP_REST_Server::CREATABLE,
    'callback' => 'api_photo_post',
  ]);
}
add_action('rest_api_init', 'register_api_photo_post');
