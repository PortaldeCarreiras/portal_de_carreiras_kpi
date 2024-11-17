<?php
// Função para converter datas do Excel para o formato SQL
function converterDataExcelParaSQL($data_raw, $indice, $coluna, &$erros, $tabela) {
    if (empty($data_raw)) {    // Verifica se a célula está vazia
        $mensagem = "Erro na linha $indice, coluna $coluna: Célula vazia, sem informação.";
        // echo $mensagem . "<br>";
        criaLogs($tabela, $mensagem); // Chama a função de log
        $data_sql = null;
        $erros++;
    } elseif (is_numeric($data_raw)) {   // Verifica se a data está em formato numérico excel
        $data_sql = date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($data_raw));
    } elseif (DateTime::createFromFormat('Y-m-d', $data_raw) !== false) { // Verifica se a data está no 
                            // formato yyyy-mm-dd
        $data_sql = $data_raw;
    } else {
        $data_obj = DateTime::createFromFormat('d/m/Y', $data_raw);   // Converte a data para o formato SQL
        if ($data_obj) {
            $data_sql = $data_obj->format('Y-m-d');
        } else {
            $mensagem = "Erro na linha $indice, coluna $coluna: Formato de data incorreto ou falha na 
                    sconversão. Conteúdo da célula: " . $data_raw;
            // echo $mensagem . "<br>";
            criaLogs($tabela, $mensagem); // Chama a função de log
            $data_sql = null;
            $erros++;
        }
    }
    return $data_sql;
}
?>
