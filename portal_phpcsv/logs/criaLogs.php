<?php
function criaLogs($nomeArquivoOrigem, $mensagem) {
    // Define o caminho do diretório de logs
    $logDir = __DIR__;
    
    // Verifica se o diretório de logs existe, se não, cria-o
    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true);
    }

    // Define o caminho do arquivo de log
    $logFilePath = $logDir . '/' . $nomeArquivoOrigem . 'Log.txt';

    // Adiciona a data e hora local à mensagem
    $dataHora = date('Y-m-d H:i:s');
    $mensagemCompleta = "[$dataHora] $mensagem\n";

    // Grava a mensagem no arquivo de log
    file_put_contents($logFilePath, $mensagemCompleta, FILE_APPEND);
}
?>