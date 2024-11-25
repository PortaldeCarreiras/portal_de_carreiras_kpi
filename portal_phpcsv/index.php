<?php
require_once 'vendor/autoload.php';
require_once 'data_processing/utils.php';
require_once 'data_processing/metaProcessFile.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

include('conn.php');

$message = metaProcessFile($conn, 'planilha_upload');

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

                // Exibir os metadados no HTML
                document.getElementById("metadadosArquivo").innerHTML = `
                    <p>Nome do Arquivo: ${nomeArquivo}</p>
                    <p>Tipo MIME: ${tipoMime}</p>
                    <p>Tamanho: ${tamanho} bytes</p>
                    <p>Data de Modificação Original: ${new Date(arquivo.lastModifiedDate).toLocaleString()}</p>
                `;
            }
        }
    </script>
</head>

<body>
    <div class="container">
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
</body>
</html>