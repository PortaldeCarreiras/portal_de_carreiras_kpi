<?php
require_once 'vendor/autoload.php'; // Inclui o autoloader do PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

// Conexão com o banco de dados (ajuste as credenciais - substitua pelos seus dados)
include('conn.php');

// Função para inserir dados na tabela portal_acesso.
// Usada mais abaixo após a coleta de dados do arquivo AcessoPortal.xls
function inserirDadosAcessoPortal($conn, $tabela, $dados)
{
    $campos = implode(", ", array_keys($dados));
    $valor1 = $dados['codigo'];
    $valor2 = $dados['portal'];
    $valor3 = $dados['mes_acesso'];
    $valor4 = $dados['ano_acesso'];
    $valor5 = $dados['numero_acessos'];
    $valor6 = $dados['data'];   // Essa data pode ser inserida
    $valores = "'$valor1','$valor2','$valor3','$valor4','$valor5','$valor6'";
    // Check - echos abaixo para verificar se os dados foram adquiridos corretamente
    // Imprime eles na tela
    echo "INSERT INTO $tabela ($campos) VALUES ($valores)";
    echo "$valores <br>";

    // Executa a query de inserção ""INSERT INTO $tabela ($campos) VALUES ($valores)""
    // e verifica o sucesso (// Faz parte da mensagem de Sucesso no envio ao DB)
    // Faz parte da mensagem de Sucesso no envio ao DB
    if (mysqli_query($conn, "INSERT INTO $tabela ($campos) VALUES ($valores)")){
        $processSuccess = true; // Define sucesso como verdadeiro
        echo "<script>
                alert('Dados enviados com sucesso!');
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 2000); // Espera 2 segundos antes de redirecionar
              </script>";
    } else {
        echo "Erro na inserção: " . mysqli_error($conn) . "<br>";
    }
}

// Função para inserir dados na tabela portal_vagas_estagio.
// Usada mais abaixo após a coleta de dados do arquivo Consulta de Vagas de estágio.xls
function inserirDadosPortalVagas($conn, $tabela, $dados)
{
    $campos = implode(", ", array_keys($dados));
    $valor1 = $dados['empresa'];
    $valor2 = $dados['item'];
    $valor3 = $dados['codigo'];
    $valor4 = $dados['nome_vaga'];
    $valor5 = $dados['data_abertura'];
    $valor6 = $dados['data_final_candidatar'];
    $valor7 = $dados['data_previsao_contratacao'];
    $valor8 = $dados['eixo_formacao'];
    $valor9 = $dados['confidencial'];
    $valor10 = $dados['responsavel'];
    $valor11 = $dados['responsavel_email'];
    $valor12 = $dados['responsavel_telefone'];
    $valor47 = $dados['data_alteracao'];
    $valor48 = $dados['revisao'];
    $valor49 = $dados['data'];   // Essa data pode ser inserida
    $valores = "'$valor1','$valor2','$valor3','$valor4','$valor5','$valor6','$valor7',
    '$valor8','$valor9','$valor10','$valor11','$valor12','$valor47','$valor48','$valor49'";
    // Check - echos abaixo para verificar se os dados foram adquiridos corretamente
    // Imprime eles na tela
    echo "INSERT INTO $tabela ($campos) VALUES ($valores)";
    echo "$valores <br>";

    if (mysqli_query($conn, "INSERT INTO $tabela ($campos) VALUES ($valores)"));
}

// Função para processar o arquivo AcessoPortal.xls
function processarAcessoPortal($file, $conn)
{
    // ... (lógica para ler o arquivo e inserir os dados na tabela portalacesso)
    // Exemplo:
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();


    foreach ($worksheet->getRowIterator()  as $indice => $row) {
        if ($indice > 1) {    // não pegar o cabeçalho 
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
                'data' => date('Y-m-d H:i:s')   // pode carregar data direto do mysql melhora performance
            ];

            // Inserindo os dados no DB tabela portal_acesso
            inserirDadosAcessoPortal($conn, 'portal_acesso', $dados);
        }
    }
}

// Função para processar o arquivo Consulta de Vagas de estágio.xlsx
function processarVagasEstagio($file, $conn)
{
    // ... (lógica para ler o arquivo e inserir os dados na tabela portal_vagas_estagio)
    // Exemplo:
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
    $worksheet = $spreadsheet->getActiveSheet();


    foreach ($worksheet->getRowIterator()  as $indice => $row) {
        if ($indice > 1) {    // não pegar o cabeçalho 
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);  // Inclui células vazias

            // Obtendo os valores de cada célula
            $empresa = $cellIterator->current()->getValue();
            $cellIterator->next(); // Move para a próxima célula
            $item = (int)$cellIterator->current()->getValue();
            $cellIterator->next();
            $codigo = (int)$cellIterator->current()->getValue();
            $cellIterator->next();
            $nome_vaga = $cellIterator->current()->getValue();
            $cellIterator->next();

            // As próximas 3 colunas serão carregadas com os dados da planilha, mas é necessaário
            // formatar a data segundo o padrão do DB (y-m-d)
            $data_abertura_raw = $cellIterator->current()->getValue();
            // Verifica se $data_abertura_raw é um número (data serial do Excel representa um dia
            // contado a partir de 1º de janeiro de 1900)
            if (is_numeric($data_abertura_raw)) {
                // Converte o número serial do Excel em uma data no formato MySQL (yyyy-mm-dd)
                $data_abertura = date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($data_abertura_raw));
                echo "Data convertida para MySQL: $data_abertura<br>";
            } else {
                // Se o valor não for um número, tenta interpretá-lo como data no formato dd/mm/yyyy
                $data_abertura_obj = DateTime::createFromFormat('d/m/Y', $data_abertura_raw);
                if ($data_abertura_obj) {
                    $data_abertura = $data_abertura_obj->format('Y-m-d');
                    echo "Data convertida para MySQL: $data_abertura<br>";
                } else {
                    echo "Erro: formato de data incorreto ou falha na conversão.<br>";
                    $data_abertura = null;
                }
            }
            $cellIterator->next(); // Move para a próxima célula

            $data_final_candidatar_raw = $cellIterator->current()->getValue();
            // Verifica se $data_final_candidatar_raw é um número (data serial do Excel)
            if (is_numeric($data_final_candidatar_raw)) {
                // Converte o número serial do Excel em uma data no formato MySQL (yyyy-mm-dd)
                $data_final_candidatar = date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($data_final_candidatar_raw));
                echo "Data convertida para MySQL: $data_final_candidatar<br>";
            } else {
                // Se o valor não for um número, tenta interpretá-lo como data no formato dd/mm/yyyy
                $data_final_candidatar_obj = DateTime::createFromFormat('d/m/Y', $data_final_candidatar_raw);
                if ($data_final_candidatar_obj) {
                    $data_final_candidatar = $data_final_candidatar_obj->format('Y-m-d');
                    echo "Data convertida para MySQL: $data_final_candidatar<br>";
                } else {
                    echo "Erro: formato de data incorreto ou falha na conversão.<br>";
                    $data_final_candidatar = null;
                }
            }
            $cellIterator->next(); // Move para a próxima célula

            $data_previsao_contratacao_raw = $cellIterator->current()->getValue();
            // Verifica se $data_previsao_contratacao_raw é um número (data serial do Excel)
            if (is_numeric($data_previsao_contratacao_raw)) {
                // Converte o número serial do Excel em uma data no formato MySQL (yyyy-mm-dd)
                $data_previsao_contratacao = date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($data_previsao_contratacao_raw));
                echo "Data convertida para MySQL: $data_previsao_contratacao<br>";
            } else {
                // Se o valor não for um número, tenta interpretá-lo como data no formato dd/mm/yyyy
                $data_previsao_contratacao_obj = DateTime::createFromFormat('d/m/Y', $data_previsao_contratacao_raw);
                if ($data_previsao_contratacao_obj) {
                    $data_previsao_contratacao = $data_previsao_contratacao_obj->format('Y-m-d');
                    echo "Data convertida para MySQL: $data_previsao_contratacao<br>";
                } else {
                    echo "Erro: formato de data incorreto ou falha na conversão.<br>";
                    $data_previsao_contratacao = null;
                }
            }
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
            $data_alteracao_raw = $worksheet->getCell('AU' . $indice)->getValue();  // Coluna "AU" da planilha excel.
            // Verifica se $data_alteracao_raw é um número (data serial do Excel)
            if (is_numeric($data_alteracao_raw)) {
                // Converte o número serial do Excel em uma data no formato MySQL (yyyy-mm-dd)
                $data_alteracao = date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($data_alteracao_raw));
                echo "Data convertida para MySQL: $data_alteracao<br>";
            } else {
                // Se o valor não for um número, tenta interpretá-lo como data no formato dd/mm/yyyy
                $data_alteracao_obj = DateTime::createFromFormat('d/m/Y', $data_alteracao_raw);
                if ($data_alteracao_obj) {
                    $data_alteracao = $data_alteracao_obj->format('Y-m-d');
                    echo "Data convertida para MySQL: $data_alteracao<br>";
                } else {
                    echo "Erro: formato de data incorreto ou falha na conversão.<br>";
                    $data_alteracao = null;
                }
            }
            
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
                'data' => date('Y-m-d H:i:s')   // pode carregar data direto do mysql melhora performance
            ];

            // Inserindo os dados no DB tabela portal_acesso
            inserirDadosPortalVagas($conn, 'portal_vagas_estagio', $dados);
        }
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
function processar_saida_csv($file, $conn)
{
    if (($handle = fopen($file, "r")) !== FALSE) {

        $data = fgetcsv($handle);

        $valor = explode(";", $data[0]);

        echo "<label>$valor[0]&nbsp-&nbsp</label>";
        echo "<label>$valor[1]&nbsp-&nbsp</label>";
        echo "<label>$valor[2]&nbsp-&nbsp</label>";
    }
}


// Metadados            VERIFICAR????
// if ($arquivo['error'] === UPLOAD_ERR_OK) {
//     // Nome original do arquivo
//     $nomeArquivo = $arquivo['name'];

//     // Tipo MIME do arquivo
//     $tipoMime = $arquivo['type'];

//     // Tamanho do arquivo (em bytes)
//     $tamanhoArquivo = $arquivo['size'];

//     // Caminho temporário no servidor
//     $caminhoTemp = $arquivo['tmp_name'];

//     // Data da última modificação (usando filemtime)
//     $dataModificacao = date('Y-m-d H:i:s', filemtime($caminhoTemp));

//     // Exibir os metadados
//     echo "<div class='mt-4'>";
//     echo "<h2>Metadados do Arquivo</h2>";
//     echo "<ul>";
//     echo "<li><strong>Nome do Arquivo:</strong> " . htmlspecialchars($nomeArquivo) . "</li>";
//     echo "<li><strong>Tipo MIME:</strong> " . htmlspecialchars($tipoMime) . "</li>";
//     echo "<li><strong>Tamanho:</strong> " . htmlspecialchars($tamanhoArquivo) . " bytes</li>";
//     echo "<li><strong>Data de Modificação:</strong> " . htmlspecialchars($dataModificacao) . "</li>";
//     echo "</ul>";
//     echo "</div>";
// } else {
//     echo "<p class='text-danger'>Erro ao enviar o arquivo.</p>";
// }

// Variável que armazenará mensagens para exibir ao usuário
$message = '';


$fileExtension = '';
$fileName = '';
$data_criacao = '';

// Verifica se o formulário foi enviado via POST e se o arquivo foi submetido corretamente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['xls_file'])) {
    // Obtém informações sobre o arquivo enviado
    // Garante que as variáveis só sejam exibidas quando definidas:
    $file = $_FILES['xls_file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $data_criacao = date('Y-m-d H:i:s', filemtime($fileTmpName));

    // Verifica se o arquivo foi enviado sem erros
    if ($file['error'] == UPLOAD_ERR_OK) {
        // Pega o nome e o caminho temporário do arquivo
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        // Pega a extensão do arquivo (csv, xls ou xlsx)
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        // Verifica a extensão do arquivo para escolher o leitor apropriado
        switch (strtolower($fileExtension)) {
            case 'csv':
                // Se for um arquivo CSV, usa o leitor de CSV
                $reader = IOFactory::createReader('Csv');
                break;
            case 'xls':
                // Se for um arquivo XLS, usa o leitor de XLS
                $reader = IOFactory::createReader('Xls');
                break;
            case 'xlsx':
                // Se for um arquivo XLSX, usa o leitor de XLSX
                $reader = IOFactory::createReader('Xlsx');
                break;
            default:
                // Se o formato não for suportado, exibe uma mensagem de erro
                die("Formato de arquivo não suportado.");
        }

        // Carrega o arquivo temporário para ser manipulado
        $spreadsheet = $reader->load($fileTmpName);

        // Define o nome do arquivo convertido, convertento o nome do arquivo original
        // para o formato XLSX, salvando com o mesmo nome, mas extensão .xlsx
        $newFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.xlsx';

        // Define o gravador (writer) para o formato XLSX
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        // Define o caminho onde o arquivo convertido será salvo
        $outputFilePath = 'uploads/' . $newFileName;

        // Verifica se o diretório 'uploads' existe, se não, cria-o
        if (!file_exists('uploads')) {
            mkdir('uploads', 0777, true);
        }   // O terceiro parâmetro, "true", indica que a função deve criar automaticamente todos os
        // diretórios pais necessários caso eles não existam. Por exemplo, se o diretório "uploads"
        // estiver dentro de outro diretório que não existe, a função irá criar esse diretório pai também.

        // Salva o arquivo convertido no diretório de uploads
        $writer->save($outputFilePath);

        // Mensagem para informar ao usuário que o arquivo foi convertido com sucesso
        $message .= "Arquivo convertido para XLSX e salvo em: $outputFilePath<br>";

        // // Leitura dos dados da planilha para inserção no banco de dados
        // $sheet = $spreadsheet->getActiveSheet();  // Obtém a planilha ativa
        // $rows = $sheet->toArray();  // Converte todas as linhas da planilha em um array

        // // Define qual tabela será usada para armazenar os dados (neste caso, "portal_acesso" é usada como exemplo)
        // $table = 'portal_acesso'; // Pode-se definir dinamicamente qual tabela usar, conforme sua lógica

        // // Itera sobre as linhas (o índice 0 é geralmente o cabeçalho, por isso é ignorado)
        // foreach ($rows as $index => $row) {
        //     if ($index == 0) {
        //         continue; // Pula o cabeçalho da planilha
        //     }

        //     // Lógica para inserção na tabela 'portal_acesso'
        //     if ($table == 'portal_acesso') {
        //         // Protege contra injeção SQL e prepara os dados
        //         $nome = mysqli_real_escape_string($conn, trim($row[0]));
        //         $cargo = intval($row[1]);

        //         // Insere os dados na tabela 'portal_acesso' (nome e cargo)
        //         $sql = "INSERT INTO portal_acesso (nome, cargo) VALUES ('$nome', $cargo)";
        //         if (mysqli_query($conn, $sql)) {
        //             // Exibe uma mensagem de sucesso caso a inserção funcione
        //             $message .= "Inserido: Nome: $nome, Cargo: $cargo<br>";
        //         } else {
        //             // Exibe uma mensagem de erro caso ocorra algum problema na inserção
        //             $message .= "Erro ao inserir dados: " . mysqli_error($conn) . "<br>";
        //         }
        //     }
        //     // Aqui você pode adicionar lógica para outras tabelas como 'pasta2' ou 'pasta3', dependendo da sua necessidade.
        // }
    } else {
        // Caso ocorra algum erro ao fazer o upload do arquivo, exibe uma mensagem de erro
        $message .= "Erro ao fazer upload do arquivo.";
    }
}


if ($fileExtension == 'xlsx' || $fileExtension == 'xls') {
    if ($newFileName == 'AcessoPortal.xlsx') {
        processarAcessoPortal($fileTmpName, $conn); // Passa o caminho temporário correto
    } elseif ($newFileName == 'Consulta de Vagas de estágio.xlsx') {
        processarVagasEstagio($fileTmpName, $conn); // (não está acessando aqui) Passa o caminho temporário correto
    } elseif ($newFileName == 'saida.xlsx') {
        // processar_saida_csv($fileTmpName, $conn); // (não está acessando aqui) Passa o caminho temporário correto
    }
}

// Exibe o nome do arquivo e a data de criação
echo "<label>&nbsp Arquivo Carregado: $fileName</label><br>";   // Espaço em branco HTML &nbsp
echo "<label>&nbsp Data de Criação: $data_criacao</label><br>";
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
                <input type="file" id="arquivo" name="xls_file" class="btn btn-success">
            </div>
            <input type="submit" value="Enviar" class="btn btn-success">
        </form>
    </div>
</body>