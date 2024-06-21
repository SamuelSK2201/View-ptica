<?
include("includes/header.php");

$nome = addslashes($_POST['nome']);
$telefone = $_POST['telefone'];
$email = $_POST['email'];
$senha = $_POST['senha'];

if($_POST['cmd'] == "edit")
{
    $strE = "SELECT * FROM usuarios WHERE email = '$email' AND idempresa = '$adm_empresa' AND codigo != '$adm_codigo'";
    $rsE  = mysqli_query($conexao, $strE) or die(mysqli_error($conexao));
    $numE = mysqli_num_rows($rsE);

    if($numE > 0)
        redireciona("meus_dados.php?ind_msg=2");

    $senha_aux = md5($senha);

    if($senha)
        $strSet = ", senha = '$senha_aux'";

    $str = "UPDATE usuarios SET nome = '$nome', telefone = '$telefone', email = '$email' $strSet WHERE codigo = '$adm_codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("meus_dados.php?ind_msg=1");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Dados editados com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Email já cadastrado para outro usuário no sistema!';

$str = "SELECT * FROM usuarios WHERE idempresa = '$adm_empresa' AND codigo = '$adm_codigo'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);
?>

<script language="javascript">
function valida()
{  
    document.form.cmd.value = "edit";
}
</script>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Meus Dados</h2>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <?
            if($_GET['ind_msg'] == 1 && !empty($_GET['ind_msg']))
            {
            ?>
            <p class="font-bold  alert alert-success m-b-sm">
                <?=$msg?>
            </p>
            <br>
            <?
            }

            if($_GET['ind_msg'] == 2)
            {
            ?>
            <p class="font-bold  alert alert-danger m-b-sm">
                <?=$msg?>
            </p>
            <br>
            <?
            }
            ?>
            <div class="ibox float-e-margins">
                <div class="ibox-title">    
                    <h5>Utilize o formulário abaixo para editar seus dados</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-down"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content" style="display: block;">
                    <form method="post" class="form-horizontal" name="form" id="form" enctype="multipart/form-data">
                        <input type="hidden" name="cmd">            

                        <div class="row">
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Nome*
                                </p>
                                <input type="text" name="nome" id="nome" class="form-control" value="<?=stripslashes($vet['nome'])?>" required>
                            </div>
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Email*
                                </p>
                                <input class="form-control" type="email" name="email" id="email" value="<?=$vet['email']?>" required>
                            </div>
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Telefone
                                </p>
                                <input class="form-control" type="text" name="telefone" id="telefone" value="<?=$vet['telefone']?>" maxlength="15" onKeyUp="javascript: mascara(this, mtel);" onKeyPress="javascript: return somenteNumeros(event);" >
                            </div>
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Senha
                                </p>
                                <input type="password" name="senha" id="senha" class="form-control" >
                            </div>                            
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-12"> 
                                <button type="submit" class="btn btn-primary" onClick="javascript: valida();">Alterar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?
include("includes/footer.php");
?>