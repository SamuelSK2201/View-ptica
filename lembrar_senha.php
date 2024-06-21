<?
session_start();

include("s_acessos.php");
include("funcoes.php"); 

if($_POST['cmd'] == "send_pass")
{
    $email = anti_injection($_POST['email']);
    $senha = anti_injection($_POST['senha']);

    $strA = "SELECT * FROM usuarios_adm WHERE status = '1' AND email = '$email'";
    $rsA  = mysqli_query($conexao, $strA) or die(mysqli_error($conexao));
    $numA = mysqli_num_rows($rsA);
    $vetA = mysqli_fetch_array($rsA);

    $str = "SELECT * FROM usuarios WHERE status = '1' AND email = '$email'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    $vet = mysqli_fetch_array($rs);

    if(!$numA && !$num)
        redireciona("lembrar_senha.php?ind_msg=1");

    $cmd = 'edit_pass';
    $codigo = $vet['codigo'];
    $nome = stripslashes($vet['nome']);    

    if($numA)
    {
        $cmd = 'edit_pass_adm';   
        $codigo = $vetA['codigo'];
        $nome = stripslashes($vetA['nome']);
    }

    $id_mail = base64_encode($codigo);
    $senha_mail = base64_encode($senha);

    $text_body = "Olá ".$nome.",<br><br>";
    $text_body .= "Sua nova senha de acesso ao View Optica é: <b>".$senha."</b>.<br>";
    $text_body .= "<hr /><br>";
    $text_body .= "Confirme a alteração de senha clicando no link abaixo:<br>";
    $text_body .= "<a href='http://viewoptica.com.br/login.php?cmd=".$cmd."&id=".$id_mail."&s=".$senha_mail."' target='_blank'>Confirmar nova senha!</a>";
    $text_body .= "<hr /><br>";
    $text_body .= "Clique no link abaixo e acesse o View Optica.<br>";
    $text_body .= "<a href='http://viewoptica.com.br/' target='_blank'>http://viewoptica.com.br/</a>";

    require 'PHPMailer/class.phpmailer.php';

    $mail = new phpmailer();
    $mail->IsSMTP(); 
    $mail->Host     = "br1010.hostgator.com.br";
    $mail->SMTPAuth = true;
    $mail->Port     = 587;
    $mail->Username = "envio@viewoptica.com.br"; 
    $mail->Password = "0GNV[T@yOf(M";
    $mail->From     = "envio@viewoptica.com.br";
    $mail->FromName = "View Optica";
    $mail->Body     = utf8_decode($text_body);
    $mail->AltBody  = "HTML";
    $mail->IsHTML(true);

    $mail->Subject  = "Nova senha de acesso!";

    $mail->AddAddress($email);
    $mail->Send();
    
    redireciona("lembrar_senha.php?ind_msg=2");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Optica | Esqueceu sua senha?</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="gray-bg">
    <div class="middle-box text-center loginscreen animated fadeInDown">
        <?
        if($_GET['ind_msg'] == 1)
        {
        ?>
        <p class="font-bold alert alert-danger m-b-sm">
            O email informado não foi encontrado no sistema ou não está ativo!
        </p>
        <?
        }
        ?>

        <?
        if($_GET['ind_msg'] == 2)
        {
        ?>
        <p class="font-bold alert alert-success m-b-sm">
            Sua nova senha será enviada para seu email, acesse-o e clique no link de ativação para atualizar a senha.
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
            <h3>Esqueceu sua senha?</h3>
            <p>Entre com seu email e uma nova senha que enviaremos as instruções por e-mail.</p>
            <form class="m-t" role="form" name="form" id="form" method="post">
                <input type="hidden" name="cmd" id="cmd" value="send_pass">                
                <div class="form-group">
                    <input type="email" name="email" id="email" class="form-control" placeholder="Email" >
                </div>
                <div class="form-group">
                    <input type="password" name="senha" id="senha" class="form-control" placeholder="Nova Senha" required="true">
                </div>

                <button type="submit" class="btn btn-primary block full-width m-b">Enviar nova senha</button>

                <!--p class="text-muted text-center"><small>Already have an account?</small></p-->
                <a class="btn btn-sm btn-white btn-block" href="index.php">Voltar</a>
            </form>
            <p class="m-t"><small>Desenvolvido por<br>View Optica &copy; 2019</small></p>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="js/jquery-2.1.1.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <!-- iCheck -->
    <script src="js/plugins/iCheck/icheck.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        });
    </script>

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
</body>

</html>
