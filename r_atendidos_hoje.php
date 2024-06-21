<?
$menu = 'relatorios';
$page = 'r_atendidos_hoje';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_relatorios != 1)
    die("Acesso negado!");

$codigo = anti_injection($_GET['codigo']); 

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM agendamentos WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("r_atendidos_hoje.php?ind_msg=1");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Consulta / agendamento excluído com sucesso!';

$title = "'View Óptica<br>Atendidos hoje'";
$columns = '0, 1, 2, 3, 4';
$order = ',order: [[ 4, "desc" ]]';
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Atendidos hoje</h2>
        <ol class="breadcrumb">
            <li class="active"><strong>Relatórios</strong></li>
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
        </div>
    </div>

    <?
    $str = "SELECT A.*, B.nome AS optometrista, C.nome AS paciente, C.cpf, C.cidade, C.estado
        FROM agendamentos A
        INNER JOIN usuarios B ON A.idoptometrista = B.codigo
        INNER JOIN pacientes C ON A.idpaciente = C.codigo
        WHERE A.idempresa = '$adm_empresa'
        AND A.status = '6'
        AND A.data = CURDATE()
        $strWhereP
        ORDER BY A.data";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    
    if($num > 0)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Lista de agendamentos atendidos hoje</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>Cidade / UF</th>
                                    <th>Especialista</th>
                                    <th>Atendido em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['paciente'])?></td>
                                    <td><?=$vet['cpf']?></td>
                                    <td><?=stripslashes($vet['cidade'])?> / <?=$vet['estado']?></td>
                                    <td><?=stripslashes($vet['optometrista'])?></td>
                                    <td><?=ConverteData($vet['data'])?> <?=substr($vet['hora_inicial'], 0, -3)?></td>
                                    <td>
                                        <a class="btn btn-info btn-circle" type="button" title="imprimir última consulta" href="prescricoes_ver.php?idagendamento=<?=$vet['codigo']?>&order=1" target="_blank"><i class="fa fa-print"></i></a>
                                        <a class="btn btn-default btn-circle" type="button" title="histórico" href="pacientes_historico.php?idpaciente=<?=$vet['idpaciente']?>" target="_blank"><i class="fa fa-user"></i></a>
                                        <a class="btn btn-info btn-circle" type="button" title="iniciar consulta" href="prescricoes.php?idagendamento=<?=$vet['codigo']?>"><i class="fa fa-check-square"></i></a>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="consultas_atendidas.php?cmd=del&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a> 
                                    </td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>Cidade / UF</th>
                                    <th>Especialista</th>
                                    <th>Atendido em</th>
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
                Nenhum agendamento atendido hoje no sistema.
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