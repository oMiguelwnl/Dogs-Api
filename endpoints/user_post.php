<?php
// Função para puxar dados
function api_user_post($request){
  $email = sanitize_email($request['email']);
  $username = sanitize_text_field($request['username']);
  $password = $request['password'];

  // Verifica se está vazio e retorna error.
  if(empty($email) || empty($username) || empty($password)){
    $response = new WP_Error('error', 'Dados incompletos', ['status' => 406]);
    return rest_ensure_response($response); 
  }

  // Verifica se o usuário existe.
  if(username_exists($username) || email_exists($email)){
    $response = new WP_Error('error', 'Email já cadastrado', ['status' => 403]);
    return rest_ensure_response($response); 
  }
  
  // Cria o novo usuário
  $user_id = wp_insert_user([
    'user_login' => $username,
    'user_email' => $email,
    'user_pass' => $password,
    'role' => 'subscriber'
  ]);

  if(is_wp_error($user_id)) {
    return rest_ensure_response($user_id);
  }

  $user = get_user_by('ID', $user_id);

  $response = array(
    'ID' => $user_id,
    'user_login' => $user->user_login
  );

  return rest_ensure_response($response); // Garante que o retorno seja sempre uma resposta rest.
}

// Função para registrar rota
function register_api_user_post(){
  register_rest_route('api', '/user', array(
    'methods' => WP_REST_Server::CREATABLE, // Certifique-se de importar a classe WP_REST_Server
    'callback' => 'api_user_post',
  ));
}
add_action('rest_api_init', 'register_api_user_post');
?>
