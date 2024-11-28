<?php
require_once '../vendor/autoload.php';
include_once('../conn.php');
include_once('../logs/criaLogs.php'); // Inclui a função de log
include_once('../dbSql/inserirDados.php'); // Inclui a função genérica de inserção de dados
include_once('../dbSql/dateConverterSql.php'); // Inclui a função de conversão de data
include_once('../data_processing/utils.php'); // Inclui as funções comuns
include_once('../dbSql/truncarTabelaSql.php');
include_once('../logs/ordenarGravarErrosLog.php');
include_once('acessoDataPipeline.php'); // Inclui a função de processamento de linha

function processarAcessoPortal($file, $conn, $tabela, $processarLinha, $dataArquivo){
    registrarLogDepuracao("Função processarAcessoPortal iniciada.");
    // Limpa a tabela antes de inserir novos dados
    // LEMBRAR DE CODIFICAR PARA QUE APENAS O USUÁRIO ADM POSSA EXECUTAR ESSA FUNÇÃO.
    truncarTabela($conn, $tabela);

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);    // Carrega a planilha
    registrarLogDepuracao("Planilha $file carregada.");

    $worksheet = $spreadsheet->getActiveSheet();    // Obtém a aba ativa da planilha
    registrarLogDepuracao("Aba ativa da planilha obtida.");

    // Capturar a última coluna usada
    $ultimaColuna = $worksheet->getHighestColumn(); // Exemplo: "D"
    registrarLogDepuracao("Última coluna usada: $ultimaColuna");

    // Inicializa as variáveis que contarão as linhas e colunas inseridas e os erros
    $totalLinhas = 0;
    $totalColunas = 0;
    $erros = 0;
    // Array para armazenar os erros detalhados
    $errosDetalhados = [];

    // Itera sobre todas as linhas da planilha
    iterarSobreLinhas($worksheet, function($cellIterator, $indice, &$erros, $tabela, &$errosDetalhados) use ($processarLinha, $dataArquivo) {
        return $processarLinha($cellIterator, $indice, $erros, $tabela, $errosDetalhados, $dataArquivo);
    }, $conn, $tabela, $totalLinhas, $totalColunas, $erros, $errosDetalhados, false, $dataArquivo);  // $metaProcess = false

    // Captura e grava os erros no log
    capturarErrosToLog($errosDetalhados, $tabela, $totalLinhas, $totalColunas, $erros, $file, false);    // $metaProcess = false

    // Exibe a mensagem resumida no navegador
    exibirMensagemResumida($tabela, $totalLinhas, $totalColunas, $erros, false);   // $metaProcess = false
}   //  Fim da função processarAcessoPortal

// Verifica se o arquivo foi enviado via GET
if (isset($_GET['file']) && isset($_GET['dataModificacao'])) {
    $file = urldecode($_GET['file']);
    $tabela = 'portal_acesso'; // Informar a tabela que será trabalhada
    $dataArquivo = urldecode($_GET['dataModificacao']); // Obtém a data do arquivo do formulário
    processarAcessoPortal($file, $conn, $tabela, 'acessoPlanilhaExtrairMapToDb', $dataArquivo);
}   //  Fim do IF de verificação de arquivo enviado via GET

$conn->close();
?>