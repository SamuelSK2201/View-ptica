<?
$menu = 'configuracoes';
$page = 'usuarios';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_usuarios != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE)
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

$idtipo = $_POST['idtipo'];
$nome = addslashes($_POST['nome']);
$funcao = addslashes($_POST['funcao']);
$telefone = $_POST['telefone'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$perfil = $_POST['perfil'];
$status = $_POST['status'];
$array_empresas = $_REQUEST['empresas'];

if($_POST['cmd'] == "add")
{   
    if(empresas_usuarios($conexao, $_SESSION['adm_empresa']) >= $_SESSION["adm_num_usuarios"])
        redireciona("usuarios.php?ind_msg=4");

    $senha_aux = md5($senha);

    $str = "INSERT INTO usuarios (idempresa, idtipo, nome, funcao, email, telefone, senha, perfil, status) 
        VALUES ('$adm_empresa', '$idtipo', '$nome', '$funcao', '$email', '$telefone', '$senha_aux', '$perfil', '$status')";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $codigo = mysqli_insert_id($conexao);

    if(@count($array_empresas))
    {
        for($i = 0; $i < @count($array_empresas); $i++)
        {
            $idempresa = $array_empresas[$i];

            $str = "INSERT INTO usuarios_emp (idusuario, idempresa) VALUES ('$codigo', '$idempresa')";
            $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        }
    }

    redireciona("usuarios.php?ind_msg=1");
}

if($_POST['cmd'] == "edit")
{
    $senha_aux = md5($senha);

    if($senha)
        $strSet = ", senha = '$senha_aux'";

    $str = "UPDATE usuarios SET idtipo = '$idtipo', nome = '$nome', funcao = '$funcao', email = '$email', telefone = '$telefone', perfil = '$perfil', status = '$status' $strSet WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM usuarios_emp WHERE idusuario = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    if(@count($array_empresas))
    {
        for($i = 0; $i < @count($array_empresas); $i++)
        {
            $idempresa = $array_empresas[$i];

            $str = "INSERT INTO usuarios_emp (idusuario, idempresa) VALUES ('$codigo', '$idempresa')";
            $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        }
    }

    redireciona("usuarios.php?ind_msg=2");
}

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM usuarios WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM usuarios_emp WHERE idusuario = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("usuarios.php?ind_msg=3");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Usuário cadastrado com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Usuário editado com sucesso!';
elseif($_GET['ind_msg'] == "3")
    $msg = 'Usuário excluído com sucesso!';
elseif($_GET['ind_msg'] == "4")
    $msg = 'Você possui um limite de '.$_SESSION["adm_num_usuarios"].' usuários para esta conta!<br>Este limite foi atingido!';

$str = "SELECT * FROM usuarios WHERE codigo = '$codigo'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);

$title = "'View Optica<br>Usuários'";
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
</script>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Usuários</h2>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <?
            if(!empty($_GET['ind_msg']) && $_GET['ind_msg'] != 4)
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
                        <input type="hidden" name="cmd">  
                        <input type="hidden" name="codigo" id="codigo" value="<?=$vet['codigo']?>">

                        <div class="row">
                            <div class="col-md-6">
                                <p class="font-bold">
                                    Nome*
                                </p>
                                <input type="text" name="nome" id="nome" class="form-control" value="<?=stripslashes($vet['nome'])?>" required>
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Email*
                                </p>
                                <input class="form-control" type="email" name="email" id="email" value="<?=$vet['email']?>" required>
                            </div> 
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Telefone
                                </p>
                                <input class="form-control" type="text" name="telefone" id="telefone" value="<?=$vet['telefone']?>" maxlength="15" onKeyUp="javascript: mascara(this, mtel);" onKeyPress="javascript: return somenteNumeros(event);" >
                            </div>
                        </div>
                        <br>
                        <div class="row">   
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Perfil*
                                </p>
                                <select class="form-control" name="perfil" id="perfil" required>
                                    <option value="">Selecione ...</option>
                                    <option value="1" <?=(1 == $vet['perfil']) ? 'selected' : ''?>>Administrador</option>
                                    <option value="2" <?=(2 == $vet['perfil']) ? 'selected' : ''?>>Optometrista</option>
                                    <option value="5" <?=(5 == $vet['perfil']) ? 'selected' : ''?>>Oftalmologista</option>
                                    <option value="3" <?=(3 == $vet['perfil']) ? 'selected' : ''?>>Atendente</option>
                                    <option value="4" <?=(4 == $vet['perfil']) ? 'selected' : ''?>>Pesquisador</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Papel*
                                </p>
                                <select name="idPapel" id="idtipo" class="form-control" required>
                                    <option value="">Selecione ...</option>
                                    <?
                                    $strC = "SELECT * FROM usuarios_tipos WHERE idempresa = '$adm_empresa' ORDER BY nome";
                                    $rsC  = mysqli_query($conexao, $strC) or die(mysqli_error($conexao));

                                    while($vetC = mysqli_fetch_array($rsC))
                                    {
                                    ?>
                                    <option value="<?=$vetC['codigo']?>" <?=($vetC['codigo'] == $vet['idtipo']) ? 'selected' : ''?>><?=stripslashes($vetC['nome'])?></option>
                                    <?
                                    }
                                    ?>
                                </select>
                            </div>                          
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Senha<?=($ind == 1) ? '*' : ''?>
                                </p>
                                <input type="password" name="senha" id="senha" class="form-control" <?=($ind == 1) ? 'required' : ''?>>
                            </div>                            
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Status*
                                </p>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="">Selecione ...</option>
                                    <option value="1" <?=(1 == $vet['status']) ? 'selected' : ''?>>Ativo</option>
                                    <option value="2" <?=(2 == $vet['status']) ? 'selected' : ''?>>Inativo</option>
                                </select>
                            </div></div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="font-bold">
                                    Empresas
                                </p>
                                <select class="form-control dual_select" name="empresas[]" id="empresas" multiple size="5">
                                    <?
                                    $strE = "SELECT * FROM empresas WHERE codigo != '$adm_empresa' ORDER BY nome_fantasia";
                                    $rsE  = mysqli_query($conexao, $strE) or die(mysqli_error($conexao));

                                    while($vetE = mysqli_fetch_array($rsE))
                                    {
                                        $idempresa = $vetE['codigo'];

                                        $strU = "SELECT codigo FROM usuarios_emp WHERE idusuario = '$codigo' AND idempresa = '$idempresa'";
                                        $rsU  = mysqli_query($conexao, $strU) or die(mysqli_error($conexao));
                                        $numU = mysqli_num_rows($rsU);
                                    ?>
                                    <option value="<?=$vetE['codigo']?>" <?=($vet['idempresa'] == $idempresa || $numU > 0) ? 'selected' : ''?>><?=stripslashes($vetE['nome_fantasia'])?></option>
                                    <?
                                    }
                                    ?>
                                </select>
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
    $str = "SELECT A.*, B.nome AS tipo 
        FROM usuarios A
        LEFT JOIN usuarios_tipos B ON A.idtipo = B.codigo AND A.idempresa = B.idempresa
        WHERE A.idempresa = '$adm_empresa'
        ORDER BY A.nome";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    
    if($num > 0)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Lista de usuários cadastrados no sistema</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Telefone</th>
                                    <th>Perfil</th>
                                    <th>Papel</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                    if($vet['perfil'] == 1)
                                        $perfil = 'Administrador';
                                    elseif($vet['perfil'] == 2)
                                        $perfil = 'Optometrista';
                                    elseif($vet['perfil'] == 3)
                                        $perfil = 'Atendente';
                                    elseif($vet['perfil'] == 4)
                                        $perfil = 'Pesquisador';
                                    elseif($vet['perfil'] == 5)
                                        $perfil = 'Oftalmologista';

                                    if($vet['status'] == 1)
                                        $status = '<span class="label label-success">Ativo</span>';
                                    else
                                        $status = '<span class="label label-danger">Inativo</span>';
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['nome'])?></td>
                                    <td><?=$vet['email']?></td>
                                    <td><a href="https://api.whatsapp.com/send?phone=55<?=preg_replace('/[^\d]/', '', $vet['telefone'])?>" target="_blank"><?=$vet['telefone']?></a></td>
                                    <td><?=$perfil?></td>
                                    <td><?=($vet['tipo']) ? stripslashes($vet['tipo']) : 'Não informado'?></td>
                                    <td><?=$status?></td>
                                    <td class="center">
                                        <a class="btn btn-warning btn-circle" type="button" title="editar / visualizar" href="usuarios.php?ind=2&codigo=<?=$vet['codigo']?>"><i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="usuarios.php?cmd=del&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a> 
                                    </td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Telefone</th>
                                    <th>Perfil</th>
                                    <th>Papel</th>
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
                Nenhum usuário encontrado no sistema.
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