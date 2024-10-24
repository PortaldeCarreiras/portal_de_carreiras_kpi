<?php
require_once 'vendor/autoload.php'; // Inclui o autoloader do PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\IOFactory;

// Conexão com o banco de dados (ajuste as credenciais - substitua pelos seus dados)
include('conn.php');

// Função para inserir dados na tabela portal_acesso.
// Usada mais abaixo após a coleta de dados do arquivo AcessoPortal.xls
function inserir_dados($conn, $tabela, $dados){ 
    $campos = implode(", ", array_keys($dados));  
    $valor1 = $dados['codigo'];
    $valor2 = $dados['portal'];
    $valor3 = $dados['mes_acesso'];
    $valor4 = $dados['ano_acesso'];
    $valor5 = $dados['numero_acessos'];
    $valor6 = $dados['data'];   // Essa data pode ser inserida
    $valores = "'$valor1','$valor2','$valor3','$valor4','$valor5','$valor6'";
    // Echos abaixo para verificar se os dados foram adquiridos corretamente
    // Imprime eles na tela
    echo "INSERT INTO $tabela ($campos) VALUES ($valores)";
    echo "$valores <br>";

    if(mysqli_query($conn,"INSERT INTO $tabela ($campos) VALUES ($valores)" ));  
}

// Função para processar o arquivo AcessoPortal.xls
function processar_acesso_portal($arquivo, $conn)
{
    // ... (lógica para ler o arquivo e inserir os dados na tabela acesso_portal)
    // Exemplo:
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($arquivo);
    $worksheet = $spreadsheet->getActiveSheet();
   

    foreach ($worksheet->getRowIterator()  as $indice => $row) {
     if($indice > 1){    // não pegar o cabeçalho 
        $cellIterator = $row->getCellIterator();       
        $cellIterator->setIterateOnlyExistingCells(false);  // Inclui células vazias

        // Obtendo os valores de cada célula
        $codigo = (int)$cellIterator->current()->getValue();
        $cellIterator->next(); // Move para a próxima célula
        $portal = $cellIterator->current()->getValue();
        $cellIterator->next();
        $mesAcesso = (int)$cellIterator->current()->getValue();
        $cellIterator->next();
        $anoAcesso = (int)$cellIterator->current()->getValue();
        $cellIterator->next();
        $numero_acessos = (int)$cellIterator->current()->getValue();

        // Construindo o array de dados
        $dados = [
            'codigo' => $codigo,
            'portal' => $portal,
            'mes_acesso' => $mesAcesso,
            'ano_acesso' => $anoAcesso,
            'numero_acessos' => $numero_acessos,
            // Adicionar a data atual para a coluna 'data'
            'data' => date('Y-m-d H:i:s')   //carregar data direto do mysql melhora performance
        ];

        // Inserindo os dados no banco de dados
        inserir_dados($conn, 'portal_acesso', $dados);
        }
    }
}

// Função para processar o arquivo Consulta de Vagas de estágio.xls
function processar_vagas_estagio($arquivo, $conn)
{
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($arquivo);
    $worksheet = $spreadsheet->getActiveSheet();
    $colunas_a_ler = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 46, 47];

    $rowIterator = $worksheet->getRowIterator();
    $rowIterator->next(); // Pula o cabeçalho

    foreach ($rowIterator as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        // Inicializa um array para armazenar os dados a serem inseridos
        $dados = [];

        // Contador para as colunas
        $colunaIndex = 0;

        // Lê os dados das colunas especificadas
        foreach ($cellIterator as $cell) {
            if (in_array($colunaIndex, $colunas_a_ler)) {
                $valor = $cell->getValue(); // Obtém o valor da célula

                // Verifica se o valor não é nulo
                if ($valor !== null) {
                    // Formata os dados conforme necessário (colunas com formato inteiro)
                    if (in_array($colunaIndex, [1, 2, 7, 47])) {
                        if (is_numeric($valor)) {
                            $valor = (int)$valor;
                        }
                    }
                    $dados[] = $valor; // Adiciona o valor ao array de dados
                } else {
                    $dados[] = null; // Ou um valor padrão, se preferir
                }
            }
            $colunaIndex++; // Incrementa o índice da coluna
        }

        // Associa os valores aos nomes das colunas da tabela
        $dadosAssociados = [
            'empresa' => $dados[0],
            'item' => $dados[1],
            'codigo' => $dados[2],
            'nome_vaga' => $dados[3],
            'data_abertura' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dados[4])->format('Y-m-d'),
            'data_final_candidatar' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dados[5])->format('Y-m-d'),
            'previsao_contratacao' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dados[6])->format('Y-m-d'),
            `eixo_formacao` => $dados[7],
            `confidencial` => $dados[8],
            `responsavel` => $dados[9],
            `email_responsavel` => $dados[10],
            `telefone_responsavel` => $dados[11],
            `data_alteracao`  => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dados[12])->format('Y-m-d'),
            `revisao` => $dados[13],
            // Adicionar a data atual para a coluna 'data'
            'data' => date('Y-m-d H:i:s')
        ];

        // Insere os dados no banco de dados
        inserir_dados($conn, 'portal_vagas_estagio', $dadosAssociados);
    }
}

// Função auxiliar para separar os dados da coluna "Aluno"
function separar_dados_aluno($valor)
{
    // ... (lógica para separar os dados utilizando expressões regulares ou outras técnicas)
    // Expressão regular para capturar os grupos de dados
    preg_match('/(\d+)\s*-\s*(\d{13})\s*-\s*([^-]*)\s*-\s*(\d+)\s*-\s*([^-]*)\s*-\s*([^-]*)\s*-\s*(\d{4}-\d{2}-\d{2})/', $valor, $matches);

    // Verifica se a separação foi bem-sucedida
    if (count($matches) === 8) {
        // Retorna um array com os dados separados
        return [
            'aluno_codigo' => intval($matches[1]), // Convertendo para int
            'aluno_ra' => intval($matches[2]),              // RA com 13 caracteres
            'aluno_nome' => trim($matches[3]),      // Nome do aluno
            'aluno_eixo' => intval($matches[4]),    // Eixo (número)
            'aluno_periodo' => trim($matches[5]),    // Período
            'aluno_categoria' => trim($matches[6]),  // Categoria
            'aluno_data' => $matches[7]              // Data devo converter???? => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($matches[7])->format('Y-m-d')
        ];
    }

    return null; // Retorna null se a separação falhar
}

// Função para processar o arquivo saida.csv
function processar_saida_csv($arquivo, $conn)
{   
    if (($handle = fopen($arquivo, "r")) !== FALSE) {
       
        $data = fgetcsv($handle);

        $valor = explode(";",$data[0]);
        
        echo $valor[0]; 
        echo $valor[1];
        echo $valor[2];
       
    }
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... (verificação do arquivo enviado e tipo de arquivo)
    $arquivo = $_FILES['arquivo_xls']['tmp_name'];
    $tipo_arquivo = strtolower(pathinfo($_FILES['arquivo_xls']['name'], PATHINFO_EXTENSION));
    $nome_arquivo = $_FILES['arquivo_xls']['name'];
    $data_criacao = date('Y-m-d H:i:s', filemtime($arquivo));

    if ($arquivo['error'] === UPLOAD_ERR_OK) {
        // Nome original do arquivo
        $nomeArquivo = $arquivo['name'];
        
        // Tipo MIME do arquivo
        $tipoMime = $arquivo['type'];
        
        // Tamanho do arquivo (em bytes)
        $tamanhoArquivo = $arquivo['size'];
        
        // Caminho temporário no servidor
        $caminhoTemp = $arquivo['tmp_name'];

        // Data da última modificação (usando filemtime)
        $dataModificacao = date('Y-m-d H:i:s', filemtime($caminhoTemp));

        // Exibir os metadados
        echo "<div class='mt-4'>";
        echo "<h2>Metadados do Arquivo</h2>";
        echo "<ul>";
        echo "<li><strong>Nome do Arquivo:</strong> " . htmlspecialchars($nomeArquivo) . "</li>";
        echo "<li><strong>Tipo MIME:</strong> " . htmlspecialchars($tipoMime) . "</li>";
        echo "<li><strong>Tamanho:</strong> " . htmlspecialchars($tamanhoArquivo) . " bytes</li>";
        echo "<li><strong>Data de Modificação:</strong> " . htmlspecialchars($dataModificacao) . "</li>";
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<p class='text-danger'>Erro ao enviar o arquivo.</p>";
    }

    if ($tipo_arquivo == 'xlsx' || $tipo_arquivo == 'xls') {
        // if ($nome_arquivo == 'AcessoPortal') {
        //     // processar_acesso_portal($arquivo, $conn); // não está acessando aqui         

        // } elseif ($nome_arquivo == '') {
        //     // processar_vagas_estagio($arquivo, $conn); // não está acessando aqui
           
        // }       
        processar_acesso_portal($arquivo, $conn); // parou aqui
    } elseif ($tipo_arquivo == 'csv') {
        processar_saida_csv($arquivo, $conn);
    }

    // Exibe o nome do arquivo e a data de criação
    echo "<label>&nbsp Arquivo Carregado: $nome_arquivo</label><br>";   // Espaço em branco HTML &nbsp
    echo "<label>&nbsp Data de Criação: $data_criacao</label><br>";
}

// Função para inserir dados na tabela
// function inserirDados($tabela, $dados)
// {
//     global $conn;
//     $campos = implode(',', array_keys($dados));
//     $valores = "'" . implode("', '", $dados) . "'";
//     $sql = "INSERT INTO $tabela ($campos) VALUES ($valores)";
//     if ($conn->query($sql) === TRUE) {
//         // echo "New record created successfully";
//     } else {
//         echo "Error: " . $sql . "<br>" . $conn->error;
//     }
// }

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <title>Upload de Arquivos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1 class="text-danger">Upload de Arquivos</h1>
        <p>Selecione um arquivo para fazer o upload.</p>

        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <input type="file" id="arquivo" name="arquivo_xls" class="btn btn-success">
            </div>
            <input type="submit" value="Enviar" class="btn btn-success">
        </form>
    </div>
</body>