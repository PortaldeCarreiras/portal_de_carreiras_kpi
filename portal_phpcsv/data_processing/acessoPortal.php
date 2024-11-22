<?php
require_once '../vendor/autoload.php';
include('../conn.php');
include('../logs/criaLogs.php'); // Inclui a função de log
include('../dbSql/inserirDados.php'); // Inclui a função genérica de inserção de dados
include('../data_processing/utils.php'); // Inclui as funções comuns
include_once('../dbSql/truncarTabelaSql.php');
include_once('../logs/ordenarGravarErrosLog.php');

function processarAcessoPortal($file, $conn, $tabela, $processarLinha)
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
    iterarSobreLinhas($worksheet, $processarLinha, $conn, $tabela, $totalLinhas, $totalColunas, 
                        $erros, $errosDetalhados);

    // Esse bloco ordena e grava os erros no log
    ordenarGravarErrosLog($errosDetalhados, $tabela);   // Chamar a função para ordenar e gravar erros no log
    // Cria logs com as informações sobre a execução (a sequencia de impressão está invertida no log)
    $mensagemErros = "Total de linhas que apresentaram erro: $erros";
    criaLogs($tabela, $mensagemErros); // Chama a função de log
    $mensagemFinal = "Total de linhas inseridas: $totalLinhas, Total de colunas: $totalColunas";
    criaLogs($tabela, $mensagemFinal); // Chama a função de log
    criaLogs($tabela, "Os dados da tabela $tabela foram substituídos com sucesso!");
    if ($erros === 0) {
        $mensagemSucesso = "Todas as informações carregadas com sucesso!";
        criaLogs($tabela, $mensagemSucesso); // Chama a função de log
    }   //  Fim do IF de verificação de erros

    // Exibe a mensagem resumida no navegador
    exibirMensagemResumida($tabela, $totalLinhas, $totalColunas, $erros);
}

// Verifica se o arquivo foi enviado via GET
if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);
    $tabela = 'portal_acesso'; // Informar a tabela que será trabalhada
    processarAcessoPortal($file, $conn, $tabela, function ($cellIterator, $indice, &$erros, $tabela, &$errosDetalhados) {
        // Processar linha específica para portal_acesso
        // Obtendo os valores de cada célula
        $codigo = (int)$cellIterator->current()->getValue();
        $cellIterator->next();
        $portal = $cellIterator->current()->getValue();
        $cellIterator->next();
        $mes_acesso = (int)$cellIterator->current()->getValue();
        $cellIterator->next();
        $ano_acesso = (int)$cellIterator->current()->getValue();
        $cellIterator->next();
        $numero_acessos = (int)$cellIterator->current()->getValue();

        return [
            'codigo' => $codigo,
            'portal' => $portal,
            'mes_acesso' => $mes_acesso,
            'ano_acesso' => $ano_acesso,
            'numero_acessos' => $numero_acessos,
            'data' => date('Y-m-d H:i:s')
        ];
    });
}

$conn->close();
?>