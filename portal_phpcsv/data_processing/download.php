<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nomeArquivoSelDrop'])) {
    $nomeArquivo = $_POST['nomeArquivoSelDrop'];
    $diretorioUploads = 'C:/xampp/htdocs/portal/portal_phpcsv/uploads';
    $caminhoArquivo = $diretorioUploads . DIRECTORY_SEPARATOR . $nomeArquivo;

    // Verifica se o arquivo existe no diretório
    if (!file_exists($caminhoArquivo)) {
        die("Erro: O arquivo solicitado não existe no servidor.");
    }

    // Configura os cabeçalhos para forçar o download
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($caminhoArquivo) . '"');
    header('Content-Length: ' . filesize($caminhoArquivo));
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    // Envia o conteúdo do arquivo para o cliente
    readfile($caminhoArquivo);
    exit;
}

die("Erro: Nenhum arquivo foi especificado para download.");
?>