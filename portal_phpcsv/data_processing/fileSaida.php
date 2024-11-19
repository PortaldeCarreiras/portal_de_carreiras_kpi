<?php
require_once '../vendor/autoload.php';
include('../conn.php');
include('../logs/criaLogs.php'); // Inclui a função de log
include('../dbSql/dateConverterSql.php'); // Inclui a função de conversão de data
include('../dbSql/inserirDados.php'); // Inclui a função genérica de inserção de dados
include('../data_processing/utils.php'); // Inclui as funções comuns
include_once('../dbSql/truncarTabelaSql.php');

function processarArquivoFileSaida($file, $conn, $tabela, $processarLinha)
{
    // Limpa a tabela no DB-SQL antes de inserir dados novos.
    // LEMBRAR DE CODIFICAR PARA QUE APENAS O USUÁRIO ADM POSSA EXECUTAR ESSA FUNÇÃO.
    truncarTabela($conn, $tabela);

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();    // Pega a aba ativa

    // Inicializa as variáveis que contarão as linhas e colunas inseridas e os erros
    $totalLinhas = 0;
    $totalColunas = 0;
    $erros = 0;

    // Itera sobre todas as linhas da planilha
    foreach ($worksheet->getRowIterator() as $indice => $row) {
        if ($indice > 1) { // não pegar o cabeçalho 
            $cellIterator = $row->getCellIterator();    // Itera sobre todas as células da linha
            $cellIterator->setIterateOnlyExistingCells(false); // Inclui células vazias

            // Inicializa a variável $dados para processar cada linha e obter os valores de cada célula.
            $dados = $processarLinha($cellIterator, $indice, $erros, $tabela);

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
}   //  Fim da função processarArquivoFileSaida

if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);
    $tabela = 'portal_saida_estagio'; // Informar a tabela que será trabalhada
    processarArquivoFileSaida($file, $conn, $tabela, function ($cellIterator, $indice, &$erros, $tabela) {
        // Processar linha específica para portal_saida_estagio
        // Obtendo os valores de cada célula
        $empresa_estagio = $cellIterator->current()->getValue();
        $cellIterator->next();  // Move para a próxima célula
        $aluno_info = $cellIterator->current()->getValue();
        $cellIterator->next();  // Move para a próxima célula
        $cellIterator->next(); // Move para a próxima célula, pulando a coluna "C" - Vagas de Estágio

        // Quebra a string do aluno em partes (coluna "B")
        list($aluno_codigo, $aluno_ra, $aluno_nome, $aluno_eixo, $aluno_periodo, $aluno_categoria, $aluno_data_raw) = explode(' - ', $aluno_info);

        // Converte os valores para os tipos apropriados
        $aluno_codigo = (int)$aluno_codigo;
        $aluno_ra_unidade = substr($aluno_ra, 0, 3);    // Pega os 3 primeiros caracteres, posição e num. de caracteres
        $aluno_ra_curso = substr($aluno_ra, 3, 3);
        $aluno_ra_ano_sem = substr($aluno_ra, 6, 3);
        $aluno_ra_periodo = substr($aluno_ra, 9, 1);
        $aluno_ra_siga = substr($aluno_ra, 10, 3);
        $aluno_eixo = (int)$aluno_eixo;
        // Converte a data para o formato SQL
        $aluno_data = converterDataExcelParaSQL($aluno_data_raw, $indice, 'B', $erros, $tabela);

        $data_inicio_raw = $cellIterator->current()->getValue();
        $data_inicio = converterDataExcelParaSQL($data_inicio_raw, $indice, $cellIterator->key(), $erros, $tabela);
        $cellIterator->next();
        $data_final_raw = $cellIterator->current()->getValue();
        $data_final = converterDataExcelParaSQL($data_final_raw, $indice, $cellIterator->key(), $erros, $tabela);
        $cellIterator->next();
        $orientador = $cellIterator->current()->getValue();
        $cellIterator->next();
        $resp_empresa = $cellIterator->current()->getValue();
        $cellIterator->next();

        // Mostra os valores capturados no browser (usados para debug)
        // echo "Aluno Código: $aluno_codigo<br>";
        // echo "Aluno RA: $aluno_ra_unidade $aluno_ra_curso $aluno_ra_ano_sem $aluno_ra_periodo $aluno_ra_siga<br>";
        // echo "Aluno Nome: $aluno_nome<br>";
        // echo "Aluno Nome: $aluno_nome<br>";
        // echo "Aluno Eixo: $aluno_eixo<br>";
        // echo "Aluno Período: $aluno_periodo<br>";
        // echo "Aluno Categoria: $aluno_categoria<br>";
        // echo "Aluno Data: $aluno_data<br>";

        return [
            'empresa_estagio' => $empresa_estagio,
            'aluno_codigo' => $aluno_codigo,
            'aluno_ra_unidade' => $aluno_ra_unidade,
            'aluno_ra_curso' => $aluno_ra_curso,
            'aluno_ra_ano_sem' => $aluno_ra_ano_sem,
            'aluno_ra_periodo' => $aluno_ra_periodo,
            'aluno_ra_siga' => $aluno_ra_siga,
            'aluno_nome' => $aluno_nome,
            'aluno_eixo' => $aluno_eixo,
            'aluno_periodo' => $aluno_periodo,
            'aluno_categoria' => $aluno_categoria,
            'aluno_data' => $aluno_data,
            'data_inicio' => $data_inicio,
            'data_final' => $data_final,
            'orientador' => $orientador,
            'resp_empresa' => $resp_empresa,
            'data' => date('Y-m-d H:i:s')
        ];
    });
}

$conn->close();
