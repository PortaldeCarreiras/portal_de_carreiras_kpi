<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Arquivos e Metadados</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- CSS para colocar os metadados a direita do Botão Escolher Arquivo -->
    <style>
        .form-group {
            display: flex;
            align-items: center;
        }

        #metadados {
            margin-left: 20px;
            /* Distância entre o botão e os metadados */
            max-width: 400px;
            /* Limita a largura da área dos metadados */
        }

        ul {
            padding-left: 0;
            list-style-type: none;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-danger">Upload de Arquivos</h1>
        <p>Selecione um arquivo para fazer o upload e obter seus metadados.</p>

        <form action="" method="post" enctype="multipart/form-data" id="uploadForm">
            <div class="form-group">
                <!-- Botão de seleção de arquivo -->
                <input type="file" id="arquivo" name="arquivo_xls" class="btn btn-success" onchange="mostrarMetadados()">
                <!-- Espaço onde os metadados serão exibidos -->
                <div id="metadados" class="mt-3"></div>
            </div>


            <!-- Campos ocultos para armazenar os metadados -->
            <input type="hidden" id="fileName" name="fileName">
            <input type="hidden" id="fileSize" name="fileSize">
            <input type="hidden" id="fileType" name="fileType">
            <input type="hidden" id="fileModified" name="fileModified">


            <div>
                <input type="submit" value="Enviar" class="btn btn-success mt-3">
            </div>
        </form>


        <script>
            function mostrarMetadados() {
                var input = document.getElementById('arquivo');
                var file = input.files[0];

                if (file) {
                    var modificacao = new Date(file.lastModified);
                    var tamanho = file.size;
                    var nome = file.name;
                    var tipo = file.type;

                    // Formatação HTML para exibição dos Metadados
                    var metadados = `
                        <h2>Metadados do Arquivo</h2>
                        <ul>
                            <li><strong>Nome do Arquivo:</strong> ${nome}</li>
                            <li><strong>Tipo MIME:</strong> ${tipo}</li>
                            <li><strong>Tamanho:</strong> ${tamanho} bytes</li>
                            <li><strong>Data de Modificação Original:</strong> ${modificacao.toLocaleString()}</li>
                        </ul>
                    `;

                    document.getElementById('metadados').innerHTML = metadados;

                    // Definir os valores dos campos ocultos para enviar os metadados ao servidor
                    document.getElementById('fileName').value = nome;
                    document.getElementById('fileSize').value = tamanho;
                    document.getElementById('fileType').value = tipo;
                    document.getElementById('fileModified').value = modificacao.toISOString(); // Formato ISO
                }
            }
        </script>
    </div>
</body>

</html>