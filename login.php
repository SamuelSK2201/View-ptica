<?
session_start();

$page = 'login';
include("s_acessos.php");
include("funcoes.php");

if($_POST['cmd'] == "login")
{
    $email = anti_injection($_POST['email']);
    $senha = anti_injection($_POST['senha']);
    $senha_aux = md5($senha);

    $str = "SELECT * FROM usuarios_adm WHERE status = '1' AND email = '$email' AND senha = '$senha_aux'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    $vet = mysqli_fetch_array($rs);
    
    if ($num > 0) 
    {
        $_SESSION["adm_verifica"] = "adm_option";
        $_SESSION["adm_codigo"] = $vet["codigo"];
        $_SESSION["adm_user"] = 1;

        redireciona("empresas.php");
    }
   
    $str = "SELECT A.*, B.num_usuarios 
        FROM usuarios A
        INNER JOIN empresas B ON A.idempresa = B.codigo
        WHERE A.status = '1' 
        AND A.email = '$email' 
        AND A.senha = '$senha_aux'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    $vet = mysqli_fetch_array($rs);
    
    if ($num > 0) 
    {
        $_SESSION["adm_verifica"] = "adm_option";
        $_SESSION["adm_codigo"] = $vet["codigo"];
        $_SESSION["adm_num_usuarios"] = $vet["num_usuarios"];
        $_SESSION["adm_user"] = 2;

        redireciona("home.php");
    }
    
    redireciona("login.php?ind_msg=1");
}

if($_GET['cmd'] == "edit_pass_adm")
{    
    $idusuario = base64_decode($_GET['id']);
    $senha = $senha = md5(base64_decode($_GET['s']));
    
    $str = "SELECT * FROM usuarios_adm WHERE codigo = '$idusuario'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    $vet = mysqli_fetch_array($rs);
    
    if ($num > 0) 
    {
        $str = "UPDATE usuarios_adm SET senha = '$senha' WHERE codigo = '$idusuario'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

        $_SESSION["adm_verifica"] = "adm_option";
        $_SESSION["adm_codigo"] = $vet["codigo"];
        $_SESSION["adm_user"] = 1;
        
        redireciona("empresas.php");
    } 
    
    redireciona("login.php?ind_msg=2");
}

if($_GET['cmd'] == "edit_pass")
{    
    $idusuario = base64_decode($_GET['id']);
    $senha = $senha = md5(base64_decode($_GET['s']));
    
    $str = "SELECT * FROM usuarios WHERE codigo = '$idusuario'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    $vet = mysqli_fetch_array($rs);
    
    if ($num > 0) 
    {
        $str = "UPDATE usuarios SET senha = '$senha' WHERE codigo = '$idusuario'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

        $_SESSION["adm_verifica"] = "adm_option";
        $_SESSION["adm_codigo"] = $vet["codigo"];
        $_SESSION["adm_user"] = 2;
        
        redireciona("home.php");
    } 
    
    redireciona("login.php?ind_msg=2");
}
?>
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>View Optica | Login</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
    #senha {
        width: 100%;
        padding-right: 20px;
    }

    .olho {
        cursor: pointer;
        left: 260px;
        position: absolute;
        width: 30px;
    }
    </style>
</head>

<body class="gray-bg">
    <div class="middle-box text-center loginscreen animated fadeInDown">
        <?
        if($_GET['ind_msg'] == 1)
        {
        ?>
        <p class="font-bold alert alert-danger m-b-sm">
            Login e / ou senha não foram encontrados ou não está ativo no sistema!
        </p>
        <?
        }

        if($_GET['ind_msg'] == 2)
        {
        ?>
        <p class="font-bold alert alert-danger m-b-sm">
            Sua senha não foi alterada, entre em contato conosco para maiores detalhes!
        </p>
        <?
        }
        ?>

        <div>
            <div>
                <h1 class="logo-name" style="margin-left: -15px;">VO</h1>
                <br><br>
                <!--img src="../img/topvix.png" class="img-responsive"/>
                <br><br-->
            </div>
            <h3>Bem-vindo ao View Óptica Sistemas</h3>
            <p>Informe seu email e senha de acesso</p>
            <form class="m-t" role="form" name="form" id="form" method="post">
                <input type="hidden" name="cmd" id="cmd" value="login">
                <div id="usuarios" style="display: inline;">
                    <div class="form-group">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email" >
                    </div>
                    <div class="form-group" id="password">
                        <img src="https://cdn0.iconfinder.com/data/icons/ui-icons-pack/100/ui-icon-pack-14-512.png" id="olho" class="olho">
                        <input type="password" name="senha" id="senha" class="form-control" placeholder="Senha" >
                    </div>

                    <button type="submit" class="btn btn-primary block full-width m-b">Acessar</button>
                    <a href="lembrar_senha.php"><small>Esqueceu sua senha?</small></a>
                </div>
            </form>
            <p class="m-t"><small>Desenvolvido por<br><a href="https://viewoptica.com.br/">View Óptica &copy; 2019</a><br><br>V. 24.05.00</small></p>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <!-- Input Mask -->
    <script src="js/plugins/jasny/jasny-bootstrap.min.js"></script>

    <script src="js/webshim/js-webshim/dev/polyfiller.js"></script> 
    <script> 
        webshim.activeLang('en');
        webshims.polyfill('forms');
        webshims.cfg.no$Switch = true;
        $(function(){
            $('.link_scroll[href^="#"]').on('click', function(event) {

                var target = $( $(this).attr('href') );

                if( target.length ) {
                    event.preventDefault();
                    $('html, body').animate({
                        scrollTop: target.offset().top
                    }, 500);
                }

            });
        });
    </script>

    <script>
        document.getElementById('olho').addEventListener('mousedown', function() {
          document.getElementById('senha').type = 'text';
        });

        document.getElementById('olho').addEventListener('mouseup', function() {
          document.getElementById('senha').type = 'password';
        });

        // Para que o password não fique exposto apos mover a imagem.
        document.getElementById('olho').addEventListener('mousemove', function() {
          document.getElementById('senha').type = 'password';
        });
    </script>
</body>
</html>
