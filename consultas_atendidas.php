<?
$menu = 'agenda';
$page = 'consultas_atendidas';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_consultas != 1)
    die("Acesso negado!");

$data_inicial = $_POST['data_inicial'];
$data_final = $_POST['data_final'];

$codigo = anti_injection($_GET['codigo']); 

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM agendamentos WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("consultas_atendidas.php?ind_msg=1");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Consulta / agendamento excluído com sucesso!';

$title = "'View Óptica<br>Consultas atendidas'";
$columns = '0, 1, 2, 3, 4';
$order = ',order: [[ 4, "desc" ]]';
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Consultas atendidas</h2>
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

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">    
                    <h5><i>Pesquise utilizando o formulário abaixo</i></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-down"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form method="post" class="form-horizontal" name="form" id="form" enctype="multipart/form-data">
                        <input type="hidden" name="cmd" value="search">                       
                        
                        <div class="row">
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Data inicial
                                </p>
                                <div class="form-group" id="data_1" style="margin-left: 0px;">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input style="width: 90%" type="text" class="form-control" name="data_inicial" id="data_inicial" value="<?=($data_inicial) ? $data_inicial : ''?>" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Data final
                                </p>
                                <div class="form-group" id="data_1" style="margin-left: 0px;">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input style="width: 90%" type="text" class="form-control" name="data_final" id="data_final" value="<?=($data_final) ? $data_final : ''?>" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12"> 
                                <button type="submit" class="btn btn-primary">Pesquisar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?
    if($_POST['cmd'] == 'search')
    {
        $strWhere = "";

        if($data_inicial)
            $strWhere .= " AND A.data >= '".ConverteData($data_inicial)."'";

        if($data_final)
            $strWhere .= " AND A.data <= '".ConverteData($data_final)."'";

        $str = "SELECT A.*, B.nome AS optometrista, C.nome AS paciente, C.cpf, C.cidade, C.estado
            FROM agendamentos A
            LEFT JOIN usuarios B ON A.idoptometrista = B.codigo
            INNER JOIN pacientes C ON A.idpaciente = C.codigo
            WHERE A.idempresa = '$adm_empresa'
            AND A.status = '6'
            $strWhere
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
                    <h5>Lista de consultas atendidas</h5>
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
                                    <th style="width: 12%">Atendido em</th>
                                    <th style="width: 18%">Ações</th>
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
                                        <a class="btn btn-success btn-circle" type="button" title="imprimir consultas" href="prescricoes_ver.php?idagendamento=<?=$vet['codigo']?>" target="_blank"><i class="fa fa-print"></i></a>
                                        <a class="btn btn-default btn-circle" type="button" title="histórico" href="pacientes_historico.php?idpaciente=<?=$vet['idpaciente']?>" target="_blank"><i class="fa fa-user"></i></a>
                                        <a class="btn btn-primary btn-circle" type="button" title="iniciar consulta" href="prescricoes.php?idagendamento=<?=$vet['codigo']?>&s=6"><i class="fa fa-check-square"></i></a>
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
                Nenhuma consulta atendida no sistema.
            </p>
        </div>
    </div>
    <?
        }
    }
    ?>
</div>
<?
include("includes/footer.php");
?>