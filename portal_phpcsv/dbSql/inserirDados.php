<?php   // Função para inserir dados no banco de dados
require_once '../logs/criaLogs.php'; // Inclui a função de log

// Função genérica para inserir dados no banco de dados
function inserirDados($conn, $tabela, $dados) {
    $campos = implode(", ", array_keys($dados));
    $valores = "'" . implode("','", array_values($dados)) . "'";
    if (mysqli_query($conn, "INSERT INTO $tabela ($campos) VALUES ($valores)")) {
        return true;
    } else {
        $mensagem = "Erro na inserção: " . mysqli_error($conn);
        criaLogs($tabela, $mensagem); // Chama a função de log
        return false;
    }
}
?>