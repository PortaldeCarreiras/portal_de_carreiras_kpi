<?php
require 'vendor/autoload.php'; // Autoload do PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

include 'conn.php'; // Inclua sua conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura o arquivo enviado
    $arquivo = $_FILES['arquivo_xls'];

    if ($arquivo['error'] == UPLOAD_ERR_OK) {
        $tempFile = $arquivo['tmp_name'];
        $fileType = $arquivo['type'];
        $nomeOriginal = basename($arquivo['name']);
        $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);

        // Verificar o tipo de arquivo e converter para XLSX
        $spreadsheet = null;
        $destino = 'uploads/' . pathinfo($nomeOriginal, PATHINFO_FILENAME) . '.xlsx'; // Nome do arquivo convertido

        if ($extensao === 'csv') {
            // Lendo CSV e convertendo para XLSX
            $reader = IOFactory::createReader('Csv');
            $spreadsheet = $reader->load($tempFile);
        } elseif ($extensao === 'xls') {
            // Lendo XLS e convertendo para XLSX
            $reader = IOFactory::createReader('Xls');
            $spreadsheet = $reader->load($tempFile);
        } elseif ($extensao === 'xlsx') {
            // Se já for XLSX, apenas carrega
            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($tempFile);
        } else {
            echo "Formato de arquivo não suportado.";
            exit;
        }

        // Salvando o arquivo convertido como XLSX
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($destino);

        echo "Arquivo convertido e salvo como XLSX: " . $destino . "<br>";

        // Agora você pode ler os dados e armazenar no banco
        processarDados($spreadsheet);

    } else {
        echo "Erro ao fazer upload do arquivo.";
    }
}

function processarDados($spreadsheet) {
    global $conn;

    // Captura a primeira planilha
    $sheet = $spreadsheet->getActiveSheet();
    $highestRow = $sheet->getHighestRow(); // Última linha com dados
    $highestColumn = $sheet->getHighestColumn(); // Última coluna com dados
    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

    // Supondo que os dados estão nas colunas Nome, Cargo, (opcionalmente Salário e Tempo)
    for ($row = 2; $row <= $highestRow; ++$row) {
        $nome = $sheet->getCellByColumnAndRow(1, $row)->getValue();
        $cargo = $sheet->getCellByColumnAndRow(2, $row)->getValue();
        $salario = $highestColumnIndex >= 3 ? $sheet->getCellByColumnAndRow(3, $row)->getValue() : null;
        $tempo = $highestColumnIndex >= 4 ? $sheet->getCellByColumnAndRow(4, $row)->getValue() : null;

        // Determinar a tabela com base no número de colunas
        if ($highestColumnIndex == 2) {
            $sql = "INSERT INTO pasta1 (nome, cargo) VALUES ('$nome', $cargo)";
        } elseif ($highestColumnIndex == 3) {
            $sql = "INSERT INTO pasta2 (nome, cargo, salario) VALUES ('$nome', $cargo, $salario)";
        } elseif ($highestColumnIndex == 4) {
            $sql = "INSERT INTO pasta3 (nome, cargo, salario, tempo) VALUES ('$nome', $cargo, $salario, $tempo)";
        }

        if (mysqli_query($conn, $sql)) {
            echo "Dados inseridos com sucesso na tabela.<br>";
        } else {
            echo "Erro ao inserir dados: " . mysqli_error($conn) . "<br>";
        }
    }
}
?>



