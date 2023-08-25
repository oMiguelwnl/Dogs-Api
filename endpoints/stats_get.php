<?php

// Função para obter as estatísticas do usuário
function api_stats_get($request) {
  // Obtém o usuário atual
  $user = wp_get_current_user();
  $user_id = $user->ID;

  // Verifica se o usuário está autenticado
  if ($user_id === 0) {
    $response = new WP_Error('error', 'Usuário não possui permissão.', ['status' => 401]);
    return rest_ensure_response($response);
  }

  // Define os argumentos para a consulta de posts do usuário
  $args = [
    'post_type' => 'post',
    'author' => $user_id,
    'posts_per_page' => -1, // Retorna todos os posts do usuário
  ];

  // Realiza a consulta
  $query = new WP_Query($args);
  $posts = $query->posts;

  $stats = [];
  if ($posts) {
    foreach ($posts as $post) {
      // Obtém dados das estatísticas para cada post
      $stats[] = [
        'id' => $post->ID,
        'title' => $post->post_title,
        'acessos' => get_post_meta($post->ID, 'acessos', true),
      ];
    }
  }

  // Retorna as estatísticas como uma resposta JSON
  return rest_ensure_response($stats);
}

// Registra a rota da API para obter as estatísticas
function register_api_stats_get() {
  register_rest_route('api', '/stats', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'api_stats_get',
  ]);
}
add_action('rest_api_init', 'register_api_stats_get');

?>
