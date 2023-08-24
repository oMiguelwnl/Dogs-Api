<?php

// Função para obter os comentários de um post através da API REST
function api_comment_get($request) {
  // Obtém o ID do post da solicitação
  $post_id = $request['id'];

  // Obtém os comentários relacionados ao post
  $comments = get_comments([
    'post_id' => $post_id,
  ]);

  // Retorna a resposta contendo os comentários
  return rest_ensure_response($comments);
}

// Função para registrar a rota de obtenção de comentários
function register_api_comment_get() {
  register_rest_route('api', '/comment/(?P<id>[0-9]+)', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'api_comment_get', 
  ]);
}

// Adiciona a ação para registrar a rota de obtenção de comentários quando a API REST é iniciada
add_action('rest_api_init', 'register_api_comment_get');

?>
