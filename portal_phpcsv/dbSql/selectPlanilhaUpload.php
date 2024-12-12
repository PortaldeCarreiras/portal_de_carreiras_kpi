<?php
function selectPlanilhaUpload($conn, $nomeArquivoSelDrop){
    // Consulta SQL com preparação para evitar SQL injection
    $stmt = $conn->prepare("
        SELECT id, arquivo_nome, arquivo_tamanho, arquivo_data, arquivo_data_upload, arquivo_local_armazenado
        FROM planilha_upload 
        WHERE arquivo_nome LIKE CONCAT(?, '%') 
        ORDER BY id DESC 
        LIMIT 1
    ");
    $stmt->bind_param("s", $nomeArquivoSelDrop);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Obter o resultado
        $row = $result->fetch_assoc();
        return [
            'id' => $row['id'],   // Recupera o ID
            'arquivoNome' => $row['arquivo_nome'],   // Recupera o nome do arquivo
            'arquivoTamanho' => $row['arquivo_tamanho'],   // Recupera o tamanho do arquivo
            'valorInput' => "ID: {$row['id']}   Tamanho: {$row['arquivo_tamanho']}", // Formata o valor para exibição no input
            'arquivoData' => (new DateTime($row['arquivo_data']))->format('d-M-Y H:i:s'),   // Recupera a data do arquivo
            'arquivoDataUpload' => (new DateTime($row['arquivo_data_upload']))->format('d-M-Y H:i:s'),   // Recupera a data de upload do arquivo
            'arquivoLocalArmazenado' => $row['arquivo_local_armazenado'],   // Recupera o local armazenado do arquivo
        ];
    } else {
        return null; // Nenhum dado encontrado
    }
}