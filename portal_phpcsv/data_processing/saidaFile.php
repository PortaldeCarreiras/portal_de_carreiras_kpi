<?php
require_once '../vendor/autoload.php';
include_once('../conn.php');
include_once('../logs/criaLogs.php'); // Inclui a função de log
include_once('../dbSql/dateConverterSql.php'); // Inclui a função de conversão de data
include_once('../dbSql/inserirDados.php'); // Inclui a função genérica de inserção de dados
include_once('../data_processing/utils.php'); // Inclui as funções comuns
include_once('../dbSql/truncarTabelaSql.php');
include_once('../logs/ordenarGravarErrosLog.php');
include_once('saidaDataPipeline.php'); // Inclui a função de processamento de linha

function processarSaidaFile($file, $conn, $tabela, $processarLinha)
{
    // Limpa a tabela no DB-SQL antes de inserir dados novos.
    // LEMBRAR DE CODIFICAR PARA QUE APENAS O USUÁRIO ADM POSSA EXECUTAR ESSA FUNÇÃO.
    truncarTabela($conn, $tabela);

    // Carrega a planilha
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();    // Pega a aba ativa

    // Inicializa as variáveis que contarão as linhas e colunas inseridas e os erros
    $totalLinhas = 0;
    $totalColunas = 0;
    $erros = 0;
    // Array para armazenar os erros detalhados
    $errosDetalhados = [];

    // Itera sobre todas as linhas da planilha
    iterarSobreLinhas($worksheet, $processarLinha, $conn, $tabela, $totalLinhas, $totalColunas, $erros, $errosDetalhados);

    // Essa função ordena e grava os erros no log
    capturarErrosToLog($errosDetalhados, $tabela, $totalLinhas, $totalColunas, $erros, $file);

    // Exibe a mensagem resumida no navegador
    exibirMensagemResumida($tabela, $totalLinhas, $totalColunas, $erros);
}   //  Fim da função processarSaidaFile

// Verifica se o arquivo foi enviado via GET
if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);
    $tabela = 'portal_saida_estagio'; // Informar a tabela que será trabalhada
    processarSaidaFile($file, $conn, $tabela, 'saidaPlanilhaExtrairMapToDb');
}   //  Fim do IF de verificação de arquivo enviado via GET

$conn->close();
?>
