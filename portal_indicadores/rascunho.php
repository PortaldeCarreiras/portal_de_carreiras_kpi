<?php
session_start();    //Cria uma sessão e tem que estar na primeira linha e mais código na linha 28
                    //e também código no arquivo index e qualquer outro arquivo que checar se a sessão 
                    //está foi criada e está aberta aberta. Para desativar a sessão precisa fechar o navegador

    //Conexão com o banco de dados
include('conn.php');

if(isset($_GET["logar"])){

    //Query para acessar o banco e verificar se existe esse usuário e senha
    //Desse jeito o banco fica vulneravel. pode-se digitar qualquer usuário "qualquer" e senha " ' or 1 = '1 "
    //O comando sql é alterado pra " SELECT * FROM tab_usuarios WHERE usuario = '$usuario' 
    //AND senha = '$senha ' or 1 = '1' " ASSIM ELE PERMITE VOCÊ LOGAR SEM USUÁRIO E SENHA.
    if(!empty($_GET["usuario"]) && !empty($_GET["senha"])){
        //Testar e filtrar valores recebidos, para evitar entradas maliciosas para entrar no sistema
        function testarValor($valor){
            $valor = htmlspecialchars($valor); //Converte informação recebida de comandos HTML para alguns caracteres especiais, evita comandos HTML maliciosos.
            $valor = stripslashes($valor);  //Retira barras invertidas (\).
            $valor = trim($valor);  //Retira espaços da informação recebida
            return $valor;
        }
    
        $usuario = testarValor($_GET["usuario"]);
        $senha = testarValor($_GET["senha"]);
    
        $sql = "SELECT * FROM tab_usuarios
        WHERE usuario = '$usuario' AND senha = '$senha'";
        $result = mysqli_query($conn,$sql);
        $quantReg = mysqli_num_rows($result);
    
        if($quantReg > 0){
            while($linha = mysqli_fetch_assoc($result)){
                $id = $linha["id"];
            }
            $_SESSION["usuario"] = $usuario;    //nome do usuário logado
            $_SESSION["id"] = $id;              //id do usuário logado
            header('location:index.php');
        }else{
            header('location:login.php?erro=1');
        }
    
    }else{
        header('location:login.php?erro=2');
    }

}



?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
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
                                        <img src="img/logo.png" style="width: 100px;" alt="logo">
                                        <h4 class="mt-1 mb-5 pb-1">Gerenciador de Tarefas</h4>
                                    </div>

                                    <?php
                                    if (isset($_GET["erro"]) && $_GET["erro"] == 2) {
                                    ?>
                                        <div id="liveAlertPlaceholder"></div>
                                        <script>
                                            const alertPlaceholder = document.getElementById('liveAlertPlaceholder')

                                            const alert = (message, type) => {
                                                const wrapper = document.createElement('div')
                                                wrapper.innerHTML = [
                                                    `<div class="alert alert-${type} alert-dismissible" role="alert">`,
                                                    `   <div>${message}</div>`,
                                                    '   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>',
                                                    '</div>'
                                                ].join('')

                                                alertPlaceholder.append(wrapper)
                                            }
                                            alert('Por favor preencha todos os campos!!!', 'success')
                                        </script>

                                    <?php
                                    }
                                    ?>

                                    <form>
                                        <p>Acessar sua conta</p>

                                        <div class="form-outline mb-4">
                                            <input type="text" id="form2Example11" name="usuario" class="form-control" placeholder="Usuario" />
                                        </div>

                                        <div class="form-outline mb-4">
                                            <input type="text" id="form2Example22" name="senha" class="form-control" placeholder="Senha" />
                                        </div>

                                        <div class="text-center pt-1 mb-5 pb-1">
                                            <button class="btn btn-primary btn-block fa-lg gradient-custom-2 mb-3" name="logar" type="submit">Logar</button>

                                        </div>

                                        <div class="d-flex align-items-center justify-content-center pb-4">
                                            <p class="mb-0 me-2">Não tem uma conta?</p>
                                            <a href="cadastrar.php" class="btn btn-outline-danger">Cadastre-se</a>  
                                            <!-- A tag <a> é usada para a função do botão enviar para a página desejada -->
                                        </div>

                                    </form>

                                </div>
                            </div>
                            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                                <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                    <h4 class="mb-4">Gerenciador de tarefas</h4>
                                    <p class="small mb-0">ask manager ou gerenciador de tarefas, anteriormente conhecido como Windows Task Manager ou gerenciador de tarefas do Windows é um gerenciador de tarefas, monitor do sistema, e gerenciador de inicialização incluído com sistemas Microsoft Windows.</p>
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