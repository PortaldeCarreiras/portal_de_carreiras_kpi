<?php
function ordenarGravarErrosLog(array $errosDetalhados, string $tabela)
{
    if (!empty($errosDetalhados)) {

        // Ordenar os erros por linha, considerando a estrutura da mensagem
        usort($errosDetalhados, function ($a, $b) {
            preg_match('/linha (\d+)/', $a, $matchesA);
            preg_match('/linha (\d+)/', $b, $matchesB);
            $linhaA = isset($matchesA[1]) ? (int)$matchesA[1] : PHP_INT_MAX;
            $linhaB = isset($matchesB[1]) ? (int)$matchesB[1] : PHP_INT_MAX;
            return $linhaB <=> $linhaA; // Ordem crescente
            // return $linhaA <=> $linhaB; // Alteração para ordem decrescente
        });


        $errosDetalhados = array_unique($errosDetalhados); // Remover duplicatas

        // Gravar os erros ordenados no arquivo de log
        foreach ($errosDetalhados as $erro) {
            criaLogs($tabela, $erro);
        }
    }
}
