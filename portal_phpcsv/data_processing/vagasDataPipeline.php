<?php
function vagasPlanilhaExtrairMapToDb($cellIterator, $indice, &$erros, $tabela, &$errosDetalhados, $worksheet)
{
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
}
