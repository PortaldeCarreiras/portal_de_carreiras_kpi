<?php
session_start();
include('../conn.php');

if (isset($_GET["logar"])) {    // Se o botão de logar foi clicado
    if ((!empty($_GET["usuario"]) || !empty($_GET["email"])) && !empty($_GET["senha"])) {   // Se os campos de usuário/senha e senha não estão vazios

        // Função para testar o valor do campo e evitar SQL Injection
        function testarValor($valor)
        {   // Versão compacta da função testarValor
            return trim(htmlspecialchars(stripslashes($valor)));
        }
        // function testarValor($valor){   // Versão explicita
        //     $valor = stripslashes($valor);  // Remove barras invertidas de uma string
        //     $valor = htmlspecialchars($valor);  // Converte caracteres especiais para a realidade HTML
        //     $valor = trim($valor);  // Retira espaços no início e final de uma string
        //     return $valor;
        // }

        // Recupera os valores dos campos
        $usuario = testarValor($_GET["usuario"]);
        $email = isset($_GET['usuario']) ? $_GET['usuario'] : '';
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {    // Se o email estiver no formato correto
            $email = $email;
          } else {
            $email = '';
          }
        $senha = testarValor($_GET["senha"]);

        // Cria a query para buscar o usuário ou email, somente o campo usuário
        // quando fazemos a busca (SELECT) por usuário/email, obtemos todos os dados do usuário,
        // inclusive a senha, que será usada para comparação
        $sql = "SELECT * FROM tab_usuarios WHERE LOWER(usuario) = ? OR LOWER(email) = LOWER(?)";    // Query para buscar o usuário e o email
        $statement = mysqli_prepare($conn, $sql);    // Prepara a query
        mysqli_stmt_bind_param($statement, 'ss', $usuario, $email);    // Substitui o ? pelo valor do usuário
        mysqli_stmt_execute($statement);    // Executa a query
        $result = mysqli_stmt_get_result($statement);    // Obtém o resultado de todas informações do usuário (usuário, email, senha e tipo de adm)
        $quantReg = mysqli_num_rows($result);   // Conta quantos registros foram encontrados
        // Verifica se o usuário existe e se a senha está correta
        if ($linha = mysqli_fetch_assoc($result)) {   // Se existir o usuário
            // Verifica a senha criptografada, que só irá existir se o usuário existir
            if (password_verify($senha, $linha['senha'])) {    // Se a senha estiver correta, a função password_verify retorna true
                $_SESSION["usuario"] = $linha["usuario"];    // Cria a sessão se a senha estiver correta
                $_SESSION["id"] = $linha["id"];
                $_SESSION["email"] = $linha["email"];
                $_SESSION["tipo_adm"] = $linha["tipo_adm"];

                header('location:../index.php');
                exit();
            }
        }
        // Redireciona para login.php caso o usuário ou senha estejam errados
        header('location:login.php?erro=1');    // Se o usuário ou senha estiverem errados
        exit();
    } else {    // Se os campos de usuário/senha e senha estão vazios
        header('location:login.php?erro=2');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logar - Indicadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="icon" href="../img/favicon.png" type="image/png">
    <style>
        .gradient-custom-2 {
            /* fallback for old browsers */
            background: #000000;

            /* Chrome 10-25, Safari 5.1-6 */
            background: -webkit-linear-gradient(to top, #ef2b41, #ef2b41, #ef2b41, #ef2b41);

            /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
            background: linear-gradient(to top, #ef2b60, #ef2b41, #ef2b19, #ef2b19);
        }

        @media (min-width: 768px) {
            .gradient-form {
                height: 100vh !important;
            }
        }

        @media (min-width: 769px) {
            .gradient-custom-2 {
                border-top-right-radius: .3rem;
                border-bottom-right-radius: .3rem;
            }
        }
    </style>
</head>

<body>

    <section class="h-100 gradient-form" style="background-color: #eee;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-xl-10">
                    <div class="card rounded-3 text-black">
                        <div class="row g-0">
                            <div class="col-lg-6">
                                <div class="card-body p-md-5 mx-md-4">

                                    <div class="text-center">
                                        <img src="../img/fatec.png" style="width: 200px;" alt="logo">
                                        <h4 class="mt-1 mb-5 pb-1">Indicadores - Portal de Carreiras</h4>
                                    </div>

                                    <?php
                                    if (isset($_GET["cad"]) && $_GET["cad"] == "ok") {
                                    ?>
                                        <div class="alert alert-primary d-flex align-items-center" role="alert">
                                            <div>
                                                Cadastro realizado com sucesso!!!
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>

                                    <form>
                                        <p>Acessar sua conta</p>

                                        <div class="form-outline mb-4">
                                            <input type="text" id="form2Example11" name="usuario" class="form-control" placeholder="Usuário ou email" />
                                            <!-- Campo oculto no formulário para enviar o email com o valor do campo usuário -->
                                            <input type="hidden" name="email" value="<?php echo isset($_GET['usuario']) ? $_GET['usuario'] : ''; ?>">
                                        </div>

                                        <div class="form-outline mb-4">
                                            <input type="text" id="form2Example22" name="senha" class="form-control" placeholder="Senha" />
                                        </div>

                                        <div class="text-center pt-1 mb-5 pb-1">
                                            <button class="btn btn-secondary text-white btn-outline-secondary btn-block fa-lg mb-3" name="logar" type="submit">Logar</button>

                                        </div>

                                        <div class="d-flex align-items-center justify-content-center pb-4">
                                            <p class="mb-0 me-2">Não tem uma conta?</p>
                                            <a href="cadastrar.php">
                                                <button type="button" class="btn btn-outline-danger">Cadastre-se</button>
                                            </a>
                                        </div>

                                    </form>

                                </div>
                            </div>
                            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                    <h4 class="mb-4">Indicadores - Portal de Carreiras</h4>
                                    <p class="small mb-0">Mussum Ipsum, cacilds vidis litro abertis.
                                        Si num tem leite então bota uma pinga aí cumpadi! Mais vale
                                        um bebadis conhecidiss, que um alcoolatra anonimis. Per aumento
                                        de cachacis, eu reclamis. Sapien in monti palavris qui num
                                        significa nadis i pareci latim.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>
</body>

</html>