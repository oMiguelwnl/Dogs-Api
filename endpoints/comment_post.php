<?php

// Função para criar um novo comentário através da API REST
function api_comment_post($request) {
  // Obtém o usuário atual
  $user = wp_get_current_user();
  
  // Obtém o ID do usuário atual
  $user_id = $user->ID;

  // Verifica se o usuário está autenticado
  if ($user_id === 0) {
    // Cria uma resposta de erro para acesso não autorizado
    $response = new WP_Error('error', 'Sem permissão.', ['status' => 401]);
    return rest_ensure_response($response);
  }

  // Obtém o conteúdo do comentário da solicitação
  $comment = sanitize_text_field($request['comment']);
  
  // Obtém o ID do post relacionado ao comentário
  $post_id = $request['id'];

  // Verifica se o comentário não está vazio
  if (empty($comment)) {
    // Cria uma resposta de erro para dados incompletos
    $response = new WP_Error('error', 'Dados incompletos.', ['status' => 422]);
    return rest_ensure_response($response);
  }

  // Cria um array com os dados do comentário
  $response = [
    'comment_author' => $user->user_login,
    'comment_content' => $comment,
    'comment_post_ID' => $post_id,
    'user_id' => $user_id,
  ];

  // Insere o novo comentário
  $comment_id = wp_insert_comment($response);
  
  // Obtém os detalhes do comentário recém-criado
  $comment = get_comment($comment_id);

  // Retorna a resposta contendo os detalhes do comentário
  return rest_ensure_response($comment);
}

// Registra a rota de criação de comentário
function register_api_comment_post() {
  register_rest_route('api', '/comment/(?P<id>[0-9]+)', [
    'methods' => WP_REST_Server::CREATABLE,
    'callback' => 'api_comment_post',
  ]);
}

// Adiciona a ação para registrar a rota de criação de comentário
add_action('rest_api_init', 'register_api_comment_post');

?>
