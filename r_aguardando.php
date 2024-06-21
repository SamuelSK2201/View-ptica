<?
$menu = 'funil';
$page = 'r_aguardando';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_prospeccao != 1)
    die("Acesso negado!");

if($perm_relatorios != 1 && $perm_relatorios_aguardando != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE)
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

$idagendamento = anti_injection($_REQUEST['idagendamento']);
$idoptometrista = anti_injection($_REQUEST['idoptometrista']);

if($_POST['cmd'] == 'search')
{
    $_SESSION['r_aguardando']['data_inicial'] = $_POST['data_inicial'];
    $_SESSION['r_aguardando']['data_final'] = $_POST['data_final'];
}

$data_inicial = $_SESSION['r_aguardando']['data_inicial'];
$data_final = $_SESSION['r_aguardando']['data_final'];

$str = "SELECT A.*, B.rg, B.cpf, B.email, B.cep, B.numero, B.complemento, B.bairro, B.cidade, B.estado, C.idoptometrista, C.idprocedimento
    FROM prospeccao A
    INNER JOIN pacientes B ON A.idpaciente = B.codigo
    INNER JOIN agendamentos C ON A.idagendamento = C.codigo
    WHERE A.codigo = '$codigo'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);

if($_GET['cmd'] == "fila")
{
    $str = "UPDATE prospeccao SET status = '3' WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "UPDATE agendamentos SET status = '4' WHERE codigo = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("r_aguardando.php?ind_msg=3");
}

if($_POST['cmd'] == "edit")
{
    $idpaciente = anti_injection($_REQUEST['idpaciente']);
    $idagendamento = anti_injection($_REQUEST['idagendamento']);
    $nome = addslashes($_POST['nome']);
    $sobrenome = addslashes($_POST['sobrenome']);
    $data_nascimento = $_POST['data_nascimento'];
    $rg = $_POST['rg'];
    $cpf = $_POST['cpf'];
    $email = $_POST['email'];
    $cep = $_POST['cep'];
    $endereco = addslashes($_POST['endereco']);
    $numero = $_POST['numero'];
    $complemento = addslashes($_POST['complemento']);
    $bairro = addslashes($_POST['bairro']);
    $cidade = addslashes($_POST['cidade']);
    $estado = $_POST['estado'];
    $idprocedimento = $_POST['idprocedimento'];
    //$data_exame = $_POST['data_exame'];
    $porque = addslashes($_POST['porque']);
    $data_agendamento = ConverteData($_POST['data_agendamento']);
    $hora_inicial = $_POST['hora_inicial'];
    $hora_final = $_POST['hora_final'];
    $telefone = $_POST['telefone'];
    $telefone_2 = $_POST['telefone_2'];
    $status = $_POST['status'];
    $array_p = $_POST['pergunta'];
    $array_r = $_POST['resposta'];

    $nome_completo = $nome.' '.$sobrenome;

    $str = "UPDATE pacientes 
        SET nome = '$nome_completo', rg = '$rg', cpf = '$cpf', email = '$email', telefone = '$telefone', telefone2 = '$telefone_2', cep = '$cep', endereco = '$endereco', numero = '$numero', complemento = '$complemento', 
        bairro = '$bairro', cidade = '$cidade', estado = '$estado' 
        WHERE codigo = '$idpaciente'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    //echo '<br>';

    $strSet = "";
    if($status == 3)
        $strSet = ", status = '4'";

    $str = "UPDATE agendamentos SET idoptometrista = '$idoptometrista', idprocedimento = '$idprocedimento', data = '$data_agendamento', hora_inicial = '$hora_inicial', hora_final = '$hora_final' $strSet WHERE codigo = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "UPDATE prospeccao 
        SET idusuario = '$adm_codigo', nome = '$nome', sobrenome = '$sobrenome', data_nascimento = '$data_nascimento', data_exame = '$data_agendamento', porque = '$porque', 
        data_agendamento = '$data_agendamento', hora_inicial = '$hora_inicial', hora_final = '$hora_final', telefone = '$telefone', telefone_2 = '$telefone_2',
        status = '$status'
        WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    //echo '<br>';

    $str = "DELETE FROM prospeccao_respostas WHERE idprospeccao = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    //echo '<br>';

    for($i = 0; $i < @count($array_p); $i++)
    {
        $pergunta = addslashes($array_p[$i]);
        $resposta = addslashes($array_r[$i]);

        $str = "INSERT INTO prospeccao_respostas (idprospeccao, pergunta, resposta) VALUES ('$codigo', '$pergunta', '$resposta')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        //echo '<br>';
    }

    //die;

    redireciona("r_aguardando.php?ind_msg=2");
}

if($_GET['cmd'] == "del")
{
    $str = "DELETE FROM prospeccao WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prospeccao_respostas WHERE idprospeccao = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("r_aguardando.php?ind_msg=1");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Auto agendamento / Prospecção excluída com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Auto agendamento editado com sucesso!';
elseif($_GET['ind_msg'] == "3")
    $msg = 'Status alterado para FILA DE ESPERA com sucesso!';

$title = "'View Óptica<br>Aguardando'";
$columns = '0, 1, 2, 4, 5, 6';
$order = ',order: [[ 5, "desc" ]]';
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Aguardando</h2>
        <ol class="breadcrumb">
            <li class="active"><strong>Funil</strong></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight"> 
    <?
    if($ind == 2)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-content" >
                    <form method="post" class="form-horizontal" name="form" id="form" enctype="multipart/form-data">
                        <input type="hidden" name="cmd" value="edit">  
                        <input type="hidden" name="codigo" id="codigo" value="<?=$vet['codigo']?>">
                        <input type="hidden" name="idpaciente" id="idpaciente" value="<?=$vet['idpaciente']?>">
                        <input type="hidden" name="idagendamento" id="idagendamento" value="<?=$vet['idagendamento']?>">
                        <input type="hidden" name="hora_final" id="hora_final" value="<?=substr($vet['hora_final'], 0, -3)?>">

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
                                    <option value="<?=$vetC['codigo']?>" <?=($vetC['codigo'] == $vet['idoptometrista']) ? 'selected' : ''?>><?=stripslashes($vetC['nome'])?></option>
                                    <?
                                    }
                                    ?>
                                </select>
                            </div> 
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
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <p class="font-bold">
                                    CPF
                                </p>
                                <input class="form-control" type="text" name="cpf" id="cpf" value="<?=$vet['cpf']?>" maxlength="14" data-mask="999.999.999-99" onKeyUp="javascript: return auto_cpf('cpf');" onKeyPress="javascript: return somenteNumeros(event);">
                            </div>
                            <div class="col-md-4">
                                <p class="font-bold">
                                    RG
                                </p>
                                <input class="form-control" type="text" name="rg" id="rg" value="<?=$vet['rg']?>" >
                            </div>                            
                            <div class="col-md-4">
                                <p class="font-bold">
                                    Data de nascimento*
                                </p>
                                <input type="text" name="data_nascimento" id="data_nascimento" class="form-control" value="<?=$vet['data_nascimento']?>" required>
                            </div>
                        </div>
                        <br>
                        <div class="row">                            
                            <div class="col-md-2">
                                <p class="font-bold">
                                    CEP
                                </p>
                                <input class="form-control" type="text" name="cep" id="cep" value="<?=$vet['cep']?>" maxlength="9" data-mask="99999-999" onKeyUp="javascript: return auto_cep('cep');" onKeyPress="javascript: return somenteNumeros(event);">
                            </div>  
                            <div class="col-md-6">
                                <p class="font-bold">
                                    Endereço
                                </p>
                                <input class="form-control" type="text" name="endereco" id="endereco" value="<?=stripslashes($vet['endereco'])?>" >
                            </div>
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Número
                                </p>
                                <input class="form-control" type="text" name="numero" id="numero" value="<?=$vet['numero']?>" >
                            </div>
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Complemento
                                </p>
                                <input class="form-control" type="text" name="complemento" id="complemento" value="<?=stripslashes($vet['complemento'])?>" >
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-5">
                                <p class="font-bold">
                                    Bairro
                                </p>
                                <input class="form-control" type="text" name="bairro" id="bairro" value="<?=stripslashes($vet['bairro'])?>" >
                            </div>
                            <div class="col-md-5">
                                <p class="font-bold">
                                    Cidade
                                </p>
                                <input class="form-control" type="text" name="cidade" id="cidade" value="<?=stripslashes($vet['cidade'])?>" >
                            </div>
                            <div class="col-md-2">
                                <p class="font-bold">
                                    Estado
                                </p>
                                <input class="form-control" type="text" name="estado" id="estado" value="<?=$vet['estado']?>" >
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="font-bold">
                                    Email
                                </p>
                                <input class="form-control" type="email" name="email" id="email" value="<?=$vet['email']?>" >
                            </div>
                            <div class="col-md-6">
                                <p class="font-bold">
                                    Enviar para*
                                </p>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="1" <?=(1 == $vet['status']) ? 'selected' : ''?>>Agendado</option>
                                    <option value="2" <?=(2 == $vet['status']) ? 'selected' : ''?>>Confirmado</option>
                                    <option value="3" <?=(3 == $vet['status']) ? 'selected' : ''?>>Fila de espera</option>
                                    <option value="7" <?=(7 == $vet['status']) ? 'selected' : ''?>>Reagendar</option>
                                    <option value="8" <?=(8 == $vet['status']) ? 'selected' : ''?>>Atendido</option>
                                    <option value="4" <?=(4 == $vet['status']) ? 'selected' : ''?>>Reservado</option>
                                    <option value="5" <?=(5 == $vet['status']) ? 'selected' : ''?>>Finalizado</option>
                                </select>
                            </div>
                        </div>
                        <br>
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
                        <?                        
                        $strP = "SELECT * FROM disponiveis_respostas WHERE idprospeccao = '$codigo' ORDER BY codigo";
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
                                    <select class="form-control" name="resposta[]" id="resposta" >
                                        <option value="">Selecione ...</option>
                                        <option value="1" <?=(1 == $vetP['resposta']) ? 'selected' : ''?>>Sim</option>
                                        <option value="2" <?=(2 == $vetP['resposta']) ? 'selected' : ''?>>Não</option>
                                    </select>
                                </div>
                                <?
                            }

                            echo '</div><br>';
                        }
                        ?>
                        <div class="row">
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Agendar para
                                </p>
                                <div class="form-group" id="data_1">
                                    <div class="input-group date" style="margin-left: 15px;">
                                        <span class="input-group-addon" ><i class="fa fa-calendar"></i></span><input style="width: 90%;" type="text" class="form-control" name="data_agendamento" id="data_agendamento" value="<?=($vet['data_agendamento']) ? ConverteData($vet['data_agendamento']) : ''?>" maxlength="10" data-mask="99/99/9999" onKeyUp="javascript: return auto_data('data_agendamento');" onKeyPress="javascript: return somenteNumeros(event);">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Horário
                                </p>
                                <input class="form-control" type="text" name="hora_inicial" id="hora_inicial" value="<?=substr($vet['hora_inicial'], 0, -3)?>" maxlength="5" data-mask="99:99" onKeyUp="javascript: return auto_hora('hora_inicial');" onKeyPress="javascript: return somenteNumeros(event);">
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Tel. 1
                                </p>
                                <input class="form-control" type="text" name="telefone" id="telefone" value="<?=$vet['telefone']?>" maxlength="15" onKeyUp="javascript: mascara(this, mtel);" onKeyPress="javascript: return somenteNumeros(event);" >
                            </div> 
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Tel. 2
                                </p>
                                <input class="form-control" type="text" name="telefone_2" id="telefone_2" value="<?=$vet['telefone_2']?>" maxlength="15" onKeyUp="javascript: mascara(this, mtel);" onKeyPress="javascript: return somenteNumeros(event);" >
                            </div>                             
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-xs-12"> 
                                <button type="submit" class="btn btn-primary">Alterar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?
    }
    ?>

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
                <div class="ibox-content" style="display: none;">
                    <form method="post" class="form-horizontal" name="form_c" id="form_c" enctype="multipart/form-data">
                        <input type="hidden" name="cmd" value="search">                       
                        
                        <div class="row">
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Data inicial
                                </p>
                                <div class="form-group" id="data_1" style="margin-left: 0px;">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input style="width: 90%" type="text" class="form-control" name="data_inicial" id="data_inicial" value="<?=($data_inicial) ? $data_inicial : ''?>" >
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <p class="font-bold">
                                    Data final
                                </p>
                                <div class="form-group" id="data_1" style="margin-left: 0px;">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input style="width: 90%" type="text" class="form-control" name="data_final" id="data_final" value="<?=($data_final) ? $data_final : ''?>" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12"> 
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
    if($_POST['cmd'] == 'search')
    {
        if($data_inicial)
            $strWhere .= " AND data_agendamento >= '".ConverteData($data_inicial)."'";

        if($data_final)
            $strWhere .= " AND data_agendamento <= '".ConverteData($data_final)."'";
    }

    $str = "SELECT * FROM prospeccao WHERE idempresa = '$adm_empresa' AND (status = '1' OR (status = '2' AND auto = '1')) $strWherePesq $strWhere ORDER BY data";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);
    
    if($num > 0)
    {
    ?>
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
                    <h5>Lista de pesquisas marcadas como AGUARDANDO</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" style="font-size: 10px;" >
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Sobrenome</th>
                                    <th>Data de nascimento (idade)</th>
                                    <th style="width: 40%">Notas</th>
                                    <th>Cupom</th>
                                    <th>Agendar para</th>
                                    <th>Horário</th>
                                    <th>Telefones</th>
                                    <th>Status atual</th>
                                    <th style="width: 12.5%">Modificar status</th>
                                    <th style="width: 10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                    $codigo = $vet['codigo'];
                                    
                                    $status = '';
                                    if($vet['status'] == 1)
                                        $status = '<span class="label label-warning">Aguardando</span>';
                                    elseif($vet['status'] == 2)
                                        $status = '<span class="label label-primary">Confirmado</span>';
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['nome'])?></td>
                                    <td><?=stripslashes($vet['sobrenome'])?></td>
                                    <td><?=$vet['data_nascimento']?></td>
                                    <td><input type="text" name="exame_<?=$codigo?>" id="exame_<?=$codigo?>" class="form-control" value="<?=$vet['data_exame']?>" onblur="javascript: altera_data_exame_prospeccao('<?=$codigo?>')" style="font-size: 10px; width: 100%"></td>
                                    <td><?=$vet['cupom']?></td>
                                    <td><?=ConverteData($vet['data_agendamento'])?></td>
                                    <td><?=substr($vet['hora_inicial'], 0, -3)?></td>
                                    <td>
                                        Tel. 1: <?=($vet['telefone']) ? '<a href="https://api.whatsapp.com/send?phone=55'.preg_replace('/[^\d]/', '', $vet['telefone']).'" target="_blank">'.$vet['telefone'].'</a>' : 'Não informado'?><br>
                                        Tel. 2: <?=($vet['telefone2']) ? '<a href="https://api.whatsapp.com/send?phone=55'.preg_replace('/[^\d]/', '', $vet['telefone2']).'" target="_blank">'.$vet['telefone2'].'</a>' : 'Não informado'?>
                                    </td>
                                    <td><div class="status_<?=$codigo?>"><?=$status?></div></td>
                                    <td class="center">
                                        <div class="prospeccao_<?=$codigo?>" style="float: left; margin-right: 2px;">
                                            <?
                                            if(!$vet['auto'] || ($vet['auto'] == 1 && $vet['status'] == 1))
                                            {
                                            ?>
                                            <a class="btn btn-info btn-circle" type="button" title="transformar em CONFIRMADO" onclick="javascript: altera_status_prospeccao('<?=$codigo?>', '<?=$vet['idagendamento']?>', 2)"><i class='fa fa-check-square'></i></a>
                                            <?
                                            }
                                            else
                                            {
                                            ?>
                                            <a class="btn btn-default btn-circle" type="button" title="desfazer CONFIRMADO" onclick="javascript: altera_status_prospeccao('<?=$codigo?>', '<?=$vet['idagendamento']?>', 1)"><i class='fa fa-reply'></i></a>
                                            <a class="btn btn-warning btn-circle" type="button" title="transformar em FILA DE ESPERA" href="r_aguardando.php?cmd=fila&codigo=<?=$vet['codigo']?>&idagendamento=<?=$vet['idagendamento']?>"><i class='fa fa-check-square'></i></a>
                                            <a class="btn btn-primary btn-circle" type="button" title="transformar em REAGENDAR" onclick="javascript: altera_status_prospeccao('<?=$codigo?>', '<?=$vet['idagendamento']?>', 7)"><i class='fa fa-check-square'></i></a>
                                            <a class="btn btn-info btn-circle" type="button" title="transformar em ATENDIDO" onclick="javascript: altera_status_prospeccao('<?=$codigo?>', '<?=$vet['idagendamento']?>', 8)"><i class='fa fa-check-square'></i></a>
                                            <?
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td class="center">
                                        <?
                                        $url = "prospeccao.php?ind=2&codigo=".$vet['codigo']."&url=".base64_encode('r_aguardando.php');
                                        if($vet['auto'] == 1)
                                            $url = "r_aguardando.php?ind=2&codigo=".$vet['codigo'];
                                        ?>
                                        <a class="btn btn-warning btn-circle" type="button" title="editar / visualizar" href="<?=$url?>"><i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="r_aguardando.php?cmd=del&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a> 
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
                                    <th>Notas</th>
                                    <th>Cupom</th>
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
                Nenhuma pesquisa maracada como AGUARDANDO no sistema.
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