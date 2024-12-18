// Função para capturar o nome do arquivo selecionado no dropdown
function capturarNomeArquivoSelDropdown() {
    const dropdown = document.getElementById("dropdownArquivos");
    const selectedOption = dropdown.options[dropdown.selectedIndex];
    const fileName = selectedOption ? selectedOption.value : "";

    // Atualiza o campo hidden no formulário
    const hiddenInput = document.querySelector("input[name='nomeArquivoSelDrop']");
    if (hiddenInput) {
        hiddenInput.value = fileName;
    }
}