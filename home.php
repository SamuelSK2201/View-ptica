<?
$page = 'home';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
	die("Acesso negado!");

?>
<div class="wrapper wrapper-content">
	<?
    if($adm_perfil == 1 || $adm_perfil == 3)
    {
    ?>
    <div class="row">
		<div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-info pull-right">Total de pacientes</span>
                    <h5>Pacientes</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><?=qtde_pacientes($conexao, $adm_empresa)?></h1>
                    <div class="stat-percent font-bold text-info"><a href="pacientes.php" style="color: #23c6c8">Acessar</a> </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-primary pull-right">Agendamentos (dia)</span>
                    <h5>Agendamentos</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><?=qtde_consultas_hoje($conexao, $adm_empresa, $adm_perfil)?></h1>
                    <div class="stat-percent font-bold text-navy"><a href="r_consultas.php" style="color: #1ab394">Acessar</a> </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-success pull-right">Consultas realizadas (dia)</span>
                    <h5>Consultas</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><?=qtde_consultas_realizadas_hoje($conexao, $adm_empresa, $adm_perfil)?></h1>
                    <div class="stat-percent font-bold text-navy"><a href="r_consultas.php?fl=1" style="color: #1c84c6">Acessar</a> </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-warning pull-right">Total consultas (mês)</span>
                    <h5>Consultas</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><?=qtde_consultas_realizadas_mes($conexao, $adm_empresa, $adm_perfil)?></h1>
                    <div class="stat-percent font-bold text-navy"><a href="r_consultas.php?fl=2" style="color: #f8ac59">Acessar</a> </div>
                </div>
            </div>
        </div>
	</div>
    <?
    }
    ?>

	<div class="row">
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Aniversariantes do mês</h5>
                </div>
                <div class="ibox-content">
                	<?
                	$mes = date("m");

                	$str = "SELECT * FROM pacientes WHERE idempresa = '$adm_empresa' AND MONTH(data_nascimento) = '$mes' ORDER BY DAY(data_nascimento) LIMIT 10";
				    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
				    $num = mysqli_num_rows($rs);
				    
				    if($num > 0)
				    {
                	?>
                    <table class="table table-hover no-margins">
                        <thead>
                        <tr>
                            <th>Data</th>
                            <th>Nome do paciente</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?
                        while($vet = mysqli_fetch_array($rs))
                        {
                            $array_data = explode("-", $vet['data_nascimento']);
                        ?>
                        <tr>
                            <td><?=$array_data[2]?>/<?=$array_data[1]?></td>
                            <td><?=stripslashes($vet['nome'])?></td>
                        </tr>
                        <?
                        }
                        ?>
                        </tbody>
                    </table>
                    <a class="btn btn-success btn-block m-t" href="r_aniversariantes.php"><i class="fa fa-arrow-down"></i> Veja mais</a>
                    <?
                    }
                    else
                    {
                    ?>
                    <p class="font-bold  alert alert-danger m-b-sm">
		                Nenhum paciente faz aniversário neste mês
		            </p>
                    <?
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Consulta vencidas</h5>
                </div>
                <div class="ibox-content">
                	<?
                	$str = "SELECT A.*, B.nome AS paciente, B.email, B.telefone
                        FROM agendamentos A
                        INNER JOIN pacientes B ON A.idpaciente = B.codigo
                        WHERE A.idempresa = '$adm_empresa'
                        AND A.status = '6'
                        AND A.data <= DATE_ADD(CURDATE(), INTERVAL -1 YEAR)
                        $strWhereP
                        ORDER BY A.data";
				    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
				    $num = mysqli_num_rows($rs);
				    
				    if($num > 0)
				    {
                	?>
                    <table class="table table-hover no-margins">
                        <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Última consulta</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?
                        while($vet = mysqli_fetch_array($rs))
                        {
                            $idpaciente = $vet['idpaciente'];

                            $strC = "SELECT data FROM agendamentos WHERE idempresa = '$adm_empresa' AND idpaciente = '$idpaciente' AND status = '6' $strWhereP ORDER BY data DESC LIMIT 1";
                            $rsC  = mysqli_query($conexao, $strC) or die(mysqli_error($conexao));
                            $vetC = mysqli_fetch_array($rsC);
                        ?>
                        <tr class="gradeX">
                            <td><?=stripslashes($vet['paciente'])?></td>
                            <td><?=$vet['email']?></td>
                            <td><a href="https://api.whatsapp.com/send?phone=55<?=preg_replace('/[^\d]/', '', $vet['telefone'])?>" target="_blank"><?=$vet['telefone']?></a></td>
                            <td><?=($vetC['data']) ? ConverteData($vetC['data']) : '-'?></td>
                        </tr>
                        <?
                        }
                        ?>
                        </tbody>
                    </table>
                    <a class="btn btn-primary btn-block m-t" href="r_consultas_vencidas.php"><i class="fa fa-arrow-down"></i> Veja mais</a>
                    <?
                    }
                    else
                    {
                    ?>
                    <p class="font-bold  alert alert-danger m-b-sm">
		                Nenhuma consulta vencida no sistema
		            </p>
                    <?
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

<?
include("includes/footer.php");
?>