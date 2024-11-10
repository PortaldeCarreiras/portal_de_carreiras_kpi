<?php
session_start();
require 'vendor/autoload.php';
include 'conn.php';
$id = $_SESSION["id"];

$htmlRel = " <h1> Relatório de Tarefas </h1>  <hr>";

$sql = "SELECT * FROM tab_tarefas WHERE id_usuario ='$id'";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $dataf = new DateTime($row["data_tarefa"]);
    $htmlRel .= "Tarefa : " . $row["nome_tarefa"] . " - ";
    $htmlRel .= "Data : " . $dataf->format('d-m-Y') . "<br><hr>";
}


// Instancia a classe
use Dompdf\Dompdf;
$dompdf = new Dompdf();

$dompdf->loadHtml($htmlRel);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Gera o PDF
//$dompdf->stream();
$dompdf->stream(
    "saida.pdf", // Nome do arquivo de saída 
    array(
        "Attachment" => false // Para download, altere para true 
    )
);

?>

