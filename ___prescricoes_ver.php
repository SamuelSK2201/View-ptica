<?
session_start();

include("s_acessos.php");
include("funcoes.php");
include("verifica.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_consultas == 1)
    die("Acesso negado!");

$order = anti_injection($_GET['order']);

$strU = "SELECT A.codigo AS idusuario, A.idempresa, A.nome AS usuario, A.email, C.ind_pesquisa, C.txt_rodape, C.imagem, B.*
    FROM usuarios A
    INNER JOIN usuarios_tipos B ON A.idtipo = B.codigo AND A.idempresa = B.idempresa
    INNER JOIN empresas C ON A.idempresa = C.codigo
    WHERE A.codigo = '".$_SESSION["adm_codigo"]."'";
$rsU  = mysqli_query($conexao, $strU) or die(mysqli_error($conexao));
$numU = mysqli_num_rows($rsU);
$vetU = mysqli_fetch_array($rsU);

$adm_empresa = $vetU['idempresa'];
$adm_codigo = $vetU['idusuario'];
$adm_nome = stripslashes($vetU["usuario"]);
$adm_email = $vetU["email"];
$adm_perfil = $vetU["perfil"];
$adm_logo = $vetU["imagem"];

$txt_rodape = stripslashes($vetU["txt_rodape"]);

$perm_agenda = $vetU['agenda'];
$perm_consultas = $vetU['consultas'];
$perm_pacientes = $vetU['pacientes'];
$perm_oticas = $vetU['oticas'];
$perm_procedimentos = $vetU['procedimentos'];
$perm_modelos = $vetU['modelos'];
$perm_prospeccao = $vetU['prospeccao'];
$perm_relatorios = $vetU['relatorios'];
$perm_usuarios_tipos = $vetU['usuarios_tipos'];
$perm_usuarios = $vetU['usuarios'];

$table = 'usuarios';
if($_SESSION["adm_user"] == 1)
    $table = 'usuarios_adm';

$strU = "SELECT * FROM $table WHERE codigo = '".$_SESSION["adm_codigo"]."'";
$rsU  = mysqli_query($conexao, $strU) or die(mysqli_error($conexao));
$numU = mysqli_num_rows($rsU);
$vetU = mysqli_fetch_array($rsU);

$adm_empresa = $vetU['idempresa'];

$idprescricao = anti_injection($_REQUEST['idprescricao']);
$idagendamento = anti_injection($_REQUEST['idagendamento']);

$strWhere = "";
$strWhereB = "";

if($idprescricao)
{
    $strWhere = " AND codigo = '$idprescricao'";
    $strWhereB = " AND idprescricao = '$idprescricao'";
}

$strWhereP = "";
if($adm_perfil == 2 || $adm_perfil == 5)
    $strWhereP = " AND idoptometrista = '$adm_codigo'";

$str = "SELECT A.*, DATE_FORMAT(A.data_pagto, '%d/%m/%Y %H:%i') AS dt_pagto, B.nome AS optometrista, C.descricao AS procedimento, C.cor, 
    D.nome AS paciente, D.data_nascimento, D.cpf, D.rg, D.email, D.telefone, D.telefone2, D.cep, D.endereco, D.numero, D.complemento, D.bairro, D.cidade, D.estado
    FROM agendamentos A
    INNER JOIN usuarios B ON A.idoptometrista = B.codigo
    LEFT JOIN procedimentos C ON A.idprocedimento = C.codigo
    INNER JOIN pacientes D ON A.idpaciente = D.codigo
    WHERE A.codigo = '$idagendamento'
    AND A.idempresa = '$adm_empresa'
    $strWhereP
    ORDER BY A.data";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);

$paciente = stripslashes($vet['paciente']);
$optometrista = stripslashes($vet['optometrista']);
$nascimento = ConverteData($vet['data_nascimento']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>View Optica</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        @media print {
            #no_print{
                display: none !important;
            }
        }
    </style>
</head>

<body class="top-navigation">
    <div id="wrapper">
        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom white-bg">
                <nav class="navbar navbar-static-top" role="navigation">
                    <div class="navbar-header">
                        <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                            <i class="fa fa-reorder"></i>
                        </button>
                        <a href="#" class="navbar-brand">View Optica</a>
                    </div>
                </nav>
            </div>
            <div class="wrapper wrapper-content">
                <p align="center">
                    <img alt="image" class="img-thumbnail" src="upload/thumbnails/<?=$adm_logo?>" style="width: 35%" />
                </p>
                <?
                $strOrder = " ORDER BY tipo ASC";
                if($order == 1)
                    $strOrder = " ORDER BY data DESC LIMIT 1";

                $strP = "SELECT *, DATE_FORMAT(data, '%d/%m/%Y %H:%i') AS dt_prescricao FROM prescricoes WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' $strWhere $strOrder";
                $rsP  = mysqli_query($conexao, $strP) or die(mysqli_error($conexao));
                $numP = mysqli_num_rows($rsP);
                
                while($vetP = mysqli_fetch_array($rsP))
                {
                    $idprescricao = $vetP['codigo'];

                    $str = "SELECT * FROM prescricoes_oculos WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao' $strWhereB";
                    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                    $num = mysqli_num_rows($rs);
                    $vet = mysqli_fetch_array($rs);
                ?>
                <div class="container" <?=($vetP['tipo'] == 1) ? 'style="display:block"' : 'style="display:none"'?>>
                    <div class="row">
                        <div class="pull-right">
                            <button id="no_print" class="btn btn-white btn-xs" type="button" onclick="javascript: window.print();">Imprimir</button>
                        </div>
                        <h2>Prescrição para óculos</h2>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="font-bold" >
                                Paciente: <?=$paciente?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="font-bold" >
                                Nascimento: <?=$nascimento?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="font-bold" >
                                Especialista: <?=$optometrista?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="font-bold" >
                                Data / hora: <?=$vetP['dt_prescricao']?>
                            </p>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Esf</th>
                                    <th>Cil</th>
                                    <th>Eixo</th>
                                    <th>AV</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>OD</th>
                                    <td><?=($vet['oculos_od_esf'] > 0) ? '+'.number_format($vet['oculos_od_esf'], 2, '.', '') : number_format($vet['oculos_od_esf'], 2, '.', '')?></td>
                                    <td><?=($vet['oculos_od_cil'] > 0) ? '+'.number_format($vet['oculos_od_cil'], 2, '.', '') : number_format($vet['oculos_od_cil'], 2, '.', '')?></td>
                                    <td><?=$vet['oculos_od_eixo']?>&deg;</td>
                                    <td><?=$vet['oculos_od_av']?></td>
                                </tr>
                                <tr>
                                    <th>OE</th>
                                    <td><?=($vet['oculos_oe_esf'] > 0) ? '+'.number_format($vet['oculos_oe_esf'], 2, '.', '') : number_format($vet['oculos_oe_esf'], 2, '.', '')?></td>
                                    <td><?=($vet['oculos_oe_cil'] > 0) ? '+'.number_format($vet['oculos_oe_cil'], 2, '.', '') : number_format($vet['oculos_oe_cil'], 2, '.', '')?></td>
                                    <td><?=$vet['oculos_oe_eixo']?>&deg;</td>
                                    <td><?=$vet['oculos_oe_av']?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-2">
                            <p class="font-bold" >
                                Adição
                            </p>
                        </div>
                        <div class="col-md-10">
                            <?=($vet['oculos_adicao'] > 0) ? '+'.number_format($vet['oculos_adicao'], 2, '.', '') : number_format($vet['oculos_adicao'], 2, '.', '')?>
                        </div>
                    </div>
                    <br>
                    <div class="row">						
                        <div class="col-md-2">
                            <p class="font-bold" >
                                Lente
                            </p>
                        </div>
                        <div class="col-md-10">
                            <select class="form-control" name="oculos_lente" id="oculos_lente" required disabled>
                                <option value="">Selecione ...</option>
                                <?
                                $strM = "SELECT * FROM tipos_lentes WHERE idempresa = '$adm_empresa' AND status = '1' ORDER BY nome";
                                $rsM  = mysqli_query($conexao, $strM) or die(mysqli_error($conexao));

                                while($vetM = mysqli_fetch_array($rsM))
                                {
                                ?>
                                <option value="<?=$vetM['codigo']?>" <?=($vetM['codigo'] == $vet['oculos_lente']) ? 'selected' : ''?>><?=stripslashes($vetM['nome'])?></option>
                                <?
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <?
                    if($vet['oculos_observacao'])
                    {
                    ?>
                    <br>
                    <div class="row">
                        <div class="col-md-2">
                            <p class="font-bold" >
                                Observação
                            </p>
                        </div>
                        <div class="col-md-10">
                            <?=nl2br(stripslashes($vet['oculos_observacao']))?>
                        </div>						
                    </div>
                    <?
                    }
                    ?>
                </div>
				
				

                <?
                $str = "SELECT * FROM prescricoes_lentes WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao' $strWhereB";
                $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                $vet = mysqli_fetch_array($rs);
                ?>
                <?=($vetP['tipo'] == 2 && $num > 0) ? '<p style="page-break-after: always; "></p>' : ''?>
                <div class="container" <?=($vetP['tipo'] == 2) ? 'style="display:block"' : 'style="display:none"'?>>
                    <div class="row">
                        <div class="pull-right">
                            <button id="no_print" class="btn btn-white btn-xs" type="button" onclick="javascript: window.print();">Imprimir</button>
                        </div>
                        <h2>Prescrição lente de contato</h2>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="font-bold" >
                                Paciente: <?=$paciente?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="font-bold" >
                                Nascimento: <?=$nascimento?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="font-bold" >
                                Especialista: <?=$optometrista?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="font-bold" >
                                Data / hora: <?=$vetP['dt_prescricao']?>
                            </p>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Esf</th>
                                    <th>Cil</th>
                                    <th>Eixo</th>
                                    <th>AV</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>OD</th>
                                    <td><?=($vet['lentes_od_esf'] > 0) ? '+'.$vet['lentes_od_esf'] : $vet['lentes_od_esf']?></td>
                                    <td><?=($vet['lentes_od_cil'] > 0) ? '+'.$vet['lentes_od_cil'] : $vet['lentes_od_cil']?></td>
                                    <td><?=$vet['lentes_od_eixo']?>&deg;</td>
                                    <td><?=$vet['lentes_od_av']?></td>
                                </tr>
                                <tr>
                                    <th>OE</th>
                                    <td><?=($vet['lentes_oe_esf'] > 0) ? '+'.$vet['lentes_oe_esf'] : $vet['lentes_oe_esf']?></td>
                                    <td><?=($vet['lentes_oe_cil'] > 0) ? '+'.$vet['lentes_oe_cil'] : $vet['lentes_oe_cil']?></td>
                                    <td><?=$vet['lentes_oe_eixo']?>&deg;</td>
                                    <td><?=$vet['lentes_oe_av']?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-2">
                            <p class="font-bold" >
                                Lente
                            </p>
                        </div>
                        <div class="col-md-10">
                            <select class="form-control" name="lentes_lente" id="lentes_lente" required disabled>
                                <option value="">Selecione ...</option>
                                <?
                                $strM = "SELECT * FROM tipos_lentes WHERE idempresa = '$adm_empresa' AND status = '1' ORDER BY nome";
                                $rsM  = mysqli_query($conexao, $strM) or die(mysqli_error($conexao));

                                while($vetM = mysqli_fetch_array($rsM))
                                {
                                ?>
                                <option value="<?=$vetM['codigo']?>" <?=($vetM['codigo'] == $vet['lentes_lente']) ? 'selected' : ''?>><?=stripslashes($vetM['nome'])?></option>
                                <?
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <?
                    if($vet['lentes_observacao'])
                    {
                    ?>
                    <br>
                    <div class="row">
                        <div class="col-md-2">
                            <p class="font-bold" >
                                Observação
                            </p>
                        </div>
                        <div class="col-md-10">
                            <?=nl2br(stripslashes($vet['lentes_observacao']))?>
                        </div>
                    </div>
                    <?
                    }
                    ?>
                </div>

                <?
                $str = "SELECT A.*, B.titulo 
                    FROM prescricoes_laudos A
                    LEFT JOIN modelos B ON A.idmodelo = B.codigo
                    WHERE A.idempresa = '$adm_empresa' 
                    AND A.idagendamento = '$idagendamento' 
                    $strWhereB";
                $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                $vet = mysqli_fetch_array($rs);
                ?>
                <?=($vetP['tipo'] == 3) ? '<p style="page-break-after: always; "></p>' : ''?>
                <div class="container" <?=($vetP['tipo'] == 3) ? 'style="display:block"' : 'style="display:none"'?>>
                    <div class="row">
                        <div class="pull-right">
                            <button id="no_print" class="btn btn-white btn-xs" type="button" onclick="javascript: window.print();">Imprimir</button>
                        </div>
                        <h2>Laudos</h2>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="font-bold" >
                                Paciente: <?=$paciente?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="font-bold" >
                                Nascimento: <?=$nascimento?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="font-bold" >
                                Especialista: <?=$optometrista?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="font-bold" >
                                Data / hora: <?=$vetP['dt_prescricao']?>
                            </p>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <?=nl2br(stripslashes($vet['laudo']))?>
                        </div>
                    </div>
                </div>

                <?
                $str = "SELECT A.*, B.titulo 
                    FROM prescricoes_declaracoes A
                    LEFT JOIN modelos B ON A.idmodelo = B.codigo
                    WHERE A.idempresa = '$adm_empresa' 
                    AND A.idagendamento = '$idagendamento' 
                    AND idprescricao = '$idprescricao'
                    $strWhereB";
                $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                $vet = mysqli_fetch_array($rs);
                ?>
                <?=($vetP['tipo'] == 4) ? '<p style="page-break-after: always; "></p>' : ''?>
                <div class="container" <?=($vetP['tipo'] == 4) ? 'style="display:block"' : 'style="display:none"'?>>
                    <div class="row">
                        <div class="pull-right">
                            <button id="no_print" class="btn btn-white btn-xs" type="button" onclick="javascript: window.print();">Imprimir</button>
                        </div>
                        <h2>Declaração</h2>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="font-bold" >
                                Paciente: <?=$paciente?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="font-bold" >
                                Nascimento: <?=$nascimento?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="font-bold" >
                                Especialista: <?=$optometrista?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="font-bold" >
                                Data / hora: <?=$vetP['dt_prescricao']?>
                            </p>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <?=nl2br(stripslashes($vet['declaracao']))?>
                        </div>
                    </div>
                </div>

                <?
                $str = "SELECT A.*, B.titulo 
                    FROM prescricoes_encaminhamentos A
                    LEFT JOIN modelos B ON A.idmodelo = B.codigo
                    WHERE A.idempresa = '$adm_empresa' 
                    AND A.idagendamento = '$idagendamento' 
                    AND idprescricao = '$idprescricao'
                    $strWhereB";
                $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                $vet = mysqli_fetch_array($rs);
                ?>
                <?=($vetP['tipo'] == 5) ? '<p style="page-break-after: always; "></p>' : ''?>
                <div class="container" <?=($vetP['tipo'] == 5) ? 'style="display:block"' : 'style="display:none"'?>>
                    <div class="row">
                        <div class="pull-right">
                            <button id="no_print" class="btn btn-white btn-xs" type="button" onclick="javascript: window.print();">Imprimir</button>
                        </div>
                        <h2>Encaminhamento</h2>
                    </div>
                    <br>
                    <div class="ibox"> 
                        <div class="row">
                            <div class="col-md-12">
                                <p class="font-bold" >
                                    Paciente: <?=$paciente?>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="font-bold" >
                                    Nascimento: <?=$nascimento?>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="font-bold" >
                                    Especialista: <?=$optometrista?>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="font-bold" >
                                    Data / hora: <?=$vetP['dt_prescricao']?>
                                </p>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <?=nl2br(stripslashes($vet['encaminhamento']))?>
                            </div>
                        </div>
                    </div>
                </div>

                <?=($vetP['tipo'] == 6) ? '<p style="page-break-after: always; "></p>' : ''?>
                <div class="container" <?=($vetP['tipo'] == 6) ? 'style="display:block"' : 'style="display:none"'?>>
                    <div class="row">
                        <div class="pull-right">
                            <button id="no_print" class="btn btn-white btn-xs" type="button" onclick="javascript: window.print();">Imprimir</button>
                        </div>
                        <h2>Anamnese</h2>
                    </div>
                    <br>
                    <?
                    $str = "SELECT * FROM prescricoes_anamnese_optometrica WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao' $strWhereB";
                    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                    $vet = mysqli_fetch_array($rs);
                    ?>
                    <div class="ibox">  
                        <div class="row">
                            <div class="col-md-12">
                                <p class="font-bold" >
                                    Paciente: <?=$paciente?>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="font-bold" >
                                    Nascimento: <?=$nascimento?>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="font-bold" >
                                    Especialista: <?=$optometrista?>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <p class="font-bold" >
                                    Data / hora: <?=$vetP['dt_prescricao']?>
                                </p>
                            </div>
                        </div>
                        <br>           
                        <div class="ibox-title ui-sortable-handle">
                            <h5>AVALIAÇÃO OPTOMÉTRICA</h5>
                        </div>
                        <div class="ibox-content" style="display: block;"> 
                            <div class="row">
                                <div class="col-md-8">
                                    <p class="font-bold">
                                        Motivo da consulta
                                    </p>
                                    <?=stripslashes($vet['motivo_consulta'])?>
                                </div>
                                <div class="col-md-4">
                                    <p class="font-bold">
                                        Último exame
                                    </p>
                                    <?=stripslashes($vet['ultimo_exame'])?>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="checkbox" name="itens[]" id="itens" value="1" <?=(strstr($vet['itens'], '|1|')) ? 'checked' : ''?> disabled> Prurido - vermelhidão sensação de areia<br>
                                    <input type="checkbox" name="itens[]" id="itens" value="2" <?=(strstr($vet['itens'], '|2|')) ? 'checked' : ''?> disabled> Fotofobia - sensibilidade a luz<br>
                                    <input type="checkbox" name="itens[]" id="itens" value="3" <?=(strstr($vet['itens'], '|3|')) ? 'checked' : ''?> disabled> Hiperemia - olho vermelho<br>
                                    <input type="checkbox" name="itens[]" id="itens" value="4" <?=(strstr($vet['itens'], '|4|')) ? 'checked' : ''?> disabled> Pterígio - carne crescida                                                
                                </div>
                                <div class="col-md-4">
                                    <input type="checkbox" name="itens[]" id="itens" value="5" <?=(strstr($vet['itens'], '|5|')) ? 'checked' : ''?> disabled> Labirintite<br>
                                    <input type="checkbox" name="itens[]" id="itens" value="6" <?=(strstr($vet['itens'], '|6|')) ? 'checked' : ''?> disabled> Catarata<br>
                                    <input type="checkbox" name="itens[]" id="itens" value="7" <?=(strstr($vet['itens'], '|7|')) ? 'checked' : ''?> disabled> Epfera - lacrimejamento em excesso<br>
                                    <input type="checkbox" name="itens[]" id="itens" value="8" <?=(strstr($vet['itens'], '|8|')) ? 'checked' : ''?> disabled> Trauma - Batida na cabeça                                             
                                </div>
                                <div class="col-md-4">
                                    <input type="checkbox" name="itens[]" id="itens" value="9" <?=(strstr($vet['itens'], '|9|')) ? 'checked' : ''?> disabled> Glaucoma - Pressão alta<br>
                                    <input type="checkbox" name="itens[]" id="itens" value="10" <?=(strstr($vet['itens'], '|10|')) ? 'checked' : ''?> disabled> Diabetes<br>
                                    <input type="checkbox" name="itens[]" id="itens" value="11" <?=(strstr($vet['itens'], '|11|')) ? 'checked' : ''?> disabled> Ceratocone
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="checkbox" name="itens[]" id="itens" value="12" <?=(strstr($vet['itens'], '|12|')) ? 'checked' : ''?> disabled> Cefaléia - Dor de cabeça
                                </div>
                            </div>
                            <br>
                            <div id="div_cefaleia" <?=(strstr($vet['itens'], '|12|')) ? 'style="display: block;"' : 'style="display: none;"'?> >
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold">
                                            Períodos
                                        </p>
                                        <input type="checkbox" name="periodos[]" id="periodos" value="1" <?=(strstr($vet['periodos'], '|1|')) ? 'checked' : ''?> disabled> Manhã&nbsp;&nbsp;&nbsp;
                                        <input type="checkbox" name="periodos[]" id="periodos" value="2" <?=(strstr($vet['periodos'], '|2|')) ? 'checked' : ''?> disabled> Tarde&nbsp;&nbsp;&nbsp;
                                        <input type="checkbox" name="periodos[]" id="periodos" value="3" <?=(strstr($vet['periodos'], '|3|')) ? 'checked' : ''?> disabled> Noite&nbsp;&nbsp;&nbsp;
                                    </div>
                                </div>
                                <br>                                            
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold">
                                            Onde?
                                        </p>
                                        <input type="checkbox" name="onde[]" id="onde" value="1" <?=(strstr($vet['onde'], '|1|')) ? 'checked' : ''?> disabled> Frontal&nbsp;&nbsp;&nbsp;
                                        <input type="checkbox" name="onde[]" id="onde" value="2" <?=(strstr($vet['onde'], '|2|')) ? 'checked' : ''?> disabled> Temporal&nbsp;&nbsp;&nbsp;
                                        <input type="checkbox" name="onde[]" id="onde" value="3" <?=(strstr($vet['onde'], '|3|')) ? 'checked' : ''?> disabled> Occipital&nbsp;&nbsp;&nbsp;
                                        <input type="checkbox" name="onde[]" id="onde" value="4" <?=(strstr($vet['onde'], '|4|')) ? 'checked' : ''?> disabled> Parental&nbsp;&nbsp;&nbsp;
                                    </div>
                                </div>
                                <br>                                            
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold">
                                            Quando?
                                        </p>
                                        <input type="checkbox" name="quando[]" id="quando" value="1" <?=(strstr($vet['quando'], '|1|')) ? 'checked' : ''?> disabled> Todos os dias&nbsp;&nbsp;&nbsp;
                                        <input type="checkbox" name="quando[]" id="quando" value="2" <?=(strstr($vet['quando'], '|2|')) ? 'checked' : ''?> disabled> Eventual&nbsp;&nbsp;&nbsp;
                                        <input type="checkbox" name="quando[]" id="quando" value="3" <?=(strstr($vet['quando'], '|3|')) ? 'checked' : ''?> disabled> Segunda a Sexta&nbsp;&nbsp;&nbsp;
                                        <input type="checkbox" name="quando[]" id="quando" value="4" <?=(strstr($vet['quando'], '|4|')) ? 'checked' : ''?> disabled> Finais de Semana&nbsp;&nbsp;&nbsp;
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold" >
                                            Alguns sintomas mais relatados
                                        </p>
                                        <?=nl2br(stripslashes($vet['sintomas_relatados']))?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>

                        <?
                        $str = "SELECT * FROM prescricoes_anamnese_pessoal WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao' $strWhereB";
                        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                        $vet = mysqli_fetch_array($rs);
                        ?>    
                        <p style="page-break-after: always; ">               
                        <div class="ibox">
                            <div class="ibox-title ui-sortable-handle">
                                <h5>ANTECEDENTE PESSOAL</h5>
                            </div>
                            <div class="ibox-content" style="display: block;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="font-bold">
                                            É diabético?
                                        </p>
                                        <select class="form-control" name="diabetico_pessoal" id="diabetico_pessoal" disabled>
                                            <option value="1" <?=(1 == $vet['diabetico_pessoal']) ? 'selected' : ''?>>Sim</option>
                                            <option value="2" <?=(2 == $vet['diabetico_pessoal']) ? 'selected' : ''?>>Não</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="font-bold">
                                            Usa medicamentos controlados?
                                        </p>
                                        <select class="form-control" name="medicamentos_controlados" id="medicamentos_controlados" disabled>
                                            <option value="1" <?=(1 == $vet['medicamentos_controlados']) ? 'selected' : ''?>>Sim</option>
                                            <option value="2" <?=(2 == $vet['medicamentos_controlados']) ? 'selected' : ''?>>Não</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="font-bold">
                                            Tem pressão alta?
                                        </p>
                                        <select class="form-control" name="pressao_alta_pessoal" id="pressao_alta_pessoal" disabled>
                                            <option value="1" <?=(1 == $vet['pressao_alta_pessoal']) ? 'selected' : ''?>>Sim</option>
                                            <option value="2" <?=(2 == $vet['pressao_alta_pessoal']) ? 'selected' : ''?>>Não</option>
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="font-bold">
                                            Usa substâncias químicas?
                                        </p>
                                        <select class="form-control" name="substancias_quimicas" id="substancias_quimicas" disabled>
                                            <option value="1" <?=(1 == $vet['substancias_quimicas']) ? 'selected' : ''?>>Sim</option>
                                            <option value="2" <?=(2 == $vet['substancias_quimicas']) ? 'selected' : ''?>>Não</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="font-bold">
                                            É fumante?
                                        </p>
                                        <select class="form-control" name="fumante" id="fumante" disabled>
                                            <option value="1" <?=(1 == $vet['fumante']) ? 'selected' : ''?>>Sim</option>
                                            <option value="2" <?=(2 == $vet['fumante']) ? 'selected' : ''?>>Não</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="font-bold">
                                            Está grávida?
                                        </p>
                                        <select class="form-control" name="gravida" id="gravida" disabled>
                                            <option value="1" <?=(1 == $vet['gravida']) ? 'selected' : ''?>>Sim</option>
                                            <option value="2" <?=(2 == $vet['gravida']) ? 'selected' : ''?>>Não</option>
                                        </select>
                                    </div>
                                </div>
                                <?
                                if($vet['obs_pessoal'])
                                {
                                ?>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold" >
                                            Observações
                                        </p>
                                        <?=nl2br(stripslashes($vet['obs_pessoal']))?>
                                    </div>
                                </div>
                                <?
                                }
                                ?>
                            </div>
                        </div>
                        </p>

                        <?
                        $str = "SELECT * FROM prescricoes_anamnese_familiar WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao' $strWhereB";
                        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                        $vet = mysqli_fetch_array($rs);
                        ?>
                        <p style="page-break-after: always; ">  
                        <div class="ibox">
                            <div class="ibox-title ui-sortable-handle">
                                <h5>ANTECEDENTE FAMILIAR</h5>
                            </div>
                            <div class="ibox-content" style="display: block;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="font-bold">
                                            Alguém é diabético?
                                        </p>
                                        <select class="form-control" name="diabetico_familiar" id="diabetico_familiar" disabled>
                                            <option value="1" <?=(1 == $vet['diabetico_familiar']) ? 'selected' : ''?>>Sim</option>
                                            <option value="2" <?=(2 == $vet['diabetico_familiar']) ? 'selected' : ''?>>Não</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="font-bold">
                                            Alguém tem pressão alta?
                                        </p>
                                        <select class="form-control" name="pressao_alta_familiar" id="pressao_alta_familiar" disabled>
                                            <option value="1" <?=(1 == $vet['pressao_alta_familiar']) ? 'selected' : ''?>>Sim</option>
                                            <option value="2" <?=(2 == $vet['pressao_alta_familiar']) ? 'selected' : ''?>>Não</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="font-bold">
                                            Alguém tem glaucoma?
                                        </p>
                                        <select class="form-control" name="glaucoma" id="glaucoma" disabled>
                                            <option value="1" <?=(1 == $vet['glaucoma']) ? 'selected' : ''?>>Sim</option>
                                            <option value="2" <?=(2 == $vet['glaucoma']) ? 'selected' : ''?>>Não</option>
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="font-bold">
                                            Alguém tem estrabismo?
                                        </p>
                                        <select class="form-control" name="estrabismo" id="estrabismo" disabled>
                                            <option value="1" <?=(1 == $vet['estrabismo']) ? 'selected' : ''?>>Sim</option>
                                            <option value="2" <?=(2 == $vet['estrabismo']) ? 'selected' : ''?>>Não</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="font-bold">
                                            Alguém tem catarata?
                                        </p>
                                        <select class="form-control" name="catarata" id="catarata" disabled>
                                            <option value="1" <?=(1 == $vet['catarata']) ? 'selected' : ''?>>Sim</option>
                                            <option value="2" <?=(2 == $vet['catarata']) ? 'selected' : ''?>>Não</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="font-bold">
                                            Alguém usa óculos?
                                        </p>
                                        <select class="form-control" name="oculos" id="oculos" disabled>
                                            <option value="1" <?=(1 == $vet['oculos']) ? 'selected' : ''?>>Sim</option>
                                            <option value="2" <?=(2 == $vet['oculos']) ? 'selected' : ''?>>Não</option>
                                        </select>
                                    </div>
                                </div>
                                <?
                                if($vet['obs_familiar'])
                                {
                                ?>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold" >
                                            Observações
                                        </p>
                                        <?=nl2br(stripslashes($vet['obs_familiar']))?>
                                    </div>
                                </div>
                                <?
                                }
                                ?>
                            </div>
                        </div>
                        </p>

                        <?
                        $str = "SELECT * FROM prescricoes_anamnese_acuidade WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao' $strWhereB";
                        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                        $vet = mysqli_fetch_array($rs);
                        ?>
                        <p style="page-break-after: always; ">  
                        <div class="ibox">
                            <div class="ibox-title ui-sortable-handle">
                                <h5>ACUIDADE VISUAL</h5>
                            </div>
                            <div class="ibox-content" style="display: block;">
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="font-bold">
                                            Optotipo
                                        </p>
                                        <input type="text" name="optotipo" id="optotipo" class="form-control" value="<?=stripslashes($vet['optotipo'])?>" disabled >
                                    </div>
                                    <div class="col-md-4">
                                        <p class="font-bold">
                                            Metros
                                        </p>
                                        <input type="text" name="metros" id="metros" class="form-control" value="<?=stripslashes($vet['metros'])?>" disabled >
                                    </div>
                                </div>
                                <?
                                if($vet['obs_acuidade'])
                                {
                                ?>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold" >
                                            Observações
                                        </p>
                                        <?=nl2br(stripslashes($vet['obs_acuidade']))?>
                                    </div>
                                </div>
                                <?
                                }
                                ?>
                            </div>
                        </div>
                        </p>

                        <?
                        $str = "SELECT * FROM prescricoes_anamnese_lensometro WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao' $strWhereB";
                        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                        $vet = mysqli_fetch_array($rs);
                        ?>
                        <p style="page-break-after: always; ">  
                        <div class="ibox">
                            <div class="ibox-title ui-sortable-handle">
                                <h5>LENSOMETRO</h5>
                            </div>
                            <div class="ibox-content" style="display: block;">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>Esférico</th>
                                        <th>Cilindrico</th>
                                        <th>Eixo</th>
                                        <th>Adição</th>
                                        <th>Prisma</th>
                                        <th>DNP Longe</th>
                                        <th>DNP Perto</th>
                                        <th>Tipo de lente</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td><b>OD</b></td>
                                        <td><input type="number" step="0.25" name="od_esferico" id="od_esferico" class="form-control" value="<?=stripslashes($vet['od_esferico'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="od_cilindrico" id="od_cilindrico" class="form-control" value="<?=stripslashes($vet['od_cilindrico'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="od_eixo" id="od_eixo" class="form-control" value="<?=stripslashes($vet['od_eixo'])?>" disabled>&deg;</td>
                                        <td><input type="number" step="0.25" name="od_adicao" id="od_adicao" class="form-control" value="<?=stripslashes($vet['od_adicao'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="od_prisma" id="od_prisma" class="form-control" value="<?=stripslashes($vet['od_prisma'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="od_dnpl" id="od_dnpl" class="form-control" value="<?=stripslashes($vet['od_dnpl'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="od_dnpp" id="od_dnpp" class="form-control" value="<?=stripslashes($vet['od_dnpp'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="od_tipo" id="od_tipo" class="form-control" value="<?=stripslashes($vet['od_tipo'])?>" disabled></td>
                                    </tr>
                                    <tr>
                                        <td><b>OE</b></td>
                                        <td><input type="number" step="0.25" name="oe_esferico" id="oe_esferico" class="form-control" value="<?=stripslashes($vet['oe_esferico'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="oe_cilindrico" id="oe_cilindrico" class="form-control" value="<?=stripslashes($vet['oe_cilindrico'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="oe_eixo" id="oe_eixo" class="form-control" value="<?=stripslashes($vet['oe_eixo'])?>" disabled>&deg;</td>
                                        <td><input type="number" step="0.25" name="oe_adicao" id="oe_adicao" class="form-control" value="<?=stripslashes($vet['oe_adicao'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="oe_prisma" id="oe_prisma" class="form-control" value="<?=stripslashes($vet['oe_prisma'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="oe_dnpl" id="oe_dnpl" class="form-control" value="<?=stripslashes($vet['oe_dnpl'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="oe_dnpp" id="oe_dnpp" class="form-control" value="<?=stripslashes($vet['oe_dnpp'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="oe_tipo" id="oe_tipo" class="form-control" value="<?=stripslashes($vet['oe_tipo'])?>" disabled></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        </p>

                        <?
                        $str = "SELECT * FROM prescricoes_anamnese_savaliacao WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao' $strWhereB";
                        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                        $vet = mysqli_fetch_array($rs);
                        ?>
                        <p style="page-break-after: always; ">  
                        <div class="ibox">
                            <div class="ibox-title ui-sortable-handle">
                                <h5>AVALIAÇÃO SEM CORREÇÃO</h5>
                            </div>
                            <div class="ibox-content" style="display: block;">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>Longe</th>
                                        <th>Perto</th>
                                        <th>PH Longe</th>
                                        <th>PH Perto</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td><b>OD</b></td>
                                        <td><input type="number" step="0.25" name="od_longe" id="od_longe" class="form-control" value="<?=stripslashes($vet['od_longe'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="od_perto" id="od_perto" class="form-control" value="<?=stripslashes($vet['od_perto'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="od_phl" id="od_phl" class="form-control" value="<?=stripslashes($vet['od_phl'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="od_php" id="od_php" class="form-control" value="<?=stripslashes($vet['od_php'])?>" disabled></td>
                                    </tr>
                                    <tr>
                                        <td><b>OE</b></td>
                                        <td><input type="number" step="0.25" name="oe_longe" id="oe_longe" class="form-control" value="<?=stripslashes($vet['oe_longe'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="oe_perto" id="oe_perto" class="form-control" value="<?=stripslashes($vet['oe_perto'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="oe_phl" id="oe_phl" class="form-control" value="<?=stripslashes($vet['oe_phl'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="oe_php" id="oe_php" class="form-control" value="<?=stripslashes($vet['oe_php'])?>" disabled></td>
                                    </tr>
                                    <tr>
                                        <td><b>Binocular</b></td>
                                        <td><input type="number" step="0.25" name="bin_longe" id="bin_longe" class="form-control" value="<?=stripslashes($vet['bin_longe'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="bin_perto" id="bin_perto" class="form-control" value="<?=stripslashes($vet['bin_perto'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="bin_phl" id="bin_phl" class="form-control" value="<?=stripslashes($vet['bin_phl'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="bin_php" id="bin_php" class="form-control" value="<?=stripslashes($vet['bin_php'])?>" disabled></td>
                                    </tr>
                                    <tr>
                                        <td><b>Olho dominante</b></td>
                                        <td><input type="number" step="0.25" name="dom_longe" id="dom_longe" class="form-control" value="<?=stripslashes($vet['dom_longe'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="dom_perto" id="dom_perto" class="form-control" value="<?=stripslashes($vet['dom_perto'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="dom_phl" id="dom_phl" class="form-control" value="<?=stripslashes($vet['dom_phl'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="dom_php" id="dom_php" class="form-control" value="<?=stripslashes($vet['dom_php'])?>" disabled></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        </p>

                        <?
                        $str = "SELECT * FROM prescricoes_anamnese_cavaliacao WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao' $strWhereB";
                        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                        $vet = mysqli_fetch_array($rs);
                        ?>
                        <p style="page-break-after: always; ">  
                        <div class="ibox">
                            <div class="ibox-title ui-sortable-handle">
                                <h5>AVALIAÇÃO COM CORREÇÃO</h5>
                            </div>
                            <div class="ibox-content" style="display: block;">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>Longe</th>
                                        <th>Perto</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td><b>OD</b></td>
                                        <td><input type="number" step="0.25" name="com_od_longe" id="com_od_longe" class="form-control" value="<?=stripslashes($vet['com_od_longe'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="com_od_perto" id="com_od_perto" class="form-control" value="<?=stripslashes($vet['com_od_perto'])?>" disabled></td>
                                    </tr>
                                    <tr>
                                        <td><b>OE</b></td>
                                        <td><input type="number" step="0.25" name="com_oe_longe" id="com_oe_longe" class="form-control" value="<?=stripslashes($vet['com_oe_longe'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="com_oe_perto" id="com_oe_perto" class="form-control" value="<?=stripslashes($vet['com_oe_perto'])?>" disabled></td>
                                    </tr>
                                    <tr>
                                        <td><b>Binocular</b></td>
                                        <td><input type="number" step="0.25" name="com_bin_longe" id="com_bin_longe" class="form-control" value="<?=stripslashes($vet['com_bin_longe'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="com_bin_perto" id="com_bin_perto" class="form-control" value="<?=stripslashes($vet['com_bin_perto'])?>" disabled></td>
                                    </tr>
                                    <tr>
                                        <td><b>Olho dominante</b></td>
                                        <td><input type="number" step="0.25" name="com_dom_longe" id="com_dom_longe" class="form-control" value="<?=stripslashes($vet['com_dom_longe'])?>" disabled></td>
                                        <td><input type="number" step="0.25" name="com_dom_perto" id="com_dom_perto" class="form-control" value="<?=stripslashes($vet['com_dom_perto'])?>" disabled></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        </p>

                        <?
                        $str = "SELECT * FROM prescricoes_anamnese_reflexos WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao' $strWhereB";
                        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                        $vet = mysqli_fetch_array($rs);
                        ?>
                        <p style="page-break-after: always; ">  
                        <div class="ibox">
                            <div class="ibox-title ui-sortable-handle">
                                <h5>EXAME REFLEXOS PUPILARES</h5>
                            </div>
                            <div class="ibox-content" style="display: block;">
                                <p>
                                    Importância: Avaliar se a sinais de patologias no nervo ótico ou de lesão extensa da retina<br>
                                    Execução: Cliente olhando para longe em um local fixo<br>
                                    Material: Lanterna Observação da Pupila
                                </p>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Exames</th>
                                        <th colspan="2">OD</th>
                                        <th colspan="2">OE</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th>Reagente</th>
                                        <th>Não Reagente</th>
                                        <th>Reagente</th>
                                        <th>Não Reagente</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td><b>Fotomotor – LUZ EM UM DOS OLHOS OBSERVA O MESMO OLHO</b></td>
                                        <td><input type="number" step="0.25" name="od_r_fotomotor" id="od_r_fotomotor" class="form-control" value="<?=stripslashes($vet['od_r_fotomotor'])?>" disabled ></td>
                                        <td><input type="number" step="0.25" name="od_nr_fotomotor" id="od_nr_fotomotor" class="form-control" value="<?=stripslashes($vet['od_nr_fotomotor'])?>" disabled ></td>
                                        <td><input type="number" step="0.25" name="oe_r_fotomotor" id="oe_r_fotomotor" class="form-control" value="<?=stripslashes($vet['oe_r_fotomotor'])?>" disabled ></td>
                                        <td><input type="number" step="0.25" name="oe_nr_fotomotor" id="oe_nr_fotomotor" class="form-control" value="<?=stripslashes($vet['oe_nr_fotomotor'])?>" disabled ></td>
                                    </tr>
                                    <tr>
                                        <td><b>Consensual – LUZ EM UM DOS OLHOS OBSERVA O OUTRO OLHO</b></td>
                                        <td><input type="number" step="0.25" name="od_r_consensual" id="od_r_consensual" class="form-control" value="<?=stripslashes($vet['od_r_consensual'])?>" disabled ></td>
                                        <td><input type="number" step="0.25" name="od_nr_consensual" id="od_nr_consensual" class="form-control" value="<?=stripslashes($vet['od_nr_consensual'])?>" disabled ></td>
                                        <td><input type="number" step="0.25" name="oe_r_consensual" id="oe_r_consensual" class="form-control" value="<?=stripslashes($vet['oe_r_consensual'])?>" disabled ></td>
                                        <td><input type="number" step="0.25" name="oe_nr_consensual" id="oe_nr_consensual" class="form-control" value="<?=stripslashes($vet['oe_nr_consensual'])?>" disabled ></td>
                                    </tr>
                                    <tr>
                                        <td><b>Acomodativo – LUZ NO CENTRO NASAL PEDIR PARA OLHAR PARA LONGE E PERTO OBSERVA AMBUS OLHOS</b></td>
                                        <td><input type="number" step="0.25" name="od_r_acomodativo" id="od_r_acomodativo" class="form-control" value="<?=stripslashes($vet['od_r_acomodativo'])?>" disabled ></td>
                                        <td><input type="number" step="0.25" name="od_nr_acomodativo" id="od_nr_acomodativo" class="form-control" value="<?=stripslashes($vet['od_nr_acomodativo'])?>" disabled ></td>
                                        <td><input type="number" step="0.25" name="oe_r_acomodativo" id="oe_r_acomodativo" class="form-control" value="<?=stripslashes($vet['oe_r_acomodativo'])?>" disabled ></td>
                                        <td><input type="number" step="0.25" name="oe_nr_acomodativo" id="oe_nr_acomodativo" class="form-control" value="<?=stripslashes($vet['oe_nr_acomodativo'])?>" disabled ></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        </p>

                        <?
                        $str = "SELECT * FROM prescricoes_anamnese_motores WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao' $strWhereB";
                        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                        $vet = mysqli_fetch_array($rs);
                        ?>
                        <p style="page-break-after: always; ">  
                        <div class="ibox">
                            <div class="ibox-title ui-sortable-handle">
                                <h5>EXAMES MOTORES - BINOCULARES</h5>
                            </div>
                            <div class="ibox-content" style="display: block;">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Hirschberg: Avaliar o paralelismo dos eixos visuais Binocular/ lanterna a 40 CM / Interciliar Anotação: Centrado ou Descentrado</th>
                                        <th>Kappa: Determina a posição do globo ocular a orbita monocular/ 50CM / Incidir luz e Observar Reflexo Corneano Anotação: K-/K+/k0 + Nasal / -Temporal</th>
                                    </tr>
                                    </thead>
                                </table>
                                <table class="table">
                                    <tr>
                                        <th>Exames</th>
                                        <th colspan="3">OD</th>
                                        <th colspan="3">OE</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td rowspan="2"><b>Hirschberg BINOPULAR</b></td>
                                        <td>15</td>
                                        <td>30</td>
                                        <td>45</td>
                                        <td>15</td>
                                        <td>30</td>
                                        <td>45</td>
                                    </tr>
                                    <tr>
                                        <td><input type="number" step="0.25" name="h_od_15" id="h_od_15" class="form-control" value="<?=stripslashes($vet['h_od_15'])?>" disabled ></td>
                                        <td><input type="number" step="0.25" name="h_od_30" id="h_od_30" class="form-control" value="<?=stripslashes($vet['h_od_30'])?>" disabled ></td>
                                        <td><input type="number" step="0.25" name="h_od_45" id="h_od_45" class="form-control" value="<?=stripslashes($vet['h_od_45'])?>" disabled ></td>
                                        <td><input type="number" step="0.25" name="h_oe_15" id="h_oe_15" class="form-control" value="<?=stripslashes($vet['h_oe_15'])?>" disabled ></td>
                                        <td><input type="number" step="0.25" name="h_oe_30" id="h_oe_30" class="form-control" value="<?=stripslashes($vet['h_oe_30'])?>" disabled ></td>
                                        <td><input type="number" step="0.25" name="h_oe_45" id="h_oe_45" class="form-control" value="<?=stripslashes($vet['h_oe_45'])?>" disabled ></td>
                                    </tr>
                                    <tr>
                                        <td rowspan="2"><b>Kappa MONOCULAR</b></td>
                                        <td>15</td>
                                        <td>30</td>
                                        <td>45</td>
                                        <td>15</td>
                                        <td>30</td>
                                        <td>45</td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" name="k_od_k0" id="k_od_k0" value="1" <?=($vet['k_od_k0'] == '1') ? 'checked' : ''?> disabled> K<small>0</small></td>
                                        <td><input type="checkbox" name="k_od_kma" id="k_od_kma" value="1" <?=($vet['k_od_kma'] == '1') ? 'checked' : ''?> disabled> K<small>+</small></td>
                                        <td><input type="checkbox" name="k_od_kme" id="k_od_kme" value="1" <?=($vet['k_od_kme'] == '1') ? 'checked' : ''?> disabled> K<small>-</small></td>
                                        <td><input type="checkbox" name="k_oe_k0" id="k_oe_k0" value="1" <?=($vet['k_oe_k0'] == '1') ? 'checked' : ''?> disabled> K<small>0</small></td>
                                        <td><input type="checkbox" name="k_oe_kma" id="k_oe_kma" value="1" <?=($vet['k_oe_kma'] == '1') ? 'checked' : ''?> disabled> K<small>+</small></td>
                                        <td><input type="checkbox" name="k_oe_kme" id="k_oe_kme" value="1" <?=($vet['k_oe_kme'] == '1') ? 'checked' : ''?> disabled> K<small>-</small></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        </p>

                        <?
                        $str = "SELECT * FROM prescricoes_anamnese_duccoes WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao' $strWhereB";
                        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                        $vet = mysqli_fetch_array($rs);
                        ?>
                        <p style="page-break-after: always; ">  
                        <div class="ibox">
                            <div class="ibox-title ui-sortable-handle">
                                <h5>DUCÇÕES - MONOCULAR</h5>
                            </div>
                            <div class="ibox-content" style="display: block;">
                                <p>
                                    Avaliar os movimentos e detectar limitações, paresias e paralisias 30-40cm movimentos forma de estrela e o cliente acompanha só com olho<br>
                                    Anotações: suave completa e continua (scc) ou limitações.
                                </p>
                                <table class="table">
                                    <tr>
                                        <th colspan="4">OD</th>
                                        <th colspan="4">OE</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td><input type="checkbox" name="od_suave" id="od_suave" value="1" <?=($vet['od_suave'] == '1') ? 'checked' : ''?> disabled> Suave</td>
                                        <td><input type="checkbox" name="od_completa" id="od_completa" value="1" <?=($vet['od_completa'] == '1') ? 'checked' : ''?> disabled> Completa</td>
                                        <td><input type="checkbox" name="od_continua" id="od_continua" value="1" <?=($vet['od_continua'] == '1') ? 'checked' : ''?> disabled> Contínua</td>
                                        <td><input type="checkbox" name="od_limitacao" id="od_limitacao" value="1" <?=($vet['od_limitacao'] == '1') ? 'checked' : ''?> disabled> Limitação</td>
                                        <td><input type="checkbox" name="oe_suave" id="oe_suave" value="1" <?=($vet['oe_suave'] == '1') ? 'checked' : ''?> disabled> Suave</td>
                                        <td><input type="checkbox" name="oe_completa" id="oe_completa" value="1" <?=($vet['oe_completa'] == '1') ? 'checked' : ''?> disabled> Completa</td>
                                        <td><input type="checkbox" name="oe_continua" id="oe_continua" value="1" <?=($vet['oe_continua'] == '1') ? 'checked' : ''?> disabled> Contínua</td>
                                        <td><input type="checkbox" name="oe_limitacao" id="oe_limitacao" value="1" <?=($vet['oe_limitacao'] == '1') ? 'checked' : ''?> disabled> Limitação</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        </p>

                        <?
                        $str = "SELECT * FROM prescricoes_anamnese_cover WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao' $strWhereB";
                        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                        $vet = mysqli_fetch_array($rs);
                        ?>
                        <p style="page-break-after: always; ">  
                        <div class="ibox">
                            <div class="ibox-title ui-sortable-handle">
                                <h5>COVER TESTE</h5>
                            </div>
                            <div class="ibox-content" style="display: block;">
                                <p>
                                    Avaliar o estado motor descartado foria e temporal<br>
                                    Endo(E) Desvio para o Nasal = mais constante Exo (X) Desvio para Temporal = mais constante
                                </p>
                                <table class="table">
                                    <tr>
                                        <th>40CM</th>
                                        <th>20CM</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td><input type="checkbox" name="com_orto40" id="com_orto40" value="1" <?=($vet['com_orto40'] == '1') ? 'checked' : ''?> disabled> Orto</td>
                                        <td><input type="checkbox" name="com_orto20" id="com_orto20" value="1" <?=($vet['com_orto20'] == '1') ? 'checked' : ''?> disabled> Orto</td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" name="com_endo40" id="com_endo40" value="1" <?=($vet['com_endo40'] == '1') ? 'checked' : ''?> disabled> Endo</td>
                                        <td><input type="checkbox" name="com_endo20" id="com_endo20" value="1" <?=($vet['com_endo20'] == '1') ? 'checked' : ''?> disabled> Endo</td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" name="com_exo40" id="com_exo40" value="1" <?=($vet['com_exo40'] == '1') ? 'checked' : ''?> disabled> Exo</td>
                                        <td><input type="checkbox" name="com_exo20" id="com_exo20" value="1" <?=($vet['com_exo20'] == '1') ? 'checked' : ''?> disabled> Exo</td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" name="com_hiper40" id="com_hiper40" value="1" <?=($vet['com_hiper40'] == '1') ? 'checked' : ''?> disabled> Hiper</td>
                                        <td><input type="checkbox" name="com_hiper20" id="com_hiper20" value="1" <?=($vet['com_hiper20'] == '1') ? 'checked' : ''?> disabled> Hiper</td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" name="com_hipo40" id="com_hipo40" value="1" <?=($vet['com_hipo40'] == '1') ? 'checked' : ''?> disabled> Hipo</td>
                                        <td><input type="checkbox" name="com_hipo20" id="com_hipo20" value="1" <?=($vet['com_hipo20'] == '1') ? 'checked' : ''?> disabled> Hipo</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        </p>

                        <?
                        $str = "SELECT * FROM prescricoes_anamnese_ppc WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao' $strWhereB";
                        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                        $vet = mysqli_fetch_array($rs);
                        ?>
                        <p style="page-break-after: always; ">  
                        <div class="ibox">
                            <div class="ibox-title ui-sortable-handle">
                                <h5>TESTE PPC</h5>
                            </div>
                            <div class="ibox-content" style="display: block;">
                                <p>
                                    Onde o objeto pode ser visto nítido usando o máximo de Convergência<br>
                                    Avaliação: O.R / luz e régua / luz e filtro<br>
                                    Anotação: a distância em cm.
                                </p>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Objeto Real</th>
                                        <th>Luz</th>
                                        <th>Luz e Filtro</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td><?=stripslashes($vet['ppc_objeto'])?></td>
                                        <td><?=stripslashes($vet['ppc_luz'])?></td>
                                        <td><?=stripslashes($vet['ppc_filtro'])?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        </p>
                    </div>
                </div>
                <?
                }
                ?>

                <?
                if($txt_rodape)
                {
                ?>
                <br><br><br>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12">
							<div style="float:left;">
								<?=nl2br($txt_rodape)?>
							</div>
							<div style="float:right;">
								<img alt="image" class="img-thumbnail" src="img/qr-code.jpeg" style="width: 100px; height:100px;" />
							</div>
                        </div>
						
                    </div>
                </div>
                <?
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- Mainly scripts -->
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Peity -->
<script src="js/plugins/peity/jquery.peity.min.js"></script>
<script src="js/demo/peity-demo.js"></script>

<!-- Custom and plugin javascript -->
<!-- Custom and plugin javascript -->
<!--script src="js/inspinia.js"></script-->
<script>
    $(document).ready(function () {

        // Add body-small class if window less than 768px
        if ($(this).width() < 769) {
            $('body').addClass('body-small')
        } else {
            $('body').removeClass('body-small')
        }

        // MetsiMenu
        $('#side-menu').metisMenu();

        // Collapse ibox function
        $('.collapse-link').on('click', function () {
            var ibox = $(this).closest('div.ibox');
            var button = $(this).find('i');
            var content = ibox.find('div.ibox-content');
            content.slideToggle(200);
            button.toggleClass('fa-chevron-up').toggleClass('fa-chevron-down');
            ibox.toggleClass('').toggleClass('border-bottom');
            setTimeout(function () {
                ibox.resize();
                ibox.find('[id^=map-]').resize();
            }, 50);
        });

        // Close ibox function
        $('.close-link').on('click', function () {
            var content = $(this).closest('div.ibox');
            content.remove();
        });

        // Fullscreen ibox function
        $('.fullscreen-link').on('click', function () {
            var ibox = $(this).closest('div.ibox');
            var button = $(this).find('i');
            $('body').toggleClass('fullscreen-ibox-mode');
            button.toggleClass('fa-expand').toggleClass('fa-compress');
            ibox.toggleClass('fullscreen');
            setTimeout(function () {
                $(window).trigger('resize');
            }, 100);
        });
        
        // Close menu in canvas mode
        $('.close-canvas-menu').on('click', function () {
            $("body").toggleClass("mini-navbar");
            SmoothlyMenu();
        });

        // Run menu of canvas
        $('body.canvas-menu .sidebar-collapse').slimScroll({
            height: '100%',
            railOpacity: 0.9
        });

        // Open close right sidebar
        $('.right-sidebar-toggle').on('click', function () {
            $('#right-sidebar').toggleClass('sidebar-open');
        });

        // Initialize slimscroll for right sidebar
        $('.sidebar-container').slimScroll({
            height: '100%',
            railOpacity: 0.4,
            wheelStep: 10
        });

        // Open close small chat
        $('.open-small-chat').on('click', function () {
            $(this).children().toggleClass('fa-comments').toggleClass('fa-remove');
            $('.small-chat-box').toggleClass('active');
        });

        // Initialize slimscroll for small chat
        $('.small-chat-box .content').slimScroll({
            height: '234px',
            railOpacity: 0.4
        });

        // Small todo handler
        $('.check-link').on('click', function () {
            var button = $(this).find('i');
            var label = $(this).next('span');
            button.toggleClass('fa-check-square').toggleClass('fa-square-o');
            label.toggleClass('todo-completed');
            return false;
        });


        // Append config box / Only for demo purpose
        // Uncomment on server mode to enable XHR calls
        //$.get("skin-config.html", function (data) {
        //    if (!$('body').hasClass('no-skin-config'))
        //        $('body').append(data);
        //});

        // Minimalize menu
        $('.navbar-minimalize').on('click', function () {
            $("body").toggleClass("mini-navbar");
            SmoothlyMenu();

        });

        // Tooltips demo
        $('.tooltip-demo').tooltip({
            selector: "[data-toggle=tooltip]",
            container: "body"
        });

        // Full height of sidebar
        function fix_height() {
            var heightWithoutNavbar = $("body > #wrapper").height() - 61;
            $(".sidebard-panel").css("min-height", heightWithoutNavbar + "px");

            var navbarHeigh = $('nav.navbar-default').height();
            var wrapperHeigh = $('#page-wrapper').height();

            if (navbarHeigh > wrapperHeigh) {
                $('#page-wrapper').css("min-height", navbarHeigh + "px");
            }

            if (navbarHeigh < wrapperHeigh) {
                $('#page-wrapper').css("min-height", $(window).height() + "px");
            }

            if ($('body').hasClass('fixed-nav')) {
                if (navbarHeigh > wrapperHeigh) {
                    $('#page-wrapper').css("min-height", navbarHeigh + "px");
                } else {
                    $('#page-wrapper').css("min-height", $(window).height() - 60 + "px");
                }
            }

        }

        fix_height();

        // Fixed Sidebar
        $(window).bind("load", function () {
            if ($("body").hasClass('fixed-sidebar')) {
                $('.sidebar-collapse').slimScroll({
                    height: '100%',
                    railOpacity: 0.9
                });
            }
        });

        // Move right sidebar top after scroll
        $(window).scroll(function () {
            if ($(window).scrollTop() > 0 && !$('body').hasClass('fixed-nav')) {
                $('#right-sidebar').addClass('sidebar-top');
            } else {
                $('#right-sidebar').removeClass('sidebar-top');
            }
        });

        $(window).bind("load resize scroll", function () {
            if (!$("body").hasClass('body-small')) {
                fix_height();
            }
        });

        $("[data-toggle=popover]")
        .popover();

        // Add slimscroll to element
        $('.full-height-scroll').slimscroll({
            height: '100%'
        })
    });

    // Minimalize menu when screen is less than 768px
    $(window).bind("resize", function () {
        if ($(this).width() < 769) {
            $('body').addClass('body-small')
        } else {
            $('body').removeClass('body-small')
        }
    });

    // Local Storage functions
    // Set proper body class and plugins based on user configuration
    $(document).ready(function () {
        if (localStorageSupport()) {

            var collapse = localStorage.getItem("collapse_menu");
            var fixedsidebar = localStorage.getItem("fixedsidebar");
            var fixednavbar = localStorage.getItem("fixednavbar");
            var boxedlayout = localStorage.getItem("boxedlayout");
            var fixedfooter = localStorage.getItem("fixedfooter");

            var body = $('body');

            if (fixedsidebar == 'on') {
                body.addClass('fixed-sidebar');
                $('.sidebar-collapse').slimScroll({
                    height: '100%',
                    railOpacity: 0.9
                });
            }

            if (collapse == 'on') {
                if (body.hasClass('fixed-sidebar')) {
                    if (!body.hasClass('body-small')) {
                        body.addClass('mini-navbar');
                    }
                } else {
                    if (!body.hasClass('body-small')) {
                        body.addClass('mini-navbar');
                    }

                }
            }

            if (fixednavbar == 'on') {
                $(".navbar-static-top").removeClass('navbar-static-top').addClass('navbar-fixed-top');
                body.addClass('fixed-nav');
            }

            if (boxedlayout == 'on') {
                body.addClass('boxed-layout');
            }

            if (fixedfooter == 'on') {
                $(".footer").addClass('fixed');
            }
        }
    });

    // check if browser support HTML5 local storage
    function localStorageSupport() {
        return (('localStorage' in window) && window['localStorage'] !== null)
    }

    // For demo purpose - animation css script
    function animationHover(element, animation) {
        element = $(element);
        element.hover(
            function () {
                element.addClass('animated ' + animation);
            },
            function () {
                //wait for animation to finish before removing classes
                window.setTimeout(function () {
                    element.removeClass('animated ' + animation);
                }, 2000);
            });
    }

    function SmoothlyMenu() {
        if (!$('body').hasClass('mini-navbar') || $('body').hasClass('body-small')) {
            // Hide menu in order to smoothly turn on when maximize menu
            $('#side-menu').hide();
            // For smoothly turn on menu
            setTimeout(
                function () {
                    $('#side-menu').fadeIn(400);
                }, 200);
        } else if ($('body').hasClass('fixed-sidebar')) {
            $('#side-menu').hide();
            setTimeout(
                function () {
                    $('#side-menu').fadeIn(400);
                }, 100);
        } else {
            // Remove all inline style from jquery fadeIn function to reset menu state
            $('#side-menu').removeAttr('style');
        }
    }

    // Dragable panels
    function WinMove() {
        var element = "[class*=col]";
        var handle = ".ibox-title";
        var connect = "[class*=col]";
        $(element).sortable(
        {
            handle: handle,
            connectWith: connect,
            tolerance: 'pointer',
            forcePlaceholderSize: true,
            opacity: 0.8
        })
        .disableSelection();
    }
</script>

<script src="js/plugins/pace/pace.min.js"></script>
<script>window.print()</script>

</body>
</html>
