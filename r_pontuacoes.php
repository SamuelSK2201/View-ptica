<?
$menu = 'relatorios';
$page = 'r_pontuacoes';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_relatorios != 1 && $perm_relatorios_pontuacoes != 1)
    die("Acesso negado!");

$data_inicial = $_REQUEST['data_inicial'];
$data_final = $_REQUEST['data_final'];
$pontuacao = $_REQUEST['pontuacao'];
$idpontuador = $_REQUEST['idpontuador'];

if($_GET['cmd'] == "del")
{
    $idusuario = anti_injection($_REQUEST['idusuario']);

    $str = "DELETE FROM sistema_pontos WHERE idusuario = '$idusuario'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("r_pontuacoes.php?ind_msg=1&pontuacao=<?=$pontuacao?>&data_inicial=<?=$data_inicial?>&data_final=<?=$data_final?>&idpontuador=<?=$idpontuador?>");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Pontos zerados com sucesso!';
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Pontuações</h2>
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
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Pontuação*
                                </p>
                                <select class="form-control" name="pontuacao" id="pontuacao" required>
                                    <option value="">Selecione ...</option>
                                    <option value="1" <?=(!$pontuacao || 1 == $pontuacao) ? 'selected' : ''?>>Exibir fracionado</option>
                                    <option value="2" <?=(2 == $pontuacao) ? 'selected' : ''?>>Exibir totais</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Data inicial
                                </p>
                                <div class="form-group" id="data_1" style="margin-left: 0px;">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input style="width: 90%" type="text" class="form-control" name="data_inicial" id="data_inicial" value="<?=($data_inicial) ? $data_inicial : ''?>" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
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
                            if($adm_perfil == 1)
                            {
                            ?>
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Pontuador
                                </p>
                                <select class="chosen-select" name="idpontuador" id="idpontuador" data-placeholder="Selecione um pontuador ..." tabindex="10" >
                                    <option value="">Todos</option>
                                    <?
                                    $strC = "SELECT DISTINCT A.* 
                                        FROM usuarios A
                                        INNER JOIN usuarios_tipos B ON A.idtipo = B.codigo
                                        WHERE A.idempresa = '$adm_empresa' 
                                        #AND B.prospeccao = '1' 
                                        AND A.status = '1' 
                                        ORDER BY A.nome";
                                    $rsC  = mysqli_query($conexao, $strC) or die(mysqli_error($conexao));

                                    while($vetC = mysqli_fetch_array($rsC))
                                    {
                                    ?>
                                    <option value="<?=$vetC['codigo']?>" <?=($vetC['codigo'] == $idpontuador) ? 'selected' : ''?>><?=stripslashes($vetC['nome'])?></option>
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
    if($_REQUEST['cmd'] == 'search' || !empty($_GET['ind_msg']))
    {
        $strWhere = "";

        if($adm_perfil != 1)
        {
            $strWhere .= " AND A.idusuario = '$adm_codigo'";
        }

        if($idpontuador)
        {
            $strWhere .= " AND A.idusuario = '$idpontuador'";
        }

        if($data_inicial)
            $strWhere .= " AND A.data_registro >= '".ConverteData($data_inicial)."'";

        if($data_final)
            $strWhere .= " AND A.data_registro <= '".ConverteData($data_final)."'";

        if($pontuacao == 1)
        {
            $title = "'View Óptica<br>Pontuações'";
            $columns = '0, 1, 2';
            $order = ',order: [[ 2, "asc" ]]';

            $str = "SELECT DISTINCT A.*, DATE_FORMAT(A.data_registro, '%d/%m/%Y %H:%i') AS dt_registro, B.nome AS pontuador 
                FROM sistema_pontos A
                INNER JOIN usuarios B ON A.idusuario = B.codigo
                WHERE A.idempresa = '$adm_empresa' 
                AND B.status = '1' 
                $strWhere
                ORDER BY B.nome";
            $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
            $num = mysqli_num_rows($rs);
        
            if($num > 0)
            {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Lista de pontuadores de <b><?=$data_inicial?></b> até <b><?=$data_final?></b></h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Pontuador</th>
                                    <th>Pontos</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['pontuador'])?></td>
                                    <td><?=$vet['pontos']?></td>
                                    <td><?=$vet['dt_registro']?></td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Pontuador</th>
                                    <th>Pontos</th>
                                    <th>Data</th>
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
                Nenhum pontuador encontrado no período de <b><?=$data_inicial?></b> até <b><?=$data_final?></b>.
            </p>
        </div>
    </div>
    <?
            }
        }
        else
        {
            $title = "'View Óptica<br>Pontuações'";
            $columns = '0, 1';
            $order = ',order: [[ 1, "desc" ]]';

            $str = "SELECT DISTINCT SUM(A.pontos) AS total_pontos, A.idusuario, B.nome AS pontuador 
                FROM sistema_pontos A
                INNER JOIN usuarios B ON A.idusuario = B.codigo
                WHERE A.idempresa = '$adm_empresa' 
                AND B.status = '1' 
                $strWhere
                GROUP BY A.idusuario
                ORDER BY B.nome";
            $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
            $num = mysqli_num_rows($rs);
        
            if($num > 0)
            {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Lista de pontuadores de <b><?=$data_inicial?></b> até <b><?=$data_final?></b></h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Pontuador</th>
                                    <th>Pontos</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['pontuador'])?></td>
                                    <td><?=$vet['total_pontos']?></td>
                                    <td>
                                        <?
                                        if($adm_perfil == 1)
                                        {
                                        ?>
                                        <a class="btn btn-danger btn-circle" type="button" title="zerar pontuação" href="r_pontuacoes.php?cmd=del&idusuario=<?=$vet['idusuario']?>&pontuacao=<?=$pontuacao?>&data_inicial=<?=$data_inicial?>&data_final=<?=$data_final?>&idpontuador=<?=$idpontuador?>" onclick="javascript: if(!confirm('Deseja realmente zerar os pontos deste usuário?')) { return false }"><i class="fa fa-eraser"></i></a> 
                                        <?
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Pontuador</th>
                                    <th>Pontos</th>
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
                Nenhum pontuador encontrado no período de <b><?=$data_inicial?></b> até <b><?=$data_final?></b>.
            </p>
        </div>
    </div>
    <?
            }
        }
    }
    ?>
</div>

<?
include("includes/footer.php");
?>