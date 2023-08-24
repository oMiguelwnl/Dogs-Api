<?php
// Remove as rotas definidas pelo WordPress
// remove_action('rest_api_init', 'create_initial_rest_routes', 99);

$dirbase = get_template_directory();
require_once $dirbase . '/endpoints/user_post.php'; // User Post
require_once $dirbase . '/endpoints/user_get.php'; // User Get

require_once $dirbase . '/endpoints/photo_post.php'; // Photo Post
require_once $dirbase . '/endpoints/photo_get.php'; // Photo Get
require_once $dirbase . '/endpoints/photo_delete.php'; // Photo Delete

require_once $dirbase . '/endpoints/comment_post.php'; // Comment Post
require_once $dirbase . '/endpoints/comment_get.php'; // Comment Get



update_option('large_size_w', 1000);
update_option('large_size_h', 1000);
update_option('large_crop', 1);

// Modifica o prefixo da API de wp-json para json apenas
// Necessário salvar os permalinks para dar um refresh nos URL's
function change_api() {
  return 'json';
}
add_filter('rest_url_prefix', 'change_api');

function expire_token() {
  return time() + (60 * 60 * 24);
}
add_action('jwt_auth_expire', 'expire_token');

?>