<?php
function criaLogs($nomeArquivoOrigem, $mensagem)
{
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
    $mensagemCompleta = "[$dataHora] $mensagem";

    // Old gravação de log
    // Grava a mensagem no arquivo de log
    // file_put_contents($logFilePath, $mensagemCompleta, FILE_APPEND);

    // New gravação de log, cod. NOVO adicional    
    // Verifica se o arquivo de log já existe
    $conteudoExistente = '';
    if (file_exists($logFilePath)) {
        $conteudoExistente = file_get_contents($logFilePath); // Lê o conteúdo existente
    }

    // Adiciona duas linhas em branco entre as novas mensagens e o conteúdo existente
    $novoConteudo = $mensagemCompleta . "\n" . $conteudoExistente;

    // Grava o novo conteúdo no arquivo, sobrescrevendo o existente
    file_put_contents($logFilePath, $novoConteudo);
}
