<?
$menu = 'relatorios';
$page = 'r_financeiro';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_relatorios != 1)
    die("Acesso negado!");

$data_inicial = $_POST['data_inicial'];
$data_final = $_POST['data_final'];
$idoptometrista = $_POST['idoptometrista'];
$forma_pagto = $_POST['forma_pagto'];

$title = "'View Óptica<br>Relatório financeiro'";
$columns = '0, 1, 2, 3, 4';
$order = ',order: [[ 3, "desc" ]]';
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Relatório financeiro</h2>
        <ol class="breadcrumb">
            <li class="active"><strong>Relatórios</strong></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">    
    <div class="row">
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-info pull-right">Total de pacientes</span>
                    <h5>Pacientes</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><?=qtde_pacientes($conexao, $adm_empresa)?></h1>
                    <div class="stat-percent font-bold text-info"><a href="pacientes.php" style="color: #23c6c8">Acessar</a> </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-primary pull-right">Consultas de hoje</span>
                    <h5>Consultas</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><?=qtde_consultas_realizadas_hoje($conexao, $adm_empresa, $adm_perfil)?></h1>
                    <div class="stat-percent font-bold text-navy"><a href="agendamentos.php" style="color: #1ab394">Acessar</a> </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-success pull-right">Valor total dia</span>
                    <h5>Pagamentos</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><?=valor_total_hoje($conexao, $adm_empresa, $adm_perfil)?></h1>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-warning pull-right">Valor total mês</span>
                    <h5>Pagamentos</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><?=valor_total_mes($conexao, $adm_empresa, $adm_perfil)?></h1>
                </div>
            </div>
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
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Especialista
                                </p>
                                <select class="chosen-select" name="idoptometrista" id="idoptometrista" data-placeholder="Selecione um especialista ..." tabindex="10" >
                                    <option value="">Todos</option>
                                    <?
                                    $strC = "SELECT * FROM usuarios WHERE idempresa = '$adm_empresa' AND perfil IN ('2', '5') AND status = '1' AND codigo != '$idusuario' ORDER BY nome";
                                    $rsC  = mysqli_query($conexao, $strC) or die(mysqli_error($conexao));

                                    while($vetC = mysqli_fetch_array($rsC))
                                    {
                                    ?>
                                    <option value="<?=$vetC['codigo']?>" <?=($vetC['codigo'] == $idoptometrista) ? 'selected' : ''?>><?=stripslashes($vetC['nome'])?></option>
                                    <?
                                    }
                                    ?>
                                </select>
                            </div> 
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Forma de pagto
                                </p>
                                <select class="form-control" name="forma_pagto" id="forma_pagto">
                                    <option value="" <?=(!$forma_pagto) ? 'selected' : ''?>>Todos</option>
                                    <option value="1" <?=($forma_pagto == 1) ? 'selected' : ''?>>Dinheiro</option>
                                    <option value="2" <?=($forma_pagto == 2) ? 'selected' : ''?>>Cartão</option>
                                    <option value="3" <?=($forma_pagto == 3) ? 'selected' : ''?>>Dinheiro / Cartão</option>
                                </select>
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

        if($idoptometrista)
            $strWhere .= " AND A.idoptometrista = '$idoptometrista'";

        if($forma_pagto)
            $strWhere .= " AND A.forma_pagto = '$forma_pagto'";

        if($data_inicial)
            $strWhere .= " AND A.data_pagto >= '".ConverteData($data_inicial)." 00:00:01'";

        if($data_final)
            $strWhere .= " AND A.data_pagto <= '".ConverteData($data_final)." 23:59:59'";

        $str = "SELECT A.*, DATE_FORMAT(A.data_pagto, '%d/%m/%Y %H:%i') AS dt_pagto, B.nome AS optometrista, C.nome AS paciente, D.nome AS otica
            FROM agendamentos A
            LEFT JOIN usuarios B ON A.idoptometrista = B.codigo
            INNER JOIN pacientes C ON A.idpaciente = C.codigo
            LEFT JOIN oticas D ON A.idotica = D.codigo
            WHERE A.idempresa = '$adm_empresa'
            AND A.status = '6'
            AND A.valor > 0
            $strWhere
            $strWhereP
            ORDER BY A.data_pagto";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        $num = mysqli_num_rows($rs);
        
        if($num > 0)
        {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Relatório de consultas de <b><?=$data_inicial?></b> até <b><?=$data_final?></b></h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Especialista</th>
                                    <th>Ótica parceira</th>
                                    <th>Data de pagto</th>
                                    <th>Forma de pagto</th>
                                    <th>Valor da consulta</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                    if(!$vet['forma_pagto'])
                                        $forma_pagto = 'Pendente';
                                    elseif($vet['forma_pagto'] == 1)
                                        $forma_pagto = 'Dinheiro';
                                    elseif($vet['forma_pagto'] == 2)
                                        $forma_pagto = 'Cartão';
                                    elseif($vet['forma_pagto'] == 3)
                                        $forma_pagto = 'Dinheiro / Cartão';
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['paciente'])?></td>
                                    <td><?=stripslashes($vet['optometrista'])?></td>
                                    <td><?=stripslashes($vet['otica'])?></td>
                                    <td><?=$vet['dt_pagto']?></td>
                                    <td><?=$forma_pagto?></td>
                                    <td>R$ <?=number_format($vet['valor'], 2, ',', '.')?></td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Especialista</th>
                                    <th>Ótica parceira</th>
                                    <th>Data de pagto</th>
                                    <th>Forma de pagto</th>
                                    <th>Valor da consulta</th>
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
include("includes/footer.php");
?>