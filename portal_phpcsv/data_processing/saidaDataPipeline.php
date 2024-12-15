<?php
function saidaPlanilhaExtrairMapToDb($cellIterator, $indice, &$erros, $tabela, &$errosDetalhados){
    // Processar linha específica para portal_saida_estagio
    // Obtendo os valores de cada célula
    $empresaEstagio = $cellIterator->current()->getValue();
    $cellIterator->next();  // Move para a próxima célula
    $alunoInfo = $cellIterator->current()->getValue(); // Obtém o valor da célula atual, que contém as informações do aluno (coluna "B")
    $cellIterator->next();  // Move para a próxima célula
    $cellIterator->next(); // Move para a próxima célula, pulando a coluna "C" - Vagas de Estágio

    // Verifica se $alunoInfo está vazio ou não é uma string válida (coluna "B")
    if (empty($alunoInfo) || !is_string($alunoInfo)) {
        $mensagem = "Erro na linha $indice: Informação do aluno inválida.";
        $errosDetalhados[] = $mensagem;
        $erros++;
        return null;
    }

    // Quebra a string do aluno em partes (coluna "B"), o separador é ' - ' (espaço, hífen, espaço)
    list($alunoCodigo, $alunoRa, $alunoNome, $alunoEixo, $alunoPeriodo, $alunoCategoria, $aluno_data_raw) = explode(' - ', $alunoInfo);

    // Converte os valores para os tipos apropriados
    $alunoCodigo = (int)$alunoCodigo;
    $alunoRaUnidade = substr($alunoRa, 0, 3);    // Pega os 3 primeiros caracteres, posição e num. de caracteres
    $alunoRaCurso = substr($alunoRa, 3, 3);
    $alunoRaAnoSem = substr($alunoRa, 6, 3);
    $alunoRaPeriodo = substr($alunoRa, 9, 1);
    $alunoRaSiga = substr($alunoRa, 10, 3);
    $alunoEixo = (int)$alunoEixo;
    // Converte a data para o formato SQL
    $alunoData = converterDataExcelParaSQL($aluno_data_raw, $indice, 'B', $erros, $tabela, $errosDetalhados);

    $data_inicio_raw = $cellIterator->current()->getValue();
    $dataInicio = converterDataExcelParaSQL($data_inicio_raw, $indice, $cellIterator->key(), $erros, $tabela, $errosDetalhados);
    $cellIterator->next();
    $data_final_raw = $cellIterator->current()->getValue();
    $dataFinal = converterDataExcelParaSQL($data_final_raw, $indice, $cellIterator->key(), $erros, $tabela, $errosDetalhados);
    $cellIterator->next();
    $orientador = $cellIterator->current()->getValue();
    $cellIterator->next();
    $respEmpresa = $cellIterator->current()->getValue();
    $cellIterator->next();
    $cellIterator->next();
    $cellIterator->next();  // Move para a célula, pulando 2 colunas, indo para "J"
    $data_arquivo_raw = $cellIterator->current()->getValue();
    $dataArquivo = converterDataExcelParaSQL($data_arquivo_raw, $indice, $cellIterator->key(), $erros, $tabela, $errosDetalhados);

    // Mostra os valores capturados no browser (usados para debug)
    // echo "Aluno Código: $alunoCodigo<br>";
    // echo "Aluno RA: $alunoRa_unidade $alunoRa_curso $alunoRa_ano_sem $alunoRa_periodo $alunoRa_siga<br>";
    // echo "Aluno Nome: $alunoNome<br>";
    // echo "Aluno Eixo: $aluno_eixo<br>";
    // echo "Aluno Período: $aluno_periodo<br>";
    // echo "Aluno Categoria: $aluno_categoria<br>";
    // echo "Aluno Data: $aluno_data<br>";

    return [
        'empresa_estagio' => $empresaEstagio,
        'aluno_codigo' => $alunoCodigo,
        'aluno_ra_unidade' => $alunoRaUnidade,
        'aluno_ra_curso' => $alunoRaCurso,
        'aluno_ra_ano_sem' => $alunoRaAnoSem,
        'aluno_ra_periodo' => $alunoRaPeriodo,
        'aluno_ra_siga' => $alunoRaSiga,
        'aluno_nome' => $alunoNome,
        'aluno_eixo' => $alunoEixo,
        'aluno_periodo' => $alunoPeriodo,
        'aluno_categoria' => $alunoCategoria,
        'aluno_data' => $alunoData,
        'data_inicio' => $dataInicio,
        'data_final' => $dataFinal,
        'orientador' => $orientador,
        'resp_empresa' => $respEmpresa,
        'data_arquivo' => $dataArquivo // Adiciona a data do arquivo
    ];  // Fim do retorno de dados
}
