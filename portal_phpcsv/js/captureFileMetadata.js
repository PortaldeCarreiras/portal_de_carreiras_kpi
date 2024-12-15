// Função para obter os metadados do arquivo
function obterMetadadosArquivo() {
    const arquivo = document.getElementById("arquivo").files[0];
    if (arquivo) {
        // Obtém os metadados do arquivo
        const nomeArquivo = arquivo.name;
        const tipoMime = arquivo.type;
        const tamanho = arquivo.size;

        // Envia o timestamp exato de "lastModified"
        const dataModificacao = arquivo.lastModified;   // Timestamp em milissegundos

        // Atualiza os campos hidden com os metadados do arquivo
        document.getElementById("nomeArquivo").value = nomeArquivo;
        document.getElementById("tipoMime").value = tipoMime;
        document.getElementById("tamanho").value = tamanho;
        document.getElementById("dataModificacao").value = dataModificacao;

        // Exibe os metadados no console para depuração
        console.log("Nome do Arquivo:", nomeArquivo); // Adiciona um log no console para verificar o nome do arquivo
        console.log("Tipo MIME:", tipoMime); // Adiciona um log no console para verificar o tipo MIME
        console.log("Tamanho:", tamanho); // Adiciona um log no console para verificar o tamanho
        console.log("Data de Modificação (formato milisegundos):", dataModificacao); // Adiciona um log no console para verificar a data
        console.log("Data de Modificação  (formato legível-date):", new Date(dataModificacao).toLocaleString());                
        // Chamando diretamente o método toLocaleString() de um objeto Date no JavaScript. Nesse contexto não é necessário usar ${} para interpolação

        // Exibe os metadados no HTML (opcional)
        document.getElementById("metadadosArquivo").innerHTML = `
            <p>Nome do Arquivo: ${nomeArquivo}</p>
            <p>Tipo MIME: ${tipoMime}</p>
            <p>Tamanho: ${tamanho} bytes</p>
            <p>Data de Modificação Original: ${new Date(dataModificacao).toLocaleString()}</p>
        `; // Está usando template literals do JavaScript (marcados por ${} dentro de uma string delimitada por crases ` `)
    }
}