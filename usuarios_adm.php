<?
$page = 'usuarios_adm';
include("includes/header.php");

if($_SESSION["adm_user"] != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE)
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

$nome = addslashes($_POST['nome']);
$funcao = addslashes($_POST['funcao']);
$telefone = $_POST['telefone'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$status = $_POST['status'];

if($_POST['cmd'] == "add")
{   
    $senha_aux = md5($senha);

    $str = "INSERT INTO usuarios_adm (nome, funcao, email, telefone, senha, status) VALUES ('$nome', '$funcao', '$email', '$telefone', '$senha_aux', '$status')";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("usuarios_adm.php?ind_msg=1");
}

if($_POST['cmd'] == "edit")
{
    $senha_aux = md5($senha);

    if($senha)
        $strSet = ", senha = '$senha_aux'";

    $str = "UPDATE usuarios_adm SET nome = '$nome', funcao = '$funcao', email = '$email', telefone = '$telefone', status = '$status' $strSet WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("usuarios_adm.php?ind_msg=2");
}

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM usuarios_adm WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("usuarios_adm.php?ind_msg=3");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Usuário cadastrado com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Usuário editado com sucesso!';
elseif($_GET['ind_msg'] == "3")
    $msg = 'Usuário excluído com sucesso!';

$str = "SELECT * FROM usuarios_adm WHERE codigo = '$codigo'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);

$title = "'View Optica<br>Usuários (admin)'";
$columns = '0, 1, 2, 3, 4';
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
</script>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Usuários (admin)</h2>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <?
            if(!empty($_GET['ind_msg']))
            {
            ?>
            <p class="font-bold  alert alert-success m-b-sm">
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
                        <input type="hidden" name="cmd">  
                        <input type="hidden" name="codigo" id="codigo" value="<?=$vet['codigo']?>">

                        <div class="row">
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Nome*
                                </p>
                                <input type="text" name="nome" id="nome" class="form-control" value="<?=stripslashes($vet['nome'])?>" required>
                            </div>
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Função
                                </p>
                                <input type="text" name="funcao" id="funcao" class="form-control" value="<?=stripslashes($vet['funcao'])?>" >
                            </div>
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Email*
                                </p>
                                <input class="form-control" type="email" name="email" id="email" value="<?=$vet['email']?>" required>
                            </div> 
                        </div>
                        <br>
                        <div class="row">  
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Telefone
                                </p>
                                <input class="form-control" type="text" name="telefone" id="telefone" value="<?=$vet['telefone']?>" maxlength="15" onKeyUp="javascript: mascara(this, mtel);" onKeyPress="javascript: return somenteNumeros(event);" >
                            </div> 
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Status*
                                </p>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="">Selecione ...</option>
                                    <option value="1" <?=(1 == $vet['status']) ? 'selected' : ''?>>Ativo</option>
                                    <option value="2" <?=(2 == $vet['status']) ? 'selected' : ''?>>Inativo</option>
                                </select>
                            </div>                          
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Senha<?=($ind == 1) ? '*' : ''?>
                                </p>
                                <input type="password" name="senha" id="senha" class="form-control" <?=($ind == 1) ? 'required' : ''?>>
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
    
    <?
    $str = "SELECT * FROM usuarios_adm ORDER BY nome";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    
    if($num > 0)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Lista de usuários administrativos cadastrados no sistema</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Função</th>
                                    <th>Email</th>
                                    <th>Telefone</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                    if($vet['status'] == 1)
                                        $status = '<span class="label label-success">Ativo</span>';
                                    else
                                        $status = '<span class="label label-danger">Inativo</span>';
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['nome'])?></td>
                                    <td><?=stripslashes($vet['funcao'])?></td>
                                    <td><?=$vet['email']?></td>
                                    <td><a href="https://api.whatsapp.com/send?phone=55<?=preg_replace('/[^\d]/', '', $vet['telefone'])?>" target="_blank"><?=$vet['telefone']?></a></td>
                                    <td><?=$status?></td>
                                    <td class="center">
                                        <a class="btn btn-warning btn-circle" type="button" title="editar / visualizar" href="usuarios_adm.php?ind=2&codigo=<?=$vet['codigo']?>"><i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="usuarios_adm.php?cmd=del&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a> 
                                    </td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Nome</th>
                                    <th>Função</th>
                                    <th>Email</th>
                                    <th>Telefone</th>
                                    <th>Status</th>
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
                Nenhum usuário administrativos encontrado no sistema.
            </p>
        </div>
    </div>
    <?
    }
    ?>
</div>
<?
include("includes/footer.php");
?>