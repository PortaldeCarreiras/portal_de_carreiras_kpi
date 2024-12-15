<?php
require_once 'vendor/autoload.php';
require_once 'data_processing/utils.php';
require_once 'data_processing/metaProcessFile.php';
require_once 'data_processing/processSpreadSheetFileAndSave.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

include_once('conn.php');

$message = metaProcessFile($conn, (obterDataOriArquivo($_POST['dataModificacao'] ?? null)));

// Variáveis que serão utilizadas em IFs diferentes abaixo
$fileExtension = '';
$fileName = '';
$dataCriacao = '';

// Lista de nomes de arquivos permitidos (normalizados, sem extensão)
$nomesPermitidos = [
    'acessoportal',
    'acesso portal',
    'consultadevagasdeestagio',
    'consulta de vagas de estagio',
    'saida'
];

// Lista de extensões de arquivos permitidas
$extensoesPermitidas = ['csv', 'xls', 'xlsx'];

// PEGA O FORMULÁRIO VIA POST, verifica se ele foi enviado e se o arquivo foi submetido corretamente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['xls_file'])) {
    // Obtém informações sobre o arquivo enviado
    $file = $_FILES['xls_file'];
    $fileName = $file['name'];
    
    // Captura o valor do timestamp obtido do Metadado e converte o timestamp para o formato desejado
    $dataCriacao = obterDataOriArquivo($_POST['dataModificacao'] ?? null);

    processSpreadSheet($file, $dataCriacao, $nomesPermitidos, $extensoesPermitidas, $message);
}

// echo "<label>&nbsp Arquivo Carregado: $fileName</label><br>";
// echo "<label>&nbsp Data de Criação: $dataCriacao</label><br>";

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <title>Upload de Arquivos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- Importa o arquivo JavaScript que contém as mensagens -->
    <script src="js/messages.js"></script>
    <!-- Importa o arquivo JavaScript que contém a função obterMetadadosArquivo -->
    <script src="js/captureFileMetadata.js"></script>
</head>

<body>
    <div class="container">
        <h1 class="text-danger">Upload de Arquivos</h1>
        <p>Selecione um arquivo para fazer o upload.</p>
        <!-- Adiciona a chamada para exibir a mensagem de processamento ao enviar o formulário -->
        <form action="" method="post" enctype="multipart/form-data" onsubmit="exibirMensagemProcessamento(); return confirmarExclusao();">
            <div class="form-group">
                <input type="file" id="arquivo" name="xls_file" class="btn btn-success" required onchange="obterMetadadosArquivo()">
            </div>
            <input type="hidden" id="nomeArquivo" name="nomeArquivo">
            <input type="hidden" id="tipoMime" name="tipoMime">
            <input type="hidden" id="tamanho" name="tamanho">
            <input type="hidden" id="dataModificacao" name="dataModificacao">
            <input type="submit" value="Enviar" class="btn btn-success">
        </form>
        <div id="mensagemProcessamento" style="display:none;">
            <p class="text-warning">Sua solicitação está sendo executada, aguarde o término da mesma!</p>
        </div>
        <div id="metadadosArquivo"></div>
    </div>
</body>
</html>