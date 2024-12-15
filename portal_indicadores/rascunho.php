

<?php
// ... (código para conectar ao banco de dados)

if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] == 0) {
    $arquivo = $_FILES['arquivo']['tmp_name'];
    $extensao = pathinfo($arquivo, PATHINFO_EXTENSION);

    if ($extensao == 'xls' || $extensao == 'csv') {
        // Mover o arquivo para um diretório temporário
        $novoNome = uniqid() . '.' . $extensao;
        move_uploaded_file($_FILES['arquivo']['tmp_name'], 'uploads/' . $novoNome);

        // Ler os dados do arquivo (exemplo usando PHPExcel)
        require 'PHPExcel/Classes/PHPExcel.php';
        $objPHPExcel = PHPExcel_IOFactory::load('uploads/' . $novoNome);
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

        // Inserir os dados no banco de dados
        foreach ($sheetData as $row) {
            $stmt = $pdo->prepare("INSERT INTO sua_tabela (campo1, campo2, ...) VALUES (?, ?, ...)");
            $stmt->execute($row);



        }
    } else {
        echo "Formato de arquivo inválido.";
    }
}




// Iterar sobre as linhas e inserir os dados no banco
foreach ($worksheet->getRowIterator() as $row) {
    $rowData = $worksheet->rangeToArray('A' . $row->getRowIndex() . ':' . 'Z' . $row->getRowIndex());
    // Montar a consulta SQL e executar
    $sql = "INSERT INTO sua_tabela (campo1, campo2, ...) VALUES (?, ?, ...)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $rowData[0][0], $rowData[0][1], ...);
    $stmt->execute();
}

SQL

INSERT INTO funcionarios (nome, cargo) VALUES ('teste', 30);



?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Indicadores</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="icon" href="./img/favicon.png" type="image/png">
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <script src="./js/jquery-3.7.1.slim.min.js"></script>
</head>

<body>
    <h1 style="background-color: red;">Hello World!</h1> <!-- HTML e CSS -->
    <p>This is a paragraph.</p>

    <script>
    console.log("Olá, Mundo!");
    </script>


PHP

<?php

fclose($handle);
$message .= "Dados importados com sucesso!";

// Redireciona para evitar a reenvio do formulário
header("Location: " . $_SERVER['PHP_SELF']);
exit; // Certifique-se de usar exit após header

<?php
require_once '../vendor/autoload.php';
include('../conn.php');
include('../logs/criaLogs.php'); // Inclui a função de log
include('../dbSql/inserirDados.php'); // Inclui a função genérica de inserção de dados

function processarAcessoPortal($file, $conn) {
    // Limpa a tabela antes de inserir novos dados
    include_once('../dbSql/truncarTabelaSql.php');
    truncarTabela($conn, 'portal_acesso');

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();

    $totalLinhas = 0;
    $totalColunas = 0;
    $erros = 0;
    $errosDetalhados = '';

    foreach ($worksheet->getRowIterator() as $indice => $row) {
        if ($indice > 1) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $codigo = (int)$cellIterator->current()->getValue();
            $cellIterator->next();
            $portal = $cellIterator->current()->getValue();
            $cellIterator->next();
            $mes_acesso = (int)$cellIterator->current()->getValue();
            $cellIterator->next();
            $ano_acesso = (int)$cellIterator->current()->getValue();
            $cellIterator->next();
            $numero_acessos = (int)$cellIterator->current()->getValue();

            $dados = [
                'codigo' => $codigo,
                'portal' => $portal,
                'mes_acesso' => $mes_acesso,
                'ano_acesso' => $ano_acesso,
                'numero_acessos' => $numero_acessos,
                'data' => date('Y-m-d H:i:s')
            ];

            if (!inserirDados($conn, 'portal_acesso', $dados)) {
                $erros++;
                $errosDetalhados .= "Erro na linha $indice, coluna " . $cellIterator->key() . ": " . mysqli_error($conn) . "\n";
            } else {
                $totalLinhas++;
                $totalColunas = max($totalColunas, count($dados));
            }
        }
    }

    criaLogs('portal_acesso', "Dados da tabela portal_acesso foram apagados.");
    criaLogs('portal_acesso', "Total de linhas inseridas: $totalLinhas, Total de colunas: $totalColunas");
    criaLogs('portal_acesso', "Total de linhas que apresentaram erro: $erros");
    if ($erros > 0) {
        criaLogs('portal_acesso', $errosDetalhados);
    } else {
        criaLogs('portal_acesso', "Todas as informações carregadas com sucesso!");
    }

    // Exibe a mensagem resumida no navegador
    echo "<script>
        alert('Dados da tabela portal_acesso foram apagados.\\n" .
                "Total de linhas inseridas: $totalLinhas, Total de colunas: $totalColunas\\n" .
                "Total de linhas que apresentaram erro: $erros\\n" .
                ($erros === 0
                    ? "Todas as informações carregadas com sucesso!\\n"
                    : "As informações de erros podem ser vistas no arquivo de log portal_acessoLog.txt") . "');
        window.location.href = '../index.php';
        </script>";
}

if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);
    processarAcessoPortal($file, $conn);
}

$conn->close();
?>




?>


Bash

composer update
composer require phpoffice/phpspreadsheet

<script>
JSON
{
    "require": {
        "phpoffice/phpspreadsheet": "^1.3"
    }
}
</script>




</body>

</html>