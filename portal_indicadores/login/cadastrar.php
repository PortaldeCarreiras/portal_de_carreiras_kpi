<?php
include('../conn.php');

// Página de cadastro de novos usuário
if (isset($_GET["cadastrar"])) {    // Se o botão de cadastrar foi clicado
    // Testar se os campos de usuário e senha não estão vazios
    if (!empty($_GET["usuario"]) && !empty($_GET["senha"]) && !empty($_GET["confirmsenha"])) {

        // Função para testar o valor do campo e evitar SQL Injection
        function testarValor($valor){
            $valor = stripslashes($valor);  // Remove barras invertidas de uma string
            $valor = htmlspecialchars($valor);  // Converte caracteres especiais para a realidade HTML
            $valor = trim($valor);  // Retira espaços no início e final de uma string
            return $valor;
        }
        $usuario = testarValor($_GET["usuario"]);   // Recupera o valor do campo usuário
        $senha = testarValor($_GET["senha"]);    // Recupera o valor do campo senha
        $confirmsenha = testarValor($_GET["confirmsenha"]);   // Recupera o valor do campo confirmação de senha
        $loginOk = false;
        $senhaOk = false;

        // Verifica se a senha e a confirmação de senha são iguais
        if ($senha == $confirmsenha) {
            $senhaOk = true;
        } else {
            header('location:cadastrar.php?erro=senha');
        }

        // Verifica se o login já existe no DB
        $sql = "SELECT * FROM tab_usuarios WHERE usuario='$usuario'";   // Verifica se o usuário/login já existe no DB
        $result = mysqli_query($conn, $sql);
        $quantReg = mysqli_num_rows($result);   // Conta o número de registros encontrados
        if ($quantReg > 0) {    // Se o login já existe
            header('location:cadastrar.php?erro=login');    // Redireciona para a página de cadastro com erro
        } else {
            $loginOk = true;    // Se o login não existe, a variável loginOk recebe true
        }

        // Se o login e a senha estão corretos
        if ($loginOk && $senhaOk) {   // NÃO ESQUECER DE TIRAR A INSERÇÃO DE "TEMP" NO INSERT DB
            $hash = password_hash($senha, PASSWORD_ARGON2ID);    // Criptografa a senha
            $sql = "INSERT INTO tab_usuarios (usuario,senha,temp)
            VALUES('$usuario','$hash', '$senha')";    // Insere o novo usuário no DB, POSTERIORMENTE TIRAR O "TEMP" ($senha) COMO PARÂMETRO

            if (mysqli_query($conn, $sql)) {
                header('location:login.php?cad=ok');    // Se o cadastro foi realizado com sucesso
            } else {
                header('location:cadastrar.php?erro=cad');  // Se houve erro no cadastro
            }
        }
    } else {
        header('location:cadastrar.php?erro=cadnaopre');    // Se os campos não foram preenchidos
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário - Indicadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="icon" href="../img/favicon.png" type="image/png">
    <style>
        .gradient-custom-2 {
            /* fallback for old browsers */
            background: #000000;

            /* Chrome 10-25, Safari 5.1-6 */
            background: -webkit-linear-gradient(to right, #ef2b41, #ef2b41, #ef2b41, #ef2b41);

            /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
            background: linear-gradient(to right, #ef2b41, #ef2b41, #ef2b41, #ef2b41);
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
                                    if (isset($_GET["erro"]) && $_GET["erro"] == "senha") {
                                    ?>
                                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                                            <div>
                                                Senha diferente nos dois campos, favor digitar senhas iguais!!!
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if (isset($_GET["erro"]) && $_GET["erro"] == "login") {
                                    ?>
                                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                                            <div>
                                                Este usuário / Login já existe !!!
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if (isset($_GET["erro"]) && $_GET["erro"] == "cad") {
                                    ?>
                                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                                            <div>
                                                Erro no cadastro, favor tentar novamente!!!
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if (isset($_GET["erro"]) && $_GET["erro"] == "cadnaopre") {
                                    ?>
                                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                                            <div>
                                                Preencha todos os campos do formulário!!!
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>





                                    <form>
                                        <p>Crie sua conta</p>

                                        <div class="form-outline mb-4">
                                            <input type="text" id="form2Example11" name="usuario" class="form-control" placeholder="Usuario" />
                                        </div>
                                        <div class="form-outline mb-4">
                                            <input type="text" id="form2Example22" name="senha" class="form-control" placeholder="Senha" />
                                        </div>
                                        <div class="form-outline mb-4">
                                            <input type="text" id="form2Example22" name="confirmsenha" class="form-control" placeholder="Confirmar Senha" />
                                        </div>

                                        <div class="text-center pt-1 mb-5 pb-1">
                                            <button class="btn btn-secondary text-white bg-gradient btn-block fa-lg mb-3" name="cadastrar" type="submit">Cadastrar</button>
                                            <button class="btn btn-success btn-block fa-lg mb-3" name="btnHome"
                                                type="button" onclick="irParaLogin()">Home</button>
                                            <script>
                                                function irParaLogin() {
                                                    window.location.href = "login.php";
                                                }
                                            </script>

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