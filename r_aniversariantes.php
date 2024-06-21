<?
$menu = 'relatorios';
$page = 'r_aniversariantes';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_relatorios != 1)
    die("Acesso negado!");

$mes = anti_injection($_POST['mes']);

if(!$mes)
    $mes = date("m");

$title = "'View Óptica<br>Aniversariantes'";
$columns = '0, 1, 2, 3, 4';
$order = ',order: [[ 0, "asc" ]]';
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Aniversariantes</h2>
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
                    <h5><i>Informe abaixo o mês que deseja exportar</i></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-down"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form method="post" class="form-horizontal" name="form" id="form" enctype="multipart/form-data">
                        <input type="hidden" name="cmd" value="excel_aniversariantes">                       
                        
                        <div class="row">
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Mês*
                                </p>
                                <select name="mes" id="mes" class="form-control" required>
                                    <option value="">Selecione ...</option>
                                    <?
                                    for($i = 1; $i <= 12; $i++)
                                    {
                                    ?>
                                    <option value="<?=$i?>" <?=($i == $mes) ? 'selected' : ''?>><?=mes_extenso($i)?></option>
                                    <?
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-xs-12"> 
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
    if($mes)
        $strWhere = " AND MONTH(data_nascimento) = '$mes'";

    $str = "SELECT * FROM pacientes WHERE idempresa = '$adm_empresa' $strWhere ORDER BY nome";
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
                                    <th>Dia</th>
                                    <th>Nome do paciente</th>
                                    <th>Nascimento</th>
                                    <th>Telefone</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                    $array_data = explode("-", $vet['data_nascimento']);
                                ?>
                                <tr class="gradeX">
                                    <td><?=$array_data[2]?></td>
                                    <td><?=stripslashes($vet['nome'])?></td>
                                    <td><?=ConverteData($vet['data_nascimento'])?></td>       
                                    <td><a href="https://api.whatsapp.com/send?phone=55<?=preg_replace('/[^\d]/', '', $vet['telefone'])?>" target="_blank"><?=$vet['telefone']?></a></td>
                                    <td><?=$vet['email']?></td>    
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Dia</th>
                                    <th>Nome do paciente</th>
                                    <th>Nascimento</th>
                                    <th>Telefone</th>
                                    <th>Email</th>
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
    ?>
</div>
<?
include("includes/footer.php");
?>