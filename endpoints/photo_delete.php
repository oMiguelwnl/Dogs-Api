<?php

// Função para deletar uma foto através da API REST
function api_photo_delete($request) {
  // Obtém o ID do post a ser deletado da solicitação
  $post_id = $request['id'];

  // Obtém o usuário atual
  $user = wp_get_current_user();

  // Obtém as informações do post
  $post = get_post($post_id);

  // Obtém o ID do autor do post
  $author_id = (int) $post->post_author;

  // Obtém o ID do usuário atual
  $user_id = (int) $user->ID;

  // Verifica se o usuário atual é o autor do post ou se o post existe
  if ($user_id !== $author_id || !isset($post)) {
    $response = new WP_Error('error', 'Sem permissão.', ['status' => 401]);

    return rest_ensure_response($response);
  }

  // Obtém o ID do anexo associado ao post
  $attachment_id = get_post_meta($post_id, 'img', true);

  // Deleta o anexo
  wp_delete_attachment($attachment_id, true);

  // Deleta o post
  wp_delete_post($post_id, true);

  // Cria uma resposta de sucesso
  $success_response = [
    'message' => 'Post deletado.'
  ];

  // Retorna a resposta de sucesso
  return rest_ensure_response($success_response);
}

// Registra a rota de exclusão da foto
function register_api_photo_delete() {
  register_rest_route('api', '/photo/(?P<id>[0-9]+)', [
    'methods' => WP_REST_Server::DELETABLE,
    'callback' => 'api_photo_delete',
  ]);
}

// Adiciona a ação para registrar a rota de exclusão
add_action('rest_api_init', 'register_api_photo_delete');

?>
