<?
$menu = 'funil';
$page = 'r_pedidos';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_prospeccao != 1)
    die("Acesso negado!");

if($perm_relatorios != 1 && $perm_relatorios_pedidos != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE) 
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

$data_inicial = $_POST['data_inicial'];
$data_final = $_POST['data_final'];
$idpesquisador = $_POST['idpesquisador'];

if(!$data_inicial)
    $data_inicial = date("01/m/Y");

if(!$data_final)
    $data_final = date("t/m/Y");

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM pedidos_laboratorio WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("r_pedidos.php?ind_msg=1");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Pedido excluído com sucesso!';

$title = "'View Óptica<br>Pesquisadores x Pesquisas - ".date("d/m/Y")."'";
$columns = '0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18';
$order = ',order: [[ 8, "desc" ]]';
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Pedidos laboratório</h2>
        <ol class="breadcrumb">
            <li class="active"><strong>Funil</strong></li>
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
                                    Data inicial*
                                </p>
                                <div class="form-group" id="data_1" style="margin-left: 0px;">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input style="width: 90%" type="text" required class="form-control" name="data_inicial" id="data_inicial" value="<?=($data_inicial) ? $data_inicial : ''?>" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Data final*
                                </p>
                                <div class="form-group" id="data_1" style="margin-left: 0px;">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input style="width: 90%" type="text" required class="form-control" name="data_final" id="data_final" value="<?=($data_final) ? $data_final : ''?>" >
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
    $strWhere = "";

    if($data_inicial)
        $strWhere .= " AND A.data_venda >= '".ConverteData($data_inicial)."'";

    if($data_final)
        $strWhere .= " AND A.data_venda <= '".ConverteData($data_final)."'";

    $str = "SELECT DISTINCT A.*, B.idagendamento
        FROM pedidos_laboratorio A
        INNER JOIN prospeccao B ON A.idprospeccao = B.codigo
        #INNER JOIN prescricoes C ON B.idagendamento = C.idagendamento
        WHERE A.idempresa = '$adm_empresa' 
        #AND C.tipo IN ('1', '2')
        $strWhere 
        ORDER BY A.data_venda";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    
    if($num > 0)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Lista de pesquisas marcadas como FINALIZADOS + PEDIDO DE LABORATÓRIO efetuado entre <b><?=$data_inicial?></b> até <b><?=$data_final?></b></h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" style="font-size: 10px;" >
                            <thead>
                                <tr>
                                    <th>TSO</th>
                                    <th>LENTE</th>
                                    <th>OD ESF</th>
                                    <th>OD CIL</th>
                                    <th>OD EIXO</th>
                                    <th>OE ESF</th>
                                    <th>OE CIL</th>
                                    <th>OE EIXO</th>
                                    <th>ADIÇÃO</th>
                                    <th>ARO</th>
                                    <th>NR</th>
                                    <th>SÉRIE</th>
                                    <th>DNP.OD</th>
                                    <th>DNP.OE</th>
                                    <th>ALTURA</th>
                                    <th>DATA DA VENDA</th>
                                    <th>PREV CLIENTE</th>
                                    <th>PRESCRIÇÃO</th>
                                    <th>PEDIDO</th>                                    
                                    <th style="width: 10%">AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                    $codigo = $vet['codigo'];
                                    $idagendamento = $vet['idagendamento'];
                                    //$idprescricao = $vet['idprescricao'];
                                    $adicao = 0;
                                    $str_adicao = 0;

                                    $strP = "SELECT DISTINCT C.codigo AS idprescricao, C.idagendamento, C.tipo AS tipo_prescricao
                                        FROM prescricoes C
                                        WHERE C.idagendamento = '$idagendamento' 
                                        AND C.tipo IN ('1', '2')
                                        ORDER BY C.codigo DESC";
                                    $rsP  = mysqli_query($conexao, $strP) or die(mysqli_error($conexao));
                                    $vetP = mysqli_fetch_array($rsP);
                                    $idprescricao = $vetP['idprescricao'];

                                    if($vetP['tipo_prescricao'] == 1)
                                        $tipopr = 'ÓCULOS';
                                    elseif($vetP['tipo_prescricao'] == 2)
                                        $tipopr = 'LENTE';

                                    if($vet['tipo'] == 1)
                                        $tipop = 'PERTO';
                                    elseif($vet['tipo'] == 2)
                                        $tipop = 'LONGE';
                                    elseif($vet['tipo'] == 3)
                                        $tipop = 'MULTIFOCAL';

                                    if($vetP['tipo_prescricao'] == 1)
                                    {
                                        $strP = "SELECT * FROM prescricoes_oculos WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                                        $rsP  = mysqli_query($conexao, $strP) or die(mysqli_error($conexao));
                                        $vetP = mysqli_fetch_array($rsP);

                                        $adicao = $vetP['oculos_adicao'];
                                        if($vet['tipo'] == 3)
                                            $str_adicao = $vetP['oculos_adicao'];

                                        $od_esf = ($vet['tipo'] != 1) ? $vetP['oculos_od_esf'] : $vetP['oculos_od_esf'] + $adicao;
                                        $od_cil = $vetP['oculos_od_cil'];
                                        $od_eixo = $vetP['oculos_od_eixo'];
                                        $oe_esf = ($vet['tipo'] != 1) ? $vetP['oculos_oe_esf'] : $vetP['oculos_oe_esf'] + $adicao;
                                        $oe_cil = $vetP['oculos_oe_cil'];
                                        $oe_eixo = $vetP['oculos_oe_eixo'];
                                    }
                                    else
                                    {
                                        $strP = "SELECT * FROM prescricoes_lentes WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                                        $rsP  = mysqli_query($conexao, $strP) or die(mysqli_error($conexao));
                                        $vetP = mysqli_fetch_array($rsP);

                                        $od_esf = $vetP['lentes_od_esf'];
                                        $od_cil = $vetP['lentes_od_cil'];
                                        $od_eixo = $vetP['lentes_od_eixo'];
                                        $oe_esf = $vetP['lentes_oe_esf'];
                                        $oe_cil = $vetP['lentes_oe_cil'];
                                        $oe_eixo = $vetP['lentes_oe_eixo'];
                                    }
                                ?>
                                <tr class="gradeX">
                                    <td><?=$vet['tso']?></td>
                                    <td><?=stripslashes($vet['lente'])?></td>
                                    <td><?=($od_esf > 0) ? '+'.number_format($od_esf, 2, '.', '') : number_format($od_esf, 2, '.', '')?></td>
                                    <td><?=($od_cil > 0) ? '+'.number_format($od_cil, 2, '.', '') : number_format($od_cil, 2, '.', '')?></td>
                                    <td><?=$od_eixo?>&deg;</td>
                                    <td><?=($oe_esf > 0) ? '+'.number_format($oe_esf, 2, '.', '') : number_format($oe_esf, 2, '.', '')?></td>
                                    <td><?=($oe_cil > 0) ? '+'.number_format($oe_cil, 2, '.', '') : number_format($oe_cil, 2, '.', '')?></td>
                                    <td><?=$oe_eixo?>&deg;</td>
                                    <td><?=($str_adicao > 0) ? $str_adicao : ''?></td>
                                    <td><?=stripslashes($vet['aro'])?></td>
                                    <td><?=$vet['nr']?></td>
                                    <td><?=$vet['serie']?></td>
                                    <td><?=$vet['dnp_od']?></td>
                                    <td><?=$vet['dnp_oe']?></td>
                                    <td><?=$vet['altura']?></td>
                                    <td><?=($vet['data_venda']) ? ConverteData($vet['data_venda']) : ''?></td>
                                    <td><?=($vet['previsao_cliente']) ? ConverteData($vet['previsao_cliente']) : ''?></td>
                                    <td><?=$tipopr?></td>
                                    <td><?=$tipop?></td>
                                    <td class="center">
                                        <a class="btn btn-info btn-circle" type="button" title="imprimir ficha" href="r_pedidos.php?cmd=pdf_ficha&codigo=<?=$vet['codigo']?>" target="_blank"><i class="fa fa-print"></i></a>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="r_pedidos.php?cmd=del&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a> 
                                    </td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>TSO</th>
                                    <th>LENTE</th>
                                    <th>OD ESF</th>
                                    <th>OD CIL</th>
                                    <th>OD EIXO</th>
                                    <th>OE ESF</th>
                                    <th>OE CIL</th>
                                    <th>OE EIXO</th>
                                    <th>ADIÇÃO</th>
                                    <th>ARO</th>
                                    <th>NR</th>
                                    <th>SÉRIE</th>
                                    <th>DNP.OD</th>
                                    <th>DNP.OE</th>
                                    <th>ALTURA</th>
                                    <th>DATA DA VENDA</th>
                                    <th>PREV CLIENTE</th>
                                    <th>PRESCRIÇÃO</th>
                                    <th>PEDIDO</th>
                                    <th>AÇÕES</th>
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
                Nenhuma pesquisa marcada como FINALIZADOS + PEDIDO DE LABORATÓRIO encontrada no período de <b><?=$data_inicial?></b> até <b><?=$data_final?></b>.
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