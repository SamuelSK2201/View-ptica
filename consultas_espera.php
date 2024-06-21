<?
$menu = 'agenda';
$page = 'consultas_espera';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_consultas != 1)
    die("Acesso negado!");

$title = "'View Óptica<br>Fila de espera'";
$columns = '0, 1, 2, 3, 4';
$order = ',order: [[ 4, "asc" ]]';
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Fila de espera</h2>        
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">    
    <?
    $str = "SELECT A.*, DATE_FORMAT(A.data_pagto, '%d/%m/%Y %H:%i') AS dt_pagto, B.nome AS optometrista, C.descricao AS procedimento, C.cor, D.nome AS paciente, D.cpf, D.cidade, D.estado
        FROM agendamentos A
        LEFT JOIN usuarios B ON A.idoptometrista = B.codigo
        LEFT JOIN procedimentos C ON A.idprocedimento = C.codigo
        INNER JOIN pacientes D ON A.idpaciente = D.codigo
        WHERE A.idempresa = '$adm_empresa'
        AND A.status = '4'
        AND A.data >= CURDATE()
        $strWhereP
        ORDER BY A.data";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    
    if($num > 0)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Lista de agendamentos em fila de espera no dia <?=date("d/m/Y")?></h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>Cidade / UF</th>
                                    <th>Especialista</th>
                                    <th>Agendado para</th>
                                    <th>Gera receita</th>
                                    <th>Pago em</th>
                                    <th>Consulta</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['paciente'])?></td>
                                    <td><?=$vet['cpf']?></td>
                                    <td><?=stripslashes($vet['cidade'])?> / <?=$vet['estado']?></td>
                                    <td><?=stripslashes($vet['optometrista'])?></td>
                                    <td><?=ConverteData($vet['data'])?> <?=substr($vet['hora_inicial'], 0, -3)?></td>
                                    <td>-</td>
                                    <td><?=$vet['dt_pagto']?></td>
                                    <td>
                                        <a class="btn btn-info btn-circle" type="button" title="Iniciar consulta" href="prescricoes.php?idagendamento=<?=$vet['codigo']?>&s=4"><i class="fa fa-check-square"></i></a>
                                    </td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Nome</th>
                                    <th>CPF</th>
                                    <th>Cidade / UF</th>
                                    <th>Especialista</th>
                                    <th>Agendado para</th>
                                    <th>Gera receita</th>
                                    <th>Pago em</th>
                                    <th>Consulta</th>
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
                Nenhum agendamento em fila de espera no sistema para o dia <?=date("d/m/Y")?>.
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