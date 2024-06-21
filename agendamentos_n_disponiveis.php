<?
$menu = 'agenda';
$page = 'agendamentos_disponiveis';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_agenda != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE) 
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

$idagendamento = $_REQUEST['idagendamento'];
$titulo = addslashes($_POST['titulo']);
$data_inicial = ConverteData($_POST['data_inicial']);
$data_final = ConverteData($_POST['data_final']);
$periodo = $_POST['periodo'];
$array_periodo = explode(" - ", $_POST['periodo']);
$hora_inicial = $array_periodo[0];
$hora_final = $array_periodo[1];

$strF = "SELECT * FROM agendamentos_disponiveis WHERE codigo = '$idagendamento'";
$rsF  = mysqli_query($conexao, $strF) or die(mysqli_error($conexao));
$vetF = mysqli_fetch_array($rsF);

$str = "SELECT * FROM agendamentos_n_disponiveis WHERE codigo = '$codigo'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);

if($_POST['cmd'] == "add")
{
    $str = "INSERT INTO agendamentos_n_disponiveis (idagendamento, titulo, data_inicial, data_final, periodo, hora_inicial, hora_final) VALUES ('$idagendamento', '$titulo', '$data_inicial', '$data_final', '$periodo', '$hora_inicial', '$hora_final')";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("agendamentos_n_disponiveis.php?ind_msg=1&idagendamento=$idagendamento");
}

if($_POST['cmd'] == "edit")
{
    $str = "UPDATE agendamentos_n_disponiveis SET idagendamento = '$idagendamento', titulo = '$titulo', data_inicial = '$data_inicial', data_final = '$data_final', periodo = '$periodo', hora_inicial = '$hora_inicial', hora_final = '$hora_final' WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("agendamentos_n_disponiveis.php?ind_msg=2&idagendamento=$idagendamento");
}

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM agendamentos_n_disponiveis WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        
    redireciona("agendamentos_n_disponiveis.php?ind_msg=3&idagendamento=$idagendamento");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Horários indisponíveis cadastradas com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Horários indisponíveis editados com sucesso!';
elseif($_GET['ind_msg'] == "3")
    $msg = 'Horários indisponíveis excluídos com sucesso!';

$title = "'View Optica<br>Horários indisponíveis'";
$columns = '0, 1, 2, 3';
$order = ',order: [[ 1, "desc" ]]';
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
        <h2>Horários indisponíveis</h2>
        <ol class="breadcrumb">
            <li><strong><?=ConverteData($vetF['data_inicial'])?> - <?=ConverteData($vetF['data_final'])?></strong></li>
            <li><strong>Auto agendamento</strong></li>
        </ol>
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
                        <input type="hidden" name="cmd" >  
                        <input type="hidden" name="codigo" id="codigo" value="<?=$vet['codigo']?>"> 
                        <input type="hidden" name="idagendamento" id="idagendamento" value="<?=$idagendamento?>">                      

                        <div class="row">
                            <div class="col-md-12">
                                <p class="font-bold">
                                    Título*
                                </p>
                                <input type="text" name="titulo" id="titulo" class="form-control" value="<?=stripslashes($vet['titulo'])?>" required>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Data inicial*
                                </p>
                                <div class="form-group" id="data_1">
                                    <div class="input-group date" style="margin-left: 15px;">
                                        <span class="input-group-addon" ><i class="fa fa-calendar"></i></span><input style="width: 90%;" type="text" class="form-control" name="data_inicial" id="data_inicial" value="<?=($vet['data_inicial']) ? ConverteData($vet['data_inicial']) : date("d/m/Y")?>" required maxlength="10" data-mask="99/99/9999" onKeyUp="javascript: return auto_data('data_inicial');" onKeyPress="javascript: return somenteNumeros(event);" onchange="javascript: document.form_c.submit();">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Data final*
                                </p>
                                <div class="form-group" id="data_1">
                                    <div class="input-group date" style="margin-left: 15px;">
                                        <span class="input-group-addon" ><i class="fa fa-calendar"></i></span><input style="width: 90%;" type="text" class="form-control" name="data_final" id="data_final" value="<?=($vet['data_final']) ? ConverteData($vet['data_final']) : date("d/m/Y")?>" required maxlength="10" data-mask="99/99/9999" onKeyUp="javascript: return auto_data('data_final');" onKeyPress="javascript: return somenteNumeros(event);" onchange="javascript: document.form_c.submit();">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Horário inicial - final*
                                </p>
                                <input class="form-control" type="text" name="periodo" id="periodo" value="<?=$vet['periodo']?>" required maxlength="13" data-mask="99:99 - 99:99" onKeyPress="javascript: return somenteNumeros(event);">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-12"> 
                                <?
                                if($ind == 1)
                                {
                                ?>
                                <button type="submit" class="btn btn-primary" onClick="javascript: valida(1);">Cadastrar</button>
                                <?
                                }
                                else
                                {
                                ?>
                                <button type="submit" class="btn btn-primary" onClick="javascript: valida(2);">Alterar</button>
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
    $str = "SELECT * FROM agendamentos_n_disponiveis WHERE idagendamento = '$idagendamento' ORDER BY codigo";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    
    if($num > 0)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Data inicial</th>
                                    <th>Data final</th>
                                    <th>Horário inicial - final</th>
                                    <th style="width: 10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['titulo'])?></td>
                                    <td><?=ConverteData($vet['data_inicial'])?></td>
                                    <td><?=ConverteData($vet['data_final'])?></td>
                                    <td><?=$vet['hora_inicial']?> - <?=$vet['hora_final']?></td>
                                    <td class="center">
                                        <a class="btn btn-warning btn-circle" type="button" title="editar / visualizar" href="agendamentos_n_disponiveis.php?ind=2&idagendamento=<?=$idagendamento?>&codigo=<?=$vet['codigo']?>"><i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="agendamentos_n_disponiveis.php?cmd=del&idagendamento=<?=$idagendamento?>&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a>
                                    </td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Título</th>
                                    <th>Data inicial</th>
                                    <th>Data final</th>
                                    <th>Horário inicial - final</th>
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
                Nenhum horários indisponível encontrado no sistema para este agendamento.
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