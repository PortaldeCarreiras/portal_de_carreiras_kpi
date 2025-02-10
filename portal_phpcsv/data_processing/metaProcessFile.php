<?php
// PLANILHA UPLOADED, INFORMAÇÕES DE METADADOS DO PLANILHA ORIGINAL
include_once(__DIR__ . '/../logs/criaLogs.php'); // Inclui a função de log
include_once(__DIR__ . '/../dbSql/truncarTabelaSql.php'); // Inclui a função de truncar tabela
include_once(__DIR__ . '/../dbSql/inserirDados.php'); // Inclui a função de inserção de dados
include_once(__DIR__ . '/../data_processing/utils.php'); // Inclui as funções comuns
include_once('metaDataPipeline.php');

function metaProcessFile($conn, $dateCreation){
    // Define a tabela a ser usada
    $tabela = 'planilha_upload';

    // Função para processar o arquivo e salvar os metadados no banco de dados
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['xls_file'])) {
        $file = $_FILES['xls_file'];
        $fileTmpName = $file['tmp_name'];
        $fileName = basename($file['name']);
        $fileType = $file['type'];
        $fileSize = $file['size'];
        $dateCreation = $dateCreation;
        $dateUpload = date('Y-m-d H:i:s');
        $outputFilePath = 'uploads/original/' . $fileName;  // Caminho relativo do arquivo de upload
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        if (!file_exists(__DIR__ . '/../uploads/original')) {
            mkdir(__DIR__ . '/../uploads/original', 0777, true);
        }

        $counter = 1;
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        while (file_exists($outputFilePath)) {
            if (md5_file($fileTmpName) === md5_file($outputFilePath)) {
                break; // Arquivo idêntico, pode sobrescrever
            }
            $outputFilePath = 'uploads/original/' . $baseName . " ($counter)." . $fileExtension;    // Caminho relativo do arquivo de upload
            $counter++;
        }

        copy($fileTmpName, $outputFilePath);    // Copia o arquivo para a pasta de uploads
        touch($outputFilePath, strtotime($dateCreation));    // Define a data de modificação do arquivo
        registrarLogDepuracao("Arquivo {$fileName} salvo em {$outputFilePath} com timestamp {$dateCreation}.");


        // Chama a função para truncar a tabela antes de inserir novos dados
        // Não mexer nessa função, ela serve para limpar a tabela durante o processo de desenvolvimento.
        // truncarTabela($conn, $tabela);

        if ($fileSize > 10 * 1024 * 1024) {
            return "O arquivo é muito grande.";
            registrarLogDepuracao("Arquivo {$fileName} grande demais (maior que 10MB): {$fileSize} Bytes.");
        } else {
            registrarLogDepuracao("Arquivo {$fileName} dentro do tamanho esperado (até 10MB): {$fileSize} Bytes.");
        }

        // Chama a função para inserir os metadados no banco de dados
        $fileMetaData = metaDataPipeline($fileName, $fileType, $fileSize, $dateCreation, $dateUpload, $outputFilePath);

        // Inicializa as variáveis que contarão as linhas e colunas inseridas e os erros
        $totalLinhas = 0;
        $totalColunas = 0;
        $erros = 0;
        // Array para armazenar os erros detalhados
        $errosDetalhados = [];

        // Insere os dados no banco de dados
        try{
            if (!inserirDados($conn, $tabela, $fileMetaData)) {
                $erros++;
            } else {
                $totalLinhas++;
                $totalColunas = max($totalColunas, count($fileMetaData));
            }
        } catch (Exception $e) {
            // Captura erros e registra no log
            registrarLogDepuracao("Erro ao inserir metadados do {$fileName} no DB: " . $e->getMessage());
            capturarErrosToLog(["Erro ao inserir metadados do {$fileName} no DB: " . $e->getMessage()], $tabela, 0, 0, 1, $fileName, true);
        
            return "Erro ao salvar metadados no banco de dados.";
        }
        // Captura e grava os erros no log com a variável $metaProcess
        capturarErrosToLog($errosDetalhados, $tabela, $totalLinhas, $totalColunas, $erros, $fileName, $metaProcess = true);

        // Exibir mensagem resumida no navegador
        exibirMensagemResumida($tabela, $totalLinhas, $totalColunas, $erros, $metaProcess = true);
        registrarLogDepuracao("Metadados do arquivo {$fileName} processado com sucesso.");

        return "Arquivo carregado e metadados salvos com sucesso.";
    }
    return "Nenhum arquivo foi enviado.";
}
?>
