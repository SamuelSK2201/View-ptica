<?
$menu = 'funil';
$page = 'r_finalizados';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_prospeccao != 1)
    die("Acesso negado!");

if($perm_relatorios != 1 && $perm_relatorios_finalizados != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE)
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

$data_inicial = $_POST['data_inicial'];
$data_final = $_POST['data_final'];
$nome = $_POST['nome'];

if($_POST['cmd'] == 'save')
{
    $idprospeccao = $_POST['idprospeccao'];
    $tso = $_POST['tso'];
    $cod = $_POST['cod'];
    $nr = $_POST['nr'];
    $aro = addslashes($_POST['aro']);
    $serie = addslashes($_POST['serie']);
    //$lente = addslashes($_POST['lente']);
    $observacao = addslashes($_POST['observacao']);
    $data_venda = ConverteData($_POST['data_venda']);
    $previsao_cliente = ConverteData($_POST['previsao_cliente']);
    $tipo = $_POST['tipo'];
    $dnp_od = $_POST['dnp_od'];
    $dnp_oe = $_POST['dnp_oe'];
    $altura = $_POST['altura'];

    $lentes = $_POST['lente'];
    //$array_lente = explode("#", addslashes($_POST['lente']));
    //$lente = $array_lente[0];
    //$pontos = $array_lente[1];

    $armacao_a = $_POST['armacao_a'];
    $armacao_b = $_POST['armacao_b'];
    $armacao_ed = $_POST['armacao_ed'];
    $ponte = $_POST['ponte'];
    $eixo = $_POST['eixo'];

    $lente = "";
    $pontos = 0;

    //LENTES
    if(count($lentes))
    {
        for($i = 0; $i < count($lentes); $i++)
        {
            $array_lente = explode("#", $lentes[$i]);
            $lente .= $array_lente[0].';';
            $pontos += $array_lente[1];
            $idlente .= $array_lente[2].';';
        }
    }

    $lente = substr($lente, 0, -1);
    $idlente = substr($idlente, 0, -1);

    $str = "INSERT INTO pedidos_laboratorio (idempresa, idusuario, idprospeccao, tso, cod, nr, aro, serie, lente, observacao, data_venda, previsao_cliente, tipo, dnp_od, dnp_oe, altura, armacao_a, armacao_b, armacao_ed, ponte, eixo, data_registro)
        VALUES ('$adm_empresa', '$adm_codigo', '$idprospeccao', '$tso', '$cod', '$nr', '$aro', '$serie', '$lente', '$observacao', '$data_venda', '$previsao_cliente', '$tipo', '$dnp_od', '$dnp_oe', '$altura', '$armacao_a', '$armacao_b', '$armacao_ed', '$ponte', '$eixo', CURDATE())";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $codigo = mysqli_insert_id($conexao);
    //echo '<br>';

    $str = "INSERT INTO sistema_pontos (idempresa, idusuario, idpedido, pontos, data_registro) VALUES ('$adm_empresa', '$adm_codigo', '$codigo', '$pontos', NOW())";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    //ESTOQUE
    $str = "SELECT DISTINCT A.*, B.idagendamento
        FROM pedidos_laboratorio A
        INNER JOIN prospeccao B ON A.idprospeccao = B.codigo
        WHERE A.codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $vet = mysqli_fetch_array($rs);
    //echo '<br>';

    $idagendamento = $vet['idagendamento'];
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
    //echo '<br>';

    if($vetP['tipo_prescricao'] == 1)
    {
        $strP = "SELECT * FROM prescricoes_oculos WHERE idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
        $rsP  = mysqli_query($conexao, $strP) or die(mysqli_error($conexao));
        $vetP = mysqli_fetch_array($rsP);

        $adicao = $vetP['oculos_adicao'];
        if($vet['tipo'] == 3)
            $str_adicao = $vetP['oculos_adicao'];

        $od_esf = ($vet['tipo'] != 1) ? $vetP['oculos_od_esf'] : $vetP['oculos_od_esf'] + $adicao;
        $od_cil = $vetP['oculos_od_cil'];
        $oe_esf = ($vet['tipo'] != 1) ? $vetP['oculos_oe_esf'] : $vetP['oculos_oe_esf'] + $adicao;
        $oe_cil = $vetP['oculos_oe_cil'];
    }
    else
    {
        $strP = "SELECT * FROM prescricoes_lentes WHERE idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
        $rsP  = mysqli_query($conexao, $strP) or die(mysqli_error($conexao));
        $vetP = mysqli_fetch_array($rsP);

        $od_esf = $vetP['lentes_od_esf'];
        $od_cil = $vetP['lentes_od_cil'];
        $oe_esf = $vetP['lentes_oe_esf'];
        $oe_cil = $vetP['lentes_oe_cil'];
    }

    if($od_esf > 0)
        $od_esf = $od_esf;
    else
        $od_esf = $od_esf;

    if($oe_esf > 0)
        $oe_esf = $oe_esf;
    else
        $oe_esf = $oe_esf;

    if($od_cil > 0)
        $od_cil = $od_cil;
    else
        $od_cil = $od_cil;

    if($oe_cil > 0)
        $oe_cil = $oe_cil;
    else
        $oe_cil = $oe_cil;

    //print_r($lentes);
    //echo '<br>';
    //die;

    //echo count($lentes);
    //echo '<br>';

    for($i = 0; $i < count($lentes); $i++)
    {
        echo $lentes[$i];
        echo '<br>';

        $cod_lente = explode(" ", $lentes[$i]);

        $strL = "SELECT * FROM tipos_lentes WHERE nome LIKE '$cod_lente[0] %'";
        $rsL  = mysqli_query($conexao, $strL) or die(mysqli_error($conexao));
        //echo '<br>';

        $str_lentes = "(";

        while($vetL = mysqli_fetch_array($rsL))
        {
            //print_r($vetL);
            //echo '<br>';
            $str_lentes .= "idlente LIKE '%|".$vetL['codigo']."|%' OR ";
        }

        $str_lentes = substr($str_lentes, 0, -4).')';
        //echo $str_lentes;
        //echo '<br>';

        $str = "SELECT * FROM estoques WHERE status = '1' AND $str_lentes AND idempresa LIKE '%|$adm_empresa|%'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        $num = mysqli_num_rows($rs);
        //echo '<br>';

        if($num > 0)
        {
            while($vet = mysqli_fetch_array($rs))
            {
                $idestoque = $vet['codigo'];
                $idlente = $vet['idlente'];
                //echo $od_esf.' - '.$oe_esf.' : '.$p.'<br>';

                if($od_esf > 0)
                {
                    registra_baixa_estoque_lentes($conexao, $idestoque, $od_esf, $od_cil, 1, $lentes[$i], $codigo, $adm_empresa);
                }
                else
                {
                    registra_baixa_estoque_lentes($conexao, $idestoque, $od_esf, $od_cil, 2, $lentes[$i], $codigo, $adm_empresa);

                }

                if($oe_esf > 0)
                {
                    registra_baixa_estoque_lentes($conexao, $idestoque, $oe_esf, $oe_cil, 1, $lentes[$i], $codigo, $adm_empresa);
                }
                else
                {
                    registra_baixa_estoque_lentes($conexao, $idestoque, $oe_esf, $oe_cil, 2, $lentes[$i], $codigo, $adm_empresa);
                }

                //sleep(3);
            }
        }
        else
        {
            registro_gerar_compra($conexao, $lentes[$i], $codigo, $adm_empresa, 1);
        }
    }

    //die;
    
    redireciona("r_finalizados.php?ind_msg=1");
}

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM prospeccao WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prospeccao_respostas WHERE idprospeccao = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("r_finalizados.php?ind_msg=2");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Dados para pedido de laboratório cadastrados com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Prospecção excluída com sucesso!';

$title = "'View Óptica<br>Finalizados'";
$columns = '0, 1, 2, 3, 4';
$order = ',order: [[ 4, "desc" ]]';
?>

<script>
    function exibe_imagem(value, idprospeccao)
    {
        document.getElementById('div_horizontal_'+idprospeccao).style.display='none';
        document.getElementById('div_vertical_'+idprospeccao).style.display='none';
        document.getElementById('div_diagonal_'+idprospeccao).style.display='none';
        document.getElementById('div_ponte_'+idprospeccao).style.display='none';

        if(value == 1)
        {
            document.getElementById('div_horizontal_'+idprospeccao).style.display='block';
        }

        if(value == 2)
        {
            document.getElementById('div_vertical_'+idprospeccao).style.display='block';
        }

        if(value == 3)
        {
            document.getElementById('div_diagonal_'+idprospeccao).style.display='block';
        }

        if(value == 4)
        {
            document.getElementById('div_ponte_'+idprospeccao).style.display='block';
        }
    }
</script>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Finalizados</h2>
        <ol class="breadcrumb">
            <li class="active"><strong>Funil</strong></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <?
    if(!empty($_GET['ind_msg']))
    {
    ?>
    <div class="row">
        <div class="col-lg-12">            
            <p class="font-bold  alert alert-success m-b-sm">
                <?=$msg?>
            </p>
            <br>
        </div>
    </div>
    <?
    }
    ?>

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
                            <div class="col-md-6">
                                <p class="font-bold" >
                                    Nome
                                </p>
                                <input type="text" name="nome" id="nome" class="form-control" value="<?=$nome?>" >
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
            $strWhere .= " AND data_agendamento >= '".ConverteData($data_inicial)."'";

        if($data_final)
            $strWhere .= " AND data_agendamento <= '".ConverteData($data_final)."'";

        if($nome)
            $strWhere .= " AND nome LIKE '%$nome%'";
    
        $str = "SELECT * FROM prospeccao WHERE idempresa = '$adm_empresa' AND status = '5' $strWhere $strWherePesq ORDER BY data_agendamento";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        $num = mysqli_num_rows($rs);
        
        if($num > 0)
        {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Lista de pesquisas marcadas como FINALIZADOS</h5>
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
                                    <th style="width: 12.5%">Ações</th>
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
                                    <td><div class="status_<?=$codigo?>"><span class="label label-danger" style="background-color: #585858; color: #fff;">Finalizados</span></div></td>
                                    <td class="center">
                                        <div class="prospeccao_<?=$codigo?>" style="float: left; margin-right: 2px;">
                                            <a class="btn btn-default btn-circle" type="button" title="desfazer FINALIZADO" onclick="javascript: altera_status_prospeccao('<?=$codigo?>', '<?=$vet['idagendamento']?>', 4)"><i class='fa fa-reply'></i></a>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <a class="btn btn-info btn-circle" type="button" title="dados para PEDIDOS LABORATÓRIO" onclick="conciliarModal(<?=$vet['codigo']?>);"><i class="fa fa-stethoscope"></i></a>
                                        <a class="btn btn-warning btn-circle" type="button" title="editar / visualizar" href="prospeccao.php?ind=2&codigo=<?=$vet['codigo']?>&url=<?=base64_encode('r_finalizados.php')?>"><i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="r_finalizados.php?cmd=del&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a> 
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
                Nenhuma pesquisa maracada como FINALZIADOS no sistema.
            </p>
        </div>
    </div>
    <?
        }

        $str = "SELECT * FROM prospeccao WHERE idempresa = '$adm_empresa' AND status = '5' $strWhere $strWherePesq ORDER BY data";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

        $i = 0;
        while($vet = mysqli_fetch_array($rs))
        {
            $i++;
            $idprospeccao = $vet['codigo'];
        ?>
        <div class="modal inmodal fade" id="pedidos_<?=$idprospeccao?>" tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
                        <h3>Dados para PEDIDOS LABORATÓRIO</h3>
                        <small><?=stripslashes($vet['nome'])?> <?=stripslashes($vet['sobrenome'])?></small>
                    </div>                                        
                    <div class="modal-body">
                        <div class="ibox float-e-margins">
                            <div class="ibox-content">
                                <form method="post" class="form-inline" name="form_l" id="form_l_<?=$idprospeccao?>" enctype="multipart/form-data"> 
                                    <input type="hidden" name="cmd" value="save">
                                    <input type="hidden" name="idprospeccao" id="idprospeccao_<?=$idprospeccao?>" value="<?=$idprospeccao?>">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="font-bold">
                                                TSO*
                                            </p>
                                            <input type="text" name="tso" id="tso_<?=$idprospeccao?>" class="form-control" style="width: 100%" required onfocus="javascript: exibe_imagem(0, <?=$idprospeccao?>);">
                                        </div>
                                        <div class="col-md-4">
                                            <p class="font-bold">
                                                CÓD*
                                            </p>
                                            <input type="text" name="cod" id="cod_<?=$idprospeccao?>" class="form-control" style="width: 100%" required onfocus="javascript: exibe_imagem(0, <?=$idprospeccao?>);">
                                        </div>
                                        <div class="col-md-4">
                                            <p class="font-bold">
                                                NR
                                            </p>
                                            <input type="number" step="0.1" name="nr" id="nr_<?=$idprospeccao?>" class="form-control" style="width: 100%"  onfocus="javascript: exibe_imagem(0, <?=$idprospeccao?>);">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="font-bold">
                                                Aro*
                                            </p>
                                            <select class="form-control" name="aro" id="aro_<?=$idprospeccao?>" required style="width: 100%" onfocus="javascript: exibe_imagem(0, <?=$idprospeccao?>);">
                                                <option value="">Selecione ...</option>
                                                <option value="ACETATO" <?=("ACETATO" == $vet['aro']) ? 'selected' : ''?>>ACETATO</option>
                                                <option value="METAL" <?=("METAL" == $vet['aro']) ? 'selected' : ''?>>METAL</option>
                                                <option value="NUMON" <?=("NUMON" == $vet['aro']) ? 'selected' : ''?>>NUMON</option>
                                                <option value="NYLON" <?=("NYLON" == $vet['aro']) ? 'selected' : ''?>>NYLON</option>
                                                <option value="3 PEÇAS" <?=("3 PEÇAS" == $vet['aro']) ? 'selected' : ''?>>3 PEÇAS</option>
                                                <option value="ALT. MOD" <?=("ALT. MOD" == $vet['aro']) ? 'selected' : ''?>>ALT. MOD</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="font-bold">
                                                Série*
                                            </p>
                                            <input type="text" name="serie" id="serie_<?=$idprospeccao?>" class="form-control" style="width: 100%" required onfocus="javascript: exibe_imagem(0, <?=$idprospeccao?>);">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">                                        
                                        <div class="col-md-12">
                                            <p class="font-bold">
                                                Lente*
                                            </p>
                                            <?
                                            $strC = "SELECT * FROM tipos_lentes WHERE idempresa = '$adm_empresa' ORDER BY nome";
                                            $rsC  = mysqli_query($conexao, $strC) or die(mysqli_error($conexao));
                                            
                                            while($vetC = mysqli_fetch_array($rsC))
                                            {
                                            ?>
                                            <input type="checkbox" name="lente[]" id="lente" value="<?=stripslashes($vetC['nome']).'#'.$vetC['pontos'].'#'.$vetC['codigo']?>" <?=(stripslashes($vetC['nome']) == stripslashes($vet['lente'])) ? 'checked' : ''?>> <?=stripslashes($vetC['nome'])?><br>
                                            <?
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="font-bold">
                                                Observação
                                            </p>
                                            <input type="text" name="observacao" id="observacao_<?=$idprospeccao?>" class="form-control" style="width: 100%"  onfocus="javascript: exibe_imagem(0, <?=$idprospeccao?>);">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="font-bold">
                                                Data da venda*
                                            </p>
                                            <div class="form-group" id="data_1">
                                                <div class="input-group date" style="margin-left: 15px;">
                                                    <span class="input-group-addon" ><i class="fa fa-calendar"></i></span><input style="width: 90%;" type="text" class="form-control" name="data_venda" id="data_venda_<?=$idprospeccao?>" maxlength="10" data-mask="99/99/9999" onKeyUp="javascript: return auto_data('data_venda_<?=$idprospeccao?>');" onKeyPress="javascript: return somenteNumeros(event);" onfocus="javascript: exibe_imagem(0, <?=$idprospeccao?>);">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="font-bold">
                                                Previsão cliente*
                                            </p>
                                            <div class="form-group" id="data_1">
                                                <div class="input-group date" style="margin-left: 15px;">
                                                    <span class="input-group-addon" ><i class="fa fa-calendar"></i></span><input style="width: 90%;" type="text" class="form-control" name="previsao_cliente" id="previsao_cliente_<?=$idprospeccao?>" maxlength="10" data-mask="99/99/9999" onKeyUp="javascript: return auto_data('previsao_cliente_<?=$idprospeccao?>');" onKeyPress="javascript: return somenteNumeros(event);" onfocus="javascript: exibe_imagem(0, <?=$idprospeccao?>);">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input type="radio" name="tipo" id="tipo_<?=$idprospeccao?>" value="1" onfocus="javascript: exibe_imagem(0, <?=$idprospeccao?>);" > Para perto     
                                            &nbsp;&nbsp;&nbsp;
                                            <input type="radio" name="tipo" id="tipo_<?=$idprospeccao?>" value="2" onfocus="javascript: exibe_imagem(0, <?=$idprospeccao?>);" > Para longe
                                            &nbsp;&nbsp;&nbsp;
                                            <input type="radio" name="tipo" id="tipo_<?=$idprospeccao?>" value="3" onfocus="javascript: exibe_imagem(0, <?=$idprospeccao?>);" > Multifocal
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="font-bold">
                                                DNP.OD*
                                            </p>
                                            <input type="number" step="0.1" name="dnp_od" id="dnp_od_<?=$idprospeccao?>" class="form-control" style="width: 100%" onfocus="javascript: exibe_imagem(0, <?=$idprospeccao?>);" >
                                        </div>
                                        <div class="col-md-4">
                                            <p class="font-bold">
                                                DNP.OE*
                                            </p>
                                            <input type="number" step="0.1" name="dnp_oe" id="dnp_oe_<?=$idprospeccao?>" class="form-control" style="width: 100%" onfocus="javascript: exibe_imagem(0, <?=$idprospeccao?>);" >
                                        </div>
                                        <div class="col-md-4">
                                            <p class="font-bold">
                                                Altura*
                                            </p>
                                            <input type="number" step="0.1" name="altura" id="altura_<?=$idprospeccao?>" class="form-control" style="width: 100%" onfocus="javascript: exibe_imagem(0, <?=$idprospeccao?>);" >
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row alert alert-success">
                                        <b>DADOS DA ARMAÇÃO</b><br>
                                        <div class="col-md-2">
                                            <p class="font-bold">
                                                Horizontal
                                            </p>
                                            <input type="text" name="armacao_a" id="armacao_a_<?=$idprospeccao?>" class="form-control" style="width: 100%" onfocus="javascript: exibe_imagem(1, <?=$idprospeccao?>);" >
                                        </div>
                                        <div class="col-md-2">
                                            <p class="font-bold">
                                                Vertical 
                                            </p>
                                            <input type="text" name="armacao_b" id="armacao_b_<?=$idprospeccao?>" class="form-control" style="width: 100%" onfocus="javascript: exibe_imagem(2, <?=$idprospeccao?>);" >
                                        </div>
                                        <div class="col-md-2">
                                            <p class="font-bold">
                                                Diagonal
                                            </p>
                                            <input type="text" name="armacao_ed" id="armacao_ed_<?=$idprospeccao?>" class="form-control" style="width: 100%" onfocus="javascript: exibe_imagem(3, <?=$idprospeccao?>);" >
                                        </div>
                                        <div class="col-md-3">
                                            <p class="font-bold">
                                                Ponte
                                            </p>
                                            <input type="text" name="ponte" id="ponte_<?=$idprospeccao?>" class="form-control" style="width: 100%" onfocus="javascript: exibe_imagem(4, <?=$idprospeccao?>);" >
                                        </div>
                                        <div class="col-md-3">
                                            <p class="font-bold">
                                                Eixo
                                            </p>
                                            <input type="text" name="eixo" id="eixo_<?=$idprospeccao?>" class="form-control" style="width: 100%" onfocus="javascript: exibe_imagem(0, <?=$idprospeccao?>);" >
                                        </div>
                                    </div>
                                    <br>
                                    
                                    <div class="row">
                                        <div class="col-md-12" id="div_horizontal_<?=$idprospeccao?>" style="display: none">
                                            <img src="img/lente_horizontal.jpeg" style="width: 100%">
                                        </div>
                                        <div class="col-md-12" id="div_vertical_<?=$idprospeccao?>" style="display: none">
                                            <img src="img/lente_vertical.jpeg" style="width: 100%">
                                        </div>
                                        <div class="col-md-12" id="div_diagonal_<?=$idprospeccao?>" style="display: none">
                                            <img src="img/lente_diagonal.jpeg" style="width: 100%">
                                        </div>
                                        <div class="col-md-12" id="div_ponte_<?=$idprospeccao?>" style="display: none">
                                            <img src="img/lente_ponte.jpeg" style="width: 100%">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-xs-12 text-right">
                                            <button type="submit" class="btn btn-primary">Salvar</button>
                                        </div>
                                    </div>
                                </form>
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
    ?>
</div>
<?
include("includes/footer.php");
?>

<script>
    function conciliarModal(id){
        var itensCheckbox = [];

        $(".checkbox_itens").each(function(){
            if($(this).is(":checked")){
                itensCheckbox.push($(this).val());
            }
        })

        $("#list_item_checked").val(itensCheckbox);
        $(".modal#pedidos_"+id).modal('show');
    }
</script> 