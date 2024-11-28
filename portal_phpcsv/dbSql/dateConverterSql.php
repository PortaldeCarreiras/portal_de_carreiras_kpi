<?php
// Função para converter datas do Excel para o formato SQL
function converterDataExcelParaSQL($data_raw, $indice, $coluna, &$erros, $tabela, &$errosDetalhados){
    if (empty($data_raw)) { // Verifica se a célula está vazia
        $mensagem = "Erro na linha $indice, coluna $coluna: Célula vazia, sem informação.";
        $errosDetalhados[] = $mensagem;
        // criaLogs($tabela, $mensagem); // Chama a função de log
        $data_sql = null;
        $erros++;
    } elseif (is_numeric($data_raw)) { // Verifica se a data está em formato numérico Excel
        $data_sql = date('Y-m-d H:i:s', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($data_raw));
    } elseif (DateTime::createFromFormat('Y-m-d H:i:s', $data_raw) !== false) { // Verifica formato com tempo
        $data_sql = $data_raw;
    } elseif (DateTime::createFromFormat('Y-m-d', $data_raw) !== false) { // Verifica formato sem tempo
        $data_sql = $data_raw . ' 00:00:00'; // Adiciona tempo padrão
    } elseif (DateTime::createFromFormat('d/m/Y H:i:s', $data_raw) !== false) { // Formato dd/mm/yyyy hh:mm:ss
        $data_obj = DateTime::createFromFormat('d/m/Y H:i:s', $data_raw);
        $data_sql = $data_obj->format('Y-m-d H:i:s');
    } elseif (DateTime::createFromFormat('d/m/Y', $data_raw) !== false) { // Formato dd/mm/yyyy
        $data_obj = DateTime::createFromFormat('d/m/Y', $data_raw);
        $data_sql = $data_obj->format('Y-m-d') . ' 00:00:00'; // Adiciona tempo padrão
    } else {
        $mensagem = "Erro na linha $indice, coluna $coluna: Formato de data incorreto ou falha na conversão. Conteúdo da célula: " . $data_raw;
        // echo $mensagem . "<br>";
        $errosDetalhados[] = $mensagem;
        // criaLogs($tabela, $mensagem); // Chama a função de log, agora está sendo armazenado no $errosDetalhados
        $data_sql = null;
        $erros++;
    }
    return $data_sql;
}
