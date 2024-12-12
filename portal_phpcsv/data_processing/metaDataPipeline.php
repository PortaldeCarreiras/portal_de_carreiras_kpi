<?php
function metaDataPipeline($nomeArquivo, $tipoMime, $tamanho, $dataCriacao, $dataUpload, $localArmazenado) {
    return [
        'arquivo_nome' => $nomeArquivo,
        'arquivo_tipo' => $tipoMime,
        'arquivo_tamanho' => $tamanho,
        'arquivo_data' => $dataCriacao,
        'arquivo_data_upload' => $dataUpload,
        'arquivo_local_armazenado' => $localArmazenado
    ];
}
?>