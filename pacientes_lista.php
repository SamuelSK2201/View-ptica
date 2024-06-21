<?
$menu = 'pacientes';
$page = 'pacientes_lista';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_pacientes != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE)
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

if($_POST['cmd'] == 'search')
{
    unset($_SESSION['data_inicial']);
    unset($_SESSION['data_final']);
    unset($_SESSION['chave']);
    unset($_SESSION['consulta']);
}

$data_inicial = $_POST['data_inicial'];
$data_final = $_POST['data_final'];
$chave = $_POST['chave'];
$consulta = $_POST['consulta'];

if(!$data_inicial)
{
    $data_inicial = '01/'.date("m/Y");
}

if(!$data_final)
{
    $data_final = date("t/m/Y");
}

if($_SESSION['data_inicial'])
    $data_inicial = $_SESSION['data_inicial'];

if($_SESSION['data_final'])
    $data_final = $_SESSION['data_final'];

if($_SESSION['chave'])
    $chave = $_SESSION['chave'];

if($_SESSION['consulta'])
    $consulta = $_SESSION['consulta'];

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM pacientes WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("pacientes_lista.php?ind_msg=1");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Paciente excluído com sucesso!';

$title = "'View Óptica<br>Pacientes'";
$columns = '0, 1, 2, 3, 4, 5, 6, 7';
$order = ',order: [[ 0, "asc" ]]';
?>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Lista de pacientes</h2>
        <ol class="breadcrumb">
            <li class="active"><strong>Pacientes</strong></li>
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
                <div class="ibox-content" <?=($_POST['cmd'] == 'search' || $ind != 2) ? 'style="display: block;"' : 'style="display: none;"'?>>
                    <form method="post" class="form-horizontal" name="form_s" id="form_s" enctype="multipart/form-data">
                        <input type="hidden" name="cmd" value="search">                       
                        
                        <div class="row">
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
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Nome
                                </p>
                                <input type="text" name="chave" id="chave" class="form-control" value="<?=$chave?>" >
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Realizou consulta?
                                </p>
                                <select class="form-control" name="consulta" id="consulta">
                                    <option value="" <?=(!$consulta) ? 'selected' : ''?>>Todos</option>
                                    <option value="1" <?=($consulta == 1) ? 'selected' : ''?>>Sim</option>
                                    <option value="2" <?=($consulta == 2) ? 'selected' : ''?>>Não</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <p class="font-bold">&nbsp;</p>
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
    $strHaving = "";

    if($consulta)
    {
        $_SESSION['consulta'] = $consulta;

        if($consulta == 1)
            $strHaving = " HAVING ultima_consulta IS NOT NULL";
        elseif($consulta == 2)
            $strHaving = " HAVING ultima_consulta IS NULL";
    }

    if($chave)
    {
        $_SESSION['chave'] = $chave;
        $strWhere .= " AND nome LIKE '%$chave%'";
    }

    if($data_inicial)
    {
        $_SESSION['data_inicial'] = $data_inicial;
        $strWhere .= " AND data_cadastro >= '".ConverteData($data_inicial)."'";
    }

    if($data_final)
    {
        $_SESSION['data_final'] = $data_final;
        $strWhere .= " AND data_cadastro <= '".ConverteData($data_final)."'";
    }

    if($_POST['cmd'] == 'search')
    {
        $str = "SELECT DISTINCT A.codigo, A.nome, A.data_nascimento, A.estado, A.cidade, A.telefone, A.telefone2, A.data_cadastro,
            (SELECT B.data FROM agendamentos B WHERE B.idempresa = '$adm_empresa' AND B.idpaciente = A.codigo AND B.status = '6' $strWhereP ORDER BY B.data DESC LIMIT 1) AS ultima_consulta
            FROM pacientes A
            WHERE A.idempresa = '$adm_empresa'
            $strWhere 
            GROUP BY A.codigo
            $strHaving
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
                    <h5>Lista de pacientes cadastrados no sistema</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Nascimento</th>
                                    <th>Estado</th>
                                    <th>Cidade</th>
                                    <th>Telefone 1</th>
                                    <th>Telefone 2</th>
                                    <th>Última consulta</th>
                                    <th>Data de cadastro</th>
                                    <th style="width: 15%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['nome'])?></td>
                                    <td><?=($vet['data_nascimento']) ? ConverteData($vet['data_nascimento']) : '-'?></td>    
                                    <td><?=$vet['estado']?></td>
                                    <td><?=$vet['cidade']?></td>  
                                    <td><a href="https://api.whatsapp.com/send?phone=55<?=preg_replace('/[^\d]/', '', $vet['telefone'])?>" target="_blank"><?=($vet['telefone']) ? '55'.preg_replace('/[^\d]/', '', $vet['telefone']) : ''?></a></td>
                                    <td><a href="https://api.whatsapp.com/send?phone=55<?=preg_replace('/[^\d]/', '', $vet['telefone2'])?>" target="_blank"><?=($vet['telefone2']) ? '55'.preg_replace('/[^\d]/', '', $vet['telefone2']) : ''?></a></td>
                                    <td><?=($vet['ultima_consulta']) ? ConverteData($vet['ultima_consulta']) : '-'?></td>    
                                    <td><?=ConverteData($vet['data_cadastro'])?></td>  
                                    <td class="center">
                                        <a class="btn btn-default btn-circle" type="button" title="histórico" href="pacientes_historico.php?idpaciente=<?=$vet['codigo']?>" target="_blank"><i class="fa fa-user"></i></a>
                                        <a class="btn btn-info btn-circle" type="button" title="agendar" href="agendamentos.php?idpaciente=<?=$vet['codigo']?>"><i class="fa fa-calendar"></i></a>
                                        <a class="btn btn-warning btn-circle" type="button" title="editar / visualizar" href="pacientes.php?ind=2&codigo=<?=$vet['codigo']?>"><i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="pacientes_lista.php?cmd=del&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a> 
                                    </td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Nome</th>
                                    <th>Nascimento</th>
                                    <th>Estado</th>
                                    <th>Cidade</th>
                                    <th>Telefone 1</th>
                                    <th>Telefone 2</th>
                                    <th>Última consulta</th>
                                    <th>Data de cadastro</th>
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
                Nenhum paciente encontrado no sistema.
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