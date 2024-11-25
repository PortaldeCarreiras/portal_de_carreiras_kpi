<?php
// Função para converter caracteres acentuados para seus equivalentes sem acento
function removerAcentos($string)
{
    $acentos = array(
        'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
        'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I', 'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
        'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U', 'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'Ç' => 'C', 'ç' => 'c', 'Ñ' => 'N', 'ñ' => 'n'
    );
    return strtr($string, $acentos);
}

// Função para converter o nome do arquivo para camelCase
function converterParaCamelCase($string)
{
    $string = removerAcentos($string);
    $string = preg_replace('/[^a-zA-Z0-9]/', ' ', $string);
    $string = ucwords(strtolower($string));
    $string = str_replace(' ', '', $string);
    return lcfirst($string);
}

// Função para normalizar o nome do arquivo
function normalizarNomeArquivo($nomeArquivo)
{
    $nomeArquivo = removerAcentos($nomeArquivo);
    $nomeArquivo = str_replace(' ', '', $nomeArquivo); // Remove espaços em branco
    return strtolower($nomeArquivo);
}

// Função para verificar se o nome do arquivo é permitido
function verificarNomeArquivo($nomeArquivo, $nomesPermitidos)
{
    $nomeArquivoNormalizado = normalizarNomeArquivo(pathinfo($nomeArquivo, PATHINFO_FILENAME));
    return in_array($nomeArquivoNormalizado, $nomesPermitidos);
}

// Função para verificar se a extensão do arquivo é permitida
function verificarExtensaoArquivo($extensaoArquivo, $extensoesPermitidas)
{
    return in_array(strtolower($extensaoArquivo), $extensoesPermitidas);
}

// Função para exibir um alerta e redirecionar
function exibirAlertaERedirecionar($mensagem)
{
    echo "<script>alert('$mensagem'); window.location.href = 'index.php';</script>";
    exit();
}

// Função para exibir mensagem resumida no navegador
function exibirMensagemResumida($tabela, $totalLinhas, $totalColunas, $erros)
{
    // Concatenando as linhas com " . " para quebra de linha no cod PHP (senão não funciona)"
    echo "<script>
        alert('Dados da tabela $tabela foram apagados.\\n" .
        "Total de linhas inseridas: $totalLinhas, Total de colunas: $totalColunas\\n" .
        "Total de linhas que apresentaram erro: *** $erros ***\\n" .
        ($erros === 0
            ? "Todas as informações carregadas com sucesso!\\n"
            : "Informações carregadas com sucesso!\\n" .
            "As informações de erros podem ser vistas no arquivo de log {$tabela}Log.txt") . "');
        window.location.href = '/portal/portal_phpcsv/index.php';
        </script>";
}

// Itera sobre todas as linhas da planilha
function iterarSobreLinhas($worksheet, $processarLinha, $conn, $tabela, &$totalLinhas, &$totalColunas, &$erros, &$errosDetalhados)
{
    // Itera sobre todas as linhas da planilha
    foreach ($worksheet->getRowIterator() as $indice => $row) {
        if ($indice > 1) { // não pegar o cabeçalho 
            $cellIterator = $row->getCellIterator();    // Itera sobre todas as células da linha
            $cellIterator->setIterateOnlyExistingCells(false); // Inclui células vazias

            // Inicializa a variável $dados para processar cada linha e obter os valores de cada célula.
            $dados = $processarLinha($cellIterator, $indice, $erros, $tabela, $errosDetalhados, $worksheet);

            // Insere os dados no banco de dados
            if (!inserirDados($conn, $tabela, $dados)) {
                $erros++;
            } else {
                $totalLinhas++;
                $totalColunas = max($totalColunas, count($dados));
            }   //  Fim do IF de inserção de dados
        }   //  Fim do IF de verificação de cabeçalho
    }   //  Fim do loop de iteração sobre as linhas
}

// Inclui a função para ordenar e gravar erros no log
include_once(__DIR__ . '/../logs/ordenarGravarErrosLog.php');

function capturarErrosToLog($errosDetalhados, $tabela, $totalLinhas, $totalColunas, $erros, $fileName, $metaProcess)
{
    // Determina o valor de $acao
    $acao = $metaProcess ? "inseridas" : "substituídas";  // Determina a ação a ser realizada

    // Esse bloco ordena e grava os erros no log
    ordenarGravarErrosLog($errosDetalhados, $tabela);   // Chamar a função para ordenar e gravar erros no log
    // Cria logs com as informações sobre a execução (a sequencia de impressão está invertida no log)
    $mensagemErros = "Total de linhas que apresentaram erro: $erros\n\n";
    criaLogs($tabela, $mensagemErros); // Chama a função de log    
    $mensagemFinal = "Total de linhas inseridas: $totalLinhas, Total de colunas: $totalColunas";
    criaLogs($tabela, $mensagemFinal); // Chama a função de log
    criaLogs($tabela, "Os dados da tabela $tabela foram $acao com sucesso!");
    if ($erros === 0) {
        $mensagemSucesso = "Todas as informações carregadas com sucesso!";
        criaLogs($tabela, $mensagemSucesso); // Chama a função de log
    }   //  Fim do IF de verificação de erros
    criaLogs($tabela, $fileName); // Chama a função de log
}

// Função genérica para exibir log de processamento no navegador
function exibirLogProcessamento($tabela, $totalLinhas, $totalColunas, $erros)
{
    echo "<p>Dados da tabela $tabela foram apagados.</p>";
    echo "<p>Total de linhas inseridas: $totalLinhas</p>";
    echo "<p>Total de colunas: $totalColunas</p>";
    echo "<p>Total de linhas que apresentaram erro: $erros</p>";
    echo "<p>Resultados do Processamento</p>";
    echo "<p>Total de linhas inseridas: $totalLinhas</p>";
    echo "<p>Total de colunas: $totalColunas</p>";
    echo "<p>Total de linhas que apresentaram erro: $erros</p>";
    echo "<p>Redirecionando em 10 segundos...</p>";
    echo "<script>
        setTimeout(function() {
            window.location.href = '../index.php';
        }, 10000);
    </script>";
}
