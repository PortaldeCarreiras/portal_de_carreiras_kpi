<?php
require_once 'vendor/autoload.php'; // Inclui o autoloader do PhpSpreadsheet
require_once 'data_processing/utils.php'; // Inclui as funções comuns
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

include('conn.php');    // Conexão com o banco de dados (ajuste as credenciais - substitua pelos seus dados)

// Variável que armazenará mensagens para exibir ao usuário
$message = '';

// Variáveis que serão utilizadas em IFs diferentes abaixo
$fileExtension = '';
$fileName = '';
$data_criacao = '';


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

// PEGA O FORMULÁRIO VIA POST, CARREGA, VERIFICA O TIPO DE EXTENSÃO,
// ABRE COM O SPREADSHEET, CONVERT PARA XLSX E SALVA NA PASTA /UPLOAD DO PROJETO
// Verifica se o formulário foi enviado via POST e se o arquivo foi submetido corretamente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['xls_file'])) {
    // Obtém informações sobre o arquivo enviado
    $file = $_FILES['xls_file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $data_criacao = date('Y-m-d H:i:s', filemtime($fileTmpName));

    // Normaliza o nome do arquivo (sem a extensão)
    $nomeArquivoNormalizado = normalizarNomeArquivo(pathinfo($fileName, PATHINFO_FILENAME));

    // Verificar nome de arquivo para ver se ele corresponde aos três esperados
    if (!in_array($nomeArquivoNormalizado, $nomesPermitidos)) {
        $nomesEsperados = "AcessoPortal, Consulta de Vagas de estágio e saida.";
        $variacoesPermitidas = "acessoportal, Acesso Portal, Consulta de Vagas de Estágio, consultadevagasdeestagio, consulta de vagas de estágio, consulta de vagas de estagio, Saída, Saida, saída.";
        echo "<script>alert('NOME DE ARQUIVO NÃO PERMITIDO!\\nOs nomes esperados são: $nomesEsperados\\nAs variações permitidas são: $variacoesPermitidas'); window.location.href = 'index.php';</script>";
        exit();
    }

    // Verificar extensão de arquivo para ver se ela é permitida (csv, xls, xlsx)
    if (!in_array(strtolower($fileExtension), $extensoesPermitidas)) {
        echo "<script>alert('Extensão de arquivo não permitida.'); window.location.href = 'index.php';</script>";
        exit();
    }

    // Verifica se o arquivo foi enviado sem erros
    if ($file['error'] == UPLOAD_ERR_OK) {

        // Verifica a extensão do arquivo para escolher o leitor apropriado
        switch (strtolower($fileExtension)) {
            case 'csv':
                // Se for um arquivo CSV, usa o leitor de CSV
                $reader = IOFactory::createReader('Csv');
                break;
            case 'xls':
                // Se for um arquivo XLS, usa o leitor de XLS
                $reader = IOFactory::createReader('Xls');
                break;
            case 'xlsx':
                // Se for um arquivo XLSX, usa o leitor de XLSX
                $reader = IOFactory::createReader('Xlsx');
                break;
            default:
                // Se o formato não for suportado, exibe uma mensagem de erro
                die("Formato de arquivo não suportado.");
        }

        // Carrega o arquivo temporário para ser manipulado
        $spreadsheet = $reader->load($fileTmpName);

        // Define o nome do arquivo convertido, convertendo o nome do arquivo original
        // para o formato XLSX, salvando com o mesmo nome, mas extensão .xlsx
        $newFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.xlsx';

        // Define o gravador (writer) para o formato XLSX
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        // Define o caminho onde o arquivo convertido será salvo
        $outputFilePath = __DIR__ . '/uploads/' . $newFileName; // Use __DIR__ para garantir o caminho absoluto

        // Verifica se o diretório 'uploads' existe, se não, cria-o
        if (!file_exists(__DIR__ . '/uploads')) {
            mkdir(__DIR__ . '/uploads', 0777, true);
        }

        // Salva o arquivo convertido no diretório de uploads
        $writer->save($outputFilePath);

        // Mensagem para informar ao usuário que o arquivo foi convertido com sucesso
        $message .= "Arquivo convertido para XLSX e salvo em: $outputFilePath<br>";

        // PROCESSAMENTO DO ARQUIVO AO SER CLICADO O BOTÃO "ENVIAR".
        $newFileName = converterParaCamelCase(pathinfo($newFileName, PATHINFO_FILENAME)) . '.xlsx';
        if (strcasecmp($newFileName, 'acessoPortal.xlsx') == 0) {
            header("Location: data_processing/acessoPortal.php?file=" . urlencode($outputFilePath)); // Linha alterada
        } elseif (strcasecmp($newFileName, 'consultaDeVagasDeEstagio.xlsx') == 0) {
            header("Location: data_processing/vagasEstagio.php?file=" . urlencode($outputFilePath)); // Linha alterada
        } elseif (strcasecmp($newFileName, 'saida.xlsx') == 0) {
            header("Location: data_processing/saidaFile.php?file=" . urlencode($outputFilePath)); // Linha alterada
        }
        exit();
    } else {
        $message .= "Erro ao fazer upload do arquivo.";
    }
}

// echo "<label>&nbsp Arquivo Carregado: $fileName</label><br>";
// echo "<label>&nbsp Data de Criação: $data_criacao</label><br>";

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <title>Upload de Arquivos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script>
        function confirmarExclusao() {
            const arquivo = document.getElementById("arquivo").files[0];
            if (arquivo) {
                return confirm(`Deseja realmente deletar todos os dados e carregar o novo arquivo ${arquivo.name} ao Banco de dados?`);
            }
            return false;
        }

        // Função para exibir a mensagem de processamento
        function exibirMensagemProcessamento() {
            const mensagemProcessamento = document.getElementById("mensagemProcessamento");
            mensagemProcessamento.style.display = "block";
        }
    </script>
</head>

<body>
    <div class="container">
        <h1 class="text-danger">Upload de Arquivos</h1>
        <p>Selecione um arquivo para fazer o upload.</p>
        <!-- Adiciona a chamada para exibir a mensagem de processamento ao enviar o formulário -->
        <form action="" method="post" enctype="multipart/form-data" onsubmit="exibirMensagemProcessamento(); return confirmarExclusao();">
            <div class="form-group">
                <input type="file" id="arquivo" name="xls_file" class="btn btn-success" required>
            </div>
            <input type="submit" value="Enviar" class="btn btn-success">
        </form>
        <!-- Div para exibir a mensagem de processamento -->
        <div id="mensagemProcessamento" style="display:none;">
            <p class="text-warning">Sua solicitação está sendo executada, aguarde o término da mesma!</p>
        </div>
        <!-- Exibe as informações do arquivo carregado -->
        <!-- <?php if (isset($fileName) && isset($data_criacao)): ?>
            <label>&nbsp Arquivo Carregado: <?php echo $fileName; ?></label><br>
            <label>&nbsp Data de Criação: <?php echo $data_criacao; ?></label><br>
        <?php endif; ?> -->
    </div>
</body>

</html>