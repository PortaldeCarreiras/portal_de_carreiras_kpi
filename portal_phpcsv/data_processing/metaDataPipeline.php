<?php
function metaDataPipeline($nomeArquivo, $tipoMime, $tamanho, $data_criacao, $dataUpload, $localArmazenado) {
    return [
        'arquivo_nome' => $nomeArquivo,
        'arquivo_tipo' => $tipoMime,
        'arquivo_tamanho' => $tamanho,
        'arquivo_data' => $data_criacao,
        'arquivo_data_upload' => $dataUpload,
        'arquivo_local_armazenado' => $localArmazenado
    ];
}
?>