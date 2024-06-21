<?
$page = 'tipos_lentes';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_tipos_lentes != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE)
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

$nome = addslashes($_POST['nome']);
$pontos = $_POST['pontos'];
$status = $_POST['status'];
$custo = str_replace(",", ".", $_POST['custo']);
$idlaboratorio = $_POST['idlaboratorio'];
$array_lentes = $_REQUEST['lentes'];

//LENTES
if(@count($array_lentes))
{
    $lentes = "|";
    for($i = 0; $i < @count($array_lentes); $i++)
        $lentes .= $array_lentes[$i]."|";
}

if($_POST['cmd'] == "add")
{   
    $str = "INSERT INTO tipos_lentes (idempresa, nome, pontos, custo, idlaboratorio, lentes, status) VALUES ('$adm_empresa', '$nome', '$pontos', '$custo', '$idlaboratorio', '$lentes', '$status')";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("tipos_lentes.php?ind_msg=1");
}

if($_POST['cmd'] == "edit")
{
    $str = "UPDATE tipos_lentes SET nome = '$nome', pontos = '$pontos', custo = '$custo', idlaboratorio = '$idlaboratorio', lentes = '$lentes', status = '$status' WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("tipos_lentes.php?ind_msg=2");
}

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM tipos_lentes WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("tipos_lentes.php?ind_msg=3");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Tipo de lente cadastrado com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Tipo de lente editado com sucesso!';
elseif($_GET['ind_msg'] == "3")
    $msg = 'Tipo de lente excluído com sucesso!';

$str = "SELECT * FROM tipos_lentes WHERE codigo = '$codigo'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);

$title = "'View Optica<br>Tipos de lentes'";
$columns = '0, 1, 2, 3';
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
        <h2>Tipos de lentes</h2>
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
                                    Nome*
                                </p>
                                <input type="text" name="nome" id="nome" class="form-control" value="<?=stripslashes($vet['nome'])?>" required>
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Pontos*
                                </p>
                                <input type="number" step="0.1" name="pontos" id="pontos" class="form-control"  value="<?=$vet['pontos']?>" required>
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
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Custo
                                </p>
                                <input class="form-control" type="text" name="custo" id="custo" value="<?=number_format($vet['custo'], 2, ',', '.')?>" <?=($adm_perfil != 1) ? 'readonly' : ''?> onKeyUp="javascript: return auto_valor('custo');" onKeyPress="javascript: return somenteNumeros(event);">
                            </div>
                            <div class="col-md-8">
                                <p class="font-bold">
                                    Laboratório
                                </p>
                                <select name="idlaboratorio" id="idlaboratorio" class="chosen-select" <?=($adm_perfil != 1) ? 'readonly' : ''?>>
                                    <option value="">Selecione ...</option>
                                    <?
                                    $strC = "SELECT * FROM laboratorios WHERE idempresa = '$adm_empresa' ORDER BY nome";
                                    $rsC  = mysqli_query($conexao, $strC) or die(mysqli_error($conexao));

                                    while($vetC = mysqli_fetch_array($rsC))
                                    {
                                    ?>
                                    <option value="<?=$vetC['codigo']?>" <?=($vetC['codigo'] == $vet['idlaboratorio']) ? 'selected' : ''?>><?=stripslashes($vetC['nome'])?></option>
                                    <?
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="font-bold">
                                    Vincular lentes (opcional)
                                </p>
                                <select class="form-control dual_select" name="lentes[]" id="lentes" multiple size="5">
                                    <?
                                    $strE = "SELECT * FROM tipos_lentes WHERE idempresa = '$adm_empresa' AND status = '1' AND codigo != '$codigo' ORDER BY nome";
                                    $rsE  = mysqli_query($conexao, $strE) or die(mysqli_error($conexao));

                                    while($vetE = mysqli_fetch_array($rsE))
                                    {
                                        $idlente = $vetE['codigo'];
                                    ?>
                                    <option value="<?=$vetE['codigo']?>" <?=(strstr($vet['lentes'], "|".$idlente."|")) ? 'selected' : ''?>><?=stripslashes($vetE['nome'])?></option>
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
    $str = "SELECT A.*, B.nome AS laboratorio
        FROM tipos_lentes A
        LEFT JOIN laboratorios B ON A.idlaboratorio = B.codigo
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
                    <h5>Lista de tipos de lentes cadastrados no sistema</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Pontos</th>
                                    <th>Custo</th>
                                    <th>Laboratório</th>
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
                                    <td><?=$vet['pontos']?></td>
                                    <td>R$ <?=number_format($vet['custo'], 2, ',', '.')?></td>
                                    <td><?=stripslashes($vet['laboratorio'])?></td>
                                    <td><?=$status?></td>
                                    <td class="center">
                                        <a class="btn btn-warning btn-circle" type="button" title="editar / visualizar" href="tipos_lentes.php?ind=2&codigo=<?=$vet['codigo']?>"><i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="tipos_lentes.php?cmd=del&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a> 
                                    </td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Nome</th>
                                    <th>Pontos</th>
                                    <th>Custo</th>
                                    <th>Laboratório</th>
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
                Nenhum tipo de lente encontrado no sistema.
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