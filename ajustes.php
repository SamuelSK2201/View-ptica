<?
$menu = 'configuracoes';
$page = 'ajustes';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_usuarios != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE)
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

$pessoa = $_POST['pessoa'];

$nome = addslashes($_POST['nome']);
$cpf = $_POST['cpf'];

$razao_social = addslashes($_POST['razao_social']);
$nome_fantasia = addslashes($_POST['nome_fantasia']);
$cnpj = $_POST['cnpj'];

$num_usuarios = $_POST['num_usuarios'];
$contato = addslashes($_POST['contato']);
$cargo = addslashes($_POST['cargo']);
$telefone = $_POST['telefone'];
$email = $_POST['email'];
$ind_pesquisa = $_POST['ind_pesquisa'];

$user_nome = addslashes($_POST['user_nome']);
$user_email = $_POST['user_email'];
$user_senha = $_POST['user_senha'];

$txt_rodape = addslashes($_POST['txt_rodape']);
$data_cadastro = ConverteData($_POST['data_cadastro']);

$str_format = formatar_string($nome);
if($pessoa == 2)
    $str_format = formatar_string($nome_fantasia);

if($_POST['cmd'] == "add")
{   
    $strE = "SELECT * FROM empresas WHERE (pessoa = '' AND cpf = '$cpf') OR (pessoa = '2' AND cnpj = '$cnpj')";
    $rsE  = mysqli_query($conexao, $strE) or die(mysqli_error($conexao));
    $numE = mysqli_num_rows($rsE);

    if($numE > 0)
        redireciona("ajustes.php?ind_msg=4");

    $str = "INSERT INTO empresas (pessoa, nome, cpf, razao_social, nome_fantasia, cnpj, contato, cargo, telefone, email, num_usuarios, ind_pesquisa, txt_rodape, str_format, data_cadastro) 
        VALUES ('$pessoa', '$nome', '$cpf', '$razao_social', '$nome_fantasia', '$cnpj', '$contato', '$cargo', '$telefone', '$email', '$num_usuarios', '$ind_pesquisa', '$txt_rodape', '$str_format', '$data_cadastro')";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $codigo = mysqli_insert_id($conexao);

    $senha = md5($user_senha);

    $str = "INSERT INTO usuarios_tipos (idempresa, nome) VALUES ('$codigo', 'Administrador')";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $idtipo = mysqli_insert_id($conexao);

    $str = "INSERT INTO usuarios (idempresa, idtipo, nome, email, senha, perfil, status) VALUES ('$codigo', '$idtipo', '$user_nome', '$user_email', '$senha', '1', '1')";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $codigo = mysqli_insert_id($conexao);

    $tr_pessoa = "";

    if($pessoa == 1)
    {
        $tr_pessoa .= "Nome: ".$_POST['nome']."<br>";
        $tr_pessoa .= "CPF: ".$_POST['cpf']."<br>";
    }
    else
    {
        $tr_pessoa .= "Razão social: ".$_POST['razao_social']."<br>";
        $tr_pessoa .= "Nome fantasia: ".$_POST['nome_fantasia']."<br>";
        $tr_pessoa .= "CNPJ: ".$_POST['cnpj']."<br>";
    }
    if($_FILES['imagem']['name'])
    {
        $dir = getcwd();    
        
        $dir_upload = $dir . "/upload/";    
        @mkdir($dir_upload, 0777);

        $dir_thumbnails = $dir . "/upload/thumbnails/";    
        @mkdir($dir_thumbnails, 0777);
        
        $strpos = strpos($_FILES['imagem']['name'], ".");
        $ext = substr($_FILES['imagem']['name'], $strpos);

        $nome_imagem = uniqid().$ext;    
        
        $imagem_upload = $dir_upload.$nome_imagem;
        $imagem_thumbs = $dir_thumbnails.$nome_imagem;

        move_uploaded_file($_FILES['imagem']['tmp_name'], $imagem_upload);
        copy($imagem_upload, $imagem_thumbs);

        @redimensiona_imagem('upload/'.$nome_imagem, 500, 500);
        @redimensiona_imagem('upload/thumbnails/'.$nome_imagem, 100, 100);

        $str = "UPDATE empresas SET imagem = '$nome_imagem' WHERE codigo = '$codigo'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    redireciona("ajustes.php?ind_msg=1");
}

if($_POST['cmd'] == "edit")
{
    $strE = "SELECT * FROM empresas WHERE ((pessoa = '1' AND cpf = '$cpf') OR (pessoa = '2' AND cnpj = '$cnpj')) AND codigo != '$codigo'";
    $rsE  = mysqli_query($conexao, $strE) or die(mysqli_error($conexao));
    $numE = mysqli_num_rows($rsE);

    if($numE > 0)
        redireciona("ajustes.php?ind_msg=4");

    $str = "UPDATE empresas 
        SET pessoa = '$pessoa', nome = '$nome', cpf = '$cpf', razao_social = '$razao_social', nome_fantasia = '$nome_fantasia', cnpj = '$cnpj', contato = '$contato', cargo = '$cargo', telefone = '$telefone', email = '$email', 
        num_usuarios = '$num_usuarios', ind_pesquisa = '$ind_pesquisa', txt_rodape = '$txt_rodape', str_format = '$str_format', data_cadastro = '$data_cadastro'
        WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $nome_imagem = "";

    if($_FILES['imagem']['name'])
    {
        $dir = getcwd();    
        
        $dir_upload = $dir . "/upload/";    
        @mkdir($dir_upload, 0777);

        $dir_thumbnails = $dir . "/upload/thumbnails/";    
        @mkdir($dir_thumbnails, 0777);
        
        $strpos = strpos($_FILES['imagem']['name'], ".");
        $ext = substr($_FILES['imagem']['name'], $strpos);

        $nome_imagem = uniqid().$ext;    
        
        $imagem_upload = $dir_upload.$nome_imagem;
        $imagem_thumbs = $dir_thumbnails.$nome_imagem;

        move_uploaded_file($_FILES['imagem']['tmp_name'], $imagem_upload);
        copy($imagem_upload, $imagem_thumbs);

        @redimensiona_imagem('upload/'.$nome_imagem, 500, 500);
        @redimensiona_imagem('upload/thumbnails/'.$nome_imagem, 100, 100);

        $str = "UPDATE empresas SET imagem = '$nome_imagem' WHERE codigo = '$codigo'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    redireciona("ajustes.php?ind_msg=2");
}

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM empresas WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM usuarios WHERE idempresa = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("ajustes.php?ind_msg=3");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Ajuste realizado com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Ajuste editado com sucesso!';
elseif($_GET['ind_msg'] == "3")
    $msg = 'Ajuste excluído com sucesso!';
elseif($_GET['ind_msg'] == "4")
    $msg = 'Ajuste já cadastrado no sistema!';

$str = "SELECT * FROM empresas WHERE codigo = '$codigo'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);

$pessoa = 2;
if($vet['pessoa'])
    $pessoa = $vet['pessoa'];

$title = "'View Óptica<br>Ajustes'";
$columns = '0, 1, 2, 3, 4, 5';
$order = ',order: [[ 0, "asc" ]]';
?>

<script language="javascript">
function valida(ind)
{   
    if(ind == 1)
        document.form.cmd.value = "add";
    else
        document.form.cmd.value = "edit";
}

function tipo_pessoa(tipo)
{  
    if(tipo == 1)
    {
        document.getElementById('pfisica').style.display='block';
        document.getElementById('pjuridica').style.display='none';

        document.getElementById('nome').required = true;
        //document.getElementById('cpf').required = true;

        document.getElementById('nome_fantasia').required = false;
        //document.getElementById('razao_social').required = false;
        //document.getElementById('cnpj').required = false;

        document.getElementById('nome_fantasia').value = '';
        document.getElementById('razao_social').value = '';
        document.getElementById('cnpj').value = '';
    }
    
    if(tipo == 2)
    {
        document.getElementById('pfisica').style.display='none';
        document.getElementById('pjuridica').style.display='block';

        document.getElementById('nome').required = false;
       // document.getElementById('cpf').required = false;

        document.getElementById('nome_fantasia').required = true;
        //document.getElementById('razao_social').required = true;
        //document.getElementById('cnpj').required = true;

        document.getElementById('nome').value = '';
        document.getElementById('cpf').value = '';
    }
}
</script>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Ajustes</h2>
        <ol class="breadcrumb">
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <?
            if(!empty($_GET['ind_msg']) && $_GET['ind_msg'] <= 4)
            {
            ?>
            <p class="font-bold  alert alert-success m-b-sm">
                <?=$msg?>
            </p>
            <br>
            <?
            }

            if($_GET['ind_msg'] >= 5)
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
                    <h5><i>Clique na seta do lado direito para abrir ou ocultar o formulário</i></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content" <?=($_POST['cmd'] == 'search' || $ind != 2) ? 'style="display: block;"' : 'style="display: none;"'?>>
                    <form method="post" class="form-horizontal" name="form_s" id="form_s" enctype="multipart/form-data">
                        <input type="hidden" name="cmd" value="search">                       
                        
                        <p class="font-bold  alert alert-info m-b-sm">
                            DADOS DA EMPRESA
                        </p>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="font-bold">
                                    Imagem*<br><small>Extensões: .gif, .png, .jpg, .jpeg</small>
                                </p>

                                <?
                                if($vet['imagem'])
                                {
                                ?>
                                <a href="upload/<?=$vet['imagem']?>" target="_blank"><img class="img-responsive img-shadow" src="upload/<?=$vet['imagem']?>" alt=""></a>
                                <br>                                
                                <?
                                }
                                ?>
                                
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput">
                                        <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                        <span class="fileinput-filename"></span>
                                    </div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">Selecionar foto</span>
                                        <span class="fileinput-exists">Alterar</span>
                                        <input type="file" name="imagem" id="imagem" accept="image/*" <?=($ind == 1) ? 'required' : ''?> >
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Excluir</a>
                                </div> 
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="font-bold">
                                    Texto p/ rodapé do receituário*
                                </p>
                                <textarea name="txt_rodape" id="txt_rodape" class="form-control" rows="5" required><?=stripslashes($vet['txt_rodape'])?></textarea>
                            </div>                 
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-12"> 
                                <?
                                if($ind == 1)
                                {
                                ?>
                                <button type="submit" class="btn btn-primary" onClick="javascript: valida(<?=$ind?>);">Cadastrar</button>
                                <?
                                }
                                else
                                {
                                ?>
                                <button type="submit" class="btn btn-primary" onClick="javascript: valida(<?=$ind?>);">Alterar</button>
                                <?
                                }
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>tipo_pessoa(<?=$pessoa?>)</script>
<?
include("includes/footer.php");
?>