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