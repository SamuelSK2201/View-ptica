<?
$menu = 'agenda';
$page = 'agendamentos_disponiveis';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_agenda != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE) 
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

$data_inicial = ConverteData($_POST['data_inicial']);
$data_final = ConverteData($_POST['data_final']);
$periodo = $_POST['periodo'];
$array_periodo = explode(" - ", $_POST['periodo']);
$hora_inicial = $array_periodo[0];
$hora_final = $array_periodo[1];
$intervalo = $_POST['intervalo'];

$dias_semana = '';
$array_dias_semana = $_POST['dias_semana'];

if(@count($array_dias_semana))
{
    for($i = 0; $i < @count($array_dias_semana); $i++)
    {
        $dias_semana .= $array_dias_semana[$i].';';
    }

    $dias_semana = substr($dias_semana, 0, -1);
}

$str = "SELECT * FROM agendamentos_disponiveis WHERE codigo = '$codigo'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);

if($_POST['cmd'] == "add" || $_POST['cmd'] == "edit")
{
    $array_datas = retorna_dias($data_inicial, $data_final);
    //print_r($array_datas);
    //echo '<br>';

    for($i = 0; $i < count($array_datas); $i++)
    {
        $array = explode("-", $array_datas[$i]);
        $dia_semana = date("w", mktime(0, 0, 0, $array[1], $array[2], $array[0]));

        if(in_array($dia_semana, $array_dias_semana))
            $array_datas_possiveis[] = $array_datas[$i];
    }

    //print_r($array_datas_possiveis);
    //echo '<br>';

    $start_time = strtotime($hora_inicial); // Hora de início
    $end_time = strtotime($hora_final); // Hora de término
    $interval = $intervalo; // Intervalo de minutos

    $current_time = $start_time;
    $times = array();

    while ($current_time <= $end_time) {
        $times[] = date('H:i', $current_time);
        $current_time += $interval * 60;
    }

    //print_r($times);
    //echo '<br>';

    for($i = 0; $i < count($times); $i++) {
        if(!empty($times[$i+1]))
        {
            $horarios[] = $times[$i].' - '.$times[$i+1];
            if($times[$i+1] == $array_atendimento[1])
                break;
        }
    }

    //print_r($horarios);
    //echo '<br>';
}

if($_POST['cmd'] == "add")
{
    $str = "SELECT * FROM agendamentos_disponiveis WHERE idempresa = '$adm_empresa' AND ('$data_inicial' BETWEEN data_inicial AND data_final OR '$data_final' BETWEEN data_inicial AND data_final)";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if($num)
        redireciona("agendamentos_disponiveis.php?ind_msg=4");

    $str = "INSERT INTO agendamentos_disponiveis (idempresa, data_inicial, data_final, periodo, intervalo, dias_semana) VALUES ('$adm_empresa', '$data_inicial', '$data_final', '$periodo', '$intervalo', '$dias_semana')";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $codigo = mysqli_insert_id($conexao);
    //echo '<br>';

    //DATAS
    if(@count($array_datas_possiveis))
    {
        $str = "DELETE FROM disponiveis_datas WHERE idagendamento = '$codigo'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        //echo '<br>';

        for($i = 0; $i < @count($array_datas_possiveis); $i++)
        {
            $data = $array_datas_possiveis[$i];

            for($j = 0; $j < @count($horarios); $j++)
            {
                $array_horarios = explode(" - ", $horarios[$j]);
                $hora_inicial = $array_horarios[0];
                $hora_final = $array_horarios[1];

                $str = "INSERT INTO disponiveis_datas (idagendamento, data, hora_inicial, hora_final) VALUES ('$codigo', '$data', '$hora_inicial', '$hora_final')";
                $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                //echo '<br>';
            }
        }
    }

    redireciona("agendamentos_disponiveis.php?ind_msg=1");
}

if($_POST['cmd'] == "edit")
{
    $str = "SELECT * FROM agendamentos_disponiveis WHERE codigo != '$codigo' AND idempresa = '$adm_empresa' AND ('$data_inicial' BETWEEN data_inicial AND data_final OR '$data_final' BETWEEN data_inicial AND data_final)";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if($num)
        redireciona("agendamentos_disponiveis.php?ind_msg=4");

    $str = "UPDATE agendamentos_disponiveis SET data_inicial = '$data_inicial', data_final = '$data_final', periodo = '$periodo', intervalo = '$intervalo', dias_semana = '$dias_semana' WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    //DATAS
    if(@count($array_datas_possiveis))
    {
        $str = "DELETE FROM disponiveis_datas WHERE idagendamento = '$codigo'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        //echo '<br>';

        for($i = 0; $i < @count($array_datas_possiveis); $i++)
        {
            $data = $array_datas_possiveis[$i];

            for($j = 0; $j < @count($horarios); $j++)
            {
                $array_horarios = explode(" - ", $horarios[$j]);
                $hora_inicial = $array_horarios[0];
                $hora_final = $array_horarios[1];

                $str = "INSERT INTO disponiveis_datas (idagendamento, data, hora_inicial, hora_final) VALUES ('$codigo', '$data', '$hora_inicial', '$hora_final')";
                $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                //echo '<br>';
            }
        }
    }

    redireciona("agendamentos_disponiveis.php?ind_msg=2");
}

if($_GET['cmd'] == "del")
{
    $str = "SELECT * FROM agendamentos_disponiveis WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $vet = mysqli_fetch_array($rs);

    $str = "DELETE FROM prospeccao WHERE idempresa = '$adm_empresa' AND auto = '1' AND data_agendamento BETWEEN '".$vet['data_inicial']."' AND '".$vet['data_final']."' ORDER BY data";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    /***************************
    //DATAS NÃO DISPONÍVEIS
    ***************************/
    $str = "DELETE FROM agendamentos_n_disponiveis WHERE idagendamento = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    /***************************
    //DATAS
    ***************************/
    $str = "DELETE FROM disponiveis_datas WHERE idagendamento = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    /***************************
    //AGENDAMENTO
    ***************************/
    $str = "DELETE FROM agendamentos_disponiveis WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("agendamentos_disponiveis.php?ind_msg=3");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Autoagendamento cadastrado com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Autoagendamento editado com sucesso!';
elseif($_GET['ind_msg'] == "3")
    $msg = 'Autoagendamento excluído com sucesso!';
elseif($_GET['ind_msg'] == "4")
    $msg = 'Período informado já existe em outro agendamento!';

$title = "'View Optica<br>Autoagendamento'";
$columns = '0, 1, 2, 3, 4, 5';
$order = ',order: [[ 0, "desc" ]]';
?>

<script language="javascript">
function valida(ind)
{   
    if(ind == 1)
        document.form.cmd.value = "add";
    else
        document.form.cmd.value = "edit";
}

function preencher_checkboxes() {
    document.getElementById('domingo').checked = false;
    document.getElementById('segunda').checked = false;
    document.getElementById('terca').checked = false;
    document.getElementById('quarta').checked = false;
    document.getElementById('quinta').checked = false;
    document.getElementById('sexta').checked = false;
    document.getElementById('sabado').checked = false;

    dataInicial = document.getElementById('data_inicial').value;
    dataFinal = document.getElementById('data_final').value;

    // Converter para o formato yyyy-mm-dd
    dataInicial = dataInicial.split('/').reverse().join('-');
    dataFinal = dataFinal.split('/').reverse().join('-');

    diasSemana = ['domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
    
    partesData = dataInicial.split('-');
    ano = parseInt(partesData[0]);
    mes = parseInt(partesData[1]) - 1; // Os meses em JavaScript começam de 0 (janeiro = 0, fevereiro = 1, etc.)
    dia = parseInt(partesData[2]);

    dataAtual = new Date(ano, mes, dia);
    dataInicial = new Date(ano, mes, dia);

    partesData = dataFinal.split('-');
    ano = parseInt(partesData[0]);
    mes = parseInt(partesData[1]) - 1; // Os meses em JavaScript começam de 0 (janeiro = 0, fevereiro = 1, etc.)
    dia = parseInt(partesData[2]);

    dataFinal = new Date(ano, mes, dia);

    console.log(dataAtual+' - '+dataInicial+' - '+dataFinal);

    while (dataAtual <= dataFinal) {
        var diaSemana = dataAtual.getDay(); // Obtém o dia da semana (0 = Domingo, 1 = Segunda, ..., 6 = Sábado)

        // Verifica se o dia da semana está dentro do período especificado e marca o checkbox correspondente
        if (dataAtual >= dataInicial && dataAtual <= dataFinal) {
            console.log(diasSemana[diaSemana]);
            document.getElementById(diasSemana[diaSemana]).checked = true;
        }

        // Incrementa a data atual para o próximo dia
        dataAtual.setDate(dataAtual.getDate() + 1);
    }
}
</script>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Autoagendamento</h2>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <?
            if(!empty($_GET['ind_msg']) && $_GET['ind_msg'] <= 3)
            {
            ?>
            <p class="font-bold  alert alert-success m-b-sm">
                <?=$msg?>
            </p>
            <br>
            <?
            }

            if($_GET['ind_msg'] >= 4)
            {
            ?>
            <p class="font-bold  alert alert-danger m-b-sm">
                <?=$msg?>
            </p>
            <br>
            <?
            }
            ?>

            <p class="font-bold  alert alert-warning m-b-sm">
                <a href="../agendamento/index.php?empresa=<?=$adm_str_format?>" target="_blank">Clique AQUI para acessar a tela de agendamento.</a>
            </p>
            <br>
            <p class="font-bold  alert alert-info m-b-sm">
                <a href="disponiveis_txt.php">Clique AQUI para gerenciar os textos das telas de agendamento.</a>
            </p>
            <br>
            <div class="ibox float-e-margins">
                <div class="ibox-title">    
                    <h5><i>Clique na seta do lado direito para abrir ou ocultar o formulário</i></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-down"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content" <?=($ind == 2) ? 'style="display: block;"' : 'style="display: none;"'?>>
                    <form method="post" class="form-horizontal" name="form" id="form" enctype="multipart/form-data">
                        <input type="hidden" name="cmd" >  
                        <input type="hidden" name="codigo" id="codigo" value="<?=$vet['codigo']?>">                      

                        <div class="row">
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Data inicial*
                                </p>
                                <div class="form-group" id="data_1">
                                    <div class="input-group date" style="margin-left: 15px;">
                                        <span class="input-group-addon" ><i class="fa fa-calendar"></i></span><input style="width: 90%;" type="text" class="form-control" name="data_inicial" id="data_inicial" value="<?=($vet['data_inicial']) ? ConverteData($vet['data_inicial']) : date("d/m/Y")?>" required maxlength="10" data-mask="99/99/9999" onKeyUp="javascript: return auto_data('data_inicial');" onKeyPress="javascript: return somenteNumeros(event);" onchange="javascript: preencher_checkboxes();">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Data final*
                                </p>
                                <div class="form-group" id="data_1">
                                    <div class="input-group date" style="margin-left: 15px;">
                                        <span class="input-group-addon" ><i class="fa fa-calendar"></i></span><input style="width: 90%;" type="text" class="form-control" name="data_final" id="data_final" value="<?=($vet['data_final']) ? ConverteData($vet['data_final']) : date("d/m/Y")?>" required maxlength="10" data-mask="99/99/9999" onKeyUp="javascript: return auto_data('data_final');" onKeyPress="javascript: return somenteNumeros(event);" onchange="javascript: preencher_checkboxes();">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Horário inicial - final*
                                </p>
                                <input class="form-control" type="text" name="periodo" id="periodo" value="<?=$vet['periodo']?>" required maxlength="13" data-mask="99:99 - 99:99" onKeyPress="javascript: return somenteNumeros(event);">
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Intervalo (minutos)*
                                </p>
                                <input class="form-control" type="number" name="intervalo" id="intervalo"  value="<?=$vet['intervalo']?>" required step="1" >
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="font-bold">
                                    Dias da semana disponíveis
                                </p>
                                <input type="checkbox" name="dias_semana[]" id="domingo" value="0" <?=(strstr($vet['dias_semana'], '0') || $vet['dias_semana'] == '0') ? 'checked' : ''?>>&nbsp;DOMINGO<br>
                                <input type="checkbox" name="dias_semana[]" id="segunda" value="1" <?=(strstr($vet['dias_semana'], '1') || $vet['dias_semana'] == '1') ? 'checked' : ''?>>&nbsp;SEGUNDA-FEIRA<br>
                                <input type="checkbox" name="dias_semana[]" id="terca" value="2" <?=(strstr($vet['dias_semana'], '2') || $vet['dias_semana'] == '2') ? 'checked' : ''?>>&nbsp;TERÇA-FEIRA<br>
                                <input type="checkbox" name="dias_semana[]" id="quarta" value="3" <?=(strstr($vet['dias_semana'], '3') || $vet['dias_semana'] == '3') ? 'checked' : ''?>>&nbsp;QUARTA-FEIRA<br>
                                <input type="checkbox" name="dias_semana[]" id="quinta" value="4" <?=(strstr($vet['dias_semana'], '4') || $vet['dias_semana'] == '4') ? 'checked' : ''?>>&nbsp;QUINTA-FEIRA<br>
                                <input type="checkbox" name="dias_semana[]" id="sexta" value="5" <?=(strstr($vet['dias_semana'], '5') || $vet['dias_semana'] == '5') ? 'checked' : ''?>>&nbsp;SEXTA-FEIRA<br>
                                <input type="checkbox" name="dias_semana[]" id="sabado" value="6" <?=(strstr($vet['dias_semana'], '6') || $vet['dias_semana'] == '6') ? 'checked' : ''?>>&nbsp;SÁBADO
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-12"> 
                                <?
                                if($ind == 1)
                                {
                                ?>
                                <script>preencher_checkboxes()</script>
                                <button type="submit" class="btn btn-primary" onClick="javascript: valida(1);">Cadastrar</button>
                                <?
                                }
                                else
                                {
                                ?>
                                <button type="submit" class="btn btn-primary" onClick="javascript: valida(2);">Alterar</button>
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
    $str = "SELECT * FROM agendamentos_disponiveis WHERE idempresa = '$adm_empresa' ORDER BY codigo";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    
    if($num > 0)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Lista de agendamentos externos cadastrados no sistema</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Data inicial</th>
                                    <th>Data final</th>
                                    <th>Horário inicial - final</th>
                                    <th>Intervalo (minutos)</th>
                                    <th>Dias da semana</th>
                                    <th>Agendamentos</th>
                                    <th style="width: 12.5%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                    $idagendamento = $vet['codigo'];

                                    $dias_semana = '';
                                    if(strstr($vet['dias_semana'], '0') || $vet['dias_semana'] == '0')
                                        $dias_semana .= 'Domingo<br>';
                                    if(strstr($vet['dias_semana'], '1') || $vet['dias_semana'] == '1')
                                        $dias_semana .= 'Segunda<br>';
                                    if(strstr($vet['dias_semana'], '2') || $vet['dias_semana'] == '2')
                                        $dias_semana .= 'Terça<br>';
                                    if(strstr($vet['dias_semana'], '3') || $vet['dias_semana'] == '3')
                                        $dias_semana .= 'Quarta<br>';
                                    if(strstr($vet['dias_semana'], '4') || $vet['dias_semana'] == '4')
                                        $dias_semana .= 'Quinta<br>';
                                    if(strstr($vet['dias_semana'], '5') || $vet['dias_semana'] == '5')
                                        $dias_semana .= 'Sexta<br>';
                                    if(strstr($vet['dias_semana'], '6') || $vet['dias_semana'] == '6')
                                        $dias_semana .= 'Sábado<br>';

                                    $strM = "SELECT * FROM prospeccao WHERE idempresa = '$adm_empresa' AND auto = '1' AND data_agendamento BETWEEN '".$vet['data_inicial']."' AND '".$vet['data_final']."' ORDER BY data";
                                    $rsM  = mysqli_query($conexao, $strM) or die(mysqli_error($conexao));
                                    $numM = mysqli_num_rows($rsM);
                                ?>
                                <tr class="gradeX">
                                    <td><?=ConverteData($vet['data_inicial'])?></td>
                                    <td><?=ConverteData($vet['data_final'])?></td>
                                    <td><?=$vet['periodo']?></td>
                                    <td><?=$vet['intervalo']?></td>
                                    <td><?=$dias_semana?></td>
                                    <td><?=$numM?></td>
                                    <td class="center">
                                        <a class="btn btn-primary btn-circle" type="button" title="horários indisponíveis" href="agendamentos_n_disponiveis.php?idagendamento=<?=$vet['codigo']?>" target="_blank"><i class="fa fa-times"></i></a>
                                        <?
                                        if(!$numM)
                                        {
                                        ?>
                                        <a class="btn btn-warning btn-circle" type="button" title="editar" href="agendamentos_disponiveis.php?ind=2&codigo=<?=$vet['codigo']?>"><i class="fa fa-edit"></i></a>
                                        <?
                                        }
                                        ?>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="agendamentos_disponiveis.php?cmd=del&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a> 
                                    </td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Data inicial</th>
                                    <th>Data final</th>
                                    <th>Horário inicial - final</th>
                                    <th>Intervalo (minutos)</th>
                                    <th>Dias da semana</th>
                                    <th>Agendamentos</th>
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
                Nenhum autoagendamento encontrado no sistema.
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