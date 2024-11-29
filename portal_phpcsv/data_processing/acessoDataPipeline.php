<?php
function acessoPlanilhaExtrairMapToDb($cellIterator, $indice, &$erros, $tabela, &$errosDetalhados, $worksheet){
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
    $dataArquivo_raw = $worksheet->getCell('F' . $indice)->getValue();
    $dataArquivo = converterDataExcelParaSQL($dataArquivo_raw, $indice, 'F', $erros, $tabela, $errosDetalhados);

    return [
        'codigo' => $codigo,
        'portal' => $portal,
        'mes_acesso' => $mes_acesso,
        'ano_acesso' => $ano_acesso,
        'numero_acessos' => $numero_acessos,
        'data_arquivo' => $dataArquivo
    ];
}
?>
