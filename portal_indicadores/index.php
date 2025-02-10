<?php
session_start();
include('conn.php');

//Finalisar tarefa
if (isset($_GET["fntarefa"])) {
  $idTarFn = $_GET["fntarefa"];
  $sql = "UPDATE tab_tarefas SET status_tarefa='1' WHERE id='$idTarFn'";

  if (mysqli_query($conn, $sql)) {
    header('location:index.php');
    exit();
  } else {
    header('location:index.php?err=fn');
    exit();
  }
}

//Excluir tarefa
if (isset($_GET["excluir"])) {
  $idTarefaExc = $_GET["excluir"];
  $sql = "DELETE FROM tab_tarefas WHERE id='$idTarefaExc'";

  if (mysqli_query($conn, $sql)) {
    header('location:index.php?msg=2');
    exit();
  } else {
    header('location:index.php?msg=errexc');
    exit();
  }
}


//Verifica a sessão se o usuario está logado
if (empty($_SESSION["logado"]) || $_SESSION["logado"] != true) {
  header('location:./login/login.php');
  exit();
}

//Atualizar
if (isset($_GET["atualizar"])) {
  if (!empty($_GET["tarefaUp"]) && !empty($_GET["descricaoUp"]) && !empty($_GET["datatarefaUp"])) {
    $tarefa = $_GET["tarefaUp"];
    $descricao = $_GET["descricaoUp"];
    $dataTarefa = $_GET["datatarefaUp"];
    $id = $_GET["idtarefaUp"];
    $prior = $_GET["prioridadeUp"];

    $sql = "UPDATE tab_tarefas SET nome_tarefa ='$tarefa', 
    desc_tarefa='$descricao', data_tarefa='$dataTarefa',prioridade='$prior'  
    WHERE id='$id'";

    if (mysqli_query($conn, $sql)) {
      header('location:index.php?msg=1');
      exit();
    } else {
      echo "<script>alert('Erro!!!')</script>";
    }
  } else {
    echo "<script>alert('Preencha todos os campos!!!')</script>";
  }
}

//Paginação - 
$tarefasf = (!empty($_GET["tarefasf"]) ? 1 : 0);
$vbuscar = (isset($_GET["txtbuscarind"]) ? $_GET["txtbuscarind"] : "");
$valor = (isset($_GET["btnbuscar"]) ? $_GET["txtbuscar"] : $vbuscar);
if (isset($_GET["btnbuscar"])) {
  $id = $_SESSION["id"];
  $sqlSelect = "SELECT t.id, t.nome_tarefa,t.desc_tarefa,
  t.data_tarefa, t.id_usuario,u.usuario, t.prioridade
  FROM tab_tarefas as t 
  inner join tab_usuarios as u 
  where t.id_usuario = u.id and t.id_usuario ='$id' and data_tarefa LIKE '$valor%'";
} else {
  $id = $_SESSION["id"];
  $dataAtual = date('Y-m-d');
  $sqlSelect = "SELECT t.id, t.nome_tarefa,t.desc_tarefa,
  t.data_tarefa, t.id_usuario,u.usuario,t.prioridade, t.status_tarefa
  FROM tab_tarefas as t 
  inner join tab_usuarios as u 
  where t.id_usuario = u.id and t.id_usuario ='$id' and t.status_tarefa='$tarefasf' and data_tarefa LIKE '$valor%'";
}

$result = mysqli_query($conn, $sqlSelect);
$quantReg = mysqli_num_rows($result);
$pag = (isset($_GET["pagina"]) ? $_GET["pagina"] : 1);
$quant_por_pag = 6;
$numero_de_pag = ceil($quantReg / $quant_por_pag);
$inicio = ($pag * $quant_por_pag) - $quant_por_pag;
$sqlPaginacao = "SELECT t.id, t.nome_tarefa,t.desc_tarefa,
t.data_tarefa, t.id_usuario,u.usuario,t.prioridade, t.status_tarefa
FROM tab_tarefas as t 
inner join tab_usuarios as u 
where t.id_usuario = u.id and 
t.id_usuario ='$id' 
and data_tarefa LIKE '$valor%'
and t.status_tarefa='$tarefasf'
Order by t.data_tarefa
 limit $inicio,$quant_por_pag";
$result = mysqli_query($conn, $sqlPaginacao);


//Cadastrar -
if (isset($_GET["cadastrar"])) {
  if (!empty($_GET["tarefa"]) && !empty($_GET["descricao"]) && !empty($_GET["datatarefa"])) {
    $tarefa = $_GET["tarefa"];
    $descricao = $_GET["descricao"];
    $dataTarefa = $_GET["datatarefa"];
    $prior = $_GET["prioridade"];

    $sql = "INSERT INTO tab_tarefas(nome_tarefa,desc_tarefa,data_tarefa,id_usuario,prioridade) 
      VALUES ('$tarefa','$descricao','$dataTarefa','$id','$prior')";

    if (mysqli_query($conn, $sql)) {
      echo "<script>alert('Cadastro Realizado!!!')</script>";
      header('location:index.php');
      exit();
    } else {
      echo "<script>alert('Erro!!!')</script>";
    }
  } else {
    echo "<script>alert('Preencha todos os campos!!!')</script>";
  }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Indicadores</title>
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
  <link rel="icon" href="./img/favicon.png" type="image/png">
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
  <script src="./js/jquery-3.7.1.slim.min.js"></script>
</head>

<body class="sb-nav-fixed">
  <?php include("estrutura/menu_superior.php") ?>

  <!-- Sidenave Nave Lateral com Menu -->
  <div id="layoutSidenav">
    <div id="layoutSidenav_nav">
      <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu modal-draggable">
          <div class="nav">
            <div class="sb-sidenav-menu-heading">Menu</div>
            <a class="nav-link" href="index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
              Home
            </a>
            <div class="sb-sidenav-menu-heading"></div>
            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
              <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
              <span>Configurações</span>
              <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
              <nav class="sb-sidenav-menu-nested nav">
                <a class="nav-link" href="" data-bs-toggle="modal" data-bs-target="#uploadModal">Upload - Selecione Arquivo</a>
                <a class="nav-link" href="index.php?tarefasf=1">Filtrar Informações</a>
                <a class="nav-link" href="relatorio.php">Gerar Relatório</a>
              </nav>
            </div>
          </div>
        </div>
      </nav>
    </div>


    <div id="layoutSidenav_content">
      <main>
        <div class="container-fluid px-4">
          <!-- Título do container principal dentro do Body (Formulário) -->
          <h1 class="mt-4">Indicadores - Portal de Carreiras</h1>
          <ol class="breadcrumb mb-4">
          </ol>

          <div class="row">
            <div class="col-xl-2 col-md-3">
              <!-- <div class="card bs-body-color text-white mb-4">
                <span class="text-center align-text-bottom">Upload Arquivos</span>
                <button type="button" class="btn btn-success btn-sm" id="btn_entrada" data-bs-toggle="modal" data-bs-target="#uploadModal" data-bs-whatever="@mdo">Selecione Arquivo &nbsp;&nbsp;<i class="fa fa-plus"></i></button>
              </div> -->

              <!-- Botão de Upload e Obtenção dos dados -->
              <div>
                <form action="upload.php" method="POST" enctype="multipart/form-data">
                  <span class="text-center align-text-bottom">&nbsp;&nbsp;&nbsp;&nbsp;Upload Arquivos</span>
                  <input type="file" id="arquivo" name="arquivo_xls" class="btn btn-success">
                  <input type="submit" value="Enviar" class="btn btn-success">
                </form>

                <script>
                  // Verifica se o arquivo selecionado é XLS ou CSV
                  document.getElementById('arquivo').addEventListener('change', function() {
                    const filePath = this.value;
                    const allowedExtensions = /(.xls|.csv)$/i;
                    if (!allowedExtensions.exec(filePath)) {
                      alert('Por favor, selecione um arquivo XLS ou CSV.');
                      this.value = '';
                    }
                  });
                </script>

                <?php

                require 'vendor/autoload.php'; // Inclui a biblioteca PhpSpreadsheet

                use PhpOffice\PhpSpreadsheet\Reader\Xls;
                use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
                use PhpOffice\PhpSpreadsheet\Reader\Csv;
                use PhpOffice\PhpSpreadsheet\Cell\DataType;

                // ... (código para conectar ao banco de dados) se não estiver aberta a sessão

                // Verificar se o arquivo foi enviado
                if (isset($_FILES['arquivo_xls']) && $_FILES['arquivo_xls']['error'] == 0) {
                  $arquivo = $_FILES['arquivo_xls']['tmp_name'];
                  $extensao = pathinfo($arquivo, PATHINFO_EXTENSION);

                  // Verificar se o arquivo é XLS, XLSX ou CSV e usar a biblioteca apropriada
                  // Criando um leitor baseado na extensão do arquivo
                  switch ($extensao) {
                    case 'xlsx':
                      $reader = new Xlsx();
                      break;
                    case 'xls':
                      $reader = new Xls();
                      break;
                    case 'csv':
                      $reader = new Csv();
                      break;
                    default:
                      die("Formato de arquivo inválido.");
                  }

                  // Carrega o arquivo
                  $spreadsheet = $reader->load($arquivo);

                  // Acessa a primeira planilha
                  $worksheet = $spreadsheet->getActiveSheet();

                  // Percorre as linhas da planilha
                  foreach ($worksheet->getRowIterator() as $row) {
                    $row_index = $row->getRowIndex();

                    // Pula a primeira linha (cabeçalho)
                    if ($row_index == 1) {
                      continue;
                    }

                    // $cellIterator = $row->getCellIterator();
                    // $cellIterator->setFormat(DataType::TYPE_STRING);
                    // $cellIterator->setFormat(PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                    // // Convertendo os tipos de dados de acordo com a tebela do BD
                    // $codigo = (int) $cellIterator->current()->getValue();
                    // $portal = (string) $cellIterator->next()->getValue();
                    // $mes_acesso = (int) $cellIterator->next()->getValue();
                    // $ano_acesso = (int) $cellIterator->next()->getValue();
                    // $numero_acessos = (int) $cellIterator->next()->getValue();

                    // Insere os dados no banco de dados
                    $sql = "INSERT INTO acesso_portal (codigo, portal, mes_acesso, ano_acesso, numero_acessos) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sisii", $codigo, $portal, $mes_acesso, $ano_acesso, $numero_acessos);
                    $stmt->execute();



                //     $sql = "INSERT INTO acesso_portal (codigo, portal, mes_acesso, ano_acesso, numero_acessos) 
                //             VALUES (?, ?, ?, ?, ?)";
                //     if ($conn->query($sql) === TRUE) {
                //       echo "Novo registro criado com sucesso";
                //     } else {
                //       echo "Error: " . $sql . "<br>" . $conn->error;
                //     }
                   }
                } else {
                  echo "Erro ao fazer upload do arquivo.";
                }
                ?>
              </div>


              <div>
                <?php if ($tarefasf == 1) { ?>
                  <div class="card bg-danger text-white mb-4">
                    <button onclick="home()" type="button" class="btn btn-danger" id="">Voltar<i class="fa fa-plus"></i></button>
                  </div>
                <?php } ?>
                <script>
                  function home() {
                    location.href = "index.php";
                  }
                </script>
              </div>
            </div>
          </div>

          <!-- Tabela Seleciona as tarefas - Cabeçalho do formulário  -->
          <table class="table table-hover">
            <thead>
              <tr class="col-12">
                <th scope="col-2">Filtrar</th>
                <th scope="col-2">Excluir</th>
                <th scope="col-2">Nome do Indicador</th>
                <th scope="col-4">Descrição</th>
                <th scope="col-4">Período</th>
                <th scope="col-4">Visualização Gráfica</th>
                <th scope="col-2">Status</th>
              </tr>
            </thead>
            <tbody>

              <?php
              $dataAtual = new DateTime('now'); //pega a data atual
              $dataAtualFormat = $dataAtual->format('Y-m-d'); //formata a data atual
              while ($linha = mysqli_fetch_assoc($result)) { //percorre todos os registros do banco $result
                $modalfinalizar = "fin" . $linha["id"]; //Cria um nome para o Modal Finalizar Tarefa
                $modalAtualizar = "atu" . $linha["id"]; //Cria um nome para o Modal Atualizar Tarefa
                $modalExcluir = "exc" . $linha["id"]; //Cria um nome para o Modal Excluir Tarefa
                $dataBanco = new DateTime($linha["data_tarefa"]); //Pega a data da tarefa no banco e cria um Objeto Datetime
                $dataBancolFormat = $dataBanco->format('Y-m-d'); //formata a data do banco para Ano-Mes-Dia
                $dataExibir = $dataBanco->format('d-m-Y H:i'); //formata a data para exibir na tabela dia-mês-ano Hora:minuto
              ?>
                <tr class="<?php //if verifica se a data da tarefa está vencida/ se está no dia/ ou se ainda irá vencer e muda as cores de exibição da linha na tabela
                            if ($dataAtualFormat > $dataBancolFormat) {
                              echo "bg-danger bg-opacity-25";
                            } else if ($dataAtualFormat == $dataBancolFormat) {
                              echo "bg-warning bg-opacity-25";
                            } else {
                              echo "bg-success bg-opacity-25";
                            } ?>">
                  <th style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#<?= $modalAtualizar //nome do modal que ele irá chamar 
                                                                                      ?>">
                    <i class="fa-solid fa-pen" style="color: #247a00;"></i>
                  </th>
                  <th style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#<?= $modalExcluir //nome do modal que ele irá chamar
                                                                                      ?>">
                    <i class="fa-solid fa-trash" style="color: #902a00;"></i>
                  </th>
                  <td><?= $linha["nome_tarefa"] ?></td>
                  <td><?= $linha["desc_tarefa"] ?></td>
                  <td><?= $dataExibir ?></td>
                  <td><?php
                      //verifica a prioridade da tarefa e escreve na tabela (ex. Baixa,Alta)
                      if ($linha["prioridade"] == 1) {
                        echo "Baixa";
                      } else if ($linha["prioridade"] == 2) {
                        echo "Média";
                      } else {
                        echo "Alta";
                      } ?></td>
                  <td>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" data-bs-toggle="modal" data-bs-target="#<?= $modalfinalizar //nome do modal que ele irá chamar 
                                                                                                              ?>" role="switch" id="flexSwitchCheckChecked">
                      <label class="form-check-label" for="flexSwitchCheckChecked">Finalizar</label>
                    </div>
                  </td>
                </tr>

                <div class="modal fade" id="<?= $modalfinalizar //define um nome dinamico para o modal 
                                            ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Finalizar Tarefa</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        Deseja finalizar esta tarefa?
                        <?= $linha["id"] ?>
                      </div>
                      <div class="modal-footer">
                        <!-- Chama a função Resetar() no evento de Click  - onClick -->
                        <button type="button" class="btn btn-secondary" onclick="Resetar()" id="btnnaofinalizar" data-bs-dismiss="modal">Fechar</button>
                        <a href="index.php?fntarefa=<?= $linha["id"] ?>">
                          <button type="button" class="btn btn-primary">Sim</button>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="modal fade text-dark" id="<?= $modalAtualizar //define um nome dinamico para o modal 
                                                      ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog text-dark">
                    <div class="modal-content text-dark">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Editar Tarefa</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body text-dark">
                        <form class="form-group text-white">
                          <div class="mb-3">
                            <label class="form-label text-dark">ID da Tarefa</label>
                            <input type="text" class="form-control" readonly name="idtarefaUp" value="<?= $linha["id"] ?>">
                          </div>
                          <div class="mb-3">
                            <label class="form-label text-dark">Nome da Tarefa</label>
                            <input type="text" class="form-control" name="tarefaUp" value="<?= $linha["nome_tarefa"] ?>">
                          </div>
                          <div class="mb-3">
                            <label class="form-label text-dark">Descrição da tarefa</label> <textarea class="form-control" name="descricaoUp" rows="3"> <?= $linha["desc_tarefa"] ?></textarea>
                          </div>
                          <div class="row">
                            <div class="mb-3 col-6">
                              <label class="form-label text-dark">Data / Prazo</label>
                              <input type="datetime-local" value="<?= $linha["data_tarefa"] ?>" class="form-control" name="datatarefaUp">
                            </div>
                            <div class="mb-3 col-6">
                              <label class="form-label text-dark">Prioridade</label>
                              <select name="prioridadeUp" class="form-select">
                                <option value="1" <?php if ($linha["prioridade"] == 1) echo "selected"; ?>>Baixa</option>
                                <option value="2" <?php if ($linha["prioridade"] == 2) echo "selected"; ?>>Média</option>
                                <option value="3" <?php if ($linha["prioridade"] == 3) echo "selected"; ?>>Alta</option>
                              </select>
                            </div>

                          </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" name="atualizar" value="Atualizar Tarefa" class="btn btn-primary">Salvar</button>
                      </div>
                      </form>
                    </div>
                  </div>
                </div>

                <div class="modal fade text-dark" id="<?= $modalExcluir //define um nome dinamico para o modal 
                                                      ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Excluir Tarefa</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        Deseja excluir mesmo?
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Não</button>
                        <a href="index.php?excluir=<?= $linha["id"] ?>">
                          <input type="submit" value="Sim" name="excluir" class="btn btn-danger text-white">
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

              <?php } ?>

            </tbody>
          </table>

          <div class="container mt-5">
            <nav aria-label="Page navigation example">
              <ul class="pagination justify-content-center">
                <li class="page-item">
                  <?php
                  //Paginação
                  $pagAnt = $pag - 1;
                  $pagPos = $pag + 1;

                  if ($pagAnt != 0) {
                  ?>
                    <a class="page-link bg-dark text-white" href="index.php?pagina=<?= $pagAnt ?>&tarefasf=<?= $tarefasf ?>">
                      <span aria-hidden="true">&laquo;</span>
                    </a>
                  <?php } else { ?>
                    <a class="page-link bg-dark text-white">
                      <span aria-hidden="true">&laquo;</span>
                    </a>
                  <?php } ?>
                </li>

                <?php for ($i = 1; $i <= $numero_de_pag; $i++) { ?>

                  <li class="page-item <?php if ($i == $pag) echo "active" ?>">
                    <a class="page-link bg-dark text-white" href="index.php?pagina=<?= $i ?>&tarefasf=<?= $tarefasf ?>&txtbuscarind=<?= $valor ?>">
                      <?= $i ?>
                    </a>
                  </li>

                <?php } ?>

                <li class="page-item">
                  <?php if ($pagPos <= $numero_de_pag) {  ?>
                    <a class="page-link bg-dark text-white" href="index.php?pagina=<?= $pagPos ?>&tarefasf=<?= $tarefasf ?>">
                      <span aria-hidden="true">&raquo;</span>
                    </a>
                  <?php } else {  ?>
                    <a class="page-link bg-dark text-white">
                      <span aria-hidden="true">&raquo;</span>
                    </a>
                  <?php  } ?>
                </li>
              </ul>
            </nav>
          </div>

      </main>
      <footer class="py-4 bg-light mt-auto">
        <div class="container-fluid px-4">
          <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">Copyright &copy; Website 4</div>

          </div>
        </div>
      </footer>




      <div class="modal fade text-dark" id="uploadModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog text-dark">
          <div class="modal-content text-dark">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">Nova Tarefa</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-dark">
              <form class="form-group text-white">
                <div class="mb-3">
                  <label class="form-label text-dark">Nome da Tarefa</label>
                  <input type="text" class="form-control" name="tarefa">
                </div>
                <div class="mb-3">
                  <label class="form-label text-dark">Descrição da tarefa</label> <textarea class="form-control" name="descricao" rows="3"></textarea>
                </div>
                <div class="row">
                  <div class="mb-3 col-6">
                    <label class="form-label text-dark">Data / Prazo</label>
                    <input type="datetime-local" value="<?= date("Y-m-d\TH:i:s") ?>" class="form-control" name="datatarefa">
                  </div>
                  <div class="mb-3 col-6">
                    <label class="form-label text-dark">Prioridade</label>
                    <select name="prioridade" class="form-select">
                      <option value="1">baixa</option>
                      <option value="2">média</option>
                      <option value="3">alta</option>
                    </select>
                  </div>

                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
              <button type="submit" name="cadastrar" value="Cadastrar Tarefa" class="btn btn-primary">Cadastrar</button>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- script em javascript para voltar o check-input para false
        criamos uma função chamada Resetar() -->
  <script>
    function Resetar() {
      let checkFinalizar = document.getElementsByClassName('form-check-input')
      for (let i = 0; i < checkFinalizar.length; i++) {
        checkFinalizar[i].checked = false
      }
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="js/scripts.js?cache_buster=<?php echo time(); ?>"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
  <script src="assets/demo/chart-area-demo.js"></script>
  <script src="assets/demo/chart-bar-demo.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
  <script src="js/datatables-simple-demo.js"></script>
</body>

</html>