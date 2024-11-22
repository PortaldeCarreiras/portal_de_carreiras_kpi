<?php
require_once '../vendor/autoload.php';
include('../conn.php');
include('../logs/criaLogs.php'); // Inclui a função de log
include('../dbSql/inserirDados.php'); // Inclui a função genérica de inserção de dados
include('../dbSql/dateConverterSql.php'); // Inclui a função de conversão de data
include('../data_processing/utils.php'); // Inclui as funções comuns
include_once('../dbSql/truncarTabelaSql.php');
include_once('../logs/ordenarGravarErrosLog.php');
include('vagasDataPipeline.php'); // Inclui a função de processamento de linha

function processarVagasEstagio($file, $conn, $tabela, $processarLinha)
{
    // Limpa a tabela antes de inserir novos dados
    // LEMBRAR DE CODIFICAR PARA QUE APENAS O USUÁRIO ADM POSSA EXECUTAR ESSA FUNÇÃO.
    truncarTabela($conn, $tabela);

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();

    // Inicializa as variáveis que contarão as linhas e colunas inseridas e os erros
    $totalLinhas = 0;
    $totalColunas = 0;
    $erros = 0;
    // Array para armazenar os erros detalhados
    $errosDetalhados = [];

    // Itera sobre todas as linhas da planilha
    iterarSobreLinhas($worksheet, $processarLinha, $conn, $tabela, $totalLinhas, $totalColunas, $erros, $errosDetalhados);

    // Essa função ordena e grava os erros no log
    capturarErrosToLog($errosDetalhados, $tabela, $totalLinhas, $totalColunas, $erros);

    // Exibe a mensagem resumida no navegador
    exibirMensagemResumida($tabela, $totalLinhas, $totalColunas, $erros);
}

// Verifica se o arquivo foi enviado via GET
if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);
    $tabela = 'portal_vagas_estagio'; // Informar a tabela que será trabalhada
    processarVagasEstagio($file, $conn, $tabela, 'vagasPlanilhaExtrairMapToDb');
}   //  Fim do IF de verificação de arquivo enviado via GET

$conn->close();
