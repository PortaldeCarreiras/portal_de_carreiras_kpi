<?php
require_once '../vendor/autoload.php';
include('../conn.php');
include('../logs/criaLogs.php'); // Inclui a função de log
include('../dbSql/inserirDados.php'); // Inclui a função genérica de inserção de dados
include('../data_processing/utils.php'); // Inclui as funções comuns
include_once('../dbSql/truncarTabelaSql.php');

function processarAcessoPortal($file, $conn, $tabela)
{
    // Limpa a tabela antes de inserir novos dados
    // LEMBRAR DE CODIFICAR PARA QUE APENAS O USUÁRIO ADM POSSA EXECUTAR ESSA FUNÇÃO.
    truncarTabela($conn, $tabela);

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

            if (!inserirDados($conn, $tabela, $dados)) {
                $erros++;
            } else {
                $totalLinhas++;
                $totalColunas = max($totalColunas, count($dados));
            }
        }
    }

    $mensagemFinal = "Total de linhas inseridas: $totalLinhas, Total de colunas: $totalColunas";
    criaLogs($tabela, $mensagemFinal); // Chama a função de log

    $mensagemErros = "Total de linhas que apresentaram erro: $erros";
    criaLogs($tabela, $mensagemErros); // Chama a função de log

    if ($erros === 0) {
        $mensagemSucesso = "Todas as informações carregadas com sucesso!";
        criaLogs($tabela, $mensagemSucesso); // Chama a função de log
    }

    // Adiciona duas linhas em branco ao final do log
    criaLogs($tabela, "\n\n");

    // Exibe a mensagem resumida no navegador
    // Concatenando as linhas com " . " para quebra de linha no cod PHP (senão não funciona)"
    exibirMensagemResumida($tabela, $totalLinhas, $totalColunas, $erros);
}

if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);
    $tabela = 'portal_acesso'; // Informar a tabela que será trabalhada
    processarAcessoPortal($file, $conn, $tabela);
}

$conn->close();
?>
