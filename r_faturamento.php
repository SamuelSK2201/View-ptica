<?
$menu = 'relatorios';
$page = 'r_faturamento';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_relatorios != 1)
    die("Acesso negado!");

if($perm_relatorios_faturamento != 1)
    die("Acesso negado!");

$data_inicial = $_POST['data_inicial'];
$data_final = $_POST['data_final'];
$idlaboratorio = $_POST['idlaboratorio'];

if($_GET['fl'] == 1)
{
    $data_inicial = date("d/m/Y");
    $data_final = date("d/m/Y");
}

if($_GET['fl'] == 2)
{
    $data_inicial = '01/'.date("m/Y");
    $data_final = date("t/m/Y");
}

$title = "'View Óptica<br>Relatório de faturamento'";
$columns = '0, 1, 2, 3, 4, 5';
$order = ',order: [[ 0, "asc" ]]';
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Relatório de faturamento</h2>
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
                            <div class="col-md-5">
                                <p class="font-bold">
                                    Laboratório
                                </p>
                                <select name="idlaboratorio" id="idlaboratorio" class="chosen-select">
                                    <option value="">Selecione ...</option>
                                    <?
                                    $strC = "SELECT * FROM laboratorios WHERE idempresa = '$adm_empresa' ORDER BY nome";
                                    $rsC  = mysqli_query($conexao, $strC) or die(mysqli_error($conexao));

                                    while($vetC = mysqli_fetch_array($rsC))
                                    {
                                    ?>
                                    <option value="<?=$vetC['codigo']?>" <?=($vetC['codigo'] == $idlaboratorio) ? 'selected' : ''?>><?=stripslashes($vetC['nome'])?></option>
                                    <?
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-1">
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

        if($idlaboratorio)
            $strWhere .= " AND A.idlaboratorio = '$idlaboratorio'";

        if($data_inicial)
            $strWhere .= " AND B.data_registro >= '".ConverteData($data_inicial)."'";

        if($data_final)
            $strWhere .= " AND B.data_registro <= '".ConverteData($data_final)."'";

        $str = "SELECT A.*, B.data_registro, B.tso, C.custo, C.nome AS lente, D.nome AS laboratorio
            FROM pedidos_compras A
            INNER JOIN pedidos_laboratorio B ON A.idpedido = B.codigo
            INNER JOIN tipos_lentes C ON A.idlente = C.codigo
            INNER JOIN laboratorios D ON A.idlaboratorio = D.codigo
            WHERE A.idempresa = '$adm_empresa'
            $strWhere
            ORDER BY B.data_registro";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        $num = mysqli_num_rows($rs);
        
        if($num > 0)
        {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Relatório de pedidos de compra de <b><?=$data_inicial?></b> até <b><?=$data_final?></b></h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Laboratório</th>
                                    <th>Data</th>
                                    <th>TSO</th>
                                    <th>NR</th>
                                    <th>Descrição</th>
                                    <th>Custo UN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                $total = 0;
                                while($vet = mysqli_fetch_array($rs))
                                {
                                    $total += $vet['custo'];
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['laboratorio'])?></td>
                                    <td><?=ConverteData($vet['data_registro'])?></td>
                                    <td><?=$vet['tso']?></td>
                                    <td><input type="text" name="nr_<?=$vet['codigo']?>" id="nr_<?=$vet['codigo']?>" class="form-control" value="<?=$vet['nr']?>" onblur="javascript: altera_nr_pedido_compra('<?=$vet["codigo"]?>')" style="font-size: 10px; width: 100%"></td>
                                    <td><?=stripslashes($vet['lente'])?></td>
                                    <td>R$ <?=number_format($vet['custo'], 2, ',', '.')?></td>
                                </tr>
                                <?
                                }
                                ?>
                                <tr>
                                    <th>Laboratório</th>
                                    <th>Data</th>
                                    <th>TSO</th>
                                    <th>NR</th>
                                    <th>Descrição</th>
                                    <th>R$ <?=number_format($total, 2, ',', '.')?></th>
                                </tr>
                            </tbody>
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
                Nenhum pedido encontrado no período de <b><?=$data_inicial?></b> até <b><?=$data_final?></b>.
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