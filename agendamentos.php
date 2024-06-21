<?
$menu = 'agenda';
$page = 'agendamentos';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_agenda != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE) 
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

if($_GET['idoptometrista'] == TRUE) 
    $idoptometrista = anti_injection($_GET['idoptometrista']); 
else 
    $idoptometrista = anti_injection($_POST['idoptometrista']);

if($adm_perfil == 2 || $adm_perfil == 5)
    $idoptometrista = $adm_codigo;

$idprocedimento = $_POST['idprocedimento'];
$idpaciente = $_REQUEST['idpaciente'];
$idotica = $_POST['idotica'];
$data = ConverteData($_POST['data']);
$hora_inicial = $_POST['hora_inicial'];
$hora_final = $_POST['hora_final'];
$observacao = addslashes($_POST['observacao']);

if($_POST['data_inicial'])
    $data_inicial = date("Y-m-d H:i", mktime(substr($_POST['data_inicial'], 11, 2), substr($_POST['data_inicial'], 14, 2), 0, substr($_POST['data_inicial'], 3, 2), substr($_POST['data_inicial'], 0, 2), substr($_POST['data_inicial'], 6, 4)));

if($_POST['data_final'])
    $data_final = date("Y-m-d H:i", mktime(substr($_POST['data_final'], 11, 2), substr($_POST['data_final'], 14, 2), 0, substr($_POST['data_final'], 3, 2), substr($_POST['data_final'], 0, 2), substr($_POST['data_final'], 6, 4)));

//print_r($_POST);
//echo '<br>';

if($_POST['cmd'] == "add")
{
    $str = "INSERT INTO agendamentos (idempresa, idoptometrista, idprocedimento, idpaciente, idotica, data, hora_inicial, hora_final, observacao) 
        VALUES ('$adm_empresa', '$idoptometrista', '$idprocedimento', '$idpaciente', '$idotica', '$data', '$hora_inicial', '$hora_final', '$observacao')";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("agendamentos.php?ind_msg=1");
}

if($_POST['cmd'] == "edit")
{
    $str = "UPDATE agendamentos 
        SET idoptometrista = '$idoptometrista', idprocedimento = '$idprocedimento', idpaciente = '$idpaciente', idotica = '$idotica', data = '$data', 
        hora_inicial = '$hora_inicial', hora_final = '$hora_final', observacao = '$observacao' 
        WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("agendamentos.php?ind_msg=2");
}

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM agendamentos WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("agendamentos.php?ind_msg=3");
}

if($_POST['cmd'] == "edit_status")
{
    $idagendamento = $_POST['idagendamento'];
    $status = $_POST['status'];

    $str = "UPDATE agendamentos SET status = '$status' WHERE codigo = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("agendamentos.php?ind_msg=4");
}

if($_POST['cmd'] == "edit_pagto")
{
    $idagendamento = $_POST['idagendamento'];
    $status = $_POST['status'];
    $valor = str_replace(",", ".", str_replace(".", "", $_POST['valor']));
    $forma_pagto = $_POST['forma_pagto'];
    $parcelas = $_POST['parcelas'];

    $str = "UPDATE agendamentos SET valor = '$valor', forma_pagto = '$forma_pagto', parcelas = '$parcelas', data_pagto = NOW() WHERE codigo = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    if($status)
    {
        $str = "UPDATE agendamentos SET status = '$status' WHERE codigo = '$idagendamento'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    
    redireciona("agendamentos.php?ind_msg=5");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Agendamento cadastrado com sucesso';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Agendamento editado com sucesso!';
elseif($_GET['ind_msg'] == "3")
    $msg = 'Agendamento excluído com sucesso!';
elseif($_GET['ind_msg'] == "4")
    $msg = 'Status do agendamento alterado com sucesso!';
elseif($_GET['ind_msg'] == "5")
    $msg = 'Recebimento lançado com sucesso!';

$str = "SELECT nome FROM usuarios WHERE codigo = '$iduser'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);

$usuario = stripslashes($vet['nome']);

$str = "SELECT * FROM agendamentos WHERE codigo = '$codigo'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);
?>

<script language="javascript">
function valida(ind)
{   
    if(ind == 1)
        document.form_c.cmd.value = "add";
    
    if(ind == 2)
        document.form_c.cmd.value = "edit";

    if(ind == 3)
        document.form_c.cmd.value = "del";
}
</script>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Agendamentos</h2>
    </div>
</div>

<div class="wrapper wrapper-content">
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

            <?
            if($adm_tipo == 1)
            {
            ?>
            <div class="ibox float-e-margins">
                <div class="ibox-title">    
                    <h5><i>Clique na seta do lado direito para abrir ou ocultar o formulário de pesquisa</i></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-down"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content" <?=($idoptometrista) ? 'style="display: block;"' : 'style="display: none;"'?>>
                    <form method="post" class="form-horizontal" name="form" id="form" enctype="multipart/form-data">
                        <input type="hidden" name="cmd" value="search">  
                        <div class="row">  
                            <div class="col-xs-8">
                                <p class="font-bold">
                                    Especialista*
                                </p>
                                <select name="idoptometrista" id="idoptometrista" required data-placeholder="Selecione um especialista ..." class="chosen-select" tabindex="10" >
                                    <option value="">Selecione ...</option>
                                    <?
                                    $strC = "SELECT * FROM usuarios WHERE idempresa = '$adm_empresa' AND perfil IN ('2', '5') AND status = '1' AND codigo != '$idusuario' ORDER BY nome";
                                    $rsC  = mysqli_query($conexao, $strC) or die(mysqli_error($conexao));

                                    while($vetC = mysqli_fetch_array($rsC))
                                    {
                                    ?>
                                    <option value="<?=$vetC['codigo']?>" <?=($vetC['codigo'] == $idoptometrista) ? 'selected' : ''?>><?=stripslashes($vetC['nome'])?></option>
                                    <?
                                    }
                                    ?>
                                </select>
                            </div> 
                            <div class="col-xs-4"> 
                                <br>
                                <button type="submit" class="btn btn-primary" >Pesquisar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?
            }
            ?>
        </div>
    </div>

    <div class="row animated fadeInDown">
        <div class="col-lg-3">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?=($ind == 1) ? 'Cadastrar' : 'Editar'?> agendamento</h5>
                </div>
                <div class="ibox-content">
                    <p>
                        <h4><i>Legenda de cores</i></h4>
                        <?
                        $strP = "SELECT * FROM procedimentos WHERE idempresa = '$adm_empresa' ORDER BY descricao";
                        $rsP  = mysqli_query($conexao, $strP) or die(mysqli_error($conexao));

                        while($vetP = mysqli_fetch_array($rsP))
                        {
                        ?>
                        <span class="label" style="background-color: <?=$vetP['cor']?>; color: #fff"><?=stripslashes($vetP['descricao'])?></span>
                        <?
                        }
                        ?>
                    </p>
                    <br>
                    <div id='external-events'>
                        <form method="post" class="form-horizontal" name="form_c" id="form_c" enctype="multipart/form-data">
                            <input type="hidden" name="cmd">  
                            <input type="hidden" name="codigo" id="codigo" value="<?=$vet['codigo']?>">

                            <?
                            if($adm_perfil == 1 || $adm_perfil == 3)
                            {
                            ?>
                            <div class="row">  
                                <div class="col-xs-12">
                                    <p class="font-bold">
                                        Especialista*
                                    </p>
                                    <select name="idoptometrista" id="idoptometrista" required data-placeholder="Selecione um especialista ..." class="chosen-select" tabindex="10" >
                                        <option value="">Selecione ...</option>
                                        <?
                                        $strC = "SELECT * FROM usuarios WHERE idempresa = '$adm_empresa' AND perfil IN ('2', '5') AND status = '1' AND codigo != '$idusuario' ORDER BY nome";
                                        $rsC  = mysqli_query($conexao, $strC) or die(mysqli_error($conexao));

                                        while($vetC = mysqli_fetch_array($rsC))
                                        {
                                        ?>
                                        <option value="<?=$vetC['codigo']?>" <?=($vetC['codigo'] == $vet['idoptometrista']) ? 'selected' : ''?>><?=stripslashes($vetC['nome'])?></option>
                                        <?
                                        }
                                        ?>
                                    </select>
                                </div> 
                            </div>
                            <br>
                            <?
                            }
                            ?>
                            <div class="row">                            
                                <div class="col-md-12">
                                    <p class="font-bold">
                                        Procedimento*
                                    </p>
                                    <select name="idprocedimento" id="idprocedimento" required data-placeholder="Selecione um procedimento ..." class="chosen-select" tabindex="10" >
                                        <option value="">Selecione ...</option>
                                        <?
                                        $strC = "SELECT * FROM procedimentos WHERE idempresa = '$adm_empresa' ORDER BY descricao";
                                        $rsC  = mysqli_query($conexao, $strC) or die(mysqli_error($conexao));

                                        while($vetC = mysqli_fetch_array($rsC))
                                        {
                                        ?>
                                        <option value="<?=$vetC['codigo']?>" <?=($vetC['codigo'] == $vet['idprocedimento']) ? 'selected' : ''?>><?=stripslashes($vetC['descricao'])?></option>
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
                                        Paciente*
                                    </p>
                                    <select name="idpaciente" id="idpaciente" required data-placeholder="Selecione um paciente ..." class="chosen-select" tabindex="10" >
                                        <option value="">Selecione ...</option>
                                        <?
                                        $strC = "SELECT * FROM pacientes WHERE idempresa = '$adm_empresa' ORDER BY nome";
                                        $rsC  = mysqli_query($conexao, $strC) or die(mysqli_error($conexao));

                                        while($vetC = mysqli_fetch_array($rsC))
                                        {
                                        ?>
                                        <option value="<?=$vetC['codigo']?>" <?=($vetC['codigo'] == $vet['idpaciente'] || $vetC['codigo'] == $idpaciente) ? 'selected' : ''?>><?=stripslashes($vetC['nome'])?></option>
                                        <?
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="row">                            
                                <div class="col-xs-12">
                                    <p class="font-bold">
                                        Data
                                    </p>
                                    <div class="form-group" id="data_1">
                                        <div class="input-group date" style="margin-left: 15px;">
                                            <span class="input-group-addon" ><i class="fa fa-calendar"></i></span><input style="width: 90%;" type="text" class="form-control" name="data" id="data" value="<?=($vet['data']) ? ConverteData($vet['data']) : ''?>" maxlength="10" data-mask="99/99/9999" onKeyUp="javascript: return auto_data('data');" onKeyPress="javascript: return somenteNumeros(event);">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">                            
                                <div class="col-xs-12">
                                    <p class="font-bold">
                                        Horário
                                    </p>
                                    <input class="form-control" type="text" name="hora_inicial" id="hora_inicial" value="<?=substr($vet['hora_inicial'], 0, -3)?>" maxlength="5" data-mask="99:99" onKeyUp="javascript: return auto_hora('hora_inicial');" onKeyPress="javascript: return somenteNumeros(event);" style="width: 45%; margin-right: 10px; float: left" />
                                    <input class="form-control" type="text" name="hora_final" id="hora_final" value="<?=substr($vet['hora_final'], 0, -3)?>" maxlength="5" data-mask="99:99" onKeyUp="javascript: return auto_hora('hora_final');" onKeyPress="javascript: return somenteNumeros(event);" style="width: 45%" />
                                </div>
                            </div>
                            <br>
                            <div class="row">                            
                                <div class="col-md-12">
                                    <p class="font-bold">
                                        Convênio
                                    </p>
                                    <select name="idotica" id="idotica" data-placeholder="Selecione um convênio ..." class="chosen-select" tabindex="10" >
                                        <option value="">Selecione ...</option>
                                        <?
                                        $strC = "SELECT * FROM oticas WHERE idempresa = '$adm_empresa' ORDER BY nome";
                                        $rsC  = mysqli_query($conexao, $strC) or die(mysqli_error($conexao));

                                        while($vetC = mysqli_fetch_array($rsC))
                                        {
                                        ?>
                                        <option value="<?=$vetC['codigo']?>" <?=($vetC['codigo'] == $vet['idotica']) ? 'selected' : ''?>><?=stripslashes($vetC['nome'])?></option>
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
                                        Observação
                                    </p>
                                    <textarea class="form-control" name="observacao" id="observacao"><?=nl2br(stripslashes($vet['observacao']))?></textarea>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <?
                                if($ind == 1)
                                {
                                ?>
                                <div class="col-xs-12"> 
                                    <button type="submit" class="btn btn-primary" onClick="javascript: valida(1);">Cadastrar</button>
                                </div>
                                <?
                                }
                                else
                                {
                                ?>
                                <div class="col-xs-6"> 
                                    <button type="submit" class="btn btn-primary" onClick="javascript: valida(2);">Alterar</button>
                                </div>
                                <?
                                }
                                ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Agenda <?=($iduser) ? 'de <b>'.$usuario.'</b>' : ''?></h5>
                </div>
                <div class="ibox-content">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?
include("includes/footer.php");
?>