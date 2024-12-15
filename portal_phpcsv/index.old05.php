<?php
require_once 'vendor/autoload.php';
require_once 'data_processing/utils.php';
require_once 'data_processing/metaProcessFile.php';
require_once 'js/obterFileNameDrop.js';

function adicionarColunaComValor($spreadsheet, $coluna, $valor) {
    $sheet = $spreadsheet->getActiveSheet();
    $highestColumn = $sheet->getHighestColumn();
    $highestColumn++;
    $sheet->setCellValue($highestColumn . '1', $coluna);
    $sheet->setCellValue($highestColumn . '2', $valor);
}

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

include_once('conn.php');

$message = metaProcessFile($conn);

// Variáveis que serão utilizadas em IFs diferentes abaixo
$fileExtension = '';
$fileName = '';
$data_criacao = '';

// Lista de nomes de arquivos permitidos (normalizados, sem extensão)
$nomesPermitidos = [
    'acessoportal',
    'acesso portal',
    'consultadevagasdeestagio',
    'consulta de vagas de estagio',
    'saida'
];

// Lista de extensões de arquivos permitidas
$extensoesPermitidas = ['csv', 'xls', 'xlsx'];

// PEGA O FORMULÁRIO VIA POST, CARREGA, VERIFICA O TIPO DE EXTENSÃO,
// ABRE COM O SPREADSHEET, CONVERT PARA XLSX E SALVA NA PASTA /UPLOAD DO PROJETO
// Verifica se o formulário foi enviado via POST e se o arquivo foi submetido corretamente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['xls_file'])) {
    // Obtém informações sobre o arquivo enviado
    $file = $_FILES['xls_file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $data_criacao = $_POST['dataModificacao'] ?? date('Y-m-d H:i:s');

    // Normaliza o nome do arquivo (sem a extensão)
    $nomeArquivoNormalizado = normalizarNomeArquivo(pathinfo($fileName, PATHINFO_FILENAME));

    // Verificar nome de arquivo para ver se ele corresponde aos três esperados
    if (!in_array($nomeArquivoNormalizado, $nomesPermitidos)) {
        $nomesEsperados = "AcessoPortal, Consulta de Vagas de estágio e saida.";
        $variacoesPermitidas = "acessoportal, Acesso Portal, Consulta de Vagas de Estágio, consultadevagasdeestagio, consulta de vagas de estágio, consulta de vagas de estagio, Saída, Saida, saída.";
        echo "<script>alert('NOME DE ARQUIVO NÃO PERMITIDO!\\nOs nomes esperados são: $nomesEsperados\\nAs variações permitidas são: $variacoesPermitidas'); window.location.href = 'index.php';</script>";
        exit();
    }

    // Verificar extensão de arquivo para ver se ela é permitida (csv, xls, xlsx)
    if (!in_array(strtolower($fileExtension), $extensoesPermitidas)) {
        echo "<script>alert('Extensão de arquivo não permitida.'); window.location.href = 'index.php';</script>";
        exit();
    }

    // Verifica se o arquivo foi enviado sem erros
    if ($file['error'] == UPLOAD_ERR_OK) {

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
        }   //  Fim do SWITCH de verificação de extensão de arquivo

        // Carrega o arquivo temporário para ser manipulado
        $spreadsheet = $reader->load($fileTmpName);

        // Adiciona a nova coluna com a data de modificação do arquivo
        adicionarColunaComValor($spreadsheet, "Data Arquivo Original", $data_criacao);

        // Define o nome do arquivo convertido, convertendo o nome do arquivo original
        // para o formato XLSX, salvando com o mesmo nome, mas extensão .xlsx
        $newFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.xlsx';

        // Define o gravador (writer) para o formato XLSX
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        // Define o caminho onde o arquivo convertido será salvo
        $outputFilePath = __DIR__ . '/uploads/' . $newFileName; // Use __DIR__ para garantir o caminho absoluto

        // Verifica se o diretório 'uploads' existe, se não, cria-o
        if (!file_exists(__DIR__ . '/uploads')) {
            mkdir(__DIR__ . '/uploads', 0777, true);
        }

        // Salva o arquivo convertido no diretório de uploads
        $writer->save($outputFilePath);

        // Mensagem para informar ao usuário que o arquivo foi convertido com sucesso
        $message .= "Arquivo convertido para XLSX e salvo em: $outputFilePath<br>";

        // PROCESSAMENTO DO ARQUIVO AO SER CLICADO O BOTÃO "ENVIAR".
        $newFileName = converterParaCamelCase(pathinfo($newFileName, PATHINFO_FILENAME)) . '.xlsx';
        // Use SEMPRE o arquivo incrementado ($outputFilePath) ao redirecionar
        if (strcasecmp($newFileName, 'acessoPortal.xlsx') == 0) {
            header("Location: data_processing/acessoPortal.php?file=" . urlencode($outputFilePath));
        } elseif (strcasecmp($newFileName, 'consultaDeVagasDeEstagio.xlsx') == 0) {
            header("Location: data_processing/vagasEstagio.php?file=" . urlencode($outputFilePath));
        } elseif (strcasecmp($newFileName, 'saida.xlsx') == 0) {
            header("Location: data_processing/saidaFile.php?file=" . urlencode($outputFilePath));
        }   //  Fim do IF de verificação de nome de arquivo

        exit();
    } else {
        $message .= "Erro ao fazer upload do arquivo.";
    }   //  Fim do IF de verificação de erro no upload do arquivo
}   //  Fim do IF de verificação de envio de arquivo via POST

// Variáveis para exibir as informações do arquivo selecionado no dropdown, vindas do DB.
$valorInput = 'Sem informações disponíveis'; // Valor padrão caso nenhuma linha seja retornada
$arquivoData = '';
$arquivoDataUpload = '';

// Verifica se o arquivo foi enviado via POST e se o arquivo foi selecionado no dropdown
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se a variável $_POST['nomeArquivoSelDrop'] foi definida
    if (isset($_POST['nomeArquivoSelDrop'])) {
        $nomeArquivoCompleto = $_POST['nomeArquivoSelDrop'] ?? '';
        $nomeArquivoSelDrop = $_POST['nomeArquivoSelDrop'] ?? '';   // Captura o valor da variável
        // Consulta SQL em planilha_upload para buscar o maior ID onde arquivo_tamanho começa com 'Acesso'

        // Consulta SQL com preparação para evitar SQL injection
        $stmt = $conn->prepare("SELECT id, arquivo_tamanho, arquivo_data, arquivo_data_upload
                                FROM planilha_upload 
                                WHERE arquivo_nome LIKE CONCAT(?, '%') 
                                ORDER BY id DESC 
                                LIMIT 1");
        $stmt->bind_param("s", $nomeArquivoSelDrop);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Obter o resultado
            $row = $result->fetch_assoc();
            $id = $row['id'];   // Recupera o ID
            $arquivoTamanho = $row['arquivo_tamanho'];   // Recupera o tamanho do arquivo
            $valorInput = $arquivoData = $arquivoDataUpload = ''; // Valores padrão vazio

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nomeArquivoSelDrop'])) {
                $nomeArquivoSelDrop = $_POST['nomeArquivoSelDrop'] ?? '';

                $stmt = $conn->prepare("SELECT id, arquivo_tamanho, arquivo_data, arquivo_data_upload, arquivo_local_armazenado
                                        FROM planilha_upload 
                                        WHERE arquivo_nome LIKE CONCAT(?, '%') 
                                        ORDER BY id DESC 
                                        LIMIT 1");
                $stmt->bind_param("s", $nomeArquivoSelDrop);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $id = $row['id'];
                    $arquivoTamanho = $row['arquivo_tamanho'];
                    $valorInput = "ID: $id   Tamanho: $arquivoTamanho"; // Atualiza apenas se os dados existirem
                    $arquivoData = (new DateTime($row['arquivo_data']))->format('d-M-Y H:i:s');
                    $arquivoDataUpload = (new DateTime($row['arquivo_data_upload']))->format('d-M-Y H:i:s');
                    $arquivoLocalArmazenado = $row['arquivo_local_armazenado'];
                }
            }
        }
    } else {
        $valorInput = 'Nenhum dado encontrado para o arquivo selecionado.';
        $arquivoData = '';
        $arquivoDataUpload = '';
    }
} else {
    echo "O formulário não foi enviado.";
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <title>Upload de Arquivos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script>
        function confirmarExclusao() {
            const arquivo = document.getElementById("arquivo").files[0];
            if (arquivo) {
                return confirm(`Deseja realmente deletar todos os dados e carregar o novo arquivo ${arquivo.name} ao Banco de dados?`);
            }
            return false;
        }

        // Função para exibir a mensagem de processamento
        function exibirMensagemProcessamento() {
            const mensagemProcessamento = document.getElementById("mensagemProcessamento");
            mensagemProcessamento.style.display = "block";
        }

        // Função para obter os metadados do arquivo
        function obterMetadadosArquivo() {
            const arquivo = document.getElementById("arquivo").files[0];
            if (arquivo) {
                // Obtém os metadados do arquivo
                const nomeArquivo = arquivo.name;
                const tipoMime = arquivo.type;
                const tamanho = arquivo.size;
                const dataModificacao = new Date(arquivo.lastModifiedDate).toISOString().slice(0, 19).replace('T', ' ');

                // Atualiza os campos hidden com os metadados do arquivo
                document.getElementById("nomeArquivo").value = nomeArquivo;
                document.getElementById("tipoMime").value = tipoMime;
                document.getElementById("tamanho").value = tamanho;
                document.getElementById("dataModificacao").value = dataModificacao;

                // Exibe os metadados no console
                console.log("Nome do Arquivo:", nomeArquivo); // Adiciona um log no console para verificar o nome do arquivo
                console.log("Tipo MIME:", tipoMime); // Adiciona um log no console para verificar o tipo MIME
                console.log("Tamanho:", tamanho); // Adiciona um log no console para verificar o tamanho
                console.log("Data de Modificação:", dataModificacao); // Adiciona um log no console para verificar a data

                // // Exibir os metadados no HTML
                // document.getElementById("metadadosArquivo").innerHTML = `
                //     <p>Nome do Arquivo: ${nomeArquivo}</p>
                //     <p>Tipo MIME: ${tipoMime}</p>
                //     <p>Tamanho: ${tamanho} bytes</p>
                //     <p>Data de Modificação Original: ${new Date(arquivo.lastModifiedDate).toLocaleString()}</p>
                // `;
            }
        }

        addEventListener();

        // document.addEventListener('DOMContentLoaded', function () {
        //     const dropdown = document.getElementById('dropdownArquivos');
        //     if (dropdown.options.length > 0) {
        //         dropdown.selectedIndex = 0; // Seleciona o primeiro item como padrão
        //         if (!sessionStorage.getItem('formSubmitted')) {
        //             capturarNomeArquivoSelDropdown();
        //             sessionStorage.setItem('formSubmitted', 'true');
        //         }
        //     }
        // });

        //         function capturarNomeArquivoSelDropdown() {
        //     // Captura o dropdown pelo ID
        //     const dropdown = document.getElementById('dropdownArquivos');

        //     // Obtém o valor selecionado
        //     const nomeArquivoSelecionado = dropdown.value;

        //     // Atualiza o texto visível no dropdown
        //     dropdown.textContent = nomeArquivoSelecionado;

        //     // Captura as cinco primeiras letras do nome do arquivo
        //     const cincoPrimeirasLetras = nomeArquivoSelecionado.substring(0, 5);

        //     // Atualiza o campo hidden
        //     document.getElementById('nomeArquivoSelDrop').value = cincoPrimeirasLetras;

        //     // Envia o formulário
        //     document.getElementById('formDropdown').submit();
        // }
    </script>
    <script src="js/obterFileNameDrop.js"></script>
</head>

<body>
    <div class="container">
        <div class="row" class="row justify-content-md-left">
            <!-- Container para Upload de Arquivos -->
            <div class="col-md-auto">
                <h1 class="text-danger">Upload de Arquivos</h1>
                <p>Selecione um arquivo para fazer o upload.</p>
                <!-- Adiciona a chamada para exibir a mensagem de processamento ao enviar o formulário -->
                <form action="" method="post" enctype="multipart/form-data" onsubmit="exibirMensagemProcessamento(); return confirmarExclusao();">
                    <div class="form-group">
                        <input type="file" id="arquivo" name="xls_file" class="btn btn-success" required onchange="obterMetadadosArquivo()">
                    </div>
                    <input type="hidden" id="nomeArquivo" name="nomeArquivo">
                    <input type="hidden" id="tipoMime" name="tipoMime">
                    <input type="hidden" id="tamanho" name="tamanho">
                    <input type="hidden" id="dataModificacao" name="dataModificacao">
                    <input type="submit" value="Enviar" class="btn btn-success">
                </form>
                <div id="mensagemProcessamento" style="display:none;">
                    <p class="text-warning">Sua solicitação está sendo executada, aguarde o término da mesma!</p>
                </div>
                <div id="metadadosArquivo"></div>
            </div>

            <!-- Espaço vazio para a segunda parte -->
            <div class="col"></div>

            <div class="col-md-auto">
                <h2 class="text-danger">Download de Arquivos</h2>
                <!-- <p>Selecione um arquivo abaixo para fazer o download.</p> -->

                <?php
                // Opções no dropdown .Função para obter todos os arquivos no diretório especificado
                function obterArquivos($diretorio)
                {
                    $arquivos = array();
                    $listaArquivos = scandir($diretorio);
                    foreach ($listaArquivos as $arquivo) {
                        $caminhoCompleto = $diretorio . DIRECTORY_SEPARATOR . $arquivo;
                        if ($arquivo !== '.' && $arquivo !== '..' && is_file($caminhoCompleto)) {
                            $arquivos[] = $arquivo;
                        }
                    }
                    return $arquivos;
                }   //  Fim da função obterArquivos

                // Diretório onde os arquivos de upload são salvos
                $diretorioUploads = 'C:/xampp/htdocs/portal/portal_phpcsv/uploads';
                $arquivos = obterArquivos($diretorioUploads);
                $nomeArquivoSelDrop = $_POST['nomeArquivoSelDrop'] ?? '';
                ?>

                <div class="form-group col-md-auto">
                    <!-- <label for="exampleFormControlSelect1">Selecione um arquivo abaixo para fazer o download.</label> -->
                    <select class="form-control" id="dropdownArquivos" onchange="capturarNomeArquivoSelDropdown()">
                        <option value="" disabled selected>Selecione um arquivo para download.</option>
                        <?php
                        // Exibe as opções no dropdown
                        if (!empty($arquivos)) {
                            foreach ($arquivos as $arquivo) {
                                // Verifica se o arquivo começa com as mesmas 5 primeiras letras do valor enviado
                                $selected = (substr($arquivo, 0, 5) === $nomeArquivoSelDrop) ? 'selected' : '';
                                echo "<option value=\"$arquivo\" $selected>$arquivo</option>";
                            }   //  Fim do FOREACH de exibição de arquivos
                        } else {
                            echo "<option>Nenhum arquivo encontrado</option>";
                        }   //  Fim do IF de verificação de arquivos
                        ?>
                    </select>
                </div>

                <form id="formDropdown" action="index.php" method="POST">
                    <input type="hidden" id="nomeArquivoSelDrop" name="nomeArquivoSelDrop" value="<?= htmlspecialchars($nomeArquivoSelDrop); ?>">

                    <div class="container">
                        <!-- Tamanho do arquivo -->
                        <div class="form-group">
                            <label for="tamanhoArquivo">Tamanho do arquivo / <?= htmlspecialchars($nomeArquivoCompleto); ?></label>
                            <input type="text" readonly class="form-control-plaintext" id="tamanhoArquivo" value="<?= htmlspecialchars($valorInput); ?> bytes">
                        </div>

                        <!-- Data original do arquivo -->
                        <div class="form-group">
                            <label for="dataArquivo">Data original do arquivo</label>
                            <input type="text" readonly class="form-control-plaintext" id="dataArquivo" value="<?= htmlspecialchars($arquivoData); ?>">
                        </div>

                        <!-- Data upload do arquivo -->
                        <div class="form-group">
                            <label for="dataArquivoUpload">Data original do arquivo</label>
                            <input type="text" readonly class="form-control-plaintext" id="dataArquivoUpload" value="<?= htmlspecialchars($arquivoDataUpload); ?>">
                        </div>
                        
                        <input type="hidden" id="arquivoOriginalPath" name="arquivoOriginalPath" value="<?= htmlspecialchars($arquivoLocalArmazenado); ?>">

                        <input type="submit" value="Download" class="btn btn-success">

                    </div>
                </form>


            </div>

        </div>
    </div>
    </div>
    <script>
        capturarNomeArquivoSelDropdown();   //  Chama a função para capturar o nome do arquivo selecionado no dropdown
    </script>
</body>

</html>