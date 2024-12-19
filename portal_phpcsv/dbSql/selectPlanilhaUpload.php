<?php
include_once(__DIR__ . '/../data_processing/utils.php');

function selectPlanilhaUpload($conn, $nomeArquivoSelDrop){
    registrarLogDepuracao("Iniciando busca DB (planilha_upload).");
    // Verificar conexão com o banco de dados
    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    // Verificar valor de $nomeArquivoSelDrop
    var_dump($nomeArquivoSelDrop);

    // Consulta SQL com preparação para evitar SQL injection e escape de caracteres especiais (', \ e ;) no nome do arquivo.
    $stmt = $conn->prepare("
        SELECT id, arquivo_nome, arquivo_tamanho, arquivo_data, arquivo_data_upload, arquivo_local_armazenado
        FROM planilha_upload 
        WHERE arquivo_nome LIKE CONCAT(?, '%') 
        ORDER BY id DESC 
        LIMIT 1
    ");

    if (!$stmt) {
        die("Erro na preparação da declaração: " . $conn->error);
    }

    $cincoPrimeirasLetras = substr($nomeArquivoSelDrop, 0, 5);
    $stmt->bind_param("s", $cincoPrimeirasLetras);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Obter o resultado
        $row = $result->fetch_assoc();
        registrarLogDepuracao("Busca DB finalizada com sucesso (planilha_upload).");
        return [
            'id' => $row['id'],   // Recupera o ID
            'fileName' => $row['arquivo_nome'],   // Recupera o nome do arquivo
            'fileSize' => $row['arquivo_tamanho'],   // Recupera o tamanho do arquivo
            'valorInput' => "ID: {$row['id']}   Tamanho: {$row['arquivo_tamanho']}", // Formata o valor para exibição no input
            'dateCreation' => (new DateTime($row['arquivo_data']))->format('d-M-Y H:i:s'),   // Recupera a data do arquivo
            'dateUpload' => (new DateTime($row['arquivo_data_upload']))->format('d-M-Y H:i:s'),   // Recupera a data de upload do arquivo
            'outputFilePath' => $row['arquivo_local_armazenado'],   // Recupera o local armazenado do arquivo
        ];
    } else {
        return null; // Nenhum dado encontrado
        registrarLogDepuracao("Busca DB sem Retorno (planilha_upload).");
    }
}
