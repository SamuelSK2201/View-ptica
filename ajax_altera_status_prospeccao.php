<?php
session_start();
include("s_acessos.php");
include("funcoes.php");

$idprospecao = anti_injection($_GET['idprospecao']);
$idagendamento = anti_injection($_GET['idagendamento']);
$status = anti_injection($_GET['status']);

$strU = "SELECT A.codigo AS idusuario, A.idempresa, A.nome AS usuario, A.email, A.perfil, C.ind_pesquisa, C.imagem, B.*
    FROM usuarios A
    INNER JOIN usuarios_tipos B ON A.idtipo = B.codigo AND A.idempresa = B.idempresa
    INNER JOIN empresas C ON A.idempresa = C.codigo
    WHERE A.codigo = '".$_SESSION["adm_codigo"]."'";
$rsU  = mysqli_query($conexao, $strU) or die(mysqli_error($conexao));
$numU = mysqli_num_rows($rsU);
$vetU = mysqli_fetch_array($rsU);

$strP = "SELECT * FROM prospeccao WHERE codigo = '$idprospecao'";
$rsP  = mysqli_query($conexao, $strP) or die(mysqli_error($conexao));
$vetP = mysqli_fetch_array($rsP);

$adm_perfil = $vetU["perfil"];

if($status == 1 || $status == 7)
{
	$s = 7;
	$str_status = 'Aguardando';

	if($status == 7)
	{
		$s = 2;
		$str_status = 'Reagendar';
	}

	$str = "UPDATE prospeccao SET status = '$status' WHERE codigo = '$idprospecao'";
	$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

	$str = "UPDATE agendamentos SET status = '$s' WHERE codigo = '$idagendamento'";
	$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

	echo '<span class="label label-warning">'.$str_status.'</span><br>';
	echo '<a class="btn btn-info btn-circle" type="button" title="transformar em CONFIRMADO" onclick="javascript: altera_status_prospeccao('.$idprospecao.', '.$idagendamento.', 2)"><i class="fa fa-check-square"></i></a>';
}
elseif($status == 2)
{
	$str = "UPDATE prospeccao SET status = '2' WHERE codigo = '$idprospecao'";
	$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

	$str = "UPDATE agendamentos SET status = '3' WHERE codigo = '$idagendamento'";
	$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

	echo '<span class="label label-primary">Confirmado</span><br>';

	if($adm_perfil != 4)
	{
		if(!$vetP['auto'])
		{
			echo '<a class="btn btn-default btn-circle" style="margin-right:3px" type="button" title="desfazer CONFIRMADO" onclick="javascript: altera_status_prospeccao('.$idprospecao.', '.$idagendamento.', 2)"><i class="fa fa-reply"></i></a>';
			echo '<a class="btn btn-info btn-circle" type="button" title="transformar em FILA DE ESPERA" href="prospeccao.php?cmd=fila&codigo='.$idprospecao.'&idagendamento='.$idagendamento.'"><i class="fa fa-check-square"></i></a>';
		}
		else
		{
			echo '<a class="btn btn-default btn-circle" style="margin-right:3px" type="button" title="desfazer CONFIRMADO" onclick="javascript: altera_status_prospeccao('.$idprospecao.', '.$idagendamento.', 2)"><i class="fa fa-reply"></i></a>';
			echo '<a class="btn btn-warning btn-circle" type="button" title="transformar em FILA DE ESPERA" href="r_aguardando.php?cmd=fila&codigo='.$idprospecao.'&idagendamento='.$idagendamento.'"><i class="fa fa-check-square"></i></a>';
			echo '<a class="btn btn-primary btn-circle" style="margin-right:3px" type="button" title="transformar em REAGENDAR" onclick="javascript: altera_status_prospeccao('.$idprospecao.', '.$idagendamento.', 7)"><i class="fa fa-check-square"></i></a>';
			echo '<a class="btn btn-info btn-circle" style="margin-right:3px" type="button" title="transformar em ATENDIDO" onclick="javascript: altera_status_prospeccao('.$idprospecao.', '.$idagendamento.', 8)"><i class="fa fa-check-square"></i></a>';
		}
	}
	else
	{
		echo '';
	}
}
elseif($status == 3 && $adm_perfil != 4)
{
	$str = "UPDATE prospeccao SET status = '3' WHERE codigo = '$idprospecao'";
	$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

	$str = "UPDATE agendamentos SET status = '4' WHERE codigo = '$idagendamento'";
	$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

	echo '<span class="label label-success">Fila de espera</span><br>';
	echo '<a class="btn btn-default btn-circle" style="margin-right:3px" type="button" title="desfazer FILA DE ESPERA" onclick="javascript: altera_status_prospeccao('.$idprospecao.', '.$idagendamento.', 2)"><i class="fa fa-reply"></i></a>';
	echo '<a class="btn btn-info btn-circle" style="margin-right:3px" type="button" title="transformar em ATENDIDO" onclick="javascript: altera_status_prospeccao('.$idprospecao.', '.$idagendamento.', 8)"><i class="fa fa-check-square"></i></a>';
	echo '<a class="btn btn-default btn-circle" style="background-color: #F3F781;" type="button" title="Detalhes do agendamento" data-toggle="modal" data-target="#fila_<?=$idprospecao?>"><i class="fa fa-list"></i></a>';
}
elseif($status == 8 && $adm_perfil != 4)
{
	$str = "UPDATE prospeccao SET status = '8' WHERE codigo = '$idprospecao'";
	$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

	$str = "UPDATE agendamentos SET status = '6' WHERE codigo = '$idagendamento'";
	$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

	echo '<span class="label label-default" style="background-color: #9F81F7; color:#fff">Atendido</span><br>';
	echo '<a class="btn btn-default btn-circle" style="margin-right:3px" type="button" title="desfazer ATENDIDO" onclick="javascript: altera_status_prospeccao('.$idprospecao.', '.$idagendamento.', 3)"><i class="fa fa-reply"></i></a>';
	echo '<a class="btn btn-info btn-circle" style="margin-right:3px" type="button" title="transformar em RESERVADO" onclick="javascript: altera_status_prospeccao('.$idprospecao.', '.$idagendamento.', 4)"><i class="fa fa-check-square"></i></a>';
}
elseif($status == 4 && $adm_perfil != 4)
{
	$str = "UPDATE prospeccao SET status = '4' WHERE codigo = '$idprospecao'";
	$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

	echo '<span class="label label-danger">Reservado</span><br>';
	echo "<a class='btn btn-default btn-circle' type='button' title='desfazer RESERVADO' onclick='javascript: altera_status_prospeccao(".$idprospecao.", ".$idagendamento.", 3)''><i class='fa fa-reply'></i></a>";
	echo "<a class='btn btn-primary btn-circle' type='button' title='transformar em FINALIZADO' onclick='javascript: altera_status_prospeccao(".$idprospecao.", ".$idagendamento.", 5)''><i class='fa fa-check-square'></i></a>";
}
elseif($status == 5 && $adm_perfil != 4)
{
	$str = "UPDATE prospeccao SET status = '5' WHERE codigo = '$idprospecao'";
	$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

	echo '<span class="label label-danger" style="background-color: #585858; color: #fff;">Finalizado</span><br>';
	echo "<a class='btn btn-default btn-circle' type='button' title='desfazer FINALIZADO' onclick='javascript: altera_status_prospeccao(".$idprospecao.", ".$idagendamento.", 4)''><i class='fa fa-reply'></i></a>";
}