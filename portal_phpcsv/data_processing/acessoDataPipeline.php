<?php
function acessoPlanilhaExtrairMapToDb($cellIterator, $indice, &$erros, $tabela, &$errosDetalhados){

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
}
