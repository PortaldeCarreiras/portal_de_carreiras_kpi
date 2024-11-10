<?php
include 'conn.php'; // Inclui a conexão com o banco de dados

// Teste da conexão
if (!$conn) {
    die("Conexão falhou: " . mysqli_connect_error());
} else {
    echo "Conexão bem-sucedida!<br>";
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicializa a variável para mensagens
$message = '';

// // TESTE Inserção de dados estáticos
// $static_nome = 'teste estatico';
// $static_cargo = 3030;

// $static_sql = "INSERT INTO pasta1 (nome, cargo) VALUES ('$static_nome', $static_cargo)";
// if (mysqli_query($conn, $static_sql)) {
//     $message .= "Inserido estático: $static_nome, Cargo: $static_cargo<br>";
// } else {
//     $message .= "Erro ao inserir dados estáticos: " . mysqli_error($conn) . "<br>";
// }   // fim Inserção de dados estáticos

// Determina o separador adequado (parte)
// Função para detectar o separador de CSV
function detectDelimiter($line) {
    $delimiters = [",", ";", "\t"];
    $counts = [];

    foreach ($delimiters as $delimiter) {
        $counts[$delimiter] = count(str_getcsv($line, $delimiter));
    }

    // Retorna o delimitador que gerou o maior número de colunas
    return array_search(max($counts), $counts);
}   // Fim da Função para detectar o separador de CSV


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    // Verifica se o arquivo foi enviado e é um CSV
    if (is_uploaded_file($file)) {
        $handle = fopen($file, 'r');

        // Determina o separador adequado
        $line = fgets($handle);
        $delimiter = detectDelimiter($line);
        rewind($handle); // Volta ao início do arquivo para leitura
        // Fim de Determina o separador adequado


        // Exibe o delimitador detectado
        echo "Delimitador detectado: " . htmlspecialchars($delimiter) . "<br>";


        // Lê o cabeçalho
        // $header = fgetcsv($handle);  // SEPARADOR PADRÃO ","
        // tentará ler até 1000 bytes por linha 
        $header = fgetcsv($handle, 1000, $delimiter);  // SEPARADOR SELECIONADO pela Função detectDelimiter()

        
        // Lê as linhas restantes do arquivo
        // while (($data = fgetcsv($handle)) !== FALSE) {   // SEPARADOR PADRÃO ","
        // tentará ler até 1000 bytes por linha 
        while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {   // SEPARADOR SELECIONADO pela Função detectDelimiter()
            if (count($data) < 2) {
                continue; // Ignora linhas inválidas
            }

            $nome = mysqli_real_escape_string($conn, trim($data[0]));
            $cargo = intval(trim($data[1]));

            // Mensagem de depuração
            $message .= "Lendo: Nome: $nome, Cargo: $cargo<br>";

            // Insere os dados na tabela
            $sql = "INSERT INTO pasta1 (nome, cargo) VALUES ('$nome', $cargo)";
            if (mysqli_query($conn, $sql)) {
                $message .= "Inserido: $nome, Cargo: $cargo<br>";
            } else {
                echo "Erro ao inserir dados: " . mysqli_error($conn) . "<br>";
                $message .= "Erro ao inserir dados: " . mysqli_error($conn) . "<br>";
            }
        }

        fclose($handle);
        $message .= "Dados importados com sucesso!";
        
        // Redireciona para evitar a reenvio do formulário
        header("Location: " . $_SERVER['PHP_SELF']);
        exit; // Certifique-se de usar exit após header

        // Usando uma Variável de Sessão, para manter o estado da operação.
        // Você pode armazenar uma variável de sessão que indique que os dados já foram importados.
        // $_SESSION['importado'] = true; // Marca como importado

    } else {
        $message .= "Erro ao enviar o arquivo.";
    }
}

// Consulta os dados do banco de dados
$result = mysqli_query($conn, "SELECT * FROM pasta1");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Upload de CSV</title>
</head>
<body>
    <h1>Importar CSV</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="file" name="csv_file" accept=".csv" required>
        <input type="submit" value="Importar">
    </form>

    <div>
        <h2><?php echo $message; ?></h2>
    </div>

    
    <h2>Dados Importados da Tabela pasta1</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Cargo</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['nome']; ?></td>
                <td><?php echo $row['cargo']; ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
