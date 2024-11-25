<?php   // Função para inserir dados no banco de dados
include_once(__DIR__ . '/../logs/criaLogs.php'); // Inclui a função de log

if (!file_exists(__DIR__ . '/../logs/criaLogs.php')) {
    die("Arquivo criaLogs.php não encontrado no caminho esperado.");
}

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