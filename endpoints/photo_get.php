<?php
// Função para obter os dados de uma foto
function photo_data($post) {
  $post_meta = get_post_meta($post->ID);
  $src = wp_get_attachment_image_src($post_meta['img'][0], 'large')[0];
  $user = get_userdata($post->post_author);
  $total_comments = get_comments_number($post->ID);

  // Retorna um array com os dados da foto
  return [
    'id' => $post->ID,
    'author' => $user->user_login,
    'title' => $post->post_title,
    'date' => $post->post_date,
    'src' => $src,
    'peso' => $post_meta['peso'][0],
    'idade' => $post_meta['idade'][0],
    'acessos' => $post_meta['acessos'][0],
    'total_comments' => $total_comments,
  ];
}

// Função para lidar com a requisição de uma foto específica
function api_photo_get($request) {
  $post_id = $request['id'];
  $post = get_post($post_id);

  // Verifica se o post existe e se o ID não está vazio
  if (!isset($post) || empty($post_id)) {
    $response = new WP_Error('error', 'Post não encontrado.', ['status' => 404]);
    return rest_ensure_response($response);
  }

  // Obtém os dados da foto e incrementa os acessos
  $photo = photo_data($post);
  $photo['acessos'] = (int) $photo['acessos'] + 1;
  update_post_meta($post_id, 'acessos', $photo['acessos']);

  // Obtém os comentários da foto
  $comments = get_comments([
    'post_id' => $post_id,
    'order' => 'ASC',
  ]);

  // Retorna um array com os dados da foto e os comentários
  $response = [
    'photo' => $photo,
    'comments' => $comments,
  ];

  return rest_ensure_response($response);
}

// Registra a rota da API para obter uma foto específica
function register_api_photo_get() {
  register_rest_route('api', '/photo/(?P<id>[0-9]+)', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'api_photo_get',
  ]);
}
add_action('rest_api_init', 'register_api_photo_get');

// Função para lidar com a requisição de várias fotos
function api_photos_get($request) {
  $_total = sanitize_text_field($request['_total']) ?: 6;
  $_page = sanitize_text_field($request['_page']) ?: 1;
  $_user = sanitize_text_field($request['_user']) ?: 0;

  // Verifica se o valor passado como _user é um número ou um nome de usuário
  if (!is_numeric($_user)) {
    // Obtém o ID do usuário a partir do nome de usuário
    $user = get_user_by('login', $_user);
    $_user = $user->ID;
  }

  // Define os argumentos para a consulta das fotos
  $args = [
    'post_type' => 'post',
    'author' => $_user,
    'posts_per_page' => $_total,
    'paged' => $_page,
  ];

  // Realiza a consulta
  $query = new WP_Query($args);
  $posts = $query->posts;

  $photos = [];
  if ($posts) {
    // Para cada post encontrado, obtém os dados da foto
    foreach ($posts as $post) {
      $photos[] = photo_data($post);
    }
  }

  // Retorna um array com os dados das fotos
  return rest_ensure_response($photos);
}

// Registra a rota da API para obter várias fotos
function register_api_photos_get() {
  register_rest_route('api', '/photo', [
    'methods' => WP_REST_Server::READABLE,
    'callback' => 'api_photos_get',
  ]);
}
add_action('rest_api_init', 'register_api_photos_get');
?>
