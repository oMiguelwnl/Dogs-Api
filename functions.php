<?php
// Desabilitar rotas REST padrão para usuários
// remove_action('rest_api_init', 'create_initial_rest_routes', 99);

// Remover endpoints específicos da API REST
add_filter('rest_endpoints', function ($endpoints) {
  // Remover endpoints de listagem e obtenção de usuários
  unset($endpoints['/wp/v2/users']);
  unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
  return $endpoints;
});

// Diretório base do tema
$dirbase = get_template_directory();

// Incluir arquivos de endpoints para CRUD de usuários e fotos
require_once $dirbase . '/endpoints/user_post.php'; 
require_once $dirbase . '/endpoints/user_get.php'; 
require_once $dirbase . '/endpoints/photo_post.php';  
require_once $dirbase . '/endpoints/photo_get.php';
require_once $dirbase . '/endpoints/photo_delete.php';
require_once $dirbase . '/endpoints/comment_post.php';
require_once $dirbase . '/endpoints/comment_get.php';
require_once $dirbase . '/endpoints/stats_get.php';
require_once $dirbase . '/endpoints/password.php';


// Atualizar as configurações de tamanho da imagem grande
update_option('large_size_w', 1000);
update_option('large_size_h', 1000);
update_option('large_crop', 1);

// Alterar o prefixo da URL da API REST para /json
function change_api() {
  return 'json';
}
add_filter('rest_url_prefix', 'change_api');

// Definir o tempo de expiração do token JWT para 1 dia
function expire_token() {
  return time() + (60 * 60 * 24);
}
add_action('jwt_auth_expire', 'expire_token');
?>
