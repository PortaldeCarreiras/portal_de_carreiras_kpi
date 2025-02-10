<?php
// Lógica para fazer o download do arquivo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['downloadArquivo'])) {
    $outputFilePath = $_POST['outputFilePath'] ?? ''; // Caminho do arquivo enviado pelo formulário
    $fileName = $_POST['fileName'] ?? ''; // Nome do arquivo enviado pelo formulário

    // Verifica se o arquivo existe no diretório de uploads
    if (!empty($outputFilePath) && file_exists($outputFilePath)) {
        // Configura os cabeçalhos para forçar o download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
        header('Content-Length: ' . filesize($outputFilePath));
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        // Envia o conteúdo do arquivo
        readfile($outputFilePath);
        exit;
    } else {
        // Exibe uma mensagem de erro caso o arquivo não seja encontrado
        $mensagemErro = "Erro: O arquivo selecionado não foi encontrado no servidor.";
    }
}