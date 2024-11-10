

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