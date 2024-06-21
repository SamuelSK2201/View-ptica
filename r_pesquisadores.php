<?
$menu = 'relatorios';
$page = 'r_pesquisadores';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_relatorios != 1 && $perm_prospeccao != 1 && $ind_pesquisa == 1)
    die("Acesso negado!");

$data_inicial = $_POST['data_inicial'];
$data_final = $_POST['data_final'];
$idpesquisador = $_POST['idpesquisador'];

$title = "'View Óptica<br>Pesquisadores x Pesquisas'";
$columns = '0, 1, 2, 3, 4';
$order = ',order: [[ 0, "asc" ]]';
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Pesquisadores x Pesquisas</h2>
        <ol class="breadcrumb">
            <li class="active"><strong>Relatórios</strong></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">  
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
                            <?
                            if($adm_perfil == 1 || $adm_perfil == 3)
                            {
                            ?>
                            <div class="col-md-6">
                                <p class="font-bold">
                                    Pesquisador
                                </p>
                                <select class="chosen-select" name="idpesquisador" id="idpesquisador" data-placeholder="Selecione um pesquisador ..." tabindex="10" >
                                    <option value="">Todos</option>
                                    <?
                                    $strC = "SELECT DISTINCT A.* 
                                        FROM usuarios A
                                        INNER JOIN usuarios_tipos B ON A.idtipo = B.codigo
                                        WHERE A.idempresa = '$adm_empresa' 
                                        AND B.prospeccao = '1' 
                                        AND A.status = '1' 
                                        ORDER BY A.nome";
                                    $rsC  = mysqli_query($conexao, $strC) or die(mysqli_error($conexao));

                                    while($vetC = mysqli_fetch_array($rsC))
                                    {
                                    ?>
                                    <option value="<?=$vetC['codigo']?>" <?=($vetC['codigo'] == $idpesquisador) ? 'selected' : ''?>><?=stripslashes($vetC['nome'])?></option>
                                    <?
                                    }
                                    ?>
                                </select>
                            </div> 
                            <?
                            }
                            ?>
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
    if($_REQUEST['cmd'] == 'search')
    {
        $strWhere = "";
        $strWhereC = "";

        if($adm_perfil == 4)
        {
            $strWhere .= " AND A.codigo = '$adm_codigo'";
        }

        if($idpesquisador)
        {
            $strWhere .= " AND A.codigo = '$idpesquisador'";
        }

        if($data_inicial)
            $strWhereC .= " AND A.data_agendamento >= '".ConverteData($data_inicial)."'";

        if($data_final)
            $strWhereC .= " AND A.data_agendamento <= '".ConverteData($data_final)."'";

        $str = "SELECT DISTINCT A.* 
            FROM usuarios A
            INNER JOIN usuarios_tipos B ON A.idtipo = B.codigo
            WHERE A.idempresa = '$adm_empresa' 
            AND B.prospeccao = '1'
            AND A.status = '1' 
            $strWhere
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
                    <h5>Lista de pesquisadores de <b><?=$data_inicial?></b> até <b><?=$data_final?></b></h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Pesquisador</th>
                                    <th>Pesquisa</th>
                                    <th>Agendado (stand by)</th>
                                    <th>Reagendar</th>
                                    <th>Confirmado</th>
                                    <th>Fila de espera</th>
                                    <th>Atendido</th>
                                    <th>Reservado</th>
                                    <th>Finalizado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                $array_agendado = array();
                                $array_reagendar = array();
                                $array_confirmado = array();
                                $array_fila = array();
                                $array_atendido = array();
                                $array_reservado = array();
                                $array_finalizado = array();
                                $array_pesquisa = array();

                                while($vet = mysqli_fetch_array($rs))
                                {
                                    $idusuario = $vet['codigo'];

                                    //STATUS
                                    $strP = "SELECT A.*, CONCAT(A.nome,' ',A.sobrenome) AS paciente
                                        FROM prospeccao A 
                                        INNER JOIN pacientes B ON A.idpaciente = B.codigo
                                        WHERE A.idempresa = '$adm_empresa' 
                                        AND A.idusuario = '$idusuario'
                                        AND A.status IN ('1', '2', '3', '4', '5', '6', '7', '8') 
                                        $strWhereC
                                        ORDER BY A.nome";
                                    $rsP  = mysqli_query($conexao, $strP) or die(mysqli_error($conexao));
                                    $numP = mysqli_num_rows($rsP);

                                    $agendado = 0;
                                    $reagendar = 0;
                                    $confirmado = 0;
                                    $fila = 0;
                                    $atendido = 0;
                                    $reservado = 0;
                                    $finalizado = 0;
                                    $pesquisa = 0;

                                    while($vetP = mysqli_fetch_array($rsP))
                                    {
                                        if($vetP['status'] == 1)
                                        {
                                            $agendado++;
                                            $array_agendado[$idusuario]['nome'][$agendado] = $vetP['paciente'];
                                            $array_agendado[$idusuario]['telefone'][$agendado] = $vetP['telefone'];
                                            $array_agendado[$idusuario]['telefone2'][$agendado] = $vetP['telefone2'];
                                        }

                                        if($vetP['status'] == 2)
                                        {
                                            $confirmado++;
                                            $array_confirmado[$idusuario]['nome'][$confirmado] = $vetP['paciente'];
                                            $array_confirmado[$idusuario]['telefone'][$confirmado] = $vetP['telefone'];
                                            $array_confirmado[$idusuario]['telefone2'][$confirmado] = $vetP['telefone2'];
                                        }

                                        if($vetP['status'] == 3)
                                        {
                                            $fila++;
                                            $array_fila[$idusuario]['nome'][$fila] = $vetP['paciente'];
                                            $array_fila[$idusuario]['telefone'][$fila] = $vetP['telefone'];
                                            $array_fila[$idusuario]['telefone2'][$fila] = $vetP['telefone2'];
                                        }

                                        if($vetP['status'] == 4)
                                        {
                                            $reservado++;
                                            $array_reservado[$idusuario]['nome'][$reservado] = $vetP['paciente'];
                                            $array_reservado[$idusuario]['telefone'][$reservado] = $vetP['telefone'];
                                            $array_reservado[$idusuario]['telefone2'][$reservado] = $vetP['telefone2'];
                                        }

                                        if($vetP['status'] == 5)
                                        {
                                            $finalizado++;
                                            $array_finalizado[$idusuario]['nome'][$finalizado] = $vetP['paciente'];
                                            $array_finalizado[$idusuario]['telefone'][$finalizado] = $vetP['telefone'];
                                            $array_finalizado[$idusuario]['telefone2'][$finalizado] = $vetP['telefone2'];
                                        }

                                        if($vetP['status'] == 6)
                                        {
                                            $pesquisa++;
                                            $array_pesquisa[$idusuario]['nome'][$pesquisa] = $vetP['paciente'];
                                            $array_pesquisa[$idusuario]['telefone'][$pesquisa] = $vetP['telefone'];
                                            $array_pesquisa[$idusuario]['telefone2'][$pesquisa] = $vetP['telefone2'];
                                        }

                                        if($vetP['status'] == 7)
                                        {
                                            $reagendar++;
                                            $array_reagendar[$idusuario]['nome'][$reagendar] = $vetP['paciente'];
                                            $array_reagendar[$idusuario]['telefone'][$reagendar] = $vetP['telefone'];
                                            $array_reagendar[$idusuario]['telefone2'][$reagendar] = $vetP['telefone2'];
                                        }

                                        if($vetP['status'] == 8)
                                        {
                                            $atendido++;
                                            $array_atendido[$idusuario]['nome'][$atendido] = $vetP['paciente'];
                                            $array_atendido[$idusuario]['telefone'][$atendido] = $vetP['telefone'];
                                            $array_atendido[$idusuario]['telefone2'][$atendido] = $vetP['telefone2'];
                                        }
                                    }
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['nome'])?></td>
                                    <td><?=$pesquisa?> <?if ($pesquisa > 0) {?><a class="btn btn-default btn-circle" type="button" title="visualizar" data-toggle="modal" data-target="#pesquisa_<?=$vet['codigo']?>"><i class="fa fa-eye"></i></a><?}?></td>
                                    <td><?=$agendado?> <?if ($agendado > 0) {?><a class="btn btn-default btn-circle" type="button" title="visualizar" data-toggle="modal" data-target="#agendado_<?=$vet['codigo']?>"><i class="fa fa-eye"></i></a><?}?></td>
                                    <td><?=$reagendar?> <?if ($reagendar > 0) {?><a class="btn btn-default btn-circle" type="button" title="visualizar" data-toggle="modal" data-target="#reagendar_<?=$vet['codigo']?>"><i class="fa fa-eye"></i></a><?}?></td>
                                    <td><?=$confirmado?> <?if ($confirmado > 0) {?><a class="btn btn-default btn-circle" type="button" title="visualizar" data-toggle="modal" data-target="#confirmado_<?=$vet['codigo']?>"><i class="fa fa-eye"></i></a><?}?></td>
                                    <td><?=$fila?> <?if ($fila > 0) {?><a class="btn btn-default btn-circle" type="button" title="visualizar" data-toggle="modal" data-target="#fila_<?=$vet['codigo']?>"><i class="fa fa-eye"></i></a><?}?></td>
                                    <td><?=$atendido?> <?if ($atendido > 0) {?><a class="btn btn-default btn-circle" type="button" title="visualizar" data-toggle="modal" data-target="#atendido_<?=$vet['codigo']?>"><i class="fa fa-eye"></i></a><?}?></td>
                                    <td><?=$reservado?> <?if ($reservado > 0) {?><a class="btn btn-default btn-circle" type="button" title="visualizar" data-toggle="modal" data-target="#reservado_<?=$vet['codigo']?>"><i class="fa fa-eye"></i></a><?}?></td>
                                    <td><?=$finalizado?> <?if ($finalizado > 0) {?><a class="btn btn-default btn-circle" type="button" title="visualizar" data-toggle="modal" data-target="#finalizado_<?=$vet['codigo']?>"><i class="fa fa-eye"></i></a><?}?></td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Pesquisador</th>
                                    <th>Pesquisa</th>
                                    <th>Agendado (stand by)</th>
                                    <th>Reagendar</th>
                                    <th>Confirmado</th>
                                    <th>Fila de espera</th>
                                    <th>Atendido</th>
                                    <th>Reservado</th>
                                    <th>Finalizado</th>
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
                Nenhuma consulta encontrada no período de <b><?=$data_inicial?></b> até <b><?=$data_final?></b>.
            </p>
        </div>
    </div>
    <?
        }
    }
    ?>
</div>

<?
$str = "SELECT DISTINCT A.* 
    FROM usuarios A
    INNER JOIN usuarios_tipos B ON A.idtipo = B.codigo
    WHERE A.idempresa = '$adm_empresa' 
    AND B.prospeccao = '1'
    AND A.status = '1' 
    $strWhere
    ORDER BY A.nome";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

while($vet = mysqli_fetch_array($rs))
{
    $idusuario = $vet['codigo'];
    $usuario = stripslashes($vet['nome']);

    for($i = 1; $i <= 8; $i++)
    {
        $array_result[$idusuario] = array();

        if($i == 1)
        {
            $tipo = 'agendado_'.$vet['codigo'];
            $str_tipo = 'Agendado (stand by)';

            $array_result[$idusuario] = $array_agendado[$idusuario];
        }
        elseif($i == 2)
        {
            $tipo = 'confirmado_'.$vet['codigo'];
            $str_tipo = 'Confirmado';

            $array_result[$idusuario] = $array_confirmado[$idusuario];
        }
        elseif($i == 3)
        {
            $tipo = 'fila_'.$vet['codigo'];
            $str_tipo = 'Fila de espera';

            $array_result[$idusuario] = $array_fila[$idusuario];
        }
        elseif($i == 4)
        {
            $tipo = 'reservado_'.$vet['codigo'];
            $str_tipo = 'Reservado';

            $array_result[$idusuario] = $array_reservado[$idusuario];
        }
        elseif($i == 5)
        {
            $tipo = 'finalizado_'.$vet['codigo'];
            $str_tipo = 'Finalizado';

            $array_result[$idusuario] = $array_finalizado[$idusuario];
        }
        elseif($i == 6)
        {
            $tipo = 'pesquisa_'.$vet['codigo'];
            $str_tipo = 'Pesquisa';

            $array_result[$idusuario] = $array_pesquisa[$idusuario];
        }
        elseif($i == 7)
        {
            $tipo = 'reagendar_'.$vet['codigo'];
            $str_tipo = 'Reagendar';

            $array_result[$idusuario] = $array_reagendar[$idusuario];
        }
        elseif($i == 8)
        {
            $tipo = 'atendido_'.$vet['codigo'];
            $str_tipo = 'Atendido';

            $array_result[$idusuario] = $array_atendido[$idusuario];
        }

        if(@count($array_result[$idusuario]) > 0)
        {
?>
<div class="modal inmodal fade" id="<?=$tipo?>" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
                <h3><?=stripslashes($usuario)?> - <?=$str_tipo?> (#<?=count($array_result[$idusuario])?>)</h3>
            </div>                                        
            <div class="modal-body">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Nome do paciente</th>
                                    <th>Telefone</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                for($j = 1; $j <= count($array_result[$idusuario]['nome']); $j++)
                                {
                                ?>
                                <tr>
                                    <td><?=$j?></td>
                                    <td><?=stripslashes($array_result[$idusuario]['nome'][$j])?></td>
                                    <td><?=($array_result[$idusuario]['telefone'][$j]) ? '<a href="https://api.whatsapp.com/send?phone=55'.preg_replace('/[^\d]/', '', $array_result[$idusuario]['telefone'][$j]).'" target="_blank">'.$array_result[$idusuario]['telefone'][$j].'</a>' : '<a href="https://api.whatsapp.com/send?phone=55'.preg_replace('/[^\d]/', '', $array_result[$idusuario]['telefone2'][$j]).'" target="_blank">'.$array_result[$idusuario]['telefone2'][$j].'</a>'?></td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<?
        }
    }
}

include("includes/footer.php");
?>