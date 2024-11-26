<?php
require_once '../vendor/autoload.php';
include_once('../conn.php');
include_once('../logs/criaLogs.php'); // Inclui a função de log
include_once('../dbSql/inserirDados.php'); // Inclui a função genérica de inserção de dados
include_once('../dbSql/dateConverterSql.php'); // Inclui a função de conversão de data
include_once('../data_processing/utils.php'); // Inclui as funções comuns
include_once('../dbSql/truncarTabelaSql.php');
include_once('../logs/ordenarGravarErrosLog.php');
include_once('vagasDataPipeline.php'); // Inclui a função de processamento de linha

function processarVagasEstagio($file, $conn, $tabela, $processarLinha)
{
    registrarLogDepuracao("Função processarVagasEstagio iniciada.");

    // Limpa a tabela antes de inserir novos dados
    // LEMBRAR DE CODIFICAR PARA QUE APENAS O USUÁRIO ADM POSSA EXECUTAR ESSA FUNÇÃO.
    truncarTabela($conn, $tabela);
    registrarLogDepuracao("Tabela $tabela truncada.");

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    registrarLogDepuracao("Planilha $file carregada.");

    $worksheet = $spreadsheet->getActiveSheet();
    registrarLogDepuracao("Aba ativa da planilha obtida.");

    // Inicializa as variáveis que contarão as linhas e colunas inseridas e os erros
    $totalLinhas = 0;
    $totalColunas = 0;
    $erros = 0;
    // Array para armazenar os erros detalhados
    $errosDetalhados = [];

    // Itera sobre todas as linhas da planilha
    registrarLogDepuracao("Iniciando iteração sobre as linhas da planilha.");
    iterarSobreLinhas($worksheet, $processarLinha, $conn, $tabela, $totalLinhas, $totalColunas, $erros, $errosDetalhados, false);
    registrarLogDepuracao("Iteração sobre as linhas finalizada. Total de linhas processadas: $totalLinhas. Total de erros: $erros.");

    // Essa função ordena e grava os erros no log
    capturarErrosToLog($errosDetalhados, $tabela, $totalLinhas, $totalColunas, $erros, $file);
    registrarLogDepuracao("Erros capturados e registrados no log.");

    registrarLogDepuracao("Exibindo mensagem resumida no navegador.");
    exibirMensagemResumida($tabela, $totalLinhas, $totalColunas, $erros);
    registrarLogDepuracao("Mensagem resumida exibida.");
}

// Verifica se o arquivo foi enviado via GET
if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);
    $tabela = 'portal_vagas_estagio'; // Informar a tabela que será trabalhada
    // $dataArquivo = urldecode($_GET['dataModificacao']); // Obtém a data do arquivo do formulário
    processarVagasEstagio($file, $conn, $tabela, 'vagasPlanilhaExtrairMapToDb');
}   //  Fim do IF de verificação de arquivo enviado via GET


$conn->close();
