<?php
require_once 'vendor/autoload.php';
include('conn.php');

// Função Truncar Tabela, para deletar e começar do "id01"
// LEMBRAR DE CODIFICAR PARA QUE APENAS O USUÁRIO ADM POSSA EXECUTAR ESSA FUNÇÃO.
function truncarTabela($conn, $tabela)
{
    $sqlTruncate = "TRUNCATE TABLE $tabela";
    if (mysqli_query($conn, $sqlTruncate)) {
        echo "Dados da tabela $tabela foram apagados.<br>";
    } else {
        echo "Erro ao apagar dados da tabela $tabela: " . mysqli_error($conn) . "<br>";
    }
}


function inserirDadosPortalVagas($conn, $tabela, $dados) {
    $campos = implode(", ", array_keys($dados));
    $valores = "'" . implode("','", array_values($dados)) . "'";
    if (mysqli_query($conn, "INSERT INTO $tabela ($campos) VALUES ($valores)")) {
        echo "Dados inseridos com sucesso!<br>";
    } else {
        echo "Erro na inserção: " . mysqli_error($conn) . "<br>";
    }
}

function processarVagasEstagio($file, $conn) {

    // Limpa a tabela no DB-SQL antes de inserir dados novos.
    // LEMBRAR DE CODIFICAR PARA QUE APENAS O USUÁRIO ADM POSSA EXECUTAR ESSA FUNÇÃO.
    truncarTabela($conn, 'portal_vagas_estagio');

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();

    foreach ($worksheet->getRowIterator() as $indice => $row) {
        if ($indice > 1) { // não pegar o cabeçalho 
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Inclui células vazias

            // Obtendo os valores de cada célula
            $empresa = $cellIterator->current()->getValue();
            $cellIterator->next(); // Move para a próxima célula
            $item = (int)$cellIterator->current()->getValue();
            $cellIterator->next();
            $codigo = (int)$cellIterator->current()->getValue();
            $cellIterator->next();
            $nome_vaga = $cellIterator->current()->getValue();
            $cellIterator->next();

            // As próximas 3 colunas serão carregadas com os dados da planilha, mas é necessário
            // formatar a data segundo o padrão do DB (y-m-d)
            $data_abertura_raw = $cellIterator->current()->getValue();
            $data_abertura = formatarData($data_abertura_raw, $indice, $cellIterator);
            $cellIterator->next(); // Move para a próxima célula

            $data_final_candidatar_raw = $cellIterator->current()->getValue();
            $data_final_candidatar = formatarData($data_final_candidatar_raw, $indice, $cellIterator);
            $cellIterator->next(); // Move para a próxima célula

            $data_previsao_contratacao_raw = $cellIterator->current()->getValue();
            $data_previsao_contratacao = formatarData($data_previsao_contratacao_raw, $indice, $cellIterator);
            $cellIterator->next(); // Move para a próxima célula

            $eixo_formacao = (int)$cellIterator->current()->getValue();
            $cellIterator->next();
            $confidencial = $cellIterator->current()->getValue();
            $cellIterator->next();
            $responsavel = $cellIterator->current()->getValue();
            $cellIterator->next();
            $responsavel_email = $cellIterator->current()->getValue();
            $cellIterator->next();
            $responsavel_telefone = $cellIterator->current()->getValue();
            $cellIterator->next();

            // Obtendo os valores diretamente das células especificadas            
            $data_alteracao_raw = $worksheet->getCell('AU' . $indice)->getValue(); // Coluna "AU" da planilha excel.
            $data_alteracao = formatarData($data_alteracao_raw, $indice, $cellIterator);

            $revisao = $worksheet->getCell('AV' . $indice)->getValue(); // Coluna "AV" da planilha excel.

            // Construindo o array de dados
            $dados = [
                'empresa' => $empresa,
                'item' => $item,
                'codigo' => $codigo,
                'nome_vaga' => $nome_vaga,
                'data_abertura' => $data_abertura,
                'data_final_candidatar' => $data_final_candidatar,
                'data_previsao_contratacao' => $data_previsao_contratacao,
                'eixo_formacao' => $eixo_formacao,
                'confidencial' => $confidencial,
                'responsavel' => $responsavel,
                'responsavel_email' => $responsavel_email,
                'responsavel_telefone' => $responsavel_telefone,
                'data_alteracao' => $data_alteracao,
                'revisao' => $revisao,
                // Adicionar a data atual para a coluna 'data'
                'data' => date('Y-m-d H:i:s') // pode carregar data direto do mysql melhora performance
            ];

            // Inserindo os dados no DB tabela portal_acesso
            inserirDadosPortalVagas($conn, 'portal_vagas_estagio', $dados);
        }
    }
}

function formatarData($data_raw, $indice, $cellIterator) {
    if (is_numeric($data_raw)) {
        // Converte o número serial do Excel em uma data no formato MySQL (yyyy-mm-dd)
        return date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($data_raw));
    } else {
        // Se o valor não for um número, tenta interpretá-lo como data no formato dd/mm/yyyy
        $data_obj = DateTime::createFromFormat('d/m/Y', $data_raw);
        if ($data_obj) {
            return $data_obj->format('Y-m-d');
        } else {
            echo "Erro na linha $indice, coluna " . $cellIterator->key() . ": formato de data incorreto ou falha na conversão.<br>";
            return null;
        }
    }
}

if (isset($_GET['file'])) {
    $file = $_GET['file'];
    processarVagasEstagio($file, $conn);
}

$conn->close();

echo "<a href='index.php'>Voltar</a>";
?>