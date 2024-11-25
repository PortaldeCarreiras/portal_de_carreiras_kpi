<?php
require_once 'metaDataPipeline.php';
require_once __DIR__ . '/../dbSql/truncarTabelaSql.php'; // Corrigir o caminho para o arquivo correto

function metaProcessFile($conn, $tabela) {
    // Limpa a tabela antes de inserir novos dados
    // LEMBRAR DE CODIFICAR PARA QUE APENAS O USUÁRIO ADM POSSA EXECUTAR ESSA FUNÇÃO.
    truncarTabela($conn, $tabela);

    // Função para processar o arquivo e salvar os metadados no banco de dados
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['xls_file'])) {
        $file = $_FILES['xls_file'];
        $dataModificacao = $_POST['dataModificacao'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $dataUpload = date('Y-m-d H:i:s');
        $fileSize = $file['size'];
        $fileType = $file['type'];
        $outputFilePath = __DIR__ . '/../uploads/original/' . $fileName;

        if (!file_exists(__DIR__ . '/../uploads/original')) {
            mkdir(__DIR__ . '/../uploads/original', 0777, true);
        }

        $counter = 1;
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        while (file_exists($outputFilePath)) {
            if (md5_file($fileTmpName) === md5_file($outputFilePath)) {
                break; // Arquivo idêntico, pode sobrescrever
            }
            $outputFilePath = __DIR__ . '/../uploads/original/' . $baseName . " ($counter)." . $fileExtension;
            $counter++;
        }

        copy($fileTmpName, $outputFilePath);
        touch($outputFilePath, strtotime($dataModificacao));

        $fileMetaData = metaDataPipeline($fileName, $fileType, $fileSize, $dataModificacao, $dataUpload, $outputFilePath);

        $stmt = $conn->prepare("INSERT INTO planilha_upload (arquivo_nome, arquivo_tipo, arquivo_tamanho, arquivo_data, arquivo_data_upload, arquivo_local_armazenado) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisss", $fileMetaData['arquivo_nome'], $fileMetaData['arquivo_tipo'], $fileMetaData['arquivo_tamanho'], $fileMetaData['arquivo_data'], $fileMetaData['arquivo_data_upload'], $fileMetaData['arquivo_local_armazenado']);
        $stmt->execute();
        $stmt->close();

        return "Arquivo carregado e metadados salvos com sucesso.";
    }
    return "Nenhum arquivo foi enviado.";
}
?>
