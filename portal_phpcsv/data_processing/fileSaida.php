<?php
require_once '../vendor/autoload.php';
include('../conn.php');
include('../logs/criaLogs.php'); // Inclui a função de log
include('../dbSql/dateConverterSql.php'); // Inclui a função de conversão de data

function inserirDadosPortalSaidaEstagio($conn, $tabela, $dados) {
    $campos = implode(", ", array_keys($dados));
    $valores = "'" . implode("','", array_values($dados)) . "'";
    if (mysqli_query($conn, "INSERT INTO $tabela ($campos) VALUES ($valores)")) {
        // echo "Dados inseridos com sucesso!<br>"; // Comentado para não exibir no navegador
        return true;
    } else {
        $mensagem = "Erro na inserção: " . mysqli_error($conn);
        // echo $mensagem . "<br>"; // Comentado para não exibir no navegador
        criaLogs($tabela, $mensagem); // Chama a função de log
        return false;
    }
}   // Fim da função inserirDadosPortalSaidaEstagio

function processarPortalSaidaEstagio($file, $conn) {
    // Limpa a tabela no DB-SQL antes de inserir dados novos.
    // LEMBRAR DE CODIFICAR PARA QUE APENAS O USUÁRIO ADM POSSA EXECUTAR ESSA FUNÇÃO.
    include_once('../dbSql/truncarTabelaSql.php');
    truncarTabela($conn, 'portal_saida_estagio');

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file); // Carrega a planilha
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

            // Obtendo os valores de cada célula
            $empresa_estagio = $cellIterator->current()->getValue();
            $cellIterator->next(); // Move para a próxima célula
            $aluno_info = $cellIterator->current()->getValue();
            $cellIterator->next(); // Move para a próxima célula, que seria a coluna "C".
            $cellIterator->next(); // Move para a próxima célula, pulando a coluna "C" - Vagas de Estágio

            // Quebra a string do aluno em partes
            list($aluno_codigo, $aluno_ra, $aluno_nome, $aluno_eixo, $aluno_periodo, $aluno_categoria, $aluno_data_raw) = explode(' - ', $aluno_info);

            // Converte os valores para os tipos apropriados
            $aluno_codigo = (int)$aluno_codigo;
            $aluno_ra_unidade = substr($aluno_ra, 0, 3);
            $aluno_ra_curso = substr($aluno_ra, 3, 3);
            $aluno_ra_ano_sem = substr($aluno_ra, 6, 3);
            $aluno_ra_periodo = substr($aluno_ra, 9, 1); // Apenas 1 dígito
            $aluno_ra_siga = substr($aluno_ra, 10, 3); // Garante três dígitos como string
            $aluno_eixo = (int)$aluno_eixo;
            $aluno_data = converterDataExcelParaSQL($aluno_data_raw, $indice, 'B', $erros, 'portal_saida_estagio');

            // Captura os valores das células restantes
            $data_inicio_raw = $cellIterator->current()->getValue();
            $data_inicio = converterDataExcelParaSQL($data_inicio_raw, $indice, $cellIterator->key(), $erros, 'portal_saida_estagio');
            $cellIterator->next();
            $data_final_raw = $cellIterator->current()->getValue();
            $data_final = converterDataExcelParaSQL($data_final_raw, $indice, $cellIterator->key(), $erros, 'portal_saida_estagio');
            $cellIterator->next();
            $orientador = $cellIterator->current()->getValue();
            $cellIterator->next();
            $resp_empresa = $cellIterator->current()->getValue();
            $cellIterator->next();

            // Mostra os valores capturados no browser
            // echo "Aluno Código: $aluno_codigo<br>";
            // echo "Aluno RA: $aluno_ra_unidade $aluno_ra_curso $aluno_ra_ano_sem $aluno_ra_periodo $aluno_ra_siga<br>";
            // echo "Aluno Nome: $aluno_nome<br>";
            // echo "Aluno Nome: $aluno_nome<br>";
            // echo "Aluno Eixo: $aluno_eixo<br>";
            // echo "Aluno Período: $aluno_periodo<br>";
            // echo "Aluno Categoria: $aluno_categoria<br>";
            // echo "Aluno Data: $aluno_data<br>";


            $dados = [
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
                'data' => date('Y-m-d H:i:s') // Adicionar a data atual para a coluna 'data'
            ];

            if (!inserirDadosPortalSaidaEstagio($conn, 'portal_saida_estagio', $dados)) {
                $erros++;
            } else {
                $totalLinhas++;
                $totalColunas = max($totalColunas, count($dados));
            }
        }
    }

    $mensagemFinal = "Total de linhas inseridas: $totalLinhas, Total de colunas: $totalColunas";
    criaLogs('portal_saida_estagio', $mensagemFinal); // Chama a função de log
    // echo $mensagemFinal . "<br>"; // Comentado para não exibir no navegador

    $mensagemErros = "Total de linhas que apresentaram erro: $erros";
    criaLogs('portal_saida_estagio', $mensagemErros); // Chama a função de log
    // echo $mensagemErros . "<br>"; // Comentado para não exibir no navegador

    if ($erros === 0) {
        $mensagemSucesso = "Todas as informações carregadas com sucesso!";
        criaLogs('portal_saida_estagio', $mensagemSucesso); // Chama a função de log
        // echo $mensagemSucesso . "<br>"; // Comentado para não exibir no navegador
    }

    // Adiciona duas linhas em branco ao final do log
    criaLogs('portal_saida_estagio', "\n\n");

    // Exibe a mensagem resumida no navegador
    // echo "<script>alert('Dados da tabela portal_vagas_estagio foram apagados.\\nTotal de linhas inseridas: $totalLinhas, Total de colunas: $totalColunas\\nTotal de linhas que apresentaram erro: $erros\\n" . ($erros === 0 ? "Todas as informações carregadas com sucesso!" : "Informações carregadas com sucesso!\\nAs informações de erros podem ser vistas no arquivo de log portal_vagas_estagioLog.txt") . "'); window.location.href = '../index.php';</script>";
    // Concatenando as linhas com " . " para quebra de linha no cod PHP (senão não funciona)"
    echo "<script>
    alert('Dados da tabela portal_saida_estagio foram apagados.\\n" .
            "Total de linhas inseridas: $totalLinhas, Total de colunas: $totalColunas\\n" .
            "Total de linhas que apresentaram erro: *** $erros ***\\n" .
            ($erros === 0
                ? "Todas as informações carregadas com sucesso!\\n"
                : "Informações carregadas com sucesso!\\n" . 
                "As informações de erros podem ser vistas no arquivo de log portal_saida_estagioLog.txt") . "');
    window.location.href = '../index.php';
    </script>";
}

if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);
    processarPortalSaidaEstagio($file, $conn);
}

$conn->close();
?>