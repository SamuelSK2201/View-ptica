<?
$menu = 'estoques';
$page = 'estoques';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if(!$perm_estoque)
    die("Acesso negado!");

$codigo = anti_injection($_GET['codigo']);

$str = "SELECT * FROM estoques WHERE codigo = '$codigo' AND idempresa LIKE '%|$adm_empresa|%'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);

$estoque = stripslashes($vet['nome']);
$status = $vet['status'];
$data_ativacao = $vet['data_ativacao'];
?>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Visualizar estoque - <?=$estoque?></h2>
        <ol class="breadcrumb">
            <li class="active"><strong>Estoques</strong></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>GRADE NEGATIVA - <?=$estoque?></h5>
                </div>

                <?
                $array_cil = array('', 0, -0.25, -0.5, -0.75, -1, -1.25, -1.5, -1.75, -2, -2.25, -2.5, -2.75, -3);
                $array_esf = array('-', -0.25, -0.5, -0.75, -1, -1.25, -1.5, -1.75, -2, -2.25, -2.5, -2.75, -3, -3.25, -3.5, -3.75, -4);
                ?>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                <?
                                for($i = 0; $i <= 13; $i++)
                                {
                                ?>
                                    <th width="7.142%"><?=$array_cil[$i]?></th>
                                <?
                                }
                                ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                for($i = 0; $i <= 16; $i++)
                                {
                                ?>
                                <tr class="gradeX">
                                    <th><?=$array_esf[$i]?></th>
                                <?
                                    for($j = 1; $j <= 13; $j++)
                                    {
                                ?>
                                    <td>
                                <?
                                        $strG = "SELECT DISTINCT A.codigo, A.qtde
                                            FROM estoques_grade_negativa A
                                            INNER JOIN estoques B ON A.idestoque = B.codigo
                                            WHERE A.idestoque = '$codigo'
                                            AND A.esf = '".$array_esf[$i]."'
                                            AND A.cil = '".$array_cil[$j]."'";
                                        $rsG  = mysqli_query($conexao, $strG) or die(mysqli_error($conexao));
                                        $vetG = mysqli_fetch_array($rsG);
                                ?>
                                        <input type="text" name="qtden_<?=$vetG['codigo']?>" id="qtden_<?=$vetG['codigo']?>" class="form-control" value="<?=$vetG['qtde']?>" onblur="javascript: alimenta_estoque(1, <?=$vetG['codigo']?>);" <?=($perm_estoque != 1) ? 'disabled' : ''?>>
                                    </td>
                                <?
                                    }
                                ?>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>GRADE POSITIVA - <?=stripslashes($vet['nome'])?></h5>
                </div>

                <?
                $array_cil = array('', 0, -0.25, -0.5, -0.75, -1, -1.25, -1.5, -1.75, -2, -2.25, -2.5, -2.75, -3);
                $array_esf = array(0.25, 0.5, 0.75, 1, 1.25, 1.5, 1.75, 2, 2.25, 2.5, 2.75, 3, 3.25, 3.5, 3.75, 4);
                ?>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                <?
                                for($i = 0; $i <= 13; $i++)
                                {
                                ?>
                                    <th width="7.142%"><?=$array_cil[$i]?></th>
                                <?
                                }
                                ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                for($i = 0; $i <= 15; $i++)
                                {
                                ?>
                                <tr class="gradeX">
                                    <th><?=$array_esf[$i]?></th>
                                <?
                                    for($j = 1; $j <= 13; $j++)
                                    {
                                ?>
                                    <td>
                                <?
                                        $strG = "SELECT DISTINCT A.codigo, A.qtde
                                            FROM estoques_grade_positiva A
                                            INNER JOIN estoques B ON A.idestoque = B.codigo
                                            WHERE A.idestoque = '$codigo'
                                            AND A.esf = '".$array_esf[$i]."'
                                            AND A.cil = '".$array_cil[$j]."'";
                                        //echo '<br>';
                                        $rsG  = mysqli_query($conexao, $strG) or die(mysqli_error($conexao));
                                        $vetG = mysqli_fetch_array($rsG);
                                ?>
                                        <input type="text" name="qtdep_<?=$vetG['codigo']?>" id="qtdep_<?=$vetG['codigo']?>" class="form-control" value="<?=$vetG['qtde']?>" onblur="javascript: alimenta_estoque(2, <?=$vetG['codigo']?>);" <?=($perm_estoque != 1) ? 'disabled' : ''?>>
                                    </td>
                                <?
                                    }
                                ?>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?
include("includes/footer.php");
?>