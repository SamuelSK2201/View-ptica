<?
$menu = 'configuracoes';
$page = 'procedimentos';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_procedimentos != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE)
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

$descricao = addslashes($_POST['descricao']);
$cor = $_POST['cor'];
$tempo = $_POST['tempo'];

if($_POST['cmd'] == "add")
{   
    $str = "INSERT INTO procedimentos (idempresa, descricao, cor, tempo) VALUES ('$adm_empresa', '$descricao', '$cor', '$tempo')";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("procedimentos.php?ind_msg=1");
}

if($_POST['cmd'] == "edit")
{
    $str = "UPDATE procedimentos SET descricao = '$descricao', cor = '$cor', tempo = '$tempo' WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("procedimentos.php?ind_msg=2");
}

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM procedimentos WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("procedimentos.php?ind_msg=3");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Procedimento cadastrado com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Procedimento editado com sucesso!';
elseif($_GET['ind_msg'] == "3")
    $msg = 'Procedimento excluído com sucesso!';

$str = "SELECT * FROM procedimentos WHERE codigo = '$codigo'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);

$title = "'View Optica<br>Procedimentos'";
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
        <h2>Procedimentos</h2>
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
                            <div class="col-md-8">
                                <p class="font-bold">
                                    Descrição*
                                </p>
                                <input type="text" name="descricao" id="descricao" class="form-control" value="<?=stripslashes($vet['descricao'])?>" required>
                            </div>
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Cor*
                                </p>
                                <input type="text" name="cor" id="cor" class="form-control demo1" value="<?=$vet['cor']?>" required>
                            </div>
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Tempo (minutos)*
                                </p>
                                <input class="form-control" type="number" name="tempo" id="tempo" value="<?=$vet['tempo']?>" required min="0" >
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
    $str = "SELECT * FROM procedimentos WHERE idempresa = '$adm_empresa' ORDER BY descricao";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    
    if($num > 0)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Lista de procedimentos cadastrados no sistema</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Descrição</th>
                                    <th>Cor</th>
                                    <th>Tempo (minutos)</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['descricao'])?></td>
                                    <td><span class="label" style="background-color: <?=$vet['cor']?>">&nbsp;</span></td>
                                    <td><?=$vet['tempo']?></td>
                                    <td class="center">
                                        <a class="btn btn-warning btn-circle" type="button" title="editar / visualizar" href="procedimentos.php?ind=2&codigo=<?=$vet['codigo']?>"><i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="procedimentos.php?cmd=del&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a> 
                                    </td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Descrição</th>
                                    <th>Cor</th>
                                    <th>Tempo (minutos)</th>
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
                Nenhum procedimento encontrado no sistema.
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