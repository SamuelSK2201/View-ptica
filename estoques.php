<?
$menu = 'estoques';
$page = 'estoques';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if(!$perm_estoque)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE)
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

$nome = addslashes($_POST['nome']);
$max_negativo = $_POST['max_negativo'];
$max_positivo = $_POST['max_positivo'];
$array_lentes = $_REQUEST['lentes'];
$array_empresas = $_REQUEST['empresas'];

//LENTES
if(@count($array_lentes))
{
    $lentes = "|";
    for($i = 0; $i < @count($array_lentes); $i++)
        $lentes .= $array_lentes[$i]."|";
}

//EMPRESAS
if(@count($array_empresas))
{
    $empresas = "|";
    for($i = 0; $i < @count($array_empresas); $i++)
        $empresas .= $array_empresas[$i]."|";
}

if($_POST['cmd'] == "upload")
{   
    $dir = getcwd();
    $dir_upload = $dir . "/upload/";    
    @mkdir($dir_upload, 0777);
    
    $strpos = strpos($_FILES['arquivo']['name'], ".");
    $ext = substr($_FILES['arquivo']['name'], $strpos);

    $vet_ext = array(".csv"); 
    
    if(!in_array($ext, $vet_ext))
        redireciona("estoques.php?ind_msg=6");

    $nome_arquivo = uniqid().$ext;    
    $arquivo_upload = $dir_upload.$nome_arquivo;

    move_uploaded_file($_FILES['arquivo']['tmp_name'], $arquivo_upload);

    $arq = fopen($arquivo_upload, "r");
    $i = 0;

    $idestoque = $_POST['idestoque'];
    $tipo_grade = $_POST['tipo_grade'];

    $table = "estoques_grade_negativa";
    $array_cil = array('', 0, -0.25, -0.5, -0.75, -1, -1.25, -1.5, -1.75, -2, -2.25, -2.5, -2.75, -3);
    $array_esf = array(0, -0.25, -0.5, -0.75, -1, -1.25, -1.5, -1.75, -2, -2.25, -2.5, -2.75, -3, -3.25, -3.5, -3.75, -4);

    if($tipo_grade == 2)
    {
        $table = "estoques_grade_positiva";
        $array_cil = array('', 0, -0.25, -0.5, -0.75, -1, -1.25, -1.5, -1.75, -2, -2.25, -2.5, -2.75, -3);
        $array_esf = array(0.25, 0.5, 0.75, 1, 1.25, 1.5, 1.75, 2, 2.25, 2.5, 2.75, 3, 3.25, 3.5, 3.75, 4);
    }

    $i = 0;

    $str = "DELETE FROM $table WHERE idestoque = '$idestoque'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    while($vet = fgetcsv($arq, 300000, ";")) 
    {
        if($x > 0)
        {
            //print_r($vet);
            //echo '<br>';

            for($j = 1; $j < count($vet); $j++)
            {
                $strG = "INSERT INTO $table (idestoque, esf, cil, qtde) VALUES ('$idestoque', '".$array_esf[$i]."', '".$array_cil[$j]."', '".$vet[$j]."')";
                $rsG  = mysqli_query($conexao, $strG) or die(mysqli_error($conexao));
                //echo '<br>';
            }

            $i++;
            //echo '<br>';
        }

        $x++;
    }

    //die;

    redireciona("estoques.php?ind_msg=5");
}

if($_POST['cmd'] == "add")
{  
    $str = "INSERT INTO estoques (nome, idempresa, idlente, max_negativo, max_positivo) VALUES ('$nome', '$empresas', '$lentes', '$max_negativo', '$max_positivo')";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $idestoque = mysqli_insert_id($conexao);

    //GRADE NEGATIVA
    $array_cil = array(0, -0.25, -0.5, -0.75, -1, -1.25, -1.5, -1.75, -2, -2.25, -2.5, -2.75, -3);
    $array_esf = array(0, -0.25, -0.5, -0.75, -1, -1.25, -1.5, -1.75, -2, -2.25, -2.5, -2.75, -3, -3.25, -3.5, -3.75, -4);

    for($i = 0; $i < count($array_esf); $i++)
    {
        for($j = 0; $j < count($array_cil); $j++)
        {
            $strG = "INSERT INTO estoques_grade_negativa (idestoque, esf, cil, qtde) VALUES ('$idestoque', '".$array_esf[$i]."', '".$array_cil[$j]."', '$max_negativo')";
            $rsG  = mysqli_query($conexao, $strG) or die(mysqli_error($conexao));
            //echo '<br>';
        }
    }

    $array_cil = array(0, -0.25, -0.5, -0.75, -1, -1.25, -1.5, -1.75, -2, -2.25, -2.5, -2.75, -3);
    $array_esf = array(0.25, 0.5, 0.75, 1, 1.25, 1.5, 1.75, 2, 2.25, 2.5, 2.75, 3, 3.25, 3.5, 3.75, 4);

    for($i = 0; $i < count($array_esf); $i++)
    {
        for($j = 0; $j < count($array_cil); $j++)
        {
            $strG = "INSERT INTO estoques_grade_positiva (idestoque, esf, cil, qtde) VALUES ('$idestoque', '".$array_esf[$i]."', '".$array_cil[$j]."', '$max_positivo')";
            $rsG  = mysqli_query($conexao, $strG) or die(mysqli_error($conexao));
            //echo '<br>';
        }
    }

    redireciona("estoques.php?ind_msg=1");
}

if($_POST['cmd'] == "edit")
{    
    $str = "UPDATE estoques SET nome = '$nome', idempresa = '$empresas', idlente = '$lentes', max_negativo = '$max_negativo', max_positivo = '$max_positivo' WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        
    redireciona("estoques.php?ind_msg=2");
}

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM estoques WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM estoques_grade_negativa WHERE idestoque = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM estoques_grade_positiva WHERE idestoque = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("estoques.php?ind_msg=3");
}

if($_GET['cmd'] == "ativar")
{
    $str = "UPDATE estoques SET status = '1', data_ativacao = CURDATE() WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("estoques.php?ind_msg=4");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Estoque cadastrado com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Estoque editado com sucesso!';
elseif($_GET['ind_msg'] == "3")
    $msg = 'Estoque excluído com sucesso!';
elseif($_GET['ind_msg'] == "4")
    $msg = 'Estoque ativado com sucesso!';
elseif($_GET['ind_msg'] == "5")
    $msg = 'Estoque importado com sucesso no sistema!';
elseif($_GET['ind_msg'] == "6")
    $msg = 'A extensão do arquivo de importação deve ser (.csv)!';

$str = "SELECT * FROM estoques WHERE codigo = '$codigo'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);

$title = "'View Optica<br>Estoques'";
$columns = '0, 1, 2, 3, 4, 5';
$order = ',order: [[ 0, "asc" ]]';
?>

<script language="javascript">
function valida(ind)
{   
    if(ind == 1)
        document.form.cmd.value = "add";
    else
        document.form.cmd.value = "edit";
}
</script>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Gerenciar estoque</h2>
        <ol class="breadcrumb">
            <li class="active"><strong>Estoques</strong></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <?
    if($perm_estoque == 1)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <?
            if(!empty($_GET['ind_msg']) && $_GET['ind_msg'] <= 6)
            {
            ?>
            <p class="font-bold  alert alert-success m-b-sm">
                <?=$msg?>
            </p>
            <br>
            <?
            }

            if($_GET['ind_msg'] == 6)
            {
            ?>
            <p class="font-bold  alert alert-danger m-b-sm">
                <?=$msg?>
            </p>
            <br>
            <?
            }
            ?>
            <div class="ibox float-e-margins">
                <div class="ibox-title">    
                    <h5>IMPORTAR ARQUIVO - <small><i>Clique na seta do lado direito para abrir ou ocultar o formulário</i></small></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-down"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content" style="display: none;">
                    <form method="post" class="form-horizontal" name="form_u" id="form_u" enctype="multipart/form-data" action="estoques.php">
                        <input type="hidden" name="cmd" value="upload">

                        <div class="alert alert-warning">
                            - Abaixo exemplo de arquivo csv utilizado para importação do estoque em lote.<br><br>
                            <a href="arquivos/import_grade_negativa.csv" download class="btn btn-white btn-sm"><i class="fa fa-folder"></i> Ver </a>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <label for="arquivo">Upload de arquivo (csv):</label>
                                <input type="file" id="arquivo" name="arquivo" required>
                                <p class="help-block">Extensão permitida: csv.</p>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="font-bold">
                                    Estoque*
                                </p>
                                <select class="chosen-select" name="idestoque" id="idestoque" required>
                                    <option value="">Selecione ...</option>
                                    <?
                                    $strC = "SELECT * FROM estoques ORDER BY nome";
                                    $rsC  = mysqli_query($conexao, $strC) or die(mysqli_error($conexao));

                                    while($vetC = mysqli_fetch_array($rsC))
                                    {
                                    ?>
                                    <option value="<?=$vetC['codigo']?>" ><?=stripslashes($vetC['nome'])?></option>
                                    <?
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <p class="font-bold">
                                    Tipo de grade*
                                </p>
                                <select class="form-control" name="tipo_grade" id="tipo_grade" required>
                                    <option value="">Selecione ...</option>
                                    <option value="1" >Negativa</option>
                                    <option value="2" >Positiva</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-12"> 
                                <button type="submit" class="btn btn-primary" onclick="javascript: if(!confirm('Aguarde, esta operação pode demorar alguns minutos.')) { return false }">Fazer upload</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="ibox float-e-margins">
                <div class="ibox-title">    
                    <h5>CADASTRO MANUAL - <small><i>Clique na seta do lado direito para abrir ou ocultar o formulário</i></small></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-down"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content" >
                    <form method="post" class="form-horizontal" name="form" id="form" enctype="multipart/form-data">
                        <input type="hidden" name="cmd">  
                        <input type="hidden" name="codigo" id="codigo" value="<?=$vet['codigo']?>">

                        <div class="row">
                            <div class="col-md-6">
                                <p class="font-bold" >
                                    Nome*
                                </p>
                                <input type="text" name="nome" id="nome" class="form-control" value="<?=stripslashes($vet['nome'])?>" required>
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold" >
                                    Máximo estoque (-)*
                                </p>
                                <input type="number" name="max_negativo" id="max_negativo" class="form-control" value="<?=$vet['max_negativo']?>" min="0" step="1" required>
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold" >
                                    Máximo estoque (+)*
                                </p>
                                <input type="number" name="max_positivo" id="max_positivo" class="form-control" value="<?=$vet['max_positivo']?>" min="0" step="1" required>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="font-bold">
                                    Lentes*
                                </p>
                                <select class="form-control dual_select" name="lentes[]" id="lentes" multiple required size="5">
                                    <?
                                    $strE = "SELECT * FROM tipos_lentes WHERE idempresa = '$adm_empresa' AND status = '1' ORDER BY nome";
                                    $rsE  = mysqli_query($conexao, $strE) or die(mysqli_error($conexao));

                                    while($vetE = mysqli_fetch_array($rsE))
                                    {
                                        $idlente = $vetE['codigo'];
                                    ?>
                                    <option value="<?=$vetE['codigo']?>" <?=(strstr($vet['idlente'], "|".$idlente."|")) ? 'selected' : ''?>><?=stripslashes($vetE['nome'])?></option>
                                    <?
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="font-bold">
                                    Empresas*
                                </p>
                                <select class="form-control dual_select" name="empresas[]" id="empresas" multiple required size="5">
                                    <?
                                    $strE = "SELECT * FROM empresas ORDER BY nome_fantasia";
                                    $rsE  = mysqli_query($conexao, $strE) or die(mysqli_error($conexao));

                                    while($vetE = mysqli_fetch_array($rsE))
                                    {
                                        $idempresa = $vetE['codigo'];
                                    ?>
                                    <option value="<?=$vetE['codigo']?>" <?=(strstr($vet['idempresa'], "|".$idempresa."|")) ? 'selected' : ''?>><?=stripslashes($vetE['nome_fantasia'])?></option>
                                    <?
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-12"> 
                                <?
                                if($ind == 1)
                                {
                                ?>
                                <button type="submit" class="btn btn-primary" onClick="javascript: valida(<?=$ind?>);">Cadastrar</button>
                                <?
                                }
                                else
                                {
                                ?>
                                <button type="submit" class="btn btn-primary" onClick="javascript: valida(<?=$ind?>);">Alterar</button>
                                <?
                                }
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?
    }

    $str = "SELECT * FROM estoques WHERE idempresa LIKE '%|$adm_empresa|%' ORDER BY nome";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if($num > 0)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Lista de estoques cadastrados no sistema</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" style="font-size: 12px;" >
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Empresas</th>
                                    <th>Lentes</th>
                                    <th>Máximo estoque (-)</th>
                                    <th>Máximo estoque (+)</th>
                                    <th>Status</th>
                                    <th style="width: 15%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                    $array_empresas = explode("|", substr(substr($vet['idempresa'], 0, -1), 1));
                                    $empresas = "";

                                    for($i = 0; $i < count($array_empresas); $i++)
                                    {
                                        $strE = "SELECT * FROM empresas WHERE codigo = '".$array_empresas[$i]."'";
                                        $rsE  = mysqli_query($conexao, $strE) or die(mysqli_error($conexao));

                                        while($vetE = mysqli_fetch_array($rsE))
                                        {
                                            $empresas .= stripslashes($vetE['nome_fantasia'])."<br>";
                                        }
                                    }

                                    $empresas = substr($empresas, 0, -2);

                                    $array_lentes = explode("|", substr(substr($vet['idlente'], 0, -1), 1));
                                    $lentes = "";

                                    for($i = 0; $i < count($array_lentes); $i++)
                                    {
                                        $strE = "SELECT * FROM tipos_lentes WHERE status = '1' AND codigo = '".$array_lentes[$i]."'";
                                        $rsE  = mysqli_query($conexao, $strE) or die(mysqli_error($conexao));

                                        while($vetE = mysqli_fetch_array($rsE))
                                        {
                                            $lentes .= stripslashes($vetE['nome'])."<br>";
                                        }
                                    }

                                    $lentes = substr($lentes, 0, -2);

                                    if($vet['status'] == 1)
                                        $status = '<span class="label label-success">Ativo</span>';
                                    else
                                        $status = '<span class="label label-danger">Inativo</span>';
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['nome'])?></td>
                                    <td><?=nl2br($empresas)?></td>
                                    <td><?=nl2br($lentes)?></td>
                                    <td><?=$vet['max_negativo']?></td>
                                    <td><?=$vet['max_positivo']?></td>
                                    <td><?=$status?></td>
                                    <td class="center">
                                        <a class="btn btn-info btn-circle" type="button" title="visualizar estoque" href="estoques_grades.php?codigo=<?=$vet['codigo']?>" target="_blank"><i class="fa fa-eye"></i></a>
                                        <?
                                        if($perm_estoque == 1)
                                        {
                                            if($vet['status'] != 1)
                                            {
                                        ?>
                                        <a class="btn btn-primary btn-circle" type="button" title="ativar estoque" href="estoques.php?cmd=ativar&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente ativar este estoque?')) { return false }"><i class="fa fa-check"></i></a>
                                        <?
                                            }
                                        ?>
                                        <a class="btn btn-warning btn-circle" type="button" title="editar / visualizar" href="estoques.php?ind=2&codigo=<?=$vet['codigo']?>"><i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="estoques.php?cmd=del&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a>
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
                                    <th>Nome</th>
                                    <th>Empresas</th>
                                    <th>Lentes</th>
                                    <th>Máximo estoque (-)</th>
                                    <th>Máximo estoque (+)</th>
                                    <th>Status</th>
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
                Nenhum estoque encontrado no sistema.
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