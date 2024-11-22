<?php
function saidaPlanilhaExtrairMapToDb($cellIterator, $indice, &$erros, $tabela, &$errosDetalhados){
    // Processar linha específica para portal_saida_estagio
    // Obtendo os valores de cada célula
    $empresa_estagio = $cellIterator->current()->getValue();
    $cellIterator->next();  // Move para a próxima célula
    $aluno_info = $cellIterator->current()->getValue();
    $cellIterator->next();  // Move para a próxima célula
    $cellIterator->next(); // Move para a próxima célula, pulando a coluna "C" - Vagas de Estágio

    // Verifica se $aluno_info está vazio ou não é uma string válida
    if (empty($aluno_info) || !is_string($aluno_info)) {
        $mensagem = "Erro na linha $indice: Informação do aluno inválida.";
        $errosDetalhados[] = $mensagem;
        $erros++;
        return null;
    }

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
    $aluno_data = converterDataExcelParaSQL($aluno_data_raw, $indice, 'B', $erros, $tabela, $errosDetalhados);

    $data_inicio_raw = $cellIterator->current()->getValue();
    $data_inicio = converterDataExcelParaSQL($data_inicio_raw, $indice, $cellIterator->key(), $erros, $tabela, $errosDetalhados);
    $cellIterator->next();
    $data_final_raw = $cellIterator->current()->getValue();
    $data_final = converterDataExcelParaSQL($data_final_raw, $indice, $cellIterator->key(), $erros, $tabela, $errosDetalhados);
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
    ];  // Fim do retorno de dados
}
