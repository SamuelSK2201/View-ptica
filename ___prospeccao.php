<?
$page = 'prospeccao';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_prospeccao != 1 && $ind_pesquisa == 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE)
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

$idpaciente = anti_injection($_REQUEST['idpaciente']);
$idagendamento = anti_injection($_REQUEST['idagendamento']);

$nome = addslashes($_POST['nome']);
$sobrenome = addslashes($_POST['sobrenome']);
$data_nascimento = $_POST['data_nascimento'];
$data_exame = $_POST['data_exame'];
$porque = addslashes($_POST['porque']);
$data_agendamento = ConverteData($_POST['data_agendamento']);
$hora_inicial = $_POST['hora_inicial'];
$hora_final = $_POST['hora_final'];
$telefone = $_POST['telefone'];
$telefone_2 = $_POST['telefone_2'];
$status = $_POST['status'];
$array_p = $_POST['pergunta'];
$array_r = $_POST['resposta'];

if($_POST['cmd'] == "add")
{   
    $nome_completo = $nome.' '.$sobrenome;

    $str = "SELECT * FROM pacientes WHERE idempresa = '$adm_empresa' AND nome = '$nome_completo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if(!$num)
    {
        $str = "INSERT INTO pacientes (idempresa, nome, telefone, telefone2, data_cadastro) VALUES ('$adm_empresa', '$nome', '$telefone', '$telefone_2', CURDATE())";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        $idpaciente = mysqli_insert_id($conexao);
    }
    else
    {
        $vet = mysqli_fetch_array($rs);
        $idpaciente = $vet['codigo'];
    }

    $str = "INSERT INTO agendamentos (idempresa, idpaciente, data, hora_inicial, hora_final, status) 
        VALUES ('$adm_empresa', '$idpaciente', '$data_agendamento', '$hora_inicial', '$hora_final', '7')";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $idagendamento = mysqli_insert_id($conexao);
    
    $str = "INSERT INTO prospeccao (idempresa, idusuario, idagendamento, idpaciente, nome, sobrenome, data_nascimento, data_exame, porque, data_agendamento, hora_inicial, hora_final, telefone, telefone_2, status, data) 
        VALUES ('$adm_empresa', '$adm_codigo', '$idagendamento', '$idpaciente', '$nome', '$sobrenome', '$data_nascimento', '$data_exame', '$porque', '$data_agendamento', '$hora_inicial', '$hora_final', '$telefone', '$telefone_2', '$status', NOW())";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $idprospeccao = mysqli_insert_id($conexao);

    for($i = 0; $i < @count($array_p); $i++)
    {
        $pergunta = addslashes($array_p[$i]);
        $resposta = addslashes($array_r[$i]);

        $str = "INSERT INTO prospeccao_respostas (idprospeccao, pergunta, resposta) VALUES ('$idprospeccao', '$pergunta', '$resposta')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    redireciona("prospeccao.php?ind_msg=1");
}

if($_POST['cmd'] == "edit")
{
    $nome_completo = $nome.' '.$sobrenome;

    $str = "UPDATE pacientes SET nome = '$nome_completo', telefone = '$telefone', telefone2 = '$telefone_2' WHERE codigo = '$idpaciente'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "UPDATE agendamentos SET data = '$data_agendamento', hora_inicial = '$hora_inicial', hora_final = '$hora_final' WHERE codigo = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "UPDATE prospeccao 
        SET nome = '$nome', sobrenome = '$sobrenome', data_nascimento = '$data_nascimento', data_exame = '$data_exame', porque = '$porque', 
        data_agendamento = '$data_agendamento', hora_inicial = '$hora_inicial', hora_final = '$hora_final', telefone = '$telefone', telefone_2 = '$telefone_2',
        status = '$status'
        WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prospeccao_respostas WHERE idprospeccao = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    for($i = 0; $i < @count($array_p); $i++)
    {
        $pergunta = addslashes($array_p[$i]);
        $resposta = addslashes($array_r[$i]);

        $str = "INSERT INTO prospeccao_respostas (idprospeccao, pergunta, resposta) VALUES ('$codigo', '$pergunta', '$resposta')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    redireciona("prospeccao.php?ind_msg=2");
}

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM prospeccao WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prospeccao_respostas WHERE idprospeccao = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM agendamentos WHERE codigo = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM pacientes WHERE codigo = '$idpaciente'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("prospeccao.php?ind_msg=3");
}

if($_POST['cmd'] == "edit_fila")
{
    $idagendamento = $_POST['idagendamento'];
    $idoptometrista = $_POST['idoptometrista'];
    $valor = str_replace(",", ".", str_replace(".", "", $_POST['valor']));
    $forma_pagto = $_POST['forma_pagto'];
    $parcelas = $_POST['parcelas'];

    $str = "UPDATE agendamentos SET idoptometrista = '$idoptometrista', valor = '$valor', forma_pagto = '$forma_pagto', parcelas = '$parcelas', data_pagto = NOW() WHERE codigo = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("prospeccao.php?ind_msg=4");
}

if($_GET['cmd'] == "fila")
{
    $str = "UPDATE prospeccao SET status = '3' WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "UPDATE agendamentos SET status = '4' WHERE codigo = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("prospeccao.php?ind_msg=5");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Prospecção cadastrada com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Prospecção editada com sucesso!';
elseif($_GET['ind_msg'] == "3")
    $msg = 'Prospecção excluída com sucesso!';
elseif($_GET['ind_msg'] == "4")
    $msg = 'Dados salvos com sucesso!';
elseif($_GET['ind_msg'] == "5")
    $msg = 'Status alterado para FILA DE ESPERA  com sucesso!';

$str = "SELECT * FROM prospeccao WHERE codigo = '$codigo'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);

$title = "'View Optica<br>Pesquisa de prospecção'";
$columns = '0, 1';
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
        <h2>Pesquisa de prospecção</h2>
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
                    <h5><i>Clique na seta do lado direito para abrir ou ocultar o formulário</i></h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-down"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content" <?=($ind == 2) ? 'style="display: block;"' : 'style="display: none;"'?>>
                    <form method="post" class="form-horizontal" name="form" id="form" enctype="multipart/form-data">
                        <input type="hidden" name="cmd">  
                        <input type="hidden" name="codigo" id="codigo" value="<?=$vet['codigo']?>">
                        <input type="hidden" name="idpaciente" id="idpaciente" value="<?=$vet['idpaciente']?>">
                        <input type="hidden" name="idagendamento" id="idagendamento" value="<?=$vet['idagendamento']?>">

                        <div class="row">
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Nome*
                                </p>
                                <input type="text" name="nome" id="nome" class="form-control" value="<?=stripslashes($vet['nome'])?>" required>
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Sobrenome
                                </p>
                                <input type="text" name="sobrenome" id="sobrenome" class="form-control" value="<?=stripslashes($vet['sobrenome'])?>" >
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Data de nascimento (idade)*
                                </p>
                                <input type="text" name="data_nascimento" id="data_nascimento" class="form-control" value="<?=$vet['data_nascimento']?>" required>
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Último exame*
                                </p>
                                <input type="text" name="data_exame" id="data_exame" class="form-control" value="<?=$vet['data_exame']?>" required>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="font-bold">
                                    Porquê*
                                </p>
                                <input type="text" name="porque" id="porque" class="form-control" value="<?=stripslashes($vet['porque'])?>" required>
                            </div>                            
                        </div>
                        <br>
                        <?
                        if($ind == 1)
                        {
                            $strP = "SELECT * FROM prospeccao_perguntas WHERE status = '1' ORDER BY codigo";
                            $rsP  = mysqli_query($conexao, $strP) or die(mysqli_error($conexao));
                            $numP = mysqli_num_rows($rsP);
                            
                            if($numP > 0)
                            {
                                echo '<div class="row">';
                                $i = 0;

                                while($vetP = mysqli_fetch_array($rsP))
                                {
                                    if($i == 2)
                                    {
                                        echo '</div><br><div class="row">';
                                        $i = 0;
                                    }

                                    $i++;

                                    ?>
                                    <div class="col-md-6">
                                        <p class="font-bold">
                                            <?=stripslashes($vetP['pergunta'])?>
                                        </p>
                                        <input type="hidden" name="pergunta[]" id="pergunta" value="<?=stripslashes($vetP['pergunta'])?>">
                                        <select class="form-control" name="resposta[]" id="resposta" <?=($perm_pesquisa_obrigatoriedade == 1) ? 'required' : ''?>>
                                            <option value="">Selecione ...</option>
                                            <option value="1" >Sim</option>
                                            <option value="2" >Não</option>
                                        </select>
                                    </div>
                                    <?
                                }

                                echo '</div><br>';
                            }
                        }
                        else
                        {
                            $strP = "SELECT * FROM prospeccao_respostas WHERE idprospeccao = '$codigo' ORDER BY codigo";
                            $rsP  = mysqli_query($conexao, $strP) or die(mysqli_error($conexao));
                            $numP = mysqli_num_rows($rsP);
                            
                            if($numP > 0)
                            {
                                echo '<div class="row">';
                                $i = 0;

                                while($vetP = mysqli_fetch_array($rsP))
                                {
                                    if($i == 2)
                                    {
                                        echo '</div><br><div class="row">';
                                        $i = 0;
                                    }

                                    $i++;

                                    ?>
                                    <div class="col-md-6">
                                        <p class="font-bold">
                                            <?=stripslashes($vetP['pergunta'])?>
                                        </p>
                                        <input type="hidden" name="pergunta[]" id="pergunta" value="<?=stripslashes($vetP['pergunta'])?>">
                                        <select class="form-control" name="resposta[]" id="resposta" <?=($perm_pesquisa_obrigatoriedade == 1) ? 'required' : ''?>>
                                            <option value="">Selecione ...</option>
                                            <option value="1" <?=(1 == $vetP['resposta']) ? 'selected' : ''?>>Sim</option>
                                            <option value="2" <?=(2 == $vetP['resposta']) ? 'selected' : ''?>>Não</option>
                                        </select>
                                    </div>
                                    <?
                                }

                                echo '</div><br>';
                            }
                        }
                        ?>
                        <div class="row">
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Agendar para
                                </p>
                                <div class="form-group" id="data_1">
                                    <div class="input-group date" style="margin-left: 15px;">
                                        <span class="input-group-addon" ><i class="fa fa-calendar"></i></span><input style="width: 90%;" type="text" class="form-control" name="data_agendamento" id="data_agendamento" value="<?=($vet['data_agendamento']) ? ConverteData($vet['data_agendamento']) : ''?>" maxlength="10" data-mask="99/99/9999" onKeyUp="javascript: return auto_data('data_agendamento');" onKeyPress="javascript: return somenteNumeros(event);">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Horário
                                </p>
                                <input class="form-control" type="text" name="hora_inicial" id="hora_inicial" value="<?=substr($vet['hora_inicial'], 0, -3)?>" maxlength="5" data-mask="99:99" onKeyUp="javascript: return auto_hora('hora_inicial');" onKeyPress="javascript: return somenteNumeros(event);" style="width: 45%; margin-right: 10px; float: left" />
                                <input class="form-control" type="text" name="hora_final" id="hora_final" value="<?=substr($vet['hora_final'], 0, -3)?>" maxlength="5" data-mask="99:99" onKeyUp="javascript: return auto_hora('hora_final');" onKeyPress="javascript: return somenteNumeros(event);" style="width: 45%" />
                            </div>
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Tel. 1
                                </p>
                                <input class="form-control" type="text" name="telefone" id="telefone" value="<?=$vet['telefone']?>" maxlength="15" onKeyUp="javascript: mascara(this, mtel);" onKeyPress="javascript: return somenteNumeros(event);" >
                            </div> 
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Tel. 2
                                </p>
                                <input class="form-control" type="text" name="telefone_2" id="telefone_2" value="<?=$vet['telefone_2']?>" maxlength="15" onKeyUp="javascript: mascara(this, mtel);" onKeyPress="javascript: return somenteNumeros(event);" >
                            </div> 
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Enviar para*
                                </p>
                                <?
                                if($ind == 1)
                                {
                                ?>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="">Selecione ...</option>
                                    <option value="6" <?=(6 == $vet['status']) ? 'selected' : ''?>>Pesquisado</option>
                                    <option value="1" <?=(1 == $vet['status']) ? 'selected' : ''?>>Agendado</option>
                                </select>
                                <?
                                }
                                else
                                {
                                ?>
                                <select class="form-control" name="status" id="status" readonly>
                                    <option value="">Selecione ...</option>
                                    <?
                                    if(1 == $vet['status'])
                                    {
                                    ?>
                                    <option value="1" <?=(1 == $vet['status']) ? 'selected' : ''?>>Agendado</option>
                                    <?
                                    }
                                    elseif(2 == $vet['status'])
                                    {
                                    ?>
                                    <option value="2" <?=(2 == $vet['status']) ? 'selected' : ''?>>Confirmado</option>
                                    <?
                                    }
                                    elseif(3 == $vet['status'])
                                    {
                                    ?>
                                    <option value="3" <?=(3 == $vet['status']) ? 'selected' : ''?>>Fila de espera</option>
                                    <?
                                    }
                                    elseif(4 == $vet['status'])
                                    {
                                    ?>
                                    <option value="4" <?=(4 == $vet['status']) ? 'selected' : ''?>>Reservado</option>
                                    <?
                                    }
                                    elseif(5 == $vet['status'])
                                    {
                                    ?>
                                    <option value="5" <?=(5 == $vet['status']) ? 'selected' : ''?>>Finalizado</option>
                                    <?
                                    }
                                    elseif(6 == $vet['status'])
                                    {
                                    ?>
                                    <option value="6" <?=(6 == $vet['status']) ? 'selected' : ''?>>Pesquisado</option>
                                    <?
                                    }
                                    elseif(7 == $vet['status'])
                                    {
                                    ?>
                                    <option value="7" <?=(7 == $vet['status']) ? 'selected' : ''?>>Reagendar</option>
                                    <?
                                    }
                                    elseif(8 == $vet['status'])
                                    {
                                    ?>
                                    <option value="8" <?=(8 == $vet['status']) ? 'selected' : ''?>>Atendido</option>
                                    <?
                                    }
                                    ?>
                                </select>
                                <?
                                }
                                ?>
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
    //STATUS: Aguardando (1), confirmado (2), fila de espera (3), reagendar (7) e atendido (8)
    $str = "SELECT * FROM prospeccao WHERE idempresa = '$adm_empresa' AND status IN ('1','2','3','7','8') $strWherePesq ORDER BY nome";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    
    if($num > 0)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Lista de propecções cadastradas no sistema</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" style="font-size: 11px;" >
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Sobrenome</th>
                                    <th style="width: 10%">Data de nascimento (idade)</th>
                                    <th>Último exame</th>
                                    <th style="width: 10%">Agendar para</th>
                                    <th>Horário</th>
                                    <th>Telefones</th>
                                    <th>Status atual</th>
                                    <th style="width: 12.5%">Modificar status</th>
                                    <th style="width: 12.5%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                    $codigo = $vet['codigo'];
                                    
                                    if($vet['status'] == 1)
                                        $status = '<span class="label label-warning">Aguardando</span>';
                                    elseif($vet['status'] == 2)
                                        $status = '<span class="label label-primary">Confirmado</span>';
                                    elseif($vet['status'] == 3)
                                        $status = '<span class="label label-success">Fila de espera</span>';
                                    elseif($vet['status'] == 8)
                                        $status = '<span class="label label-default" style="background-color: #9F81F7; color:#fff">Atendido</span>';
                                    elseif($vet['status'] == 6)
                                        $status = '<span class="label label-default">Pesquisado</span>';
                                    elseif($vet['status'] == 7)
                                        $status = '<span class="label label-warning">Reagendar</span>';
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['nome'])?></td>
                                    <td><?=stripslashes($vet['sobrenome'])?></td>
                                    <td><?=$vet['data_nascimento']?></td>
                                    <td><?=$vet['data_exame']?></td>
                                    <td><?=ConverteData($vet['data_agendamento'])?></td>
                                    <td><?=substr($vet['hora_inicial'], 0, -3)?> - <?=substr($vet['hora_final'], 0, -3)?></td>
                                    <td>
                                        Tel. 1: <?=($vet['telefone']) ? '<a href="https://api.whatsapp.com/send?phone=55'.preg_replace('/[^\d]/', '', $vet['telefone']).'" target="_blank">'.$vet['telefone'].'</a>' : 'Não informado'?><br>
                                        Tel. 2: <?=($vet['telefone']) ? '<a href="https://api.whatsapp.com/send?phone=55'.preg_replace('/[^\d]/', '', $vet['telefone2']).'" target="_blank">'.$vet['telefone2'].'</a>' : 'Não informado'?>
                                    </td>
                                    <td><div class="status_<?=$codigo?>"><?=$status?></div></td>
                                    <td class="center">
                                        <div class="prospeccao_<?=$codigo?>" style="float: left; margin-right: 2px;">
                                            <?
                                            if($vet['status'] == 1 || $vet['status'] == 7)
                                            {
                                                if($vet['status'] == 7 && $adm_perfil == 4)
                                                {
                                            ?>
                                            <a class="btn btn-primary btn-circle" type="button" title="transformar em AGUARDANDO" onclick="javascript: altera_status_prospeccao('<?=$codigo?>', '<?=$vet['idagendamento']?>', 1)"><i class='fa fa-check-square'></i></a>
                                            <?
                                                }
                                            ?>
                                            <a class="btn btn-info btn-circle" type="button" title="transformar em CONFIRMADO" onclick="javascript: altera_status_prospeccao('<?=$codigo?>', '<?=$vet['idagendamento']?>', 2)"><i class='fa fa-check-square'></i></a>
                                            <?
                                            }
                                            elseif($vet['status'] == 2 && $adm_perfil != 4)
                                            {
                                            ?>
                                            <a class="btn btn-default btn-circle" type="button" title="desfazer CONFIRMADO" onclick="javascript: altera_status_prospeccao('<?=$codigo?>', '<?=$vet['idagendamento']?>', 7)"><i class='fa fa-reply'></i></a>
                                            <a class="btn btn-info btn-circle" type="button" title="transformar em FILA DE ESPERA" href="prospeccao.php?cmd=fila&codigo=<?=$vet['codigo']?>&idagendamento=<?=$vet['idagendamento']?>"><i class='fa fa-check-square'></i></a>
                                            <?
                                            }
                                            elseif($vet['status'] == 3 && $adm_perfil != 4)
                                            {
                                            ?>
                                            <a class="btn btn-default btn-circle" type="button" title="desfazer FILA DE ESPERA" onclick="javascript: altera_status_prospeccao('<?=$codigo?>', '<?=$vet['idagendamento']?>', 2)"><i class='fa fa-reply'></i></a>
                                            <a class="btn btn-info btn-circle" type="button" title="transformar em ATENDIDO" onclick="javascript: altera_status_prospeccao('<?=$codigo?>', '<?=$vet['idagendamento']?>', 8)"><i class='fa fa-check-square'></i></a>
                                            <a class="btn btn-default btn-circle" style="background-color: #F3F781;" type="button" title="Detalhes do agendamento" data-toggle="modal" data-target="#fila_<?=$vet['codigo']?>"><i class="fa fa-list"></i></a>
                                            <?
                                            }
                                            elseif($vet['status'] == 8 && $adm_perfil != 4)
                                            {
                                            ?>
                                            <a class="btn btn-default btn-circle" type="button" title="desfazer ATENDIDO" onclick="javascript: altera_status_prospeccao('<?=$codigo?>', '<?=$vet['idagendamento']?>', 3)"><i class='fa fa-reply'></i></a>
                                            <a class="btn btn-info btn-circle" type="button" title="transformar em RESERVADO" onclick="javascript: altera_status_prospeccao('<?=$codigo?>', '<?=$vet['idagendamento']?>', 4)"><i class='fa fa-check-square'></i></a>
                                            <a class="btn btn-primary btn-circle" type="button" title="transformar em FINALIZADO" onclick="javascript: altera_status_prospeccao('<?=$codigo?>', '<?=$vet['idagendamento']?>', 5)"><i class='fa fa-check-square'></i></a>
                                            <?
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <?
                                        if($vet['status'] != 6 && $adm_perfil != 4)
                                        {
                                        ?>
                                        <a class="btn btn-default btn-circle" type="button" title="editar dados paciente" href="pacientes.php?ind=2&codigo=<?=$vet['idpaciente']?>"><i class="fa fa-user"></i></a>
                                        <?
                                        }
                                        ?>
                                        <a class="btn btn-warning btn-circle" type="button" title="editar / visualizar" href="prospeccao.php?ind=2&codigo=<?=$vet['codigo']?>"><i class="fa fa-edit"></i></a>
                                        <?
                                        if($adm_perfil != 4)
                                        {
                                        ?>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="prospeccao.php?cmd=del&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a> 
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
                                    <th>Sobrenome</th>
                                    <th>Data de nascimento (idade)</th>
                                    <th>Último exame</th>
                                    <th>Agendar para</th>
                                    <th>Horário</th>
                                    <th>Telefones</th>
                                    <th>Status atual</th>
                                    <th>Modificar status</th>
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
                Nenhuma prospecção encontrada no sistema.
            </p>
        </div>
    </div>
    <?
    }
    ?>
</div>

<script>
function exibe_parcelas(value, idagendamento)
{
    if(value == 1)
    {
        document.getElementById('div_parcelas_'+idagendamento).style.display = 'none';
        document.getElementById('parcelas_'+idagendamento).value = '';
        document.getElementById('parcelas_'+idagendamento).required = false;
    }
    else
    {
        document.getElementById('div_parcelas_'+idagendamento).style.display = 'block';
        document.getElementById('parcelas_'+idagendamento).value = '';
        document.getElementById('parcelas_'+idagendamento).required = true;
    }
}
</script>
<?
$str = "SELECT B.*, A.codigo AS idprospeccao 
    FROM prospeccao A
    INNER JOIN agendamentos B ON A.idagendamento = B.codigo 
    WHERE A.idempresa = '$adm_empresa' 
    AND A.status = '3' 
    $strWherePesq 
    ORDER BY A.nome";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$num = mysqli_num_rows($rs);

if($num > 0)
{
    while($vet = mysqli_fetch_array($rs))
    {
        $idagendamento = $vet['codigo'];
        $idpaciente = $vet['idpaciente'];
        $idoptometrista = $vet['idoptometrista'];

        $data = explode("-", $vet['data']);
        $data = $data[2] . "/" . $data[1] . "/" .$data[0];

        $hora_inicial = explode(":", $vet['hora_inicial']);
        $hora_inicial = $hora_inicial[0] . ":" . $hora_inicial[1];

        $hora_final = explode(":", $vet['hora_final']);
        $hora_final = $hora_final[0] . ":" . $hora_final[1];

        $strP = "SELECT * FROM pacientes WHERE codigo = $idpaciente LIMIT 1";
        $rsP = mysqli_query($conexao, $strP) or die(mysqli_error($conexao));
        $numP = mysqli_num_rows($rsP);
        $vetP = mysqli_fetch_array($rsP);

        $data_nascimento = explode("-", $vetP['data_nascimento']);
        $data_nascimento = $data_nascimento[2] . "/" . $data_nascimento[1] . "/" .$data_nascimento[0];
        ?>
        <div class="modal inmodal fade" id="fila_<?=$vet['idprospeccao']?>" tabindex="-1" role="dialog"  aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
                        <h3>Detalhes do agendamento</h3>
                        <p>
                            <b>Nome do Paciente:</b> <?=$vetP['nome'];?><br>
                            <b>Data de Nascimento:</b> <?=$data_nascimento;?><br>
                            <b>Agendado para:</b> <?=$data;?> das <?=$hora_inicial;?> às <?=$hora_final;?>                                            
                        </p>
                    </div>                                        
                    <div class="modal-body">
                        <div class="ibox float-e-margins">
                            <div class="ibox-content">
                                <form method="post" class="form-inline" name="form_a" id="form_a" enctype="multipart/form-data"> 
                                    <input type="hidden" name="cmd" value="edit_fila">
                                    <input type="hidden" name="idagendamento" value="<?=$idagendamento;?>">                                                

                                    <div class="row">
                                        <div class="col-md-6">
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
                                        <div class="col-md-6">
                                            <p class="font-bold">
                                                Valor da consulta*
                                            </p>
                                            <input class="form-control" type="text" name="valor" id="valor_<?=$vet['idprospeccao']?>" value="<?=number_format($vet['valor'], 2, ',', '.')?>" required onKeyUp="javascript: return auto_valor('valor_<?=$vet['idprospeccao']?>');" onKeyPress="javascript: return somenteNumeros(event);">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="font-bold">
                                                Forma de pagto*
                                            </p>
                                            <select class="form-control" name="forma_pagto" id="forma_pagto" required onchange="javascript: exibe_parcelas(this.value, <?=$idagendamento?>)">
                                                <option value="" <?=(!$vet['forma_pagto']) ? 'selected' : ''?>>Selecione ...</option>
                                                <option value="1" <?=($vet['forma_pagto'] == 1) ? 'selected' : ''?>>Dinheiro</option>
                                                <option value="2" <?=($vet['forma_pagto'] == 2) ? 'selected' : ''?>>Cartão</option>
                                                <option value="3" <?=($vet['forma_pagto'] == 3) ? 'selected' : ''?>>Dinheiro / Cartão</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6" id="div_parcelas_<?=$idagendamento?>" <?=($vet['forma_pagto'] == 1) ? 'style="display: none"' : ''?>>
                                            <p class="font-bold">
                                                Parcelas*
                                            </p>
                                            <input class="form-control" type="number" name="parcelas" id="parcelas_<?=$idagendamento?>" value="<?=$vet['parcelas']?>" required min="0" >
                                        </div>
                                    </div>
                                    <br>

                                    <div class="row">
                                        <div class="col-xs-12">
                                            <button type="submit" class="btn btn-primary">Salvar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
        <?
    }
}

include("includes/footer.php");
?>