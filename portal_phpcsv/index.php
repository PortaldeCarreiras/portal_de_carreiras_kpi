<?php
require_once 'vendor/autoload.php'; // Inclui o autoloader do PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

// Conexão com o banco de dados (ajuste as credenciais - substitua pelos seus dados)
include('conn.php');

// Função para converter caracteres acentuados para seus equivalentes sem acento
function removerAcentos($string) {
    $acentos = array(
        'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
        'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I', 'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
        'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U', 'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'Ç' => 'C', 'ç' => 'c', 'Ñ' => 'N', 'ñ' => 'n'
    );
    return strtr($string, $acentos);
}

// Função para converter o nome do arquivo para camelCase
function converterParaCamelCase($string) {
    $string = removerAcentos($string);
    $string = preg_replace('/[^a-zA-Z0-9]/', ' ', $string);
    $string = ucwords(strtolower($string));
    $string = str_replace(' ', '', $string);
    return lcfirst($string);
}

// Função para normalizar o nome do arquivo
function normalizarNomeArquivo($nomeArquivo) {
    $nomeArquivo = removerAcentos($nomeArquivo);
    return strtolower($nomeArquivo);
}

// Variável que armazenará mensagens para exibir ao usuário
$message = '';

// Variáveis que serão utilizadas em IFs diferentes abaixo
$fileExtension = '';
$fileName = '';
$data_criacao = '';

// Lista de nomes de arquivos permitidos (normalizados, sem extensão)
$nomesPermitidos = [
    'acessoportal',
    'consulta de vagas de estagio',
    'saida'
];

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

    // Normaliza o nome do arquivo (sem a extensão)
    $nomeArquivoNormalizado = normalizarNomeArquivo(pathinfo($fileName, PATHINFO_FILENAME));

    // Verificar nome de arquivo para ver se ele corresponde aos três esperados
    if (!in_array($nomeArquivoNormalizado, $nomesPermitidos)) {
        $nomesEsperados = implode(', ', $nomesPermitidos);
        echo "<script>alert('Nome de arquivo não permitido. Os nomes esperados são: $nomesEsperados'); window.location.href = 'index.php';</script>";
        exit();
    }

    // Verificar extensão de arquivo para ver se ela é permitida (csv, xls, xlsx)
    $extensoesPermitidas = ['csv', 'xls', 'xlsx'];
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