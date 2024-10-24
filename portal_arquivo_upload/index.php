<?php
// Inclui a biblioteca PHPSpreadsheet necessária para manipular arquivos de planilhas (XLS, XLSX, CSV)
require 'vendor/autoload.php';

// Usa classes da PHPSpreadsheet para manipular planilhas
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

// Inclui a conexão com o banco de dados
include 'conn.php';

// Variável que armazenará mensagens para exibir ao usuário
$message = '';

// Verifica se o formulário foi enviado via POST e se o arquivo foi submetido corretamente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file'])) {
    // Obtém informações sobre o arquivo enviado
    $file = $_FILES['csv_file'];

    // Verifica se o arquivo foi enviado sem erros
    if ($file['error'] == UPLOAD_ERR_OK) {
        // Pega o nome e o caminho temporário do arquivo
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        // Pega a extensão do arquivo (csv, xls ou xlsx)
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

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

        // Define o nome do arquivo convertido, convertento o nome do arquivo original
        // para o formato XLSX, salvando com o mesmo nome, mas extensão .xlsx
        $newFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.xlsx';

        // Define o gravador (writer) para o formato XLSX
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        // Define o caminho onde o arquivo convertido será salvo
        $outputFilePath = 'uploads/' . $newFileName;

        // Verifica se o diretório 'uploads' existe, se não, cria-o
        if (!file_exists('uploads')) {
            mkdir('uploads', 0777, true);
        }   // O terceiro parâmetro, "true", indica que a função deve criar automaticamente todos os
            // diretórios pais necessários caso eles não existam. Por exemplo, se o diretório "uploads"
            // estiver dentro de outro diretório que não existe, a função irá criar esse diretório pai também.

        // Salva o arquivo convertido no diretório de uploads
        $writer->save($outputFilePath);

        // Mensagem para informar ao usuário que o arquivo foi convertido com sucesso
        $message .= "Arquivo convertido para XLSX e salvo em: $outputFilePath<br>";

        // Leitura dos dados da planilha para inserção no banco de dados
        $sheet = $spreadsheet->getActiveSheet();  // Obtém a planilha ativa
        $rows = $sheet->toArray();  // Converte todas as linhas da planilha em um array

        // Define qual tabela será usada para armazenar os dados (neste caso, "pasta1" é usada como exemplo)
        $table = 'pasta1'; // Pode-se definir dinamicamente qual tabela usar, conforme sua lógica

        // Itera sobre as linhas (o índice 0 é geralmente o cabeçalho, por isso é ignorado)
        foreach ($rows as $index => $row) {
            if ($index == 0) {
                continue; // Pula o cabeçalho da planilha
            }

            // Lógica para inserção na tabela 'pasta1'
            if ($table == 'pasta1') {
                // Protege contra injeção SQL e prepara os dados
                $nome = mysqli_real_escape_string($conn, trim($row[0]));
                $cargo = intval($row[1]);

                // Insere os dados na tabela 'pasta1' (nome e cargo)
                $sql = "INSERT INTO pasta1 (nome, cargo) VALUES ('$nome', $cargo)";
                if (mysqli_query($conn, $sql)) {
                    // Exibe uma mensagem de sucesso caso a inserção funcione
                    $message .= "Inserido: Nome: $nome, Cargo: $cargo<br>";
                } else {
                    // Exibe uma mensagem de erro caso ocorra algum problema na inserção
                    $message .= "Erro ao inserir dados: " . mysqli_error($conn) . "<br>";
                }
            }
            // Aqui você pode adicionar lógica para outras tabelas como 'pasta2' ou 'pasta3', dependendo da sua necessidade.
        }
    } else {
        // Caso ocorra algum erro ao fazer o upload do arquivo, exibe uma mensagem de erro
        $message .= "Erro ao fazer upload do arquivo.";
    }
}

// Consulta os dados da tabela 'pasta1' para exibir na página HTML
$result = mysqli_query($conn, "SELECT * FROM pasta1");

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Upload de Arquivos e Importação para DB</title>
</head>

<body>
    <h1>Importar Arquivo</h1>
    <!-- Formulário para upload do arquivo CSV, XLS ou XLSX -->
    <form action="" method="POST" enctype="multipart/form-data">
        <!-- Input para selecionar o arquivo -->
        <input type="file" name="csv_file" accept=".csv,.xls,.xlsx" required>
        <!-- Botão para submeter o formulário -->
        <input type="submit" value="Importar">
    </form>

    <div>
        <!-- Exibe as mensagens sobre o processo de upload/conversão/inserção -->
        <h2><?php echo $message; ?></h2>
    </div>

    <h2>Dados Importados da Tabela pasta1</h2>
    <!-- Tabela que exibe os dados importados da tabela 'pasta1' -->
    <table border="1">
        <tr>
            <!-- Cabeçalhos da tabela: ID, Nome, Cargo -->
            <th>ID</th>
            <th>Nome</th>
            <th>Cargo</th>
        </tr>
        <!-- Loop pelos resultados da consulta SQL para exibir os dados -->
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <!-- Exibe o ID, Nome e Cargo para cada linha de dados -->
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['nome']; ?></td>
                <td><?php echo $row['cargo']; ?></td>
            </tr>
        <?php } ?>
    </table>
</body>

</html>