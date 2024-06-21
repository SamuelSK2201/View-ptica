<?
$page = 'aniversariantes';
include("includes/header.php");

if($_SESSION["adm_user"] != 1)
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
        $strWhere = " AND MONTH(data_cadastro) = '$mes'";

    $str = "SELECT *, CASE WHEN pessoa = '1' THEN nome ELSE nome_fantasia END AS 'empresa' FROM empresas WHERE 1 = 1 $strWhere ORDER BY empresa";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    
    if($num > 0)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Lista de empresas cadastradas no sistema há mais de um ano</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Dia (cadastro)</th> 
                                    <th>Empresa</th>
                                    <th>Data do cadastro</th>
                                    <th>Telefone</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                    $array_data = explode("-", $vet['data_cadastro']);
                                    $dias = calculo_data($vet['data_cadastro'], date('Y-m-d H:i:s'));

                                    if($dias >= 365)
                                    {
                                ?>
                                <tr class="gradeX">
                                    <td><?=$array_data[2]?></td>
                                    <td><?=stripslashes($vet['empresa'])?></td>
                                    <td><?=ConverteData($vet['data_cadastro'])?></td>       
                                    <td><a href="https://api.whatsapp.com/send?phone=55<?=preg_replace('/[^\d]/', '', $vet['telefone'])?>" target="_blank"><?=$vet['telefone']?></a></td>
                                    <td><?=$vet['email']?></td>    
                                </tr>
                                <?
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Dia (cadastro)</th>
                                    <th>Empresa</th>
                                    <th>Data do cadastro</th>
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
                Nenhuma empresa encontrada no sistema.
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