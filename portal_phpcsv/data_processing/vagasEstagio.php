<?php
require_once '../vendor/autoload.php';
include('../conn.php');
include('../logs/criaLogs.php'); // Inclui a função de log
include('../dbSql/dateConverterSql.php'); // Inclui a função de conversão de data
include('../dbSql/inserirDados.php'); // Inclui a função genérica de inserção de dados
include('../data_processing/utils.php'); // Inclui as funções comuns
include_once('../dbSql/truncarTabelaSql.php');

function processarVagasEstagio($file, $conn, $tabela)
{
    // Limpa a tabela no DB-SQL antes de inserir dados novos.
    // LEMBRAR DE CODIFICAR PARA QUE APENAS O USUÁRIO ADM POSSA EXECUTAR ESSA FUNÇÃO.
    truncarTabela($conn, $tabela);

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();

    // Criar Logs. Variáveis para contagem de linhas, colunas e erros
    $totalLinhas = 0;
    $totalColunas = 0;
    $erros = 0;

    foreach ($worksheet->getRowIterator() as $indice => $row) { // Loop para percorrer as linhas
        if ($indice > 1) { // não pegar o cabeçalho 
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Inclui células vazias

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
            // Chama a função de conversão de data
            $data_abertura = converterDataExcelParaSQL($data_abertura_raw, $indice, $cellIterator->key(), $erros, 'portal_vagas_estagio');
            $cellIterator->next();
            $data_final_candidatar_raw = $cellIterator->current()->getValue();
            // Chama a função de conversão de data
            $data_final_candidatar = converterDataExcelParaSQL($data_final_candidatar_raw, $indice, $cellIterator->key(), $erros, 'portal_vagas_estagio');
            $cellIterator->next();
            $data_previsao_contratacao_raw = $cellIterator->current()->getValue();
            // Chama a função de conversão de data
            $data_previsao_contratacao = converterDataExcelParaSQL($data_previsao_contratacao_raw, $indice, $cellIterator->key(), $erros, 'portal_vagas_estagio');
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
            // Chama a função de conversão de data
            $data_alteracao = converterDataExcelParaSQL($data_alteracao_raw, $indice, 'AU', $erros, 'portal_vagas_estagio');
            $revisao = $worksheet->getCell('AV' . $indice)->getValue();

            $dados = [
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
    $tabela = 'portal_vagas_estagio'; // Informar a tabela que será trabalhada
    processarVagasEstagio($file, $conn, $tabela);
}

$conn->close();
