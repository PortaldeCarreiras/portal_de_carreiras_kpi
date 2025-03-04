<?php
// O session_start() é essencial para garantir que o PHP acesse a sessão antes de destruí-la.
// O session_start() precisa ser chamado em qualquer página que vá manipular sessões.
// Se não for chamado, as funções session_unset() e session_destroy() poderão não
// ter acesso à sessão existente. O PHP não "presume" que você quer manipular uma
// sessão só porque já foi criada antes. Mesmo que a sessão já exista,
// session_start(), a recupera para ser manipulada.

session_start();      // Inicia a sessão atual para que possa ser destruída
session_destroy();    // Destroi a sessão no servidor

// Remove o cookie da sessão para garantir que o usuário realmente foi desconectado
setcookie(session_name(), '', time() - 3600, '/'); 

header('Location: login.php'); // Redireciona o usuário para a página de login.
exit(); // Garante que o script pare aqui e não continue executando.