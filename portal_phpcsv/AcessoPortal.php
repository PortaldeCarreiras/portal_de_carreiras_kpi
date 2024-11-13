<?php
require_once 'vendor/autoload.php';
include('conn.php');

// Função Truncar Tabela, para deletar e começar do "id01"
// LEMBRAR DE CODIFICAR PARA QUE APENAS O USUÁRIO ADM POSSA EXECUTAR ESSA FUNÇÃO.
function truncarTabela($conn, $tabela)
{
    $sqlTruncate = "TRUNCATE TABLE $tabela";
    if (mysqli_query($conn, $sqlTruncate)) {
        echo "Dados da tabela $tabela foram apagados.<br>";
    } else {
        echo "Erro ao apagar dados da tabela $tabela: " . mysqli_error($conn) . "<br>";
    }
}


function inserirDadosAcessoPortal($conn, $tabela, $dados) {
    $campos = implode(", ", array_keys($dados));
    $valores = "'" . implode("','", array_values($dados)) . "'";
    if (mysqli_query($conn, "INSERT INTO $tabela ($campos) VALUES ($valores)")) {
        echo "Dados inseridos com sucesso!<br>";
    } else {
        echo "Erro na inserção: " . mysqli_error($conn) . "<br>";
    }
}

function processarAcessoPortal($file, $conn) {
    
    // Limpa a tabela no DB-SQL antes de inserir dados novos.
    // LEMBRAR DE CODIFICAR PARA QUE APENAS O USUÁRIO ADM POSSA EXECUTAR ESSA FUNÇÃO.
    truncarTabela($conn, 'portal_acesso');

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();

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

            inserirDadosAcessoPortal($conn, 'portal_acesso', $dados);
        }
    }
}

if (isset($_GET['file'])) {
    $file = $_GET['file'];
    processarAcessoPortal($file, $conn);
}

$conn->close();

echo "<a href='index.php'>Voltar</a>";
?>