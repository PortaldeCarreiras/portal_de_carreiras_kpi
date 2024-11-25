<?php
include_once(__DIR__ . '/../logs/criaLogs.php'); // Inclui a função de log
include_once(__DIR__ . '/../dbSql/truncarTabelaSql.php'); // Inclui a função de truncar tabela
include_once(__DIR__ . '/../dbSql/inserirDados.php'); // Inclui a função de inserção de dados
include_once(__DIR__ . '/../data_processing/utils.php'); // Inclui as funções comuns
include_once('metaDataPipeline.php');

function metaProcessFile($conn)
{
    // Define a tabela a ser usada
    $tabela = 'planilha_upload';

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

        // Chama a função para truncar a tabela antes de inserir novos dados
        // Não mexer nessa função, ela serve para limpar a tabela durante o processo de desenvolvimento.
        // truncarTabela($conn, $tabela);

        $fileMetaData = metaDataPipeline($fileName, $fileType, $fileSize, $dataModificacao, $dataUpload, $outputFilePath);

        // Inicializa as variáveis que contarão as linhas e colunas inseridas e os erros
        $totalLinhas = 0;
        $totalColunas = 0;
        $erros = 0;
        // Array para armazenar os erros detalhados
        $errosDetalhados = [];

        // Insere os dados no banco de dados
        if (!inserirDados($conn, $tabela, $fileMetaData)) {
            $erros++;
        } else {
            $totalLinhas++;
            $totalColunas = max($totalColunas, count($fileMetaData));
        }

        // Captura e grava os erros no log com a variável $metaProcess
        capturarErrosToLog($errosDetalhados, $tabela, $totalLinhas, $totalColunas, $erros, $fileName, $metaProcess = true);

        // Exibir mensagem resumida no navegador
        exibirMensagemResumida($tabela, $totalLinhas, $totalColunas, $erros, $metaProcess = true);

        return "Arquivo carregado e metadados salvos com sucesso.";
    }
    return "Nenhum arquivo foi enviado.";
}
?>
