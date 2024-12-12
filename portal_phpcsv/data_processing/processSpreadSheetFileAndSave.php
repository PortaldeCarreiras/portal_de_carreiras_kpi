<?php
// CARREGA, VERIFICA O TIPO DE EXTENSÃO, ABRE COM O SPREADSHEET,
// CONVERT PARA XLSX, ADICIONA A COLUNA COM DATA DE UPLOAD E SALVA NA PASTA /UPLOAD DO PROJETO

require_once 'utils.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Processa o arquivo enviado via formulário, converte para XLSX e salva na pasta /uploads do projeto.
 *
 * @param array $file O arquivo enviado via formulário.
 * @param string $dataCriacao A data de criação do arquivo.
 * @param array $nomesPermitidos Lista de nomes de arquivos permitidos.
 * @param array $extensoesPermitidas Lista de extensões de arquivos permitidas.
 * @param string $message Mensagem de retorno para o usuário.
 */
function processSpreadSheet($file, $dataCriacao, $nomesPermitidos, $extensoesPermitidas, &$message) {
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

    // Normaliza o nome do arquivo (sem a extensão)
    $nomeArquivoNormalizado = normalizarNomeArquivo(pathinfo($fileName, PATHINFO_FILENAME));

    // Verificar nome de arquivo para ver se ele corresponde aos três esperados
    if (!in_array($nomeArquivoNormalizado, $nomesPermitidos)) {
        $nomesEsperados = "AcessoPortal, Consulta de Vagas de estágio e saida.";
        $variacoesPermitidas = "acessoportal, Acesso Portal, Consulta de Vagas de Estágio, consultadevagasdeestagio, consulta de vagas de estágio, consulta de vagas de estagio, Saída, Saida, saída.";
        criaLogs('processSpreadSheetFileAndSave', "NOME DE ARQUIVO NÃO PERMITIDO! Os nomes esperados são: $nomesEsperados. As variações permitidas são: $variacoesPermitidas");
        echo "<script>alert('NOME DE ARQUIVO NÃO PERMITIDO!\\nOs nomes esperados são: $nomesEsperados\\nAs variações permitidas são: $variacoesPermitidas'); window.location.href = '../index.php';</script>";
        exit();
    }

    // Verificar extensão de arquivo para ver se ela é permitida (csv, xls, xlsx)
    if (!in_array(strtolower($fileExtension), $extensoesPermitidas)) {
        criaLogs('processSpreadSheetFileAndSave', 'Extensão de arquivo não permitida.');
        echo "<script>alert('Extensão de arquivo não permitida.'); window.location.href = '../index.php';</script>";
        exit();
    }

    if ($file['error'] == UPLOAD_ERR_OK) {
        // Verifica a extensão do arquivo para escolher o leitor apropriado
        switch (strtolower($fileExtension)) {
            case 'csv':
                $reader = IOFactory::createReader('Csv');
                break;
            case 'xls':
                $reader = IOFactory::createReader('Xls');
                break;
            case 'xlsx':
                $reader = IOFactory::createReader('Xlsx');
                break;
            default:
                die("Formato de arquivo não suportado.");
        }

        // Carrega o arquivo temporário para ser manipulado
        $spreadsheet = $reader->load($fileTmpName);

        // Adiciona a nova coluna com a data de modificação do arquivo
        addColumnAndFill($spreadsheet, "Data Arquivo Ori", $dataCriacao);

        // Define o nome do arquivo convertido, convertendo o nome do arquivo original
        $newFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.xlsx';

        // Define o gravador (writer) para o formato XLSX
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

        // Define o caminho onde o arquivo convertido será salvo
        $outputFilePath = __DIR__ . '/../uploads/' . $newFileName; // Use __DIR__ para garantir o caminho absoluto

        // Verifica se o diretório 'uploads' existe, se não, cria-o
        if (!file_exists(__DIR__ . '/../uploads')) {
            mkdir(__DIR__ . '/../uploads', 0777, true);
        }

        // Salva o arquivo convertido no diretório de uploads
        $writer->save($outputFilePath);

        // Mensagem para informar ao usuário que o arquivo foi convertido com sucesso
        $message .= "Arquivo convertido para XLSX e salvo em: $outputFilePath<br>";

        // PROCESSAMENTO DO ARQUIVO AO SER CLICADO O BOTÃO "ENVIAR".
        $newFileName = converterParaCamelCase(pathinfo($newFileName, PATHINFO_FILENAME)) . '.xlsx';
        if (strcasecmp($newFileName, 'acessoPortal.xlsx') == 0) {
            header("Location: data_processing/acessoPortal.php?file=" . urlencode($outputFilePath) . "&dataModificacao=" . urlencode($dataCriacao));
        } elseif (strcasecmp($newFileName, 'consultaDeVagasDeEstagio.xlsx') == 0) {
            header("Location: data_processing/vagasEstagio.php?file=" . urlencode($outputFilePath));
        } elseif (strcasecmp($newFileName, 'saida.xlsx') == 0) {
            header("Location: data_processing/saidaFile.php?file=" . urlencode($outputFilePath));
        }
        exit();
    } else {
        $message .= "Erro ao fazer upload do arquivo.";
    }
}
