<?php
date_default_timezone_set('America/Sao_Paulo');

$localServidor = "localhost";
$user = "root";
$senha = "";
$banco = "banco";

$conn = mysqli_connect($localServidor,$user,$senha,$banco);

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}


/* Perguntas
Por que as vezes vezes não temos a tag  de fechamento do PHP?
Como faço pra guardar o arquivo no BD também?
Tem diferenças entre XLS e XLSX na hora de carregar ou posso carregar os dois com XLSX?
O que é melhor?
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
    $spreadsheet = $reader->load($arquivo);
    ou
    $spreadsheet = IOFactory::load($arquivo);

Ainda preciso associar o botão enviar a função correspondente, certo?

Esse código tem essa "," antes de "]"?
            // Cria um array com os dados a serem inseridos         linha 147 index.php
            $dados = [
                'empresa_estagio' => $data[0],
                'aluno_codigo' => $dados_aluno['aluno_codigo'],
                'aluno_ra' => $dados_aluno['aluno_ra'],
                'aluno_nome' => $dados_aluno['aluno_nome'],
                'aluno_eixo' => $dados_aluno['aluno_eixo'],
                'aluno_periodo' => $dados_aluno['aluno_periodo'],
                'aluno_categoria' => $dados_aluno['aluno_categoria'],
                'aluno_data' => $dados_aluno['aluno_data'],
                'mes_acesso' => (int)date('m'),
                'ano_acesso' => (int)date('Y'),
                'numero_acessos' => 0, // Pode ser ajustado conforme necessário
                'data' => date('Y-m-d H:i:s'),
            ];
UM ARQUIVO ATUALIZADO SERÁ UPLOADED PERIODICAMENTE, O QUE FAZER?
        DELETAR OS DADOS ANTERIORES
        FAZER O UPLOAD APENAS DAS LINHAS ADICIONAIS(COMPARAR COM QUE?)

Como faço para usar as conexões com o BD?
quando abrir uma sessão e quando feixa-lá?

Qual a posição da funtion, ela deve ficar sempre antes de onde será utilizada no PHP?

Terei problemas com arquivos nomeados com acentos?
    Consulta de Vagas de estágio.xls

Devo converter para data?
    'aluno_data' => $matches[7]              // Data    linha 149 index.php
    dessa forma ->  "=> \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($matches[7])->format('Y-m-d')"
*/