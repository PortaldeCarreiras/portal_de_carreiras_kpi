<?php
require_once '../vendor/autoload.php';
include('../conn.php');
include('../logs/criaLogs.php'); // Inclui a função de log
include('../dbSql/inserirDados.php'); // Inclui a função genérica de inserção de dados
include('../dbSql/dateConverterSql.php'); // Inclui a função de conversão de data
include('../data_processing/utils.php'); // Inclui as funções comuns
include_once('../dbSql/truncarTabelaSql.php');
include_once('../logs/ordenarGravarErrosLog.php');

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
    $tabela = 'portal_vagas_estagio'; // Informar a tabela que será trabalhada
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();
    processarVagasEstagio($file, $conn, $tabela, function ($cellIterator, $indice, &$erros, $tabela, &$errosDetalhados) use ($worksheet) {
        // Processar linha específica para portal_vagas_estagio
        // Obtendo os valores de cada célula
        $empresa = $cellIterator->current()->getValue();
        $cellIterator->next(); // Move para a próxima célula
        $item = (int)$cellIterator->current()->getValue();
        $cellIterator->next();
        $codigo = (int)$cellIterator->current()->getValue();
        $cellIterator->next();
        $nome_vaga = $cellIterator->current()->getValue();
        $cellIterator->next();
        $data_abertura_raw = $cellIterator->current()->getValue();
        $data_abertura = converterDataExcelParaSQL($data_abertura_raw, $indice, $cellIterator->key(), $erros, $tabela, $errosDetalhados);
        $cellIterator->next();
        $data_final_candidatar_raw = $cellIterator->current()->getValue();
        $data_final_candidatar = converterDataExcelParaSQL($data_final_candidatar_raw, $indice, $cellIterator->key(), $erros, $tabela, $errosDetalhados);
        $cellIterator->next();
        $data_previsao_contratacao_raw = $cellIterator->current()->getValue();
        $data_previsao_contratacao = converterDataExcelParaSQL($data_previsao_contratacao_raw, $indice, $cellIterator->key(), $erros, $tabela, $errosDetalhados);
        $cellIterator->next();
        $eixo_formacao = (int)$cellIterator->current()->getValue();
        $cellIterator->next();
        $confidencial = $cellIterator->current()->getValue();
        $cellIterator->next();
        $responsavel = $cellIterator->current()->getValue();
        $cellIterator->next();
        $responsavel_email = $cellIterator->current()->getValue();
        $cellIterator->next();
        $responsavel_telefone = $cellIterator->current()->getValue();
        $cellIterator->next();
        $data_alteracao_raw = $worksheet->getCell('AU' . $indice)->getValue();
        $data_alteracao = converterDataExcelParaSQL($data_alteracao_raw, $indice, 'AU', $erros, $tabela, $errosDetalhados);
        $revisao = $worksheet->getCell('AV' . $indice)->getValue();

        return [
            'empresa' => $empresa,
            'item' => $item,
            'codigo' => $codigo,
            'nome_vaga' => $nome_vaga,
            'data_abertura' => $data_abertura,
            'data_final_candidatar' => $data_final_candidatar,
            'data_previsao_contratacao' => $data_previsao_contratacao,
            'eixo_formacao' => $eixo_formacao,
            'confidencial' => $confidencial,
            'responsavel' => $responsavel,
            'responsavel_email' => $responsavel_email,
            'responsavel_telefone' => $responsavel_telefone,
            'data_alteracao' => $data_alteracao,
            'revisao' => $revisao,
            'data' => date('Y-m-d H:i:s') // Adicionar a data atual para a coluna 'data'
        ];
    });
}

$conn->close();
?>
