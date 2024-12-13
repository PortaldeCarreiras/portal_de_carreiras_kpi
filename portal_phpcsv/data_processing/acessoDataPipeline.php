<?php
function acessoPlanilhaExtrairMapToDb($cellIterator, $indice, &$erros, $tabela, &$errosDetalhados) {
    // Processar linha específica para portal_acesso
    // Obtendo os valores de cada célula
    $codigo = (int)$cellIterator->current()->getValue();
    $cellIterator->next();
    $portal = $cellIterator->current()->getValue();
    $cellIterator->next();
    $mesAcesso = (int)$cellIterator->current()->getValue();
    $cellIterator->next();
    $anoAcesso = (int)$cellIterator->current()->getValue();
    $cellIterator->next();
    $numeroAcessos = (int)$cellIterator->current()->getValue();
    $cellIterator->next();
    $data_arquivo_raw = $cellIterator->current()->getValue();
    $dataArquivo = converterDataExcelParaSQL($data_arquivo_raw, $indice, $cellIterator->key(), $erros, $tabela, $errosDetalhados);

    return [
        'codigo' => $codigo,
        'portal' => $portal,
        'mes_acesso' => $mesAcesso,
        'ano_acesso' => $anoAcesso,
        'numero_acessos' => $numeroAcessos,
        'data_arquivo' => $dataArquivo // Adiciona a data do arquivo
    ];
}
