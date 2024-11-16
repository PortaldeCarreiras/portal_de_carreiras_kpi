<?php
require_once '../vendor/autoload.php';
include('../conn.php');
include('../logs/criaLogs.php'); // Inclui a função de log

function inserirDadosAcessoPortal($conn, $tabela, $dados)
{
    $campos = implode(", ", array_keys($dados));
    $valores = "'" . implode("','", array_values($dados)) . "'";
    if (mysqli_query($conn, "INSERT INTO $tabela ($campos) VALUES ($valores)")) {
        echo "Dados inseridos com sucesso!<br>";
    } else {
        $mensagem = "Erro na inserção: " . mysqli_error($conn);
        echo $mensagem . "<br>";
        criaLogs($tabela, $mensagem); // Chama a função de log
    }
}

function processarAcessoPortal($file, $conn)
{
    // Limpa a tabela antes de inserir novos dados
    // LEMBRAR DE CODIFICAR PARA QUE APENAS O USUÁRIO ADM POSSA EXECUTAR ESSA FUNÇÃO.
    // include_once('../dbSql/truncarTabelaSql.php');
    // truncarTabela($conn, 'portal_acesso');

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();

    $totalLinhas = 0;
    $totalColunas = 0;
    $erros = 0;

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

            // $data_acesso = converterDataExcelParaSQL($data_acesso_raw, $indice, $cellIterator->key(), $erros, 'portal_acesso');

            if (!mysqli_query($conn, "INSERT INTO portal_acesso (codigo, portal, mes_acesso, ano_acesso, numero_acessos, data) VALUES ('$codigo', '$portal', '$mes_acesso', '$ano_acesso', '$numero_acessos', NOW())")) {
                $mensagem = "Erro na inserção: " . mysqli_error($conn);
                echo $mensagem . "<br>";
                criaLogs('portal_acesso', $mensagem); // Chama a função de log
                $erros++;
            } else {
                $totalLinhas++;
                $totalColunas = max($totalColunas, count($dados));
            }
        }
    }

    $mensagemFinal = "Total de linhas inseridas: $totalLinhas, Total de colunas: $totalColunas";
    criaLogs('portal_acesso', $mensagemFinal); // Chama a função de log
    echo $mensagemFinal . "<br>";

    $mensagemErros = "Total de linhas que apresentaram erro: $erros";
    criaLogs('portal_acesso', $mensagemErros); // Chama a função de log
    echo $mensagemErros . "<br>";

    if ($erros === 0) {
        $mensagemSucesso = "Todas as informações carregadas com sucesso!";
        criaLogs('portal_acesso', $mensagemSucesso); // Chama a função de log
        echo $mensagemSucesso . "<br>";
    }

    // Adiciona duas linhas em branco ao final do log
    criaLogs('portal_acesso', "\n\n");
}

if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);
    processarAcessoPortal($file, $conn);
}

$conn->close();

echo "<a href='../index.php'>Voltar</a>";
