<?
$menu = 'relatorios';
$page = 'r_reposicao';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_relatorios != 1)
    die("Acesso negado!");

if($perm_estoque != 1)
    die("Acesso negado!");

$idestoque = $_POST['idestoque'];
$grade = $_POST['grade'];

$title = "'View Óptica<br>Relatório de reposição'";
$columns = '0, 1, 2, 3, 4, 5';
$order = ',order: [[ 0, "asc" ]]';
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Relatório de reposição</h2>
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
                            <div class="col-md-5">
                                <p class="font-bold">
                                    Estoque
                                </p>
                                <select class="chosen-select" name="idestoque" id="idestoque" data-placeholder="Selecione um estoque ..." tabindex="10" >
                                    <option value="">Todos</option>
                                    <?
                                    $strC = "SELECT * FROM estoques WHERE status = '1' AND idempresa LIKE '%|$adm_empresa|%' ORDER BY nome";
                                    $rsC  = mysqli_query($conexao, $strC) or die(mysqli_error($conexao));

                                    while($vetC = mysqli_fetch_array($rsC))
                                    {
                                    ?>
                                    <option value="<?=$vetC['codigo']?>" <?=($vetC['codigo'] == $idestoque) ? 'selected' : ''?>><?=stripslashes($vetC['nome'])?></option>
                                    <?
                                    }
                                    ?>
                                </select>
                            </div> 
                            <div class="col-md-5">
                                <p class="font-bold">
                                    Grade
                                </p>
                                <select class="form-control" name="grade" id="grade">
                                    <option value="" <?=(!$grade) ? 'selected' : ''?>>Todos</option>
                                    <option value="1" <?=($grade == 1) ? 'selected' : ''?>>Negativa</option>
                                    <option value="2" <?=($grade == 2) ? 'selected' : ''?>>Positiva</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <p class="font-bold">
                                    &nbsp;
                                </p>
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
        $strInnerN = "";
        $strInnerP = "";

        if($idestoque)
            $strWhere .= " AND A.codigo = '$idestoque'";

        $strSelect = " , N.*, P.*, CASE WHEN N.idestoque IS NOT NULL THEN '1' ELSE '2' END AS grade";
        $strInnerN = " INNER JOIN estoques_grade_negativa N ON A.codigo = N.idestoque";
        $strInnerP = " INNER JOIN estoques_grade_positiva P ON A.codigo = P.idestoque";

        if($grade == 1)
        {
            $str = "
                SELECT DISTINCT A.codigo, A.max_negativo, A.max_positivo, A.nome, N.*, '1' AS grade
                FROM estoques A
                INNER JOIN estoques_grade_negativa N ON A.codigo = N.idestoque
                WHERE A.status = '1'
                $strWhere
                ORDER BY A.codigo";
        }
        elseif($grade == 2)
        {
            $str = "SELECT DISTINCT A.codigo, A.max_negativo, A.max_positivo, A.nome, P.*, '2' AS grade
                FROM estoques A
                INNER JOIN estoques_grade_positiva P ON A.codigo = P.idestoque
                WHERE A.status = '1'
                $strWhere
                ORDER BY A.codigo";
        }
        else
        {
            $str = "
                (
                SELECT DISTINCT A.codigo, A.max_negativo, A.max_positivo, A.nome, N.*, '1' AS grade
                FROM estoques A
                INNER JOIN estoques_grade_negativa N ON A.codigo = N.idestoque
                WHERE A.status = '1'
                $strWhere
                ORDER BY A.codigo
                )
                UNION ALL
                (
                SELECT DISTINCT A.codigo, A.max_negativo, A.max_positivo, A.nome, P.*, '2' AS grade
                FROM estoques A
                INNER JOIN estoques_grade_positiva P ON A.codigo = P.idestoque
                WHERE A.status = '1'
                $strWhere
                ORDER BY A.codigo
                )";
        }

        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        $num = mysqli_num_rows($rs);
        
        if($num > 0)
        {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Relatório de reposição</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Estoque</th>
                                    <th>Grade</th>
                                    <th>Esférico</th>
                                    <th>Cilíndrico</th>
                                    <th>Falta</th>
                                    <th>Estoque máximo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                    $grade = "Negativa";
                                    if($vet['grade'] == 2)
                                        $grade = "Positiva";

                                    $max_estoque = $vet['max_negativo'];
                                    if($vet['grade'] == 2)
                                        $max_estoque = $vet['max_positivo'];
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['nome'])?></td>
                                    <td><?=$grade?></td>
                                    <td><?=$vet['esf']?></td>
                                    <td><?=$vet['cil']?></td>
                                    <td><?=$vet['qtde']?></td>
                                    <td><?=$max_estoque?></td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Estoque</th>
                                    <th>Grade</th>
                                    <th>Esférico</th>
                                    <th>Cilíndrico</th>
                                    <th>Falta</th>
                                    <th>Estoque máximo</th>
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