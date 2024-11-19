<?php

// Função Truncar Tabela, para deletar e começar do "id01"
// LEMBRAR DE CODIFICAR PARA QUE APENAS O USUÁRIO ADM POSSA EXECUTAR ESSA FUNÇÃO.
function truncarTabela($conn, $tabela) {
    $sqlTruncate = "TRUNCATE TABLE $tabela";
    if (mysqli_query($conn, $sqlTruncate)) {
        $mensagem = "Dados da tabela $tabela foram apagados.";
        echo $mensagem . "<br>";
        criaLogs($tabela, "$mensagem\n\n"); // Chama a função de log
    } else {
        $mensagem = "Erro ao apagar dados da tabela $tabela: " . mysqli_error($conn);
        echo $mensagem . "<br>";
        criaLogs($tabela, $mensagem); // Chama a função de log
    }
}