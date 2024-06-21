<?
$page = "empresas";
include("includes/header.php");

if($_SESSION["adm_user"] != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE) 
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

$pessoa = $_POST['pessoa'];

$chatbot = addslashes($_POST['chatbot']);

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
$ind_atendimento = $_POST['ind_atendimento'];

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
    $strE = "SELECT * FROM empresas WHERE (pessoa = '1' AND cpf = '$cpf') OR (pessoa = '2' AND cnpj = '$cnpj')";
    $rsE  = mysqli_query($conexao, $strE) or die(mysqli_error($conexao));
    $numE = mysqli_num_rows($rsE);

    if($numE > 0)
        redireciona("empresas.php?ind_msg=4");

    $str = "INSERT INTO empresas (pessoa, nome, cpf, razao_social, nome_fantasia, cnpj, contato, cargo, telefone, email, num_usuarios, ind_pesquisa, ind_atendimento, txt_rodape, str_format, data_cadastro, chatbot) 
        VALUES ('$pessoa', '$nome', '$cpf', '$razao_social', '$nome_fantasia', '$cnpj', '$contato', '$cargo', '$telefone', '$email', '$num_usuarios', '$ind_pesquisa', '$ind_atendimento', '$txt_rodape', '$str_format', '$data_cadastro', '$chatbot')";
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

    /***********************************************************
    //EMPRESA E USUÁRIO MASTER RECEBEM EMAIL COM DADOS DE ACESSO
    ***********************************************************/
    $text_body = "Olá <b>".$_POST['user_nome']."</b>, acabamos de fazer o cadastro da sua empresa em nosso sistema, seguem os dados da empresa e de acesso:<br>";
    $text_body .= "<hr /><br>";
    $text_body .= $tr_pessoa;
    $text_body .= "Contato: ".$_POST['contato']."<br>";
    $text_body .= "Cargo: ".$_POST['cargo']."<br>";
    $text_body .= "Telefone: ".$_POST['telefone']."<br>";
    $text_body .= "Email: ".$_POST['email']."<br>";
    $text_body .= "<hr /><br>";
    $text_body .= "Dados do usuário (master):<br>";
    $text_body .= "<hr /><br>";
    $text_body .= "Nome: ".$_POST['user_nome']."<br>";
    $text_body .= "Email: ".$_POST['user_email']."<br>";
    $text_body .= "Senha: ".$_POST['user_senha']."<br>";
    $text_body .= "<hr /><br>";
    $text_body .= "Clique no link abaixo e acesse o sistema.<br>";
    $text_body .= "<a href='http://viewoptica.com.br/new/' target='_blank'>http://viewoptica.com.br/new/</a>";

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

    $mail->Subject  = "Dados de acesso ao sistema!";

    $mail->AddAddress($_POST['user_email']);
    $mail->Send();

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

    redireciona("empresas.php?ind_msg=1");
}

if($_POST['cmd'] == "edit")
{
    $strE = "SELECT * FROM empresas WHERE ((pessoa = '1' AND cpf = '$cpf') OR (pessoa = '2' AND cnpj = '$cnpj')) AND codigo != '$codigo'";
    $rsE  = mysqli_query($conexao, $strE) or die(mysqli_error($conexao));
    $numE = mysqli_num_rows($rsE);

    if($numE > 0)
        redireciona("empresas.php?ind_msg=4");

    $str = "UPDATE empresas 
        SET pessoa = '$pessoa', nome = '$nome', cpf = '$cpf', razao_social = '$razao_social', nome_fantasia = '$nome_fantasia', cnpj = '$cnpj', contato = '$contato', cargo = '$cargo', telefone = '$telefone', email = '$email', chatbot = '$chatbot'
        num_usuarios = '$num_usuarios', ind_pesquisa = '$ind_pesquisa', ind_atendimento = '$ind_atendimento', txt_rodape = '$txt_rodape', str_format = '$str_format', data_cadastro = '$data_cadastro'
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

    redireciona("empresas.php?ind_msg=2");
}

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM empresas WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM usuarios WHERE idempresa = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("empresas.php?ind_msg=3");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Empresa cadastrada com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Empresa editada com sucesso!';
elseif($_GET['ind_msg'] == "3")
    $msg = 'Empresa excluída com sucesso!';
elseif($_GET['ind_msg'] == "4")
    $msg = 'CPF / CNPJ já cadastrado no sistema!';

$str = "SELECT * FROM empresas WHERE codigo = '$codigo'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);

$pessoa = 2;
if($vet['pessoa'])
    $pessoa = $vet['pessoa'];

$title = "'View Óptica<br>Empresas'";
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
        <h2>Empresas</h2>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <?
            if($_GET['ind_msg'] != 4 && !empty($_GET['ind_msg']))
            {
            ?>
            <p class="font-bold  alert alert-success m-b-sm">
                <?=$msg?>
            </p>
            <br>
            <?
            }

            if($_GET['ind_msg'] == 4)
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
                            <i class="fa fa-chevron-down"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content" <?=($ind == 2) ? 'style="display: block;"' : 'style="display: none;"'?>>
                    <form method="post" class="form-horizontal" name="form" id="form" enctype="multipart/form-data">
                        <input type="hidden" name="cmd" value="edit">  
                        <input type="hidden" name="codigo" id="codigo" value="<?=$vet['codigo']?>">                      

                        <p class="font-bold  alert alert-info m-b-sm">
                            DADOS DA EMPRESA
                        </p>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <div>
                                    <p class="font-bold">
                                        ChatBot*
                                    </p>
                                    <input type="text" name="chatbot" id="chatbot" class="form-control" value="<?=stripslashes($vet['chatbot'])?>" required>
                                </div>
                                
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
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Pessoa *
                                </p>
                                <select name="pessoa" id="pessoa" class="form-control" onchange="javascript: tipo_pessoa(this.value)">
                                    <option value="1" <?=($vet['pessoa'] == 1) ? 'selected' : ''?>>Física</option>
                                    <option value="2" <?=(!$vet['pessoa'] || $vet['pessoa'] == 2) ? 'selected' : ''?>>Jurídica</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div id="pfisica" <?=($pessoa == 1) ? 'style="display: block;"' : 'style="display: none;"'?>>
                        <div class="row">
                            <div class="col-md-8">
                                <p class="font-bold">
                                    Nome*
                                </p>
                                <input type="text" name="nome" id="nome" class="form-control" value="<?=stripslashes($vet['nome'])?>" required>
                            </div> 
                            <div class="col-md-4">
                                <p class="font-bold">
                                    CPF
                                </p>
                                <input type="text" class="form-control" name="cpf" id="cpf" value="<?=$vet['cpf']?>" placeholder="CPF" maxlength="14" data-mask="999.999.999-99" >
                                <!--div id="v_cpf" class="valid"><code>CPF inválido</code></div-->
                            </div>                  
                        </div>
                        </div>

                        <div id="pjuridica" <?=($pessoa == 2) ? 'style="display: block;"' : 'style="display: none;"'?>>
                        <div class="row">
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Nome Fantasia*
                                </p>
                                <input type="text" name="nome_fantasia" id="nome_fantasia" class="form-control" value="<?=stripslashes($vet['nome_fantasia'])?>" required>
                            </div>
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Razão Social
                                </p>
                                <input type="text" name="razao_social" id="razao_social" class="form-control" value="<?=stripslashes($vet['razao_social'])?>" >
                            </div>                            
                            <div class="col-md-4">
                                <p class="font-bold">
                                    CNPJ
                                </p>
                                <input class="form-control" type="text" name="cnpj" id="cnpj" value="<?=$vet['cnpj']?>" maxlength="18" data-mask="99.999.999/9999-99" onKeyUp="javascript: return auto_cnpj('cnpj');" onKeyPress="javascript: return somenteNumeros(event);">
                                <!--div id="v_cnpj" class="valid"><code>CNPJ inválido</code></div-->
                            </div>                           
                        </div>
                        </div>
                        <br>

                        <div class="row">
                             <div class="col-md-2">
                                <p class="font-bold">
                                    Núm. Usuários*
                                </p>
                                <input class="form-control" type="number" name="num_usuarios" id="num_usuarios" value="<?=$vet['num_usuarios']?>" required min="0" >
                            </div>
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Contato*
                                </p>
                                <input class="form-control" type="text" name="contato" id="contato" value="<?=stripslashes($vet['contato'])?>" required  >
                            </div>
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Cargo*
                                </p>
                                <input class="form-control" type="text" name="cargo" id="cargo" value="<?=stripslashes($vet['cargo'])?>" required  >
                            </div>
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Telefone*
                                </p>
                                <input class="form-control" type="text" name="telefone" id="telefone" value="<?=$vet['telefone']?>" required maxlength="15" onKeyUp="javascript: mascara(this, mtel);" onKeyPress="javascript: return somenteNumeros(event);" >
                            </div>                            
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Email*
                                </p>
                                <input class="form-control" type="email" name="email" id="email" value="<?=$vet['email']?>" required>
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Acesso a pesquisa*
                                </p>
                                <select class="form-control" name="ind_pesquisa" id="ind_pesquisa" required>
                                    <option value="">Selecione ...</option>
                                    <option value="1" <?=(1 == $vet['ind_pesquisa']) ? 'selected' : ''?>>Sim</option>
                                    <option value="2" <?=(2 == $vet['ind_pesquisa']) ? 'selected' : ''?>>Não</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Acesso auto agendamento*
                                </p>
                                <select class="form-control" name="ind_atendimento" id="ind_atendimento" required>
                                    <option value="">Selecione ...</option>
                                    <option value="1" <?=(1 == $vet['ind_atendimento']) ? 'selected' : ''?>>Sim</option>
                                    <option value="2" <?=(2 == $vet['ind_atendimento']) ? 'selected' : ''?>>Não</option>
                                </select>
                            </div> 
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Data de cadastro*
                                </p>
                                <input class="form-control" type="text" name="data_cadastro" id="data_cadastro" value="<?=($vet['data_cadastro']) ? ConverteData($vet['data_cadastro']) : date("d/m/Y")?>" required maxlength="10" data-mask="99/99/9999" onKeyUp="javascript: return auto_data('data_cadastro');" onKeyPress="javascript: return somenteNumeros(event);">
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

                        <?
                        if($ind == 1)
                        {
                        ?>
                        <p class="font-bold  alert alert-info m-b-sm">
                            DADOS DO USUÁRIO MASTER
                        </p>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Nome*
                                </p>
                                <input type="text" name="user_nome" id="user_nome" class="form-control" value="<?=stripslashes($vet['user_nome'])?>" <?=($ind == 2) ? 'readonly' : 'required'?>>
                            </div>
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Email*
                                </p>
                                <input class="form-control" type="email" name="user_email" id="user_email" value="<?=$vet['user_email']?>" <?=($ind == 2) ? 'readonly' : 'required'?>>
                            </div>
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Senha<?=($ind == 1) ? '*' : ''?>
                                </p>
                                <input type="password" name="user_senha" id="user_senha" class="form-control" <?=($ind == 2) ? 'readonly' : 'required'?>>
                            </div>   
                        </div>
                        <br>
                        <?
                        }
                        ?>
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
    
    <?
    $str = "SELECT * FROM empresas ORDER BY razao_social";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    
    if($num > 0)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Lista de empresas cadastradas no sistema</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Nome / Nome Fantasia</th>
                                    <th>CPF / CNPJ</th>
                                    <th>Núm. Usúarios</th>
                                    <th>Telefone</th>
                                    <th>Pessoa</th>
                                    <th>Data de cadastro</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                    if($vet['pessoa'] == 1)
                                    {
                                        $tpessoa = 'Física';
                                        $nome = stripslashes($vet['nome']);
                                        $cpf_cnpj = $vet['cpf'];
                                    }
                                    else
                                    {
                                        $tpessoa = 'Jurídica';
                                        $nome = stripslashes($vet['nome_fantasia']);
                                        $cpf_cnpj = $vet['cnpj'];
                                    }
                                ?>
                                <tr class="gradeX">
                                    <td><?=$nome?></td>
                                    <td><?=$cpf_cnpj?></td>
                                    <td><?=$vet['num_usuarios']?></td>
                                    <td><a href="https://api.whatsapp.com/send?phone=55<?=preg_replace('/[^\d]/', '', $vet['telefone'])?>" target="_blank"><?=$vet['telefone']?></a></td>
                                    <td><?=$tpessoa?></td>
                                    <td><?=ConverteData($vet['data_cadastro'])?></td>  
                                    <td class="center">
                                        <a class="btn btn-warning btn-circle" type="button" title="editar" href="empresas.php?ind=2&codigo=<?=$vet['codigo']?>"><i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="empresas.php?cmd=del&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a> 
                                    </td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Nome / Nome Fantasia</th>
                                    <th>CPF / CNPJ</th>
                                    <th>Núm. Usúarios</th>
                                    <th>Telefone</th>
                                    <th>Pessoa</th>
                                    <th>Data de cadastro</th>
                                    <th>Ações</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?
    }
    else
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <p class="font-bold  alert alert-danger m-b-sm">
                Nenhuma empresa encontrada no sistema.
            </p>
        </div>
    </div>
    <?
    }
    ?>
</div>
<script>tipo_pessoa(<?=$pessoa?>)</script>
<?
include("includes/footer.php");
?>