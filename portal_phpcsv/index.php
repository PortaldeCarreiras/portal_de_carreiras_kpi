<?php
require_once 'vendor/autoload.php'; // Inclui o autoloader do PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

// Conexão com o banco de dados (ajuste as credenciais - substitua pelos seus dados)
include('conn.php');


// Variável que armazenará mensagens para exibir ao usuário
$message = '';

// Variáveis que serão utilizadas em IFs diferentes abaixo
$fileExtension = '';
$fileName = '';
$data_criacao = '';

// PEGA O FORMULÁRIO VIA POST, CARREGA, VERIFICA O TIPO DE EXTENSÃO,
// ABRE COM O SPREADSHEET, CONVERT PARA XLSX E SALVA NA PASTA /UPLOAD DO PROJETO
// Verifica se o formulário foi enviado via POST e se o arquivo foi submetido corretamente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['xls_file'])) {
    // Obtém informações sobre o arquivo enviado
    // Garante que as variáveis só sejam exibidas quando definidas:
    $file = $_FILES['xls_file'];
    // Pega o nome e o caminho temporário do arquivo
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    // Pega a extensão do arquivo (csv, xls ou xlsx)
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $data_criacao = date('Y-m-d H:i:s', filemtime($fileTmpName));

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
        if ($newFileName == 'AcessoPortal.xlsx') {
            header("Location: data_processing/acessoPortal.php?file=" . urlencode($outputFilePath)); // Linha alterada
        } elseif ($newFileName == 'Consulta de Vagas de estágio.xlsx') {
            header("Location: data_processing/vagasEstagio.php?file=" . urlencode($outputFilePath)); // Linha alterada
        } elseif ($newFileName == 'saida.xlsx') {
            header("Location: data_processing/fileSaida.php?file=" . urlencode($outputFilePath)); // Linha alterada
        }
        exit();
    } else {
        $message .= "Erro ao fazer upload do arquivo.";
    }
}

echo "<label>&nbsp Arquivo Carregado: $fileName</label><br>";
echo "<label>&nbsp Data de Criação: $data_criacao</label><br>";
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
                return confirm(`Deseja realmente deletar todos os dados e carregar o novo arquivo ${arquivo.name}?`);
            }
            return false;
        }
    </script>
</head>

<body>
    <div class="container">
        <h1 class="text-danger">Upload de Arquivos</h1>
        <p>Selecione um arquivo para fazer o upload.</p>
        <form action="" method="post" enctype="multipart/form-data" onsubmit="return confirmarExclusao();">
            <div class="form-group">
                <input type="file" id="arquivo" name="xls_file" class="btn btn-success" required>
            </div>
            <input type="submit" value="Enviar" class="btn btn-success">
        </form>
    </div>
</body>

</html>