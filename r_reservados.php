<?
$menu = 'funil';
$page = 'r_reservados';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_prospeccao != 1)
    die("Acesso negado!");

if($perm_relatorios != 1 && $perm_relatorios_reservados != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE)
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM prospeccao WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prospeccao_respostas WHERE idprospeccao = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("r_reservados.php?ind_msg=1");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Prospecção excluída com sucesso!';

$title = "'View Óptica<br>Reservados'";
$columns = '0, 1, 2, 3, 4';
$order = ',order: [[ 4, "desc" ]]';
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Reservados</h2>
        <ol class="breadcrumb">
            <li class="active"><strong>Funil</strong></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">  
    <?
    $str = "SELECT * FROM prospeccao WHERE idempresa = '$adm_empresa' AND status = '4' $strWherePesq ORDER BY data";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    
    if($num > 0)
    {
    ?>
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
                    <h5>Lista de pesquisas marcadas como RESERVADO</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" style="font-size: 10px;" >
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Sobrenome</th>
                                    <th>Data de nascimento (idade)</th>
                                    <th style="width: 40%">Status</th>
                                    <th>Agendar para</th>
                                    <th>Horário</th>
                                    <th>Telefones</th>
                                    <th>Status atual</th>
                                    <th style="width: 10%">Modificar status</th>
                                    <th style="width: 10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                    $codigo = $vet['codigo'];
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['nome'])?></td>
                                    <td><?=stripslashes($vet['sobrenome'])?></td>
                                    <td><?=$vet['data_nascimento']?></td>
                                    <td><input type="text" name="exame_<?=$codigo?>" id="exame_<?=$codigo?>" class="form-control" value="<?=$vet['data_exame']?>" onblur="javascript: altera_data_exame_prospeccao('<?=$codigo?>')" style="font-size: 10px; width: 100%"></td>
                                    <td><?=ConverteData($vet['data_agendamento'])?></td>
                                    <td><?=substr($vet['hora_inicial'], 0, -3)?></td>
                                    <td>
                                        Tel. 1: <?=($vet['telefone']) ? '<a href="https://api.whatsapp.com/send?phone=55'.preg_replace('/[^\d]/', '', $vet['telefone']).'" target="_blank">'.$vet['telefone'].'</a>' : 'Não informado'?><br>
                                        Tel. 2: <?=($vet['telefone2']) ? '<a href="https://api.whatsapp.com/send?phone=55'.preg_replace('/[^\d]/', '', $vet['telefone2']).'" target="_blank">'.$vet['telefone2'].'</a>' : 'Não informado'?>
                                    </td>
                                    <td><div class="status_<?=$codigo?>"><span class="label label-danger">Reservado</span></div></td>
                                    <td class="center">
                                        <div class="prospeccao_<?=$codigo?>" style="float: left; margin-right: 2px;">
                                            <a class="btn btn-primary btn-circle" type="button" title="transformar em FINALIZADO" onclick="javascript: altera_status_prospeccao('<?=$codigo?>', '<?=$vet['idagendamento']?>', 5)"><i class='fa fa-check-square'></i></a>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <a class="btn btn-warning btn-circle" type="button" title="editar / visualizar" href="prospeccao.php?ind=2&codigo=<?=$vet['codigo']?>&url=<?=base64_encode('r_reservados.php')?>"><i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="r_reservados.php?cmd=del&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a> 
                                    </td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Nome</th>
                                    <th>Sobrenome</th>
                                    <th>Data de nascimento (idade)</th>
                                    <th>Status</th>
                                    <th>Agendar para</th>
                                    <th>Horário</th>
                                    <th>Telefones</th>
                                    <th>Status atual</th>
                                    <th>Modificar status</th>
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
                Nenhuma pesquisa maracada como RESERVADO no sistema.
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