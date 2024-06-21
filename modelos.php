<?
$page = 'modelos';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_modelos != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE)
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

$titulo = addslashes($_POST['titulo']);
$categoria = $_POST['categoria'];
$descricao = addslashes($_POST['descricao']);
$status = $_POST['status'];

if($_POST['cmd'] == "add")
{   
    $str = "INSERT INTO modelos (idempresa, titulo, categoria, descricao, status) VALUES ('$adm_empresa', '$titulo', '$categoria', '$descricao', '$status')";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("modelos.php?ind_msg=1");
}

if($_POST['cmd'] == "edit")
{
    $str = "UPDATE modelos SET titulo = '$titulo', categoria = '$categoria', descricao = '$descricao', status = '$status' WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("modelos.php?ind_msg=2");
}

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM modelos WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("modelos.php?ind_msg=3");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Modelo cadastrado com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Modelo editado com sucesso!';
elseif($_GET['ind_msg'] == "3")
    $msg = 'Modelo excluído com sucesso!';

$str = "SELECT * FROM modelos WHERE codigo = '$codigo'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);

$title = "'View Optica<br>Modelos'";
$columns = '0, 1, 2';
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
        <h2>Modelos</h2>
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
                            <div class="col-md-6">
                                <p class="font-bold">
                                    Título*
                                </p>
                                <input type="text" name="titulo" id="titulo" class="form-control" value="<?=stripslashes($vet['titulo'])?>" required>
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Categoria*
                                </p>
                                <select class="form-control" name="categoria" id="categoria" required>
                                    <option value="">Selecione ...</option>
                                    <option value="1" <?=(1 == $vet['status']) ? 'selected' : ''?>>Prescrição para óculos</option>
                                    <option value="2" <?=(2 == $vet['status']) ? 'selected' : ''?>>Prescrição lente de contato</option>    
                                    <option value="3" <?=(3 == $vet['status']) ? 'selected' : ''?>>Laudo</option>    
                                    <option value="4" <?=(4 == $vet['status']) ? 'selected' : ''?>>Declaração</option>    
                                    <option value="5" <?=(5 == $vet['status']) ? 'selected' : ''?>>Encaminhamento</option>                         
                                </select>
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
                            </div>  
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="font-bold">
                                    Descrição*
                                </p>
                                <textarea name="descricao" id="editor_txt" rows="10" required ><?=stripslashes($vet['descricao'])?></textarea>
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
    $str = "SELECT * FROM modelos WHERE idempresa = '$adm_empresa' ORDER BY titulo";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    
    if($num > 0)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Lista de modelos cadastrados no sistema</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Categoria</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                    if($vet['categoria'] == 1)
                                        $categoria = 'Prescrição óculos';
                                    elseif($vet['categoria'] == 2)
                                        $categoria = 'Prescrição lentes';
                                    elseif($vet['categoria'] == 3)
                                        $categoria = 'Laudo';
                                    elseif($vet['categoria'] == 4)
                                        $categoria = 'Declaração';
                                    else
                                        $categoria = 'Encaminhamento';

                                    if($vet['status'] == 1)
                                        $status = '<span class="label label-success">Ativo</span>';
                                    else
                                        $status = '<span class="label label-danger">Inativo</span>';
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['titulo'])?></td>
                                    <td><?=$categoria?></td>
                                    <td><?=$status?></td>
                                    <td class="center">
                                        <a class="btn btn-warning btn-circle" type="button" title="editar / visualizar" href="modelos.php?ind=2&codigo=<?=$vet['codigo']?>"><i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="modelos.php?cmd=del&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a> 
                                    </td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Título</th>
                                    <th>Categoria</th>
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
                Nenhum modelo encontrado no sistema.
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