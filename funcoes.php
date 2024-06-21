<?
// Reporta erros simples
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', '1');

$datahj 	= date("Y-m-d");
$datahora 	= date("Y-m-d H:i:s");

$datahj_mktime = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

$diab = date("d");
$mesb = date("m");
$anob = date("Y");

$ip = getenv("REMOTE_ADDR");

function msg($string)
{
	print("<script>alert(unescape(\"$string\"));</script>");
}

function redireciona($string)
{
	print ("<script language='JavaScript'>self.location.href=\"$string\";</script>");
	die;
}

function window_open($url)
{
	print ("<script language='JavaScript'>window.open('".$url."', 'Impressao', 'width=1280, height=1024, left=100, top=100, scrollbars=yes');</script>");
	die;
}

function voltar()
{
	print("<script>javascript:history.go(-1)</script>");
}

function fechar()
{
	print("<script>window.close();</script>");
}

function reload()
{
	print("<script>window.opener.location.reload();</script>");
}

function formatar_string($str) 
{
    // Remover caracteres especiais, cedilha e acentos
    $str = preg_replace('/[^A-Za-z0-9]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str));
    
    // Converter para minúsculas
    $str = strtolower($str);
        
    return $str;
}

function ConverteData($Data)
{
	//verifica se tem a barra
	if(strstr($Data, "/") == TRUE)
	{
		$d = explode ("/", $Data);
		$rstData = "$d[2]-$d[1]-$d[0]";
		return $rstData;
	} 
	elseif(strstr($Data, "-") == TRUE)
	{
		$d = explode ("-", $Data);
		$rstData = "$d[2]/$d[1]/$d[0]"; 
		return $rstData;
	}
	else
	{
		return "Data invalida";
	}
}

function anti_injection($sql)
{
    // remove palavras que contenham sintaxe sql
    $sql = preg_replace(preg_quote("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"),"",$sql);
    $sql = trim($sql);//limpa espaços vazio
    $sql = strip_tags($sql);//tira tags html e php
    $sql = addslashes($sql);//Adiciona barras invertidas a uma string
    return $sql;
}

function mes_extenso($numMes)
{
    /* guardando o nome do mes */
    switch ($numMes)
    {
        case "01":
            $strMes = 'Janeiro';
            break;
        case "02":
            $strMes = 'Fevereiro';
            break;
        case "03":
            $strMes = 'Março';
            break;
        case "04":
            $strMes = 'Abril';
            break;
        case "05":
            $strMes = 'Maio';
            break;
        case "06":
            $strMes = 'Junho';
            break;
        case "07":
            $strMes = 'Julho';
            break;
        case "08":
            $strMes = 'Agosto';
            break;
        case "09":
            $strMes = 'Setembro';
            break;
        case "10":
            $strMes = 'Outubro';
            break;
        case "11":
            $strMes = 'Novembro';
            break;
        case "12":
            $strMes = 'Dezembro';
            break;
        default:
        
            $strMes = '';
            break;
    }
    
    return $strMes;
}

function ultimo_dia_mes($ano, $mes)
{
	if (((fmod($ano, 4) == 0) and (fmod($ano, 100) != 0)) or (fmod($ano, 400) == 0)) 
	{
		$dias_fevereiro = 29;
	} 
	else 
	{
		$dias_fevereiro = 28;
	}
	
	switch($mes) 
	{
		case "1": return 31; break;
		case "2": return $dias_fevereiro; break;
		case "3": return 31; break;
		case "4": return 30; break;
		case "5": return 31; break;
		case "6": return 30; break;
		case "7": return 31; break;
		case "8": return 31; break;
		case "9": return 30; break;
		case "10": return 31; break;
		case "11": return 30; break;
		case "12": return 31; break;
	}
}

function soma_horas($array)
{
    foreach ($array as $time)
    {
        list($g, $i) = explode(':', $time);
        $seconds += $g * 3600;
        $seconds += $i * 60;
    }

    $hours = floor($seconds / 3600);
    $seconds -= $hours * 3600;
    $minutes = floor($seconds / 60);
    $minutes = str_pad($minutes, 2, "0", STR_PAD_LEFT);

    return "{$hours}:{$minutes}";
}

function calculo_data($data1, $data2)
{
	//calculo timestam das duas datas 
	$timestamp1 = mktime(0, 0, 0, substr($data1, 5, 2), substr($data1, 8, 2), substr($data1, 0, 4));
	$timestamp2 = mktime(0, 0, 0, substr($data2, 5, 2), substr($data2, 8, 2), substr($data2, 0, 4));
	
	//diminuo a uma data a outra
	$segundos_diferenca = $timestamp2 - $timestamp1;
	
	//converto segundos em dias
	$dias_diferenca = $segundos_diferenca / 86400;
	
	//obtenho o valor absoulto dos dias (tiro o possível sinal negativo)
	//$dias_diferenca = abs($dias_diferenca);
	
	//tiro os decimais aos dias de diferença
	$dias_diferenca = ceil($dias_diferenca);
	
	return $dias_diferenca; 
}

function retorna_dias($data1, $data2)
{
	$ano1 = substr($data1, 0, 4);
	$mes1 = substr($data1, 5, 2);
	$dia1 = substr($data1, 8, 2);
	
	$dias_diferenca = calculo_data($data1, $data2);
	
	for($i = 0; $i <= $dias_diferenca; $i++)
	{	
		$data = date("Y-m-d", mktime(0, 0, 0, $mes1, $dia1 + $i, $ano1));
		
		$vet[$i] = $data;
	}
	
	return $vet;
}

function retorna_meses($data1, $data2)
{
    $diferenca = strtotime($data2) - strtotime($data1); 
    $meses = floor($diferenca / (60 * 60 * 24 * 30));
    
    return $meses;
}

function retorna_anos($data1, $data2)
{
    $date = new DateTime($data1);
    $interval = $date->diff(new DateTime($data2));
    $anos = $interval->format('%Y');

    return $anos;
}

function tempo_post($data_post)
{
    $datahj = date("Y-m-d H:i:s");

    $date_time = new DateTime($datahj);
    $diff = $date_time->diff( new DateTime($data_post));

    $tempo = $diff->format('%y,%m,%d,%H,%i,%s'); 
    $array_tempo = explode(',', $tempo);

    //print_r($array_tempo);

    if($array_tempo[0] > 0)
    {
        if($array_tempo[0] == 1)
            $str = (int) $array_tempo[0].' ano';
        else
            $str = (int) $array_tempo[0].' anos';
    }
    elseif($array_tempo[1] > 0)
    {
        if($array_tempo[1] == 1)
            $str = (int) $array_tempo[1].utf8_decode(' mês');
        else
            $str = (int) $array_tempo[1].' meses';
    }
    elseif($array_tempo[2] > 0)
    {
        if($array_tempo[2] == 1)
            $str = (int) $array_tempo[2].' dia';
        else
            $str = (int) $array_tempo[2].' dias';
    }
    elseif($array_tempo[3] > 0)
    {
        if($array_tempo[3] == 1)
            $str = (int) $array_tempo[3].' hora';
        else
            $str = (int) $array_tempo[3].' horas';
    }
    elseif($array_tempo[4] > 0)
    {
        if($array_tempo[4] == 1)
            $str = (int) $array_tempo[4].' minuto';
        else
            $str = (int) $array_tempo[4].' minutos';
    }
    elseif($array_tempo[5] > 0)
    {
        if($array_tempo[5] == 1)
            $str = (int) $array_tempo[5].' segundo';
        else
            $str = (int) $array_tempo[5].' segundos';
    }

    return utf8_encode($str);

    // 27 year(s), 4 month(s), 13 day(s), 23 hour(s), 50 minute(s) and 40 second(s)
}

function redimensiona_imagem($imagem, $altura, $largura)
{
	//REDIMENSIONANDO AS IMAGENS
    $foto = 'upload/'.$imagem;

    $tamMax = array($altura, $largura);

    // Comprime imagem
    // 0 => Sem comprimir
    // 100 => Melhor compreção
    $comprimi = 100;

    //0 => largura
    //1 => Altura
    //2 => Formato da imagem
    list($imgLarg, $imgAlt, $imgTipo) = getimagesize($foto);

    //verifica se a imagem é maior que o máximo permitido
    if($imgLarg > $tamMax[0] || $imgAlt > $tamMax[1])
    {
        //verifica se a largura é maior que a altura
        if($imgLarg > $imgAlt)
        {
            $novaLargura = $tamMax[0];
            $novaAltura = round(($novaLargura / $imgLarg) * $imgAlt);
        }
        //se a altura for maior que a largura
        elseif($imgAlt > $imgLarg)
        {
            $novaAltura = $tamMax[1];
            $novaLargura = round(($novaAltura / $imgAlt) * $imgLarg);
        }
        //altura == largura
        else
        {
            $novaAltura = $novaLargura = max($tamMax);
        }
    }

    // Cria a imagem baseada na imagem original
    switch ($imgTipo){
        case 1: 
        $srcImg = imagecreatefromgif($foto); 
        break;

        case 2: 
        $srcImg = imagecreatefromjpeg($foto);
        break;
        
        case 3: 
        $srcImg = imagecreatefrompng($foto);
        break;
        
        default: 
        return ''; 
        break;
    }

    // cria a nova imagem
    $destImg = imagecreatetruecolor($novaLargura, $novaAltura);

    // copia para a imagem de destino a imagem original redimensionada
    imagecopyresampled($destImg, $srcImg, 0, 0, 0, 0, $novaLargura, $novaAltura, $imgLarg, $imgAlt);

    // Sava a imagem
    switch ($imgTipo){
        case 1: 
        imagegif($destImg,'upload/'.$imagem, NULL, $comprimi); 
        break;
        case 2: 
        imagejpeg($destImg,'upload/'.$imagem, $comprimi); 
        break; 
        case 3: 
        imagepng($destImg,'upload/'.$imagem, NULL, $comprimi);
        break;
        
        default: echo '';
        break;
    }
}

function menu_class_ativo_ul($pagina_atual, $nome_pagina)
{
    if(strstr($pagina_atual, $nome_pagina))
        return 'in';

    return '';
}

function menu_class_ativo_li($pagina_atual, $nome_pagina, $ind)
{
    $array_pagina_atual = explode("/", $pagina_atual);
    $array_nome_pagina = explode(".", $array_pagina_atual[2]);

    if(!$ind && strstr($pagina_atual, $nome_pagina))
        return 'class="active"';
    elseif($ind == 1 && $array_nome_pagina[0] == $nome_pagina)
        return 'class="active"';

    return '';
}

function mask($val, $mask)
{
    $maskared = '';
    $k = 0;

    for($i = 0; $i<=strlen($mask)-1; $i++)
    {
        if($mask[$i] == '#')
        {
            if(isset($val[$k]))
                $maskared .= $val[$k++];
        }
        else
        {
            if(isset($mask[$i]))
                $maskared .= $mask[$i];
        }
    }

    return $maskared;
}

function alfabeto($i)
{
    switch($i)
    {
        case "1": return 'A'; break;
        case "2": return 'B'; break;
        case "3": return 'C'; break;
        case "4": return 'D'; break;
        case "5": return 'E'; break;
        case "6": return 'F'; break;
        case "7": return 'G'; break;
        case "8": return 'H'; break;
        case "9": return 'I'; break;
        case "10": return 'J'; break;
        case "11": return 'K'; break;
        case "12": return 'L'; break;
        case "13": return 'M'; break;
        case "14": return 'N'; break;
        case "15": return 'O'; break;
        case "16": return 'P'; break;
        case "17": return 'Q'; break;
        case "18": return 'R'; break;
        case "19": return 'S'; break;
        case "20": return 'T'; break;
        case "21": return 'U'; break;
        case "22": return 'V'; break;
        case "23": return 'X'; break;
        case "24": return 'Z'; break;
        default: '';
    }
}

function empresas_usuarios($conexao, $idempresa)
{
    $str = "SELECT codigo FROM usuarios WHERE idempresa = '$idempresa'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    return $num;
}

function paciente_endereco($conexao, $idpaciente)
{
    $str = "SELECT * FROM pacientes WHERE codigo = '$idpaciente'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $vet = mysqli_fetch_array($rs);

    $endereco = '';

    if($vet['endereco'])
        $endereco .= stripslashes($vet['endereco']);

    if($vet['numero'])
        $endereco .= ', N: '.$vet['numero'];

    if($vet['complemento'])
        $endereco .= ', '.stripslashes($vet['complemento']);

    if($vet['bairro'])
        $endereco .= ' - '.stripslashes($vet['bairro']);

    if($vet['cidade'])
        $endereco .= ' - '.stripslashes($vet['cidade']);

    if($vet['estado'])
        $endereco .= ' / '.stripslashes($vet['estado']);

    if($vet['cep'])
        $endereco .= '<br>CEP: '.$vet['cep'];

    return $endereco;
}

function qtde_pacientes($conexao, $idempresa)
{
    $str = "SELECT codigo FROM pacientes WHERE idempresa = '$idempresa'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    return $num;
}

function qtde_consultas_hoje($conexao, $idempresa, $adm_perfil)
{
    $strWhereP = "";
    if($adm_perfil == 2 || $adm_perfil == 5)
        $strWhereP = " AND idoptometrista = '$adm_codigo'";

    $str = "SELECT codigo FROM agendamentos WHERE idempresa = '$idempresa' AND data = CURDATE() $strWhereP ORDER BY data";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    return $num;
}

function qtde_consultas_realizadas_hoje($conexao, $idempresa, $adm_perfil)
{
    $strWhereP = "";
    if($adm_perfil == 2 || $adm_perfil == 5)
        $strWhereP = " AND idoptometrista = '$adm_codigo'";

    $str = "SELECT codigo FROM agendamentos WHERE idempresa = '$idempresa' AND data = CURDATE() AND status = '6' $strWhereP ORDER BY data";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    return $num;
}

function qtde_consultas_realizadas_mes($conexao, $idempresa, $adm_perfil)
{
    $strWhereP = "";
    if($adm_perfil == 2 || $adm_perfil == 5)
        $strWhereP = " AND idoptometrista = '$adm_codigo'";

    $mes = date("m");

    $str = "SELECT codigo FROM agendamentos WHERE idempresa = '$idempresa' AND MONTH(data) = '$mes' AND status = '6' $strWhereP ORDER BY data";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    return $num;
}

function valor_total_hoje($conexao, $idempresa, $adm_perfil)
{
    $strWhereP = "";
    if($adm_perfil == 2 || $adm_perfil == 5)
        $strWhereP = " AND idoptometrista = '$adm_codigo'";

    $str = "SELECT SUM(valor) AS total FROM agendamentos WHERE idempresa = '$idempresa' AND data = CURDATE() AND status = '6' AND valor > '0' $strWhereP";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $vet = mysqli_fetch_array($rs);

    return 'R$ '.number_format($vet['total'], 2, ',', '.');
}

function valor_total_mes($conexao, $idempresa, $adm_perfil)
{
    $strWhereP = "";
    if($adm_perfil == 2 || $adm_perfil == 5)
        $strWhereP = " AND idoptometrista = '$adm_codigo'";
    
    $mes = date("m");
    
    $str = "SELECT SUM(valor) AS total FROM agendamentos WHERE idempresa = '$idempresa' AND MONTH(data) = '$mes' AND status = '6' AND valor > '0' $strWhereP";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $vet = mysqli_fetch_array($rs);

    return 'R$ '.number_format($vet['total'], 2, ',', '.');
}

function registra_baixa_estoque_lentes($conexao, $idestoque, $esf, $cil, $ind_table, $lente, $idpedido, $idempresa)
{
    $table = 'estoques_grade_positiva';
    if($ind_table == 2)
        $table = 'estoques_grade_negativa';

    echo $strG = "SELECT codigo, qtde FROM $table WHERE idestoque = '$idestoque' AND esf = '$esf' AND cil = '$cil'";
    $rsG  = mysqli_query($conexao, $strG) or die(mysqli_error($conexao));
    $vetG = mysqli_fetch_array($rsG);
    echo '<br>';

    echo $vetG['qtde'].'<br>';
    if($vetG['qtde'] > 0)
    {
        echo $strG1 = "UPDATE $table SET qtde = qtde - 1 WHERE idestoque = '$idestoque' AND esf = '$esf' AND cil = '$cil'";
        $rsG1  = mysqli_query($conexao, $strG1) or die(mysqli_error($conexao));
        echo '<br>';

        printf("Affected rows (UPDATE1): %d\n", mysqli_affected_rows($conexao));
        echo '<br>';

        sleep(1);

        echo $strG = "SELECT codigo, qtde FROM $table WHERE idestoque = '$idestoque' AND esf = '$esf' AND cil = '$cil'";
        $rsG  = mysqli_query($conexao, $strG) or die(mysqli_error($conexao));
        $vetG = mysqli_fetch_array($rsG);
        echo '<br>';

        echo $vetG['qtde'].'<br>';

        echo $strE = "SELECT codigo FROM $table WHERE idestoque = '$idestoque' AND esf = '$esf' AND cil = '$cil' AND qtde = '".$vetG['qtde']."'";
        $rsE  = mysqli_query($conexao, $strE) or die(mysqli_error($conexao));
        $numE = mysqli_num_rows($rsE);
        echo '<br>';

        if(!$numE)
        {
            echo $strU = "UPDATE $table SET qtde = '".$vetG['qtde']."' WHERE idestoque = '$idestoque' AND esf = '$esf' AND cil = '$cil'";
            $rsU  = mysqli_query($conexao, $strU) or die(mysqli_error($conexao));
            echo '<br>';
        }
    }
    else
    {
        registro_gerar_compra($conexao, $lente, $idpedido, $idempresa, 2);
    }
}

function registro_gerar_compra($conexao, $lente, $idpedido, $idempresa, $ind)
{
    $array_lente = explode("#", $lente);

    $nome_lente = $array_lente[0];
    $id_lente = $array_lente[2];

    echo 'LENTE - '.$lente.'<br>';
    echo 'GEROU COMPRA - '.$nome_lente.' - '.$id_lente.'<br>';

    $str = "SELECT * FROM tipos_lentes WHERE codigo = '$id_lente'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $vet = mysqli_fetch_array($rs);

    $idlaboratorio = $vet['idlaboratorio'];

    echo $strL = "SELECT codigo FROM tipos_lentes WHERE lentes LIKE '%|$id_lente|%'";
    $rsL  = mysqli_query($conexao, $strL) or die(mysqli_error($conexao));
    $numL = mysqli_num_rows($rsL);
    echo '<br>';

    echo $ind.' - '.$numL.'<br>';

    if($ind == 1 || $ind == 2 || $numL > 0)
    {
        echo $str = "INSERT INTO pedidos_compras (idempresa, idlente, idlaboratorio, idpedido) VALUES ('$idempresa', '$id_lente', '$idlaboratorio', '$idpedido')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        echo '<br>';
    }
}
?>