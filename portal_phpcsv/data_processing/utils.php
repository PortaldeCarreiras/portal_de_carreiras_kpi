<?php
// Inclui a função para ordenar e gravar erros no log
include_once(__DIR__ . '/../logs/ordenarGravarErrosLog.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Adiciona uma coluna adicional a uma planilha com um cabeçalho e valores para cada linha.
 *
 * @param Spreadsheet $spreadsheet Objeto Spreadsheet carregado.
 * @param string $nomeColuna Nome da nova coluna (cabeçalho).
 * @param string $valorColuna Valor que será preenchido em todas as linhas da nova coluna.
 * @return void
 */
function addColumnAndFill(Spreadsheet $spreadsheet, $nomeColuna, $valorColuna) {
    // Obtém a aba ativa
    $worksheet = $spreadsheet->getActiveSheet();

    // Calcula a próxima coluna disponível
    $ultimaColuna = $worksheet->getHighestColumn(); // Última coluna usada, no formato 'A', 'B', etc.
    $novaColuna = ++$ultimaColuna; // Incrementa para obter a próxima coluna disponível (ex.: de 'Z' para 'AA')

    // Define o cabeçalho da nova coluna
    $worksheet->setCellValue($novaColuna . '1', $nomeColuna);

    // Obtém o número total de linhas
    $ultimaLinha = $worksheet->getHighestRow();

    // Preenche a nova coluna com o valor especificado
    for ($linha = 2; $linha <= $ultimaLinha; $linha++) {
        $worksheet->setCellValue($novaColuna . $linha, $valorColuna);
    }
}
function capturarErrosToLog($errosDetalhados, $tabela, $totalLinhas, $totalColunas, $erros, $fileName, $metaProcess = false){
    // Determina o valor de $acao
    $acao = $metaProcess ? "inseridas" : "substituídas";  // Determina a ação a ser realizada

    // Esse bloco ordena e grava os erros no log
    ordenarGravarErrosLog($errosDetalhados, $tabela);   // Chamar a função para ordenar e gravar erros no log
    // Cria logs com as informações sobre a execução (a sequencia de impressão está invertida no log)
    $mensagemErros = "Total de linhas que apresentaram erro: $erros" . ($metaProcess ? "\n\n" : "");
    criaLogs($tabela, $mensagemErros); // Chama a função de log    
    $mensagemFinal = "Total de linhas inseridas: $totalLinhas, Total de colunas: $totalColunas";
    criaLogs($tabela, $mensagemFinal); // Chama a função de log
    criaLogs($tabela, "Os dados da tabela $tabela foram $acao com sucesso!");
    if ($erros === 0) {
        $mensagemSucesso = "Todas as informações carregadas com sucesso!";
        criaLogs($tabela, $mensagemSucesso); // Chama a função de log
    }   //  Fim do IF de verificação de erros
    criaLogs($tabela, $fileName); // Chama a função de log
    registrarLogDepuracao("Erros capturados e registrados no log ($tabela).");
}

// Função para converter o nome do arquivo para camelCase
function converterParaCamelCase($string){
    $string = removerAcentos($string);
    $string = preg_replace('/[^a-zA-Z0-9]/', ' ', $string);
    $string = ucwords(strtolower($string));
    $string = str_replace(' ', '', $string);
    return lcfirst($string);
}

// Função para exibir um alerta e redirecionar
function exibirAlertaERedirecionar($mensagem){
    echo "<script>alert('$mensagem'); window.location.href = 'index.php';</script>";
    exit();
}

// Função para exibir mensagem resumida no navegador
function exibirMensagemResumida($tabela, $totalLinhas, $totalColunas, $erros, $metaProcess = false){
    registrarLogDepuracao("Exibindo mensagem resumida no navegador ($tabela).");
    // Condicional PHP com o uso de operador ternário
    // Concatenando as linhas com " . " para quebra de linha no cod PHP (senão não funciona)"
    $aux = $metaProcess ? "
        alert('Total de linhas inseridas: $totalLinhas, Total de colunas: $totalColunas\\n" .
        "Total de linhas que apresentaram erro: *** $erros ***\\n" .
        ($erros === 0
            ? "Todas as informações carregadas com sucesso!\\n"
            : "Informações carregadas com sucesso!\\n" .
            "As informações de erros podem ser vistas no arquivo de log {$tabela}Log.txt") . "');
        window.location.href = '/portal/portal_phpcsv/index.php';
        " : "
        alert('Dados da tabela $tabela foram apagados.\\n" .
        "Total de linhas inseridas: $totalLinhas, Total de colunas: $totalColunas\\n" .
        "Total de linhas que apresentaram erro: *** $erros ***\\n" .
        ($erros === 0
            ? "Todas as informações carregadas com sucesso!\\n"
            : "Informações carregadas com sucesso!\\n" .
            "As informações de erros podem ser vistas no arquivo de log {$tabela}Log.txt") . "');
        window.location.href = '/portal/portal_phpcsv/index.php';
        ";

    echo "<script>$aux</script>";
    registrarLogDepuracao("Mensagem resumida exibida ($tabela).\n");
}

// Função genérica para exibir log de processamento no navegador
function exibirLogProcessamento($tabela, $totalLinhas, $totalColunas, $erros){
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

// Itera sobre todas as linhas da planilha
// Variável global para o contador de inserções bem-sucedidas
$contadorInsercoes = 0;
function iterarSobreLinhas($worksheet, $processarLinha, $conn, $tabela, &$totalLinhas, &$totalColunas, 
                            &$erros, &$errosDetalhados, $metaProcess = false){
    global $contadorInsercoes; // Torna o contador acessível dentro da função 
    // Itera sobre todas as linhas da planilha
    registrarLogDepuracao("Iniciando iteração sobre as linhas da planilha.");
    foreach ($worksheet->getRowIterator() as $indice => $row) {
        if ($indice > 1) { // não pegar o cabeçalho 
            $cellIterator = $row->getCellIterator();    // Itera sobre todas as células da linha
            $cellIterator->setIterateOnlyExistingCells(false); // Inclui células vazias
            // Inicializa a variável $dados para processar cada linha e obter os valores de cada célula.
            $dados = $processarLinha($cellIterator, $indice, $erros, $tabela, $errosDetalhados, $worksheet);
            // Insere os dados no banco de dados
            if (!inserirDados($conn, $tabela, $dados)) {
                $erros++;
                // registrarLogDepuracao("Erro ao inserir linha $indice na tabela $tabela.");
            } else {
                $contadorInsercoes++;   // Incrementa o contador de inserções bem-sucedidas
                // LogDepuração das primeiras 5 inserções de cada lote de 1000
                if ($contadorInsercoes <= 5 || $contadorInsercoes % 1000 < 5) {
                    registrarLogDepuracao("Tabela $tabela Linha $indice processada: " . json_encode($dados));
                }
                $totalLinhas++;
                $totalColunas = max($totalColunas, count($dados));
            }   //  Fim do IF de inserção de dados
        }   //  Fim do IF de verificação de cabeçalho
    }   //  Fim do loop de iteração sobre as linhas
    registrarLogDepuracao("Iteração sobre as linhas finalizada. Total de linhas processadas: $totalLinhas. Total de erros: $erros.");
}

// Função para normalizar o nome do arquivo
function normalizarNomeArquivo($nomeArquivo){
    $nomeArquivo = removerAcentos($nomeArquivo);
    $nomeArquivo = str_replace(' ', '', $nomeArquivo); // Remove espaços em branco
    return strtolower($nomeArquivo);
}

// Função para obter a data de criação do arquivo (Data original da planilha)
// Captura o valor do timestamp obtido do Metadado e converte o timestamp para o formato desejado
function obterDataOriArquivo($dataModificacao = null) {
    $timestamp = isset($dataModificacao) ? intval($dataModificacao) : time();
    return date('Y-m-d H:i:s', $timestamp / 1000); // Formata e retorna a data, divide por 1000 para converter de milissegundos para segundos
}

// Função para registrar log de depuração
function registrarLogDepuracao($mensagem){
    $arquivoLog = __DIR__ . '/../logs/log_depuracao.txt';
    $hora = date('Y-m-d H:i:s');
    // Verifica se o diretório existe
    if (!file_exists(dirname($arquivoLog))) {
        mkdir(dirname($arquivoLog), 0755, true); // Cria o diretório se não existir
    }
    // Tenta registrar a mensagem no log
    if (file_put_contents($arquivoLog, "[$hora] $mensagem\n", FILE_APPEND) === false) {
        error_log("Erro ao escrever no arquivo de log: $arquivoLog");
    }
}

// Função para converter caracteres acentuados para seus equivalentes sem acento
function removerAcentos($string){
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

// Função para verificar se a extensão do arquivo é permitida
function verificarExtensaoArquivo($extensaoArquivo, $extensoesPermitidas){
    return in_array(strtolower($extensaoArquivo), $extensoesPermitidas);
}

// Função para verificar se o nome do arquivo é permitido
function verificarNomeArquivo($nomeArquivo, $nomesPermitidos){
    $nomeArquivoNormalizado = normalizarNomeArquivo(pathinfo($nomeArquivo, PATHINFO_FILENAME));
    return in_array($nomeArquivoNormalizado, $nomesPermitidos);
}
