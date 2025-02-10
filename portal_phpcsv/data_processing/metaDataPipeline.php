<?php
function metaDataPipeline($fileName, $fileType, $fileSize, $dateCreation, $dateUpload, $outputFilePath) {
    return [
        'arquivo_nome' => $fileName,
        'arquivo_tipo' => $fileType,
        'arquivo_tamanho' => $fileSize,
        'arquivo_data' => $dateCreation,
        'arquivo_data_upload' => $dateUpload,
        'arquivo_local_armazenado' => $outputFilePath
    ];
}
?>