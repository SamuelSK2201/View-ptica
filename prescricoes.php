<?
$page = 'consultas_espera';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_consultas != 1)
    die("Acesso negado!");

$idprescricao = anti_injection($_REQUEST['idprescricao']);
$idagendamento = anti_injection($_REQUEST['idagendamento']);
$status = anti_injection($_REQUEST['s']);
$data_prescricao = anti_injection($_REQUEST['data_prescricao']);

//print_r($_POST);
//echo '<br>';

if($_POST['cmd'] == 'save_oculos')
{
    $tipo = $_POST['tipo'];
    $oculos_od_esf = $_POST['oculos_od_esf'];
    $oculos_od_cil = $_POST['oculos_od_cil'];
    $oculos_od_eixo = $_POST['oculos_od_eixo'];
    $oculos_od_av = $_POST['oculos_od_av'];
    $oculos_oe_esf = $_POST['oculos_oe_esf'];
    $oculos_oe_cil = $_POST['oculos_oe_cil'];
    $oculos_oe_eixo = $_POST['oculos_oe_eixo'];
    $oculos_oe_av = $_POST['oculos_oe_av'];
    $oculos_adicao = $_POST['oculos_adicao'];
    $oculos_lente = $_POST['oculos_lente'];
    $oculos_observacao = addslashes($_POST['oculos_observacao']);

    $str = "UPDATE agendamentos SET status = '6' WHERE codigo = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $status = 6;

    if($tipo == 1)
    {
        $str = "UPDATE prospeccao SET status = '8' WHERE idagendamento = '$idagendamento'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    $str = "SELECT * FROM prescricoes_oculos WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if(!$num)
    {
        $str = "INSERT INTO prescricoes (idempresa, idagendamento, tipo, data) VALUES ('$adm_empresa', '$idagendamento', '1', NOW())";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        $idprescricao = mysqli_insert_id($conexao);

        $str = "INSERT INTO prescricoes_oculos (idempresa, idagendamento, idprescricao, oculos_od_esf, oculos_od_cil, oculos_od_eixo, oculos_od_av, oculos_oe_esf, oculos_oe_cil, oculos_oe_eixo, oculos_oe_av, oculos_adicao, oculos_lente, oculos_observacao) 
            VALUES ('$adm_empresa', '$idagendamento', '$idprescricao', '$oculos_od_esf', '$oculos_od_cil', '$oculos_od_eixo', '$oculos_od_av', '$oculos_oe_esf', '$oculos_oe_cil', '$oculos_oe_eixo', '$oculos_oe_av', '$oculos_adicao', '$oculos_lente', '$oculos_observacao')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    else
    {
        $str = "UPDATE prescricoes_oculos 
            SET oculos_od_esf = '$oculos_od_esf', oculos_od_cil = '$oculos_od_cil', oculos_od_eixo = '$oculos_od_eixo', oculos_od_av = '$oculos_od_av', 
            oculos_oe_esf = '$oculos_oe_esf', oculos_oe_cil = '$oculos_oe_cil', oculos_oe_eixo = '$oculos_oe_eixo', oculos_oe_av = '$oculos_oe_av', 
            oculos_adicao = '$oculos_adicao', oculos_lente = '$oculos_lente', oculos_observacao = '$oculos_observacao' 
            WHERE idempresa = '$adm_empresa' 
            AND idagendamento = '$idagendamento'
            AND idprescricao = '$idprescricao'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    if($data_prescricao)
    {
        $array = explode(" ", $data_prescricao);
        $dt_prescricao = ConverteData($array[0]).' '.$array[1].':00';

        $str = "UPDATE prescricoes SET data = '$dt_prescricao' WHERE codigo = '$idprescricao' AND idempresa = '$adm_empresa'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    if($tipo == 1)
        redireciona("prescricoes.php?idagendamento=$idagendamento&s=$status&ind_msg=1");
    else
        redireciona("r_finalizados.php");
}

if($_POST['cmd'] == 'save_lentes')
{
    $lentes_od_esf = $_POST['lentes_od_esf'];
    $lentes_od_cil = $_POST['lentes_od_cil'];
    $lentes_od_eixo = $_POST['lentes_od_eixo'];
    $lentes_od_av = $_POST['lentes_od_av'];
    $lentes_oe_esf = $_POST['lentes_oe_esf'];
    $lentes_oe_cil = $_POST['lentes_oe_cil'];
    $lentes_oe_eixo = $_POST['lentes_oe_eixo'];
    $lentes_oe_av = $_POST['lentes_oe_av'];
    $lentes_lente = $_POST['lentes_lente'];
    $lentes_observacao = addslashes($_POST['lentes_observacao']);

    $str = "UPDATE agendamentos SET status = '6' WHERE codigo = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $status = 6;

    $str = "UPDATE prospeccao SET status = '8' WHERE idagendamento = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "SELECT * FROM prescricoes_lentes WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if(!$num)
    {
        $str = "INSERT INTO prescricoes (idempresa, idagendamento, tipo, data) VALUES ('$adm_empresa', '$idagendamento', '2', NOW())";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        $idprescricao = mysqli_insert_id($conexao);

        $str = "INSERT INTO prescricoes_lentes (idempresa, idagendamento, idprescricao, lentes_od_esf, lentes_od_cil, lentes_od_eixo, lentes_od_av, lentes_oe_esf, lentes_oe_cil, lentes_oe_eixo, lentes_oe_av, lentes_lente, lentes_observacao) 
            VALUES ('$adm_empresa', '$idagendamento', '$idprescricao', '$lentes_od_esf', '$lentes_od_cil', '$lentes_od_eixo', '$lentes_od_av', '$lentes_oe_esf', '$lentes_oe_cil', '$lentes_oe_eixo', '$lentes_oe_av', '$lentes_lente', '$lentes_observacao')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    else
    {
        $str = "UPDATE prescricoes_lentes 
            SET lentes_od_esf = '$lentes_od_esf', lentes_od_cil = '$lentes_od_cil', lentes_od_eixo = '$lentes_od_eixo', lentes_od_av = '$lentes_od_av', 
            lentes_oe_esf = '$lentes_oe_esf', lentes_oe_eixo = '$lentes_oe_eixo', lentes_oe_av = '$lentes_oe_av', lentes_lente = '$lentes_lente', 
            lentes_observacao = '$lentes_observacao'
            WHERE idempresa = '$adm_empresa' 
            AND idagendamento = '$idagendamento'
            AND idprescricao = '$idprescricao'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    if($data_prescricao)
    {
        $array = explode(" ", $data_prescricao);
        $dt_prescricao = ConverteData($array[0]).' '.$array[1].':00';

        $str = "UPDATE prescricoes SET data = '$dt_prescricao' WHERE codigo = '$idprescricao' AND idempresa = '$adm_empresa'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    redireciona("prescricoes.php?idagendamento=$idagendamento&s=$status&ind_msg=2");
}

if($_POST['cmd'] == 'save_laudo')
{
    $idmodelo = $_POST['idmodelo'];
    $laudo = addslashes($_POST['laudo']);

    $str = "UPDATE agendamentos SET status = '6' WHERE codigo = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $status = 6;

    $str = "UPDATE prospeccao SET status = '8' WHERE idagendamento = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "SELECT * FROM prescricoes_laudos WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if(!$num)
    {
        $str = "INSERT INTO prescricoes (idempresa, idagendamento, tipo, data) VALUES ('$adm_empresa', '$idagendamento', '3', NOW())";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        $idprescricao = mysqli_insert_id($conexao);

        $str = "INSERT INTO prescricoes_laudos (idempresa, idagendamento, idprescricao, idmodelo, laudo) VALUES ('$adm_empresa', '$idagendamento', '$idprescricao', '$idmodelo', '$laudo')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    else
    {
        $str = "UPDATE prescricoes_laudos SET idmodelo = '$idmodelo', laudo = '$laudo' WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    if($data_prescricao)
    {
        $array = explode(" ", $data_prescricao);
        $dt_prescricao = ConverteData($array[0]).' '.$array[1].':00';

        $str = "UPDATE prescricoes SET data = '$dt_prescricao' WHERE codigo = '$idprescricao' AND idempresa = '$adm_empresa'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    redireciona("prescricoes.php?idagendamento=$idagendamento&s=$status&ind_msg=3");
}

if($_POST['cmd'] == 'save_declaracao')
{
    $idmodelo = $_POST['idmodelo'];
    $declaracao = addslashes($_POST['declaracao']);

    $str = "UPDATE agendamentos SET status = '6' WHERE codigo = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $status = 6;

    $str = "UPDATE prospeccao SET status = '8' WHERE idagendamento = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "SELECT * FROM prescricoes_declaracoes WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if(!$num)
    {
        $str = "INSERT INTO prescricoes (idempresa, idagendamento, tipo, data) VALUES ('$adm_empresa', '$idagendamento', '4', NOW())";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        $idprescricao = mysqli_insert_id($conexao);

        $str = "INSERT INTO prescricoes_declaracoes (idempresa, idagendamento, idprescricao, idmodelo, declaracao) VALUES ('$adm_empresa', '$idagendamento', '$idprescricao', '$idmodelo', '$declaracao')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    else
    {
        $str = "UPDATE prescricoes_declaracoes SET idmodelo = '$idmodelo', declaracao = '$declaracao' WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    if($data_prescricao)
    {
        $array = explode(" ", $data_prescricao);
        $dt_prescricao = ConverteData($array[0]).' '.$array[1].':00';

        $str = "UPDATE prescricoes SET data = '$dt_prescricao' WHERE codigo = '$idprescricao' AND idempresa = '$adm_empresa'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    redireciona("prescricoes.php?idagendamento=$idagendamento&s=$status&ind_msg=4");
}

if($_POST['cmd'] == 'save_encaminhamento')
{
    $idmodelo = $_POST['idmodelo'];
    $encaminhamento = addslashes($_POST['encaminhamento']);

    $str = "UPDATE agendamentos SET status = '6' WHERE codigo = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $status = 6;

    $str = "UPDATE prospeccao SET status = '8' WHERE idagendamento = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "SELECT * FROM prescricoes_encaminhamentos WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if(!$num)
    {
        $str = "INSERT INTO prescricoes (idempresa, idagendamento, tipo, data) VALUES ('$adm_empresa', '$idagendamento', '5', NOW())";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        $idprescricao = mysqli_insert_id($conexao);

        $str = "INSERT INTO prescricoes_encaminhamentos (idempresa, idagendamento, idprescricao, idmodelo, encaminhamento) VALUES ('$adm_empresa', '$idagendamento', '$idprescricao', '$idmodelo', '$encaminhamento')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    else
    {
        $str = "UPDATE prescricoes_encaminhamentos SET idmodelo = '$idmodelo', encaminhamento = '$encaminhamento' WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    if($data_prescricao)
    {
        $array = explode(" ", $data_prescricao);
        $dt_prescricao = ConverteData($array[0]).' '.$array[1].':00';

        $str = "UPDATE prescricoes SET data = '$dt_prescricao' WHERE codigo = '$idprescricao' AND idempresa = '$adm_empresa'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    redireciona("prescricoes.php?idagendamento=$idagendamento&s=$status&ind_msg=5");
}

if($_POST['cmd'] == 'save_anamnese')
{
    $motivo_consulta = addslashes($_POST['motivo_consulta']);
    $ultimo_exame = addslashes($_POST['ultimo_exame']);
    $itens = $_POST['itens'];
    $periodos = $_POST['periodos'];
    $onde = $_POST['onde'];
    $quando = $_POST['quando'];
    $sintomas_relatados = addslashes($_POST['sintomas_relatados']);
    $diabetico_pessoal = $_POST['diabetico_pessoal'];
    $medicamentos_controlados = $_POST['medicamentos_controlados'];
    $pressao_alta_pessoal = $_POST['pressao_alta_pessoal'];
    $substancias_quimicas = $_POST['substancias_quimicas'];
    $fumante = $_POST['fumante'];
    $gravida = $_POST['gravida'];
    $obs_pessoal = addslashes($_POST['obs_pessoal']);
    $diabetico_familiar = $_POST['diabetico_familiar'];
    $pressao_alta_familiar = $_POST['pressao_alta_familiar'];
    $glaucoma = $_POST['glaucoma'];
    $estrabismo = $_POST['estrabismo'];
    $catarata = $_POST['catarata'];
    $oculos = $_POST['oculos'];
    $obs_familiar = addslashes($_POST['obs_familiar']);
    $optotipo = addslashes($_POST['optotipo']);
    $metros = addslashes($_POST['metros']);
    $obs_acuidade = addslashes($_POST['obs_acuidade']);
    $od_esferico = $_POST['od_esferico'];
    $od_cilindrico = $_POST['od_cilindrico'];
    $od_eixo = $_POST['od_eixo'];
    $od_adicao = $_POST['od_adicao'];
    $od_prisma = $_POST['od_prisma'];
    $od_dnpl = $_POST['od_dnpl'];
    $od_dnpp = $_POST['od_dnpp'];
    $od_tipo = $_POST['od_tipo'];
    $oe_esferico = $_POST['oe_esferico'];
    $oe_cilindrico = $_POST['oe_cilindrico'];
    $oe_eixo = $_POST['oe_eixo'];
    $oe_adicao = $_POST['oe_adicao'];
    $oe_prisma = $_POST['oe_prisma'];
    $oe_dnpl = $_POST['oe_dnpl'];
    $oe_dnpp = $_POST['oe_dnpp'];
    $oe_tipo = $_POST['oe_tipo'];
    $od_longe = $_POST['od_longe'];
    $od_perto = $_POST['od_perto'];
    $od_phl = $_POST['od_phl'];
    $od_php = $_POST['od_php'];
    $oe_longe = $_POST['oe_longe'];
    $oe_perto = $_POST['oe_perto'];
    $oe_phl = $_POST['oe_phl'];
    $oe_php = $_POST['oe_php'];
    $bin_longe = $_POST['bin_longe'];
    $bin_perto = $_POST['bin_perto'];
    $bin_phl = $_POST['bin_phl'];
    $bin_php = $_POST['bin_php'];
    $dom_longe = $_POST['dom_longe'];
    $dom_perto = $_POST['dom_perto'];
    $dom_phl = $_POST['dom_phl'];
    $dom_php = $_POST['dom_php'];
    $com_od_longe = $_POST['com_od_longe'];
    $com_od_perto = $_POST['com_od_perto'];
    $com_oe_longe = $_POST['com_oe_longe'];
    $com_oe_perto = $_POST['com_oe_perto'];
    $com_bin_longe = $_POST['com_bin_longe'];
    $com_bin_perto = $_POST['com_bin_perto'];
    $com_dom_longe = $_POST['com_dom_longe'];
    $com_dom_perto = $_POST['com_dom_perto'];
    $od_r_fotomotor = $_POST['od_r_fotomotor'];
    $od_nr_fotomotor = $_POST['od_nr_fotomotor'];
    $oe_r_fotomotor = $_POST['oe_r_fotomotor'];
    $oe_nr_fotomotor = $_POST['oe_nr_fotomotor'];
    $od_r_consensual = $_POST['od_r_consensual'];
    $od_nr_consensual = $_POST['od_nr_consensual'];
    $oe_r_consensual = $_POST['oe_r_consensual'];
    $oe_nr_consensual = $_POST['oe_nr_consensual'];
    $od_r_acomodativo = $_POST['od_r_acomodativo'];
    $od_nr_acomodativo = $_POST['od_nr_acomodativo'];
    $oe_r_acomodativo = $_POST['oe_r_acomodativo'];
    $oe_nr_acomodativo = $_POST['oe_nr_acomodativo'];
    $h_od_15 = $_POST['h_od_15'];
    $h_od_30 = $_POST['h_od_30'];
    $h_od_45 = $_POST['h_od_45'];
    $h_oe_15 = $_POST['h_oe_15'];
    $h_oe_30 = $_POST['h_oe_30'];
    $h_oe_45 = $_POST['h_oe_45'];
    $k_od_k0 = $_POST['k_od_k0'];
    $k_od_kme = $_POST['k_od_kme'];
    $k_od_kma = $_POST['k_od_kma'];
    $k_oe_k0 = $_POST['k_oe_k0'];
    $k_oe_kme = $_POST['k_oe_kme'];
    $k_oe_kma = $_POST['k_oe_kma'];
    $od_suave = $_POST['od_suave'];
    $od_completa = $_POST['od_completa'];
    $od_continua = $_POST['od_continua'];
    $od_limitacao = $_POST['od_limitacao'];
    $oe_suave = $_POST['oe_suave'];
    $oe_completa = $_POST['oe_completa'];
    $oe_continua = $_POST['oe_continua'];
    $oe_limitacao = $_POST['oe_limitacao'];
    $com_orto40 = $_POST['com_orto40'];
    $com_orto20 = $_POST['com_orto20'];
    $com_endo40 = $_POST['com_endo40'];
    $com_endo20 = $_POST['com_endo20'];
    $com_exo40 = $_POST['com_exo40'];
    $com_exo20 = $_POST['com_exo20'];
    $com_hiper40 = $_POST['com_hiper40'];
    $com_hiper20 = $_POST['com_hiper20'];
    $com_hipo40 = $_POST['com_hipo40'];
    $com_hipo20 = $_POST['com_hipo20'];
    $ppc_objeto = addslashes($_POST['ppc_objeto']);
    $ppc_luz = addslashes($_POST['ppc_luz']);
    $ppc_filtro = addslashes($_POST['ppc_filtro']);

    $itens = "";
    $array_itens = $_POST['itens'];

    if(@count($array_itens))
    {
        for($i = 0; $i < @count($array_itens); $i++)
            $itens .= "|".$array_itens[$i];

        $itens .= "|";
    }

    $periodos = "";
    $array_periodos = $_POST['periodos'];

    if(@count($array_periodos))
    {
        for($i = 0; $i < @count($array_periodos); $i++)
            $periodos .= "|".$array_periodos[$i];

        $periodos .= "|";
    }

    $onde = "";
    $array_onde = $_POST['onde'];

    if(@count($array_onde))
    {
        for($i = 0; $i < @count($array_onde); $i++)
            $onde .= "|".$array_onde[$i];

        $onde .= "|";
    }

    $quando = "";
    $array_quando = $_POST['quando'];

    if(@count($array_quando))
    {
        for($i = 0; $i < @count($array_quando); $i++)
            $quando .= "|".$array_quando[$i];

        $quando .= "|";
    }

    $str = "UPDATE agendamentos SET status = '6' WHERE codigo = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $status = 6;

    $str = "UPDATE prospeccao SET status = '8' WHERE idagendamento = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "SELECT * FROM prescricoes_anamnese_optometrica WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if(!$num)
    {
        $str = "INSERT INTO prescricoes (idempresa, idagendamento, tipo, data) VALUES ('$adm_empresa', '$idagendamento', '6', NOW())";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
        $idprescricao = mysqli_insert_id($conexao);

        $str = "INSERT INTO prescricoes_anamnese_optometrica (idempresa, idagendamento, idprescricao, motivo_consulta, ultimo_exame, itens, periodos, onde, quando, sintomas_relatados) 
            VALUES ('$adm_empresa', '$idagendamento', '$idprescricao', '$motivo_consulta', '$ultimo_exame', '$itens', '$periodos', '$onde', '$quando', '$sintomas_relatados')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    else
    {
        $str = "UPDATE prescricoes_anamnese_optometrica 
            SET motivo_consulta = '$motivo_consulta', ultimo_exame = '$ultimo_exame', itens = '$itens', periodos = '$periodos', onde = '$onde', quando = '$quando',
            sintomas_relatados = '$sintomas_relatados'
            WHERE idempresa = '$adm_empresa' 
            AND idagendamento = '$idagendamento'
            AND idprescricao = '$idprescricao'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    if($data_prescricao)
    {
        $array = explode(" ", $data_prescricao);
        $dt_prescricao = ConverteData($array[0]).' '.$array[1].':00';

        $str = "UPDATE prescricoes SET data = '$dt_prescricao' WHERE codigo = '$idprescricao' AND idempresa = '$adm_empresa'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    $str = "SELECT * FROM prescricoes_anamnese_pessoal WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if(!$num)
    {
        $str = "INSERT INTO prescricoes_anamnese_pessoal (idempresa, idagendamento, idprescricao, diabetico_pessoal, medicamentos_controlados, pressao_alta_pessoal, 
            substancias_quimicas, fumante, gravida, obs_pessoal) 
            VALUES ('$adm_empresa', '$idagendamento', '$idprescricao', '$diabetico_pessoal', '$medicamentos_controlados', '$pressao_alta_pessoal', '$substancias_quimicas', '$fumante', 
            '$gravida', '$obs_pessoal')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    else
    {
        $str = "UPDATE prescricoes_anamnese_pessoal 
            SET diabetico_pessoal = '$diabetico_pessoal', medicamentos_controlados = '$medicamentos_controlados',
            pressao_alta_pessoal = '$pressao_alta_pessoal', substancias_quimicas = '$substancias_quimicas', fumante = '$fumante', gravida = '$gravida',
            obs_pessoal = '$obs_pessoal'
            WHERE idempresa = '$adm_empresa' 
            AND idagendamento = '$idagendamento'
            AND idprescricao = '$idprescricao'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    $str = "SELECT * FROM prescricoes_anamnese_familiar WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if(!$num)
    {
        $str = "INSERT INTO prescricoes_anamnese_familiar (idempresa, idagendamento, idprescricao, diabetico_familiar, pressao_alta_familiar, glaucoma, estrabismo, catarata, oculos, obs_familiar) 
            VALUES ('$adm_empresa', '$idagendamento', '$idprescricao', '$diabetico_familiar', '$pressao_alta_familiar', '$glaucoma', '$estrabismo', '$catarata', '$oculos', '$obs_familiar')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    else
    {
        $str = "UPDATE prescricoes_anamnese_familiar 
            SET diabetico_familiar = '$diabetico_familiar', pressao_alta_familiar = '$pressao_alta_familiar', glaucoma = '$glaucoma',
            estrabismo = '$estrabismo', catarata = '$catarata', oculos = '$oculos', obs_familiar = '$obs_familiar'
            WHERE idempresa = '$adm_empresa' 
            AND idagendamento = '$idagendamento'
            AND idprescricao = '$idprescricao'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    $str = "SELECT * FROM prescricoes_anamnese_acuidade WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if(!$num)
    {
        $str = "INSERT INTO prescricoes_anamnese_acuidade (idempresa, idagendamento, idprescricao, optotipo, metros, obs_acuidade) 
            VALUES ('$adm_empresa', '$idagendamento', '$idprescricao', '$optotipo', '$metros', '$obs_acuidade')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    else
    {
        $str = "UPDATE prescricoes_anamnese_acuidade 
            SET optotipo = '$optotipo', metros = '$metros', obs_acuidade = '$obs_acuidade'
            WHERE idempresa = '$adm_empresa' 
            AND idagendamento = '$idagendamento'
            AND idprescricao = '$idprescricao'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    $str = "SELECT * FROM prescricoes_anamnese_lensometro WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if(!$num)
    {
        $str = "INSERT INTO prescricoes_anamnese_lensometro (idempresa, idagendamento, idprescricao, od_esferico, od_cilindrico,
            od_eixo, od_adicao, od_prisma, od_dnpl, od_dnpp, od_tipo, oe_esferico, oe_cilindrico, oe_eixo, oe_adicao, oe_prisma, oe_dnpl, oe_dnpp, oe_tipo) 
            VALUES ('$adm_empresa', '$idagendamento', '$idprescricao', '$od_esferico', '$od_cilindrico', '$od_eixo', '$od_adicao', '$od_prisma', '$od_dnpl', '$od_dnpp', '$od_tipo', 
            '$oe_esferico', '$oe_cilindrico', '$oe_eixo', '$oe_adicao', '$oe_prisma', '$oe_dnpl', '$oe_dnpp', '$oe_tipo')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    else
    {
        $str = "UPDATE prescricoes_anamnese_lensometro 
            SET od_esferico = '$od_esferico', od_cilindrico = '$od_cilindrico', od_eixo = '$od_eixo', od_adicao = '$od_adicao', od_prisma = '$od_prisma', 
            od_dnpl = '$od_dnpl', od_dnpp = '$od_dnpp', od_tipo = '$od_tipo', oe_esferico = '$oe_esferico', oe_cilindrico = '$oe_cilindrico', oe_eixo = '$oe_eixo', 
            oe_adicao = '$oe_adicao', oe_prisma = '$oe_prisma', oe_dnpl = '$oe_dnpl', oe_dnpp = '$oe_dnpp', oe_tipo = '$oe_tipo'
            WHERE idempresa = '$adm_empresa' 
            AND idagendamento = '$idagendamento'
            AND idprescricao = '$idprescricao'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    $str = "SELECT * FROM prescricoes_anamnese_savaliacao WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if(!$num)
    {
        $str = "INSERT INTO prescricoes_anamnese_savaliacao (idempresa, idagendamento, idprescricao, od_longe, od_perto, od_phl, od_php, oe_longe, oe_perto, oe_phl, oe_php, 
            bin_longe, bin_perto, bin_phl, bin_php, dom_longe, dom_perto, dom_phl, dom_php) 
            VALUES ('$adm_empresa', '$idagendamento', '$idprescricao', '$od_longe', '$od_perto', '$od_phl', '$od_php', '$oe_longe', '$oe_perto', '$oe_phl', '$oe_php', '$bin_longe', 
            '$bin_perto', '$bin_phl', '$bin_php', '$dom_longe', '$dom_perto', '$dom_phl', '$dom_php')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    else
    {
        $str = "UPDATE prescricoes_anamnese_savaliacao 
            SET od_longe = '$od_longe', od_perto = '$od_perto', od_phl = '$od_phl', od_php = '$od_php', oe_longe = '$oe_longe', oe_perto = '$oe_perto', 
            oe_phl = '$oe_phl', oe_php = '$oe_php', bin_longe = '$bin_longe', bin_perto = '$bin_perto', bin_phl = '$bin_phl', bin_php = '$bin_php', 
            dom_longe = '$dom_longe', dom_perto = '$dom_perto', dom_phl = '$dom_phl', dom_php = '$dom_php'
            WHERE idempresa = '$adm_empresa' 
            AND idagendamento = '$idagendamento'
            AND idprescricao = '$idprescricao'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    $str = "SELECT * FROM prescricoes_anamnese_cavaliacao WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if(!$num)
    {
        $str = "INSERT INTO prescricoes_anamnese_cavaliacao (idempresa, idagendamento, idprescricao, com_od_longe, com_od_perto, com_oe_longe, com_oe_perto, com_bin_longe, 
            com_bin_perto, com_dom_longe, com_dom_perto) 
            VALUES ('$adm_empresa', '$idagendamento', '$idprescricao', '$com_od_longe', '$com_od_perto', '$com_oe_longe', '$com_oe_perto', '$com_bin_longe', '$com_bin_perto', 
            '$com_dom_longe', '$com_dom_perto')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    else
    {
        $str = "UPDATE prescricoes_anamnese_cavaliacao 
            SET com_od_longe = '$com_od_longe', com_od_perto = '$com_od_perto', com_oe_longe = '$com_oe_longe', com_oe_perto = '$com_oe_perto', 
            com_bin_longe = '$com_bin_longe', com_bin_perto = '$com_bin_perto', com_dom_longe = '$com_dom_longe', com_dom_perto = '$com_dom_perto'
            WHERE idempresa = '$adm_empresa' 
            AND idagendamento = '$idagendamento'
            AND idprescricao = '$idprescricao'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    $str = "SELECT * FROM prescricoes_anamnese_reflexos WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if(!$num)
    {
        $str = "INSERT INTO prescricoes_anamnese_reflexos (idempresa, idagendamento, idprescricao, od_r_fotomotor, od_nr_fotomotor, oe_r_fotomotor, oe_nr_fotomotor, 
            od_r_consensual, od_nr_consensual, oe_r_consensual, oe_nr_consensual, od_r_acomodativo, od_nr_acomodativo, oe_r_acomodativo, oe_nr_acomodativo) 
            VALUES ('$adm_empresa', '$idagendamento', '$idprescricao', '$od_r_fotomotor', '$od_nr_fotomotor', '$oe_r_fotomotor', '$oe_nr_fotomotor', '$od_r_consensual', '$od_nr_consensual', 
            '$oe_r_consensual', '$oe_nr_consensual', '$od_r_acomodativo', '$od_nr_acomodativo', '$oe_r_acomodativo', '$oe_nr_acomodativo')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    else
    {
        $str = "UPDATE prescricoes_anamnese_reflexos 
            SET od_r_fotomotor = '$od_r_fotomotor', od_nr_fotomotor = '$od_nr_fotomotor', oe_r_fotomotor = '$oe_r_fotomotor', oe_nr_fotomotor = '$oe_nr_fotomotor', 
            od_r_consensual = '$od_r_consensual', od_nr_consensual = '$od_nr_consensual', oe_r_consensual = '$oe_r_consensual', oe_nr_consensual = '$oe_nr_consensual', 
            od_r_acomodativo = '$od_r_acomodativo', od_nr_acomodativo = '$od_nr_acomodativo', oe_r_acomodativo = '$oe_r_acomodativo', oe_nr_acomodativo = '$oe_nr_acomodativo'
            WHERE idempresa = '$adm_empresa' 
            AND idagendamento = '$idagendamento'
            AND idprescricao = '$idprescricao'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    $str = "SELECT * FROM prescricoes_anamnese_motores WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if(!$num)
    {
        $str = "INSERT INTO prescricoes_anamnese_motores (idempresa, idagendamento, idprescricao, h_od_15, h_od_30, h_od_45, h_oe_15, h_oe_30, h_oe_45, k_od_k0, k_od_kma, k_od_kme, 
            k_oe_k0, k_oe_kma, k_oe_kme) 
            VALUES ('$adm_empresa', '$idagendamento', '$idprescricao', '$h_od_15', '$h_od_30', '$h_od_45', '$h_oe_15', '$h_oe_30', '$h_oe_45', '$k_od_k0', '$k_od_kma', '$k_od_kme', 
            '$k_oe_k0', '$k_oe_kma', '$k_oe_kme')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    else
    {
        $str = "UPDATE prescricoes_anamnese_motores 
            SET h_od_15 = '$h_od_15', h_od_30 = '$h_od_30', h_od_45 = '$h_od_45', h_oe_15 = '$h_oe_15', h_oe_30 = '$h_oe_30', h_oe_45 = '$h_oe_45', k_od_k0 = '$k_od_k0', 
            k_od_kma = '$k_od_kma', k_od_kme = '$k_od_kme', k_oe_k0 = '$k_oe_k0', k_oe_kma = '$k_oe_kma', k_oe_kme = '$k_oe_kme'
            WHERE idempresa = '$adm_empresa' 
            AND idagendamento = '$idagendamento'
            AND idprescricao = '$idprescricao'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    $str = "SELECT * FROM prescricoes_anamnese_duccoes WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if(!$num)
    {
        $str = "INSERT INTO prescricoes_anamnese_duccoes (idempresa, idagendamento, idprescricao, od_suave, od_completa, od_continua, od_limitacao, oe_suave, oe_completa, 
            oe_continua, oe_limitacao) 
            VALUES ('$adm_empresa', '$idagendamento', '$idprescricao', '$od_suave', '$od_completa', '$od_continua', '$od_limitacao', '$oe_suave', '$oe_completa', 
            '$oe_continua', '$oe_limitacao')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    else
    {
        $str = "UPDATE prescricoes_anamnese_duccoes 
            SET od_suave = '$od_suave', od_completa = '$od_completa', od_continua = '$od_continua', od_limitacao = '$od_limitacao', oe_suave = '$oe_suave', 
            oe_completa = '$oe_completa', oe_continua = '$oe_continua', oe_limitacao = '$oe_limitacao'
            WHERE idempresa = '$adm_empresa' 
            AND idagendamento = '$idagendamento'
            AND idprescricao = '$idprescricao'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    $str = "SELECT * FROM prescricoes_anamnese_cover WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if(!$num)
    {
        $str = "INSERT INTO prescricoes_anamnese_cover (idempresa, idagendamento, idprescricao, com_orto40, com_orto20, com_endo40, com_endo20, com_exo40, com_exo20, com_hiper40, 
            com_hiper20, com_hipo40, com_hipo20) 
            VALUES ('$adm_empresa', '$idagendamento', '$idprescricao', '$com_orto40', '$com_orto20', '$com_endo40', '$com_endo20', '$com_exo40', '$com_exo20', '$com_hiper40', 
            '$com_hiper20', '$com_hipo40', '$com_hipo20')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    else
    {
        $str = "UPDATE prescricoes_anamnese_cover 
            SET com_orto40 = '$com_orto40', com_orto20 = '$com_orto20', com_endo40 = '$com_endo40', com_endo20 = '$com_endo20', com_exo40 = '$com_exo40', 
            com_exo20 = '$com_exo20', com_hiper40 = '$com_hiper40', com_hiper20 = '$com_hiper20', com_hipo40 = '$com_hipo40', com_hipo20 = '$com_hipo20'
            WHERE idempresa = '$adm_empresa' 
            AND idagendamento = '$idagendamento'
            AND idprescricao = '$idprescricao'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }

    $str = "SELECT * FROM prescricoes_anamnese_ppc WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $num = mysqli_num_rows($rs);

    if(!$num)
    {
        $str = "INSERT INTO prescricoes_anamnese_ppc (idempresa, idagendamento, idprescricao, ppc_objeto, ppc_luz, ppc_filtro) 
            VALUES ('$adm_empresa', '$idagendamento', '$idprescricao', '$ppc_objeto', '$ppc_luz', '$ppc_filtro')";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    else
    {
        $str = "UPDATE prescricoes_anamnese_ppc 
            SET ppc_objeto = '$ppc_objeto', ppc_luz = '$ppc_luz', ppc_filtro = '$ppc_filtro'
            WHERE idempresa = '$adm_empresa' 
            AND idagendamento = '$idagendamento'
            AND idprescricao = '$idprescricao'";
        $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    }
    
    redireciona("prescricoes.php?idagendamento=$idagendamento&s=$status&ind_msg=6");
}

if($_GET['cmd'] == 'del')
{
    $str = "DELETE FROM prescricoes WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND codigo = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prescricoes_oculos WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prescricoes_lentes WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prescricoes_laudos WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prescricoes_encaminhamentos WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prescricoes_anamnese_savaliacao WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prescricoes_anamnese_acuidade WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prescricoes_anamnese_cavaliacao WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prescricoes_anamnese_cover WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prescricoes_anamnese_duccoes WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prescricoes_anamnese_familiar WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prescricoes_anamnese_lensometro WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prescricoes_anamnese_motores WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prescricoes_anamnese_optometrica WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prescricoes_anamnese_pessoal WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prescricoes_anamnese_ppc WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    $str = "DELETE FROM prescricoes_anamnese_reflexos WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao)); 

    redireciona("prescricoes.php?idagendamento=$idagendamento&s=$status&ind_msg=7");
}

$str = "SELECT A.*, DATE_FORMAT(A.data_pagto, '%d/%m/%Y %H:%i') AS dt_pagto, B.nome AS optometrista, C.descricao AS procedimento, C.cor, 
    D.nome AS paciente, D.data_nascimento, D.cpf, D.rg, D.email, D.telefone, D.telefone2, D.cep, D.endereco, D.numero, D.complemento, D.bairro, D.cidade, D.estado, D.tipo
    FROM agendamentos A
    LEFT JOIN usuarios B ON A.idoptometrista = B.codigo
    LEFT JOIN procedimentos C ON A.idprocedimento = C.codigo
    INNER JOIN pacientes D ON A.idpaciente = D.codigo
    WHERE A.codigo = '$idagendamento'
    AND A.idempresa = '$adm_empresa'
    AND A.status = '$status'    
    $strWhereP
    ORDER BY A.data";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);

$idpaciente = $vet['idpaciente'];
$endereco = paciente_endereco($conexao, $idpaciente);
$tipo_c = $vet['tipo'];

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Prescrição para óculos cadastrada com sucesso';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Prescrição para lentes cadastrada com sucesso';
elseif($_GET['ind_msg'] == "3")
    $msg = 'Laudo cadastrado com sucesso';
elseif($_GET['ind_msg'] == "4")
    $msg = 'Declaração cadastrada com sucesso';
elseif($_GET['ind_msg'] == "5")
    $msg = 'Encaminhamento cadastrado com sucesso';
elseif($_GET['ind_msg'] == "6")
    $msg = 'Anamnese cadastrada com sucesso';
elseif($_GET['ind_msg'] == "7")
    $msg = 'Prescrição excluída com sucesso';
?>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Cadastro de prescrição</h2>
        <ol class="breadcrumb">
            <li class="active"><strong>Iniciar consulta</strong></li>
            <li><a href="consultas_espera.php">Fila de espera</a></li>
        </ol>
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
        </div>
    </div>
    <div class="row m-b-lg m-t-lg">
        <div class="col-md-4">
            <div class="profile-info" style="margin-left: 0px;">
                <div class="">
                    <div>
                        <h2 class="no-margins"><?=stripslashes($vet['paciente'])?></h2>
                        <h4><?=$vet['cpf']?></h4>
                        <small><?=nl2br($endereco)?></small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <table class="table small m-b-xs">
                <tbody>
                    <tr>
                        <td><strong><?=$vet['rg']?></strong><br>RG</td>
                        <td><strong><?=ConverteData($vet['data_nascimento'])?></strong><br>Nascimento</td>
                    </tr>
                    <tr>
                        <td><strong><a href="https://api.whatsapp.com/send?phone=55<?=preg_replace('/[^\d]/', '', $vet['telefone'])?>" target="_blank"><?=$vet['telefone']?></a></strong><br>Telefone</td>
                        <td><strong><?=$vet['email']?></strong><br>Email</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-4">
            <small>Agendado para</small>
            <h2 class="no-margins"><?=ConverteData($vet['data'])?> <?=substr($vet['hora_inicial'], 0, -3)?></h2>
            <div id="sparkline1"></div>
        </div>
    </div>

    <?
    $strP = "SELECT DISTINCT *, DATE_FORMAT(data, '%d/%m/%Y %H:%i') AS dt_prescricao FROM prescricoes WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' ORDER BY data DESC";
    $rsP  = mysqli_query($conexao, $strP) or die(mysqli_error($conexao));
    $numP = mysqli_num_rows($rsP);
    
    if($numP > 0)
    {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Prescrições cadastradas</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Título</th>
                                    <th>Data de cadastro</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vetP = mysqli_fetch_array($rsP))
                                {
                                    if($vetP['tipo'] == 1)
                                    {
                                        $img = '<i class="fa fa-eye"></i>';
                                        $tipo = 'Prescrição para óculos';
                                    }
                                    elseif($vetP['tipo'] == 2)
                                    {
                                        $img = '<i class="fa fa-circle"></i>';
                                        $tipo = 'Prescrição lente de contato';
                                    }
                                    elseif($vetP['tipo'] == 3)
                                    {
                                        $img = '<i class="fa fa-file"></i>';
                                        $tipo = 'Laudo';
                                    }
                                    elseif($vetP['tipo'] == 4)
                                    {
                                        $img = '<i class="fa fa-bullhorn"></i>';
                                        $tipo = 'Declaração';
                                    }
                                    elseif($vetP['tipo'] == 5)
                                    {
                                        $img = '<i class="fa fa-share"></i>';
                                        $tipo = 'Encaminhamento';
                                    }
                                    elseif($vetP['tipo'] == 6)
                                    {
                                        $img = '<i class="fa fa-flag"></i>';
                                        $tipo = 'Anamnese';
                                    }
                                ?>
                                <tr class="gradeX">
                                    <td><?=$img?></td>
                                    <td><?=$tipo?></td>
                                    <td><?=$vetP['dt_prescricao']?></td>
                                    <td>
                                        <a class="btn btn-success btn-circle" type="button" title="imprimir prescrição" href="prescricoes_ver.php?idagendamento=<?=$idagendamento?>&idprescricao=<?=$vetP['codigo']?>" target="_blank"><i class="fa fa-print"></i></a>
                                        <?
                                        if($perm_gerenciar_prescricao == 1)
                                        {
                                        ?>
                                        <a class="btn btn-warning btn-circle" type="button" title="editar / visualizar" href="prescricoes.php?ind=2&idagendamento=<?=$idagendamento?>&s=<?=$status?>&idprescricao=<?=$vetP['codigo']?>"><i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger btn-circle" type="button" title="excluir" href="prescricoes.php?cmd=del&idagendamento=<?=$idagendamento?>&s=<?=$status?>&idprescricao=<?=$vetP['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a> 
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
                                    <th>#</th>
                                    <th>Título</th>
                                    <th>Data de cadastro</th>
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
                Nenhuma prescrição cadastrada para este agendamento.
            </p>
        </div>
    </div>
    <?
    }
    ?>
    <br>

    <?
    if($perm_gerenciar_prescricao == 1)
    {
        $strP = "SELECT *, DATE_FORMAT(data, '%d/%m/%Y %H:%i') AS dt_prescricao FROM prescricoes WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND codigo = '$idprescricao' ORDER BY data DESC";
        $rsP  = mysqli_query($conexao, $strP) or die(mysqli_error($conexao));
        $numP = mysqli_num_rows($rsP);
        $vetP = mysqli_fetch_array($rsP);
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="tabs-container">
                <ul class="nav nav-tabs">
                    <li <?=(!$numP || $vetP['tipo'] == 1) ? 'class="active"' : ''?>><a data-toggle="tab" href="#tab-1">Prescrição para óculos</a></li>
                    <li <?=($vetP['tipo'] == 2) ? 'class="active"' : ''?>><a data-toggle="tab" href="#tab-2">Prescrição lente de contato</a></li>
                    <li <?=($vetP['tipo'] == 3) ? 'class="active"' : ''?>><a data-toggle="tab" href="#tab-3">Laudo</a></li>
                    <li <?=($vetP['tipo'] == 4) ? 'class="active"' : ''?>><a data-toggle="tab" href="#tab-4">Declaração</a></li>
                    <li <?=($vetP['tipo'] == 5) ? 'class="active"' : ''?>><a data-toggle="tab" href="#tab-5">Encaminhamento</a></li>
                    <li <?=($vetP['tipo'] == 6) ? 'class="active"' : ''?>><a data-toggle="tab" href="#tab-6">Anamnese</a></li>
                </ul>
                <div class="tab-content">
                    <?
                    $str = "SELECT * FROM prescricoes_oculos WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                    $vet = mysqli_fetch_array($rs);
                    ?>
                    <div id="tab-1" <?=(!$numP || $vetP['tipo'] == 1) ? 'class="tab-pane active"' : 'class="tab-pane"'?>>
                        <div class="panel-body">
                            <strong>Prescrição para óculos</strong><br><br>
                            <form method="post" class="form-horizontal" name="form" id="form" enctype="multipart/form-data">
                                <input type="hidden" name="cmd" value="save_oculos">  
                                <input type="hidden" name="idagendamento" id="idagendamento" value="<?=$idagendamento?>">
                                <input type="hidden" name="idprescricao" id="idprescricao" value="<?=$idprescricao?>">
                                <input type="hidden" name="s" id="s" value="<?=$status?>">
                                <input type="hidden" name="tipo" id="tipo" value="<?=$tipo_c?>">

                                <div class="row">
                                    <div class="col-md-2">
                                        <p class="font-bold" >
                                            Data da prescrição*
                                        </p>
                                    </div>
                                    <div class="col-md-2">
                                        <input class="form-control" type="text" name="data_prescricao" id="data_prescricao" value="<?=($vetP['dt_prescricao']) ? $vetP['dt_prescricao'] : date('d/m/Y H:i')?>" required maxlength="16" data-mask="99/99/9999 99:99" onKeyUp="javascript: return auto_data_hora('data_prescricao');" onKeyPress="javascript: return somenteNumeros(event);">
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-2">
                                        <p class="font-bold" >
                                            OD
                                        </p>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" step="0.25" name="oculos_od_esf" id="oculos_od_esf" class="form-control" value="<?=$vet['oculos_od_esf']?>" placeholder="Esf" >
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" step="0.25" name="oculos_od_cil" id="oculos_od_cil" class="form-control" value="<?=$vet['oculos_od_cil']?>" placeholder="Cil" max="0">
                                    </div> 
                                    <div class="col-md-2">
                                        <input type="number" step="0.25" name="oculos_od_eixo" id="oculos_od_eixo" class="form-control" value="<?=$vet['oculos_od_eixo']?>" placeholder="Eixo" >
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="oculos_od_av" id="oculos_od_av" class="form-control" value="<?=$vet['oculos_od_av']?>" placeholder="AV" >
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-2">
                                        <p class="font-bold" >
                                            OE
                                        </p>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" step="0.25" name="oculos_oe_esf" id="oculos_oe_esf" class="form-control" value="<?=$vet['oculos_oe_esf']?>" placeholder="Esf" >
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" step="0.25" name="oculos_oe_cil" id="oculos_oe_cil" class="form-control" value="<?=$vet['oculos_oe_cil']?>"  placeholder="Cil"  max="0">
                                    </div> 
                                    <div class="col-md-2">
                                        <input type="number" step="0.25" name="oculos_oe_eixo" id="oculos_oe_eixo" class="form-control" value="<?=$vet['oculos_oe_eixo']?>"  placeholder="Eixo" >
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="oculos_oe_av" id="oculos_oe_av" class="form-control" value="<?=$vet['oculos_oe_av']?>" placeholder="AV" >
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-2">
                                        <p class="font-bold" >
                                            Adição
                                        </p>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" step="0.25" name="oculos_adicao" id="oculos_adicao" class="form-control" value="<?=$vet['oculos_adicao']?>" placeholder="Adição" >
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-2">
                                        <p class="font-bold" >
                                            Lente
                                        </p>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-control" name="oculos_lente" id="oculos_lente" >
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
                                <br>
                                <div class="row">
                                    <div class="col-md-2">
                                        <p class="font-bold" >
                                            Observação
                                        </p>
                                    </div>
                                    <div class="col-md-9">
                                        <textarea name="oculos_observacao" id="oculos_observacao" class="form-control" rows="5"><?=nl2br(stripslashes($vet['oculos_observacao']))?></textarea>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-xs-12"> 
                                        <button type="submit" class="btn btn-primary" >Salvar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?
                    $str = "SELECT * FROM prescricoes_lentes WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                    $vet = mysqli_fetch_array($rs);
                    ?>
                    <div id="tab-2" <?=($vetP['tipo'] == 2) ? 'class="tab-pane active"' : 'class="tab-pane"'?>>
                        <div class="panel-body">
                            <strong>Prescrição lente de contato</strong><br><br>
                            <form method="post" class="form-horizontal" name="form" id="form" enctype="multipart/form-data">
                                <input type="hidden" name="cmd" value="save_lentes">  
                                <input type="hidden" name="idagendamento" id="idagendamento" value="<?=$idagendamento?>">
                                <input type="hidden" name="idprescricao" id="idprescricao" value="<?=$idprescricao?>">
                                <input type="hidden" name="s" id="s" value="<?=$status?>">
                                <input type="hidden" name="tipo" id="tipo" value="<?=$tipo_c?>">

                                <div class="row">
                                    <div class="col-md-2">
                                        <p class="font-bold" >
                                            Data da prescrição*
                                        </p>
                                    </div>
                                    <div class="col-md-2">
                                        <input class="form-control" type="text" name="data_prescricao" id="data_prescricao" value="<?=($vetP['dt_prescricao']) ? $vetP['dt_prescricao'] : date('d/m/Y H:i')?>" required maxlength="16" data-mask="99/99/9999 99:99" onKeyUp="javascript: return auto_data_hora('data_prescricao');" onKeyPress="javascript: return somenteNumeros(event);">
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-2">
                                        <p class="font-bold" >
                                            OD
                                        </p>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" step="0.25" name="lentes_od_esf" id="lentes_od_esf" class="form-control" value="<?=$vet['lentes_od_esf']?>" placeholder="Esf" >
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" step="0.25" name="lentes_od_cil" id="lentes_od_cil" class="form-control" value="<?=$vet['lentes_od_cil']?>"  placeholder="Cil"  max="0">
                                    </div> 
                                    <div class="col-md-2">
                                        <input type="number" step="0.25" name="lentes_od_eixo" id="lentes_od_eixo" class="form-control" value="<?=$vet['lentes_od_eixo']?>"  placeholder="Eixo" >
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="lentes_od_av" id="lentes_od_av" class="form-control" value="<?=$vet['lentes_od_av']?>" placeholder="AV" >
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-2">
                                        <p class="font-bold" >
                                            OE
                                        </p>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" step="0.25" name="lentes_oe_esf" id="lentes_oe_esf" class="form-control" value="<?=$vet['lentes_oe_esf']?>" placeholder="Esf" >
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" step="0.25" name="lentes_oe_cil" id="lentes_oe_cil" class="form-control" value="<?=$vet['lentes_oe_cil']?>"  placeholder="Cil"  max="0">
                                    </div> 
                                    <div class="col-md-2">
                                        <input type="number" step="0.25" name="lentes_oe_eixo" id="lentes_oe_eixo" class="form-control" value="<?=$vet['lentes_oe_eixo']?>"  placeholder="Eixo" >
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="lentes_oe_av" id="lentes_oe_av" class="form-control" value="<?=$vet['lentes_oe_av']?>" placeholder="AV" >
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-2">
                                        <p class="font-bold" >
                                            Lente
                                        </p>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-control" name="lentes_lente" id="lentes_lente" >
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
                                <br>
                                <div class="row">
                                    <div class="col-md-2">
                                        <p class="font-bold" >
                                            Observação
                                        </p>
                                    </div>
                                    <div class="col-md-9">
                                        <textarea name="lentes_observacao" id="lentes_observacao" class="form-control" rows="5"><?=nl2br(stripslashes($vet['lentes_observacao']))?></textarea>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-xs-12"> 
                                        <button type="submit" class="btn btn-primary" >Salvar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?
                    $str = "SELECT * FROM prescricoes_laudos WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                    $vet = mysqli_fetch_array($rs);
                    ?>
                    <div id="tab-3" <?=($vetP['tipo'] == 3) ? 'class="tab-pane active"' : 'class="tab-pane"'?>>
                        <div class="panel-body">
                            <strong>Laudo</strong><br><br>
                            <form method="post" class="form-horizontal" name="form" id="form" enctype="multipart/form-data">
                                <input type="hidden" name="cmd" value="save_laudo">  
                                <input type="hidden" name="idagendamento" id="idagendamento" value="<?=$idagendamento?>">
                                <input type="hidden" name="idprescricao" id="idprescricao" value="<?=$idprescricao?>">
                                <input type="hidden" name="s" id="s" value="<?=$status?>">
                                <input type="hidden" name="tipo" id="tipo" value="<?=$tipo_c?>">

                                <div class="row">
                                    <div class="col-md-2">
                                        <p class="font-bold" >
                                            Data da prescrição*
                                        </p>
                                    </div>
                                    <div class="col-md-2">
                                        <input class="form-control" type="text" name="data_prescricao" id="data_prescricao" value="<?=($vetP['dt_prescricao']) ? $vetP['dt_prescricao'] : date('d/m/Y H:i')?>" required maxlength="16" data-mask="99/99/9999 99:99" onKeyUp="javascript: return auto_data_hora('data_prescricao');" onKeyPress="javascript: return somenteNumeros(event);">
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="font-bold" >
                                            Modelo
                                        </p>
                                        <select class="form-control" name="idmodelo" id="idmodelo" >
                                            <option value="">Selecione ...</option>
                                            <?
                                            $strM = "SELECT * FROM modelos WHERE idempresa = '$adm_empresa' AND categoria = '3' ORDER BY descricao";
                                            $rsM  = mysqli_query($conexao, $strM) or die(mysqli_error($conexao));

                                            while($vetM = mysqli_fetch_array($rsM))
                                            {
                                            ?>
                                            <option value="<?=$vetM['codigo']?>" <?=($vetM['codigo'] == $vet['idmodelo']) ? 'selected' : ''?>><?=stripslashes($vetM['titulo'])?></option>
                                            <?
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <textarea name="laudo" id="editor_txt" rows="10" required ><?=stripslashes($vet['laudo'])?></textarea>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-xs-12"> 
                                        <button type="submit" class="btn btn-primary" >Salvar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?
                    $str = "SELECT * FROM prescricoes_declaracoes WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                    $vet = mysqli_fetch_array($rs);
                    ?>
                    <div id="tab-4" <?=($vetP['tipo'] == 4) ? 'class="tab-pane active"' : 'class="tab-pane"'?>>
                        <div class="panel-body">
                            <strong>Declaração</strong><br><br>
                            <form method="post" class="form-horizontal" name="form" id="form" enctype="multipart/form-data">
                                <input type="hidden" name="cmd" value="save_declaracao">  
                                <input type="hidden" name="idagendamento" id="idagendamento" value="<?=$idagendamento?>">
                                <input type="hidden" name="idprescricao" id="idprescricao" value="<?=$idprescricao?>">
                                <input type="hidden" name="s" id="s" value="<?=$status?>">
                                <input type="hidden" name="tipo" id="tipo" value="<?=$tipo_c?>">

                                <div class="row">
                                    <div class="col-md-2">
                                        <p class="font-bold" >
                                            Data da prescrição*
                                        </p>
                                    </div>
                                    <div class="col-md-2">
                                        <input class="form-control" type="text" name="data_prescricao" id="data_prescricao" value="<?=($vetP['dt_prescricao']) ? $vetP['dt_prescricao'] : date('d/m/Y H:i')?>" required maxlength="16" data-mask="99/99/9999 99:99" onKeyUp="javascript: return auto_data_hora('data_prescricao');" onKeyPress="javascript: return somenteNumeros(event);">
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="font-bold" >
                                            Modelo
                                        </p>
                                        <select class="form-control" name="idmodelo" id="idmodelo" >
                                            <option value="">Selecione ...</option>
                                            <?
                                            $strM = "SELECT * FROM modelos WHERE idempresa = '$adm_empresa' AND categoria = '4' ORDER BY descricao";
                                            $rsM  = mysqli_query($conexao, $strM) or die(mysqli_error($conexao));

                                            while($vetM = mysqli_fetch_array($rsM))
                                            {
                                            ?>
                                            <option value="<?=$vetM['codigo']?>" <?=($vetM['codigo'] == $vet['idmodelo']) ? 'selected' : ''?>><?=stripslashes($vetM['titulo'])?></option>
                                            <?
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <textarea name="declaracao" id="editor_txt2" rows="10" required ><?=stripslashes($vet['declaracao'])?></textarea>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-xs-12"> 
                                        <button type="submit" class="btn btn-primary" >Salvar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?
                    $str = "SELECT * FROM prescricoes_encaminhamentos WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                    $vet = mysqli_fetch_array($rs);
                    ?>
                    <div id="tab-5" <?=($vetP['tipo'] == 5) ? 'class="tab-pane active"' : 'class="tab-pane"'?>>
                        <div class="panel-body">
                            <strong>Encaminhamento</strong><br><br>
                            <form method="post" class="form-horizontal" name="form" id="form" enctype="multipart/form-data">
                                <input type="hidden" name="cmd" value="save_encaminhamento">  
                                <input type="hidden" name="idagendamento" id="idagendamento" value="<?=$idagendamento?>">
                                <input type="hidden" name="idprescricao" id="idprescricao" value="<?=$idprescricao?>">
                                <input type="hidden" name="s" id="s" value="<?=$status?>">
                                <input type="hidden" name="tipo" id="tipo" value="<?=$tipo_c?>">

                                <div class="row">
                                    <div class="col-md-2">
                                        <p class="font-bold" >
                                            Data da prescrição*
                                        </p>
                                    </div>
                                    <div class="col-md-2">
                                        <input class="form-control" type="text" name="data_prescricao" id="data_prescricao" value="<?=($vetP['dt_prescricao']) ? $vetP['dt_prescricao'] : date('d/m/Y H:i')?>" required maxlength="16" data-mask="99/99/9999 99:99" onKeyUp="javascript: return auto_data_hora('data_prescricao');" onKeyPress="javascript: return somenteNumeros(event);">
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="font-bold" >
                                            Modelo
                                        </p>
                                        <select class="form-control" name="idmodelo" id="idmodelo" >
                                            <option value="">Selecione ...</option>
                                            <?
                                            $strM = "SELECT * FROM modelos WHERE idempresa = '$adm_empresa' AND categoria = '5' ORDER BY descricao";
                                            $rsM  = mysqli_query($conexao, $strM) or die(mysqli_error($conexao));

                                            while($vetM = mysqli_fetch_array($rsM))
                                            {
                                            ?>
                                            <option value="<?=$vetM['codigo']?>" <?=($vetM['codigo'] == $vet['idmodelo']) ? 'selected' : ''?>><?=stripslashes($vetM['titulo'])?></option>
                                            <?
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <textarea name="encaminhamento" id="editor_txt3" rows="10" required ><?=stripslashes($vet['encaminhamento'])?></textarea>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-xs-12"> 
                                        <button type="submit" class="btn btn-primary" >Salvar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <script>
                    function cefaleia() 
                    {
                        var checkboxes = document.forms['form_encaminhamento'].elements[ 'itens[]' ];
                        //alert(checkboxes.length); 
                        
                        var selected = [];
                        var filtros = 0;
                        for (var i=0; i<checkboxes.length; i++) {
                            if (checkboxes[i].checked) {
                                if (checkboxes[i].value == 12) {
                                    filtros++;
                                }
                            }
                        }   

                        //alert(filtros);

                        if(filtros == 1)
                        {
                            document.getElementById('div_cefaleia').style.display = 'block';
                        }
                        else
                        {
                            document.getElementById('div_cefaleia').style.display = 'none';
                        }
                    }
                    </script>
                    <div id="tab-6" <?=($vetP['tipo'] == 6) ? 'class="tab-pane active"' : 'class="tab-pane"'?>>
                        <form method="post" class="form-horizontal" name="form_encaminhamento" id="form_encaminhamento" enctype="multipart/form-data">
                        <input type="hidden" name="cmd" value="save_anamnese">  
                        <input type="hidden" name="idagendamento" id="idagendamento" value="<?=$idagendamento?>">
                        <input type="hidden" name="idprescricao" id="idprescricao" value="<?=$idprescricao?>">
                        <input type="hidden" name="s" id="s" value="<?=$status?>">
                        <input type="hidden" name="tipo" id="tipo" value="<?=$tipo_c?>">

                        <div class="panel-body">
                            <strong>Anamnese</strong><br><br>
                            <?
                            $str = "SELECT * FROM prescricoes_anamnese_optometrica WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                            $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                            $vet = mysqli_fetch_array($rs);
                            ?>
                            <div class="row">
                                <div class="col-md-2">
                                    <p class="font-bold" >
                                        Data da prescrição*
                                    </p>
                                </div>
                                <div class="col-md-2">
                                    <input class="form-control" type="text" name="data_prescricao" id="data_prescricao" value="<?=($vetP['dt_prescricao']) ? $vetP['dt_prescricao'] : date('d/m/Y H:i')?>" required maxlength="16" data-mask="99/99/9999 99:99" onKeyUp="javascript: return auto_data_hora('data_prescricao');" onKeyPress="javascript: return somenteNumeros(event);">
                                </div>
                            </div>
                            <br>
                            <div class="ibox">                                
                                <div class="ibox-title ui-sortable-handle">
                                    <h5>AVALIAÇÃO OPTOMÉTRICA</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link ui-sortable">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display: none;"> 
                                    <div class="row">
                                        <div class="col-md-8">
                                            <p class="font-bold">
                                                Motivo da consulta
                                            </p>
                                            <input type="text" name="motivo_consulta" id="motivo_consulta" class="form-control" value="<?=stripslashes($vet['motivo_consulta'])?>" >
                                        </div>
                                        <div class="col-md-4">
                                            <p class="font-bold">
                                                Último exame
                                            </p>
                                            <input type="text" name="ultimo_exame" id="ultimo_exame" class="form-control" value="<?=stripslashes($vet['ultimo_exame'])?>" >
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="checkbox" name="itens[]" id="itens" value="1" <?=(strstr($vet['itens'], '|1|')) ? 'checked' : ''?>> Prurido - vermelhidão sensação de areia<br>
                                            <input type="checkbox" name="itens[]" id="itens" value="2" <?=(strstr($vet['itens'], '|2|')) ? 'checked' : ''?>> Fotofobia - sensibilidade a luz<br>
                                            <input type="checkbox" name="itens[]" id="itens" value="3" <?=(strstr($vet['itens'], '|3|')) ? 'checked' : ''?>> Hiperemia - olho vermelho<br>
                                            <input type="checkbox" name="itens[]" id="itens" value="4" <?=(strstr($vet['itens'], '|4|')) ? 'checked' : ''?>> Pterígio - carne crescida                                                
                                        </div>
                                        <div class="col-md-4">
                                            <input type="checkbox" name="itens[]" id="itens" value="5" <?=(strstr($vet['itens'], '|5|')) ? 'checked' : ''?>> Labirintite<br>
                                            <input type="checkbox" name="itens[]" id="itens" value="6" <?=(strstr($vet['itens'], '|6|')) ? 'checked' : ''?>> Catarata<br>
                                            <input type="checkbox" name="itens[]" id="itens" value="7" <?=(strstr($vet['itens'], '|7|')) ? 'checked' : ''?>> Epfera - lacrimejamento em excesso<br>
                                            <input type="checkbox" name="itens[]" id="itens" value="8" <?=(strstr($vet['itens'], '|8|')) ? 'checked' : ''?>> Trauma - Batida na cabeça                                             
                                        </div>
                                        <div class="col-md-4">
                                            <input type="checkbox" name="itens[]" id="itens" value="9" <?=(strstr($vet['itens'], '|9|')) ? 'checked' : ''?>> Glaucoma - Pressão alta<br>
                                            <input type="checkbox" name="itens[]" id="itens" value="10" <?=(strstr($vet['itens'], '|10|')) ? 'checked' : ''?>> Diabetes<br>
                                            <input type="checkbox" name="itens[]" id="itens" value="11" <?=(strstr($vet['itens'], '|11|')) ? 'checked' : ''?>> Ceratocone
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="checkbox" name="itens[]" id="itens" value="12" <?=(strstr($vet['itens'], '|12|')) ? 'checked' : ''?> onclick="javascript: cefaleia();"> Cefaléia - Dor de cabeça
                                        </div>
                                    </div>
                                    <br>
                                    <div id="div_cefaleia" <?=(strstr($vet['itens'], '|12|')) ? 'style="display: block;"' : 'style="display: none;"'?> >
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="font-bold">
                                                    Períodos
                                                </p>
                                                <input type="checkbox" name="periodos[]" id="periodos" value="1" <?=(strstr($vet['periodos'], '|1|')) ? 'checked' : ''?>> Manhã&nbsp;&nbsp;&nbsp;
                                                <input type="checkbox" name="periodos[]" id="periodos" value="2" <?=(strstr($vet['periodos'], '|2|')) ? 'checked' : ''?>> Tarde&nbsp;&nbsp;&nbsp;
                                                <input type="checkbox" name="periodos[]" id="periodos" value="3" <?=(strstr($vet['periodos'], '|3|')) ? 'checked' : ''?>> Noite&nbsp;&nbsp;&nbsp;
                                            </div>
                                        </div>
                                        <br>                                            
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="font-bold">
                                                    Onde?
                                                </p>
                                                <input type="checkbox" name="onde[]" id="onde" value="1" <?=(strstr($vet['onde'], '|1|')) ? 'checked' : ''?>> Frontal&nbsp;&nbsp;&nbsp;
                                                <input type="checkbox" name="onde[]" id="onde" value="2" <?=(strstr($vet['onde'], '|2|')) ? 'checked' : ''?>> Temporal&nbsp;&nbsp;&nbsp;
                                                <input type="checkbox" name="onde[]" id="onde" value="3" <?=(strstr($vet['onde'], '|3|')) ? 'checked' : ''?>> Occipital&nbsp;&nbsp;&nbsp;
                                                <input type="checkbox" name="onde[]" id="onde" value="4" <?=(strstr($vet['onde'], '|4|')) ? 'checked' : ''?>> Parental&nbsp;&nbsp;&nbsp;
                                            </div>
                                        </div>
                                        <br>                                            
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="font-bold">
                                                    Quando?
                                                </p>
                                                <input type="checkbox" name="quando[]" id="quando" value="1" <?=(strstr($vet['quando'], '|1|')) ? 'checked' : ''?>> Todos os dias&nbsp;&nbsp;&nbsp;
                                                <input type="checkbox" name="quando[]" id="quando" value="2" <?=(strstr($vet['quando'], '|2|')) ? 'checked' : ''?>> Eventual&nbsp;&nbsp;&nbsp;
                                                <input type="checkbox" name="quando[]" id="quando" value="3" <?=(strstr($vet['quando'], '|3|')) ? 'checked' : ''?>> Segunda a Sexta&nbsp;&nbsp;&nbsp;
                                                <input type="checkbox" name="quando[]" id="quando" value="4" <?=(strstr($vet['quando'], '|4|')) ? 'checked' : ''?>> Finais de Semana&nbsp;&nbsp;&nbsp;
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="font-bold" >
                                                    Alguns sintomas mais relatados
                                                </p>
                                                <textarea name="sintomas_relatados" id="sintomas_relatados" class="form-control" rows="5"><?=nl2br(stripslashes($vet['sintomas_relatados']))?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>  
                            <?
                            $str = "SELECT * FROM prescricoes_anamnese_pessoal WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                            $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                            $vet = mysqli_fetch_array($rs);
                            ?>                          
                            <div class="ibox">
                                <div class="ibox-title ui-sortable-handle">
                                    <h5>ANTECEDENTE PESSOAL</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link ui-sortable">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="font-bold">
                                                É diabético?
                                            </p>
                                            <select class="form-control" name="diabetico_pessoal" id="diabetico_pessoal" >
                                                <option value="">Selecione ...</option>
                                                <option value="1" <?=(1 == $vet['diabetico_pessoal']) ? 'selected' : ''?>>Sim</option>
                                                <option value="2" <?=(2 == $vet['diabetico_pessoal']) ? 'selected' : ''?>>Não</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="font-bold">
                                                Usa medicamentos controlados?
                                            </p>
                                            <select class="form-control" name="medicamentos_controlados" id="medicamentos_controlados" >
                                                <option value="">Selecione ...</option>
                                                <option value="1" <?=(1 == $vet['medicamentos_controlados']) ? 'selected' : ''?>>Sim</option>
                                                <option value="2" <?=(2 == $vet['medicamentos_controlados']) ? 'selected' : ''?>>Não</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="font-bold">
                                                Tem pressão alta?
                                            </p>
                                            <select class="form-control" name="pressao_alta_pessoal" id="pressao_alta_pessoal" >
                                                <option value="">Selecione ...</option>
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
                                            <select class="form-control" name="substancias_quimicas" id="substancias_quimicas" >
                                                <option value="">Selecione ...</option>
                                                <option value="1" <?=(1 == $vet['substancias_quimicas']) ? 'selected' : ''?>>Sim</option>
                                                <option value="2" <?=(2 == $vet['substancias_quimicas']) ? 'selected' : ''?>>Não</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="font-bold">
                                                É fumante?
                                            </p>
                                            <select class="form-control" name="fumante" id="fumante" >
                                                <option value="">Selecione ...</option>
                                                <option value="1" <?=(1 == $vet['fumante']) ? 'selected' : ''?>>Sim</option>
                                                <option value="2" <?=(2 == $vet['fumante']) ? 'selected' : ''?>>Não</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="font-bold">
                                                Está grávida?
                                            </p>
                                            <select class="form-control" name="gravida" id="gravida" >
                                                <option value="">Selecione ...</option>
                                                <option value="1" <?=(1 == $vet['gravida']) ? 'selected' : ''?>>Sim</option>
                                                <option value="2" <?=(2 == $vet['gravida']) ? 'selected' : ''?>>Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="font-bold" >
                                                Observações
                                            </p>
                                            <textarea name="obs_pessoal" id="obs_pessoal" class="form-control" rows="5"><?=nl2br(stripslashes($vet['obs_pessoal']))?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?
                            $str = "SELECT * FROM prescricoes_anamnese_familiar WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                            $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                            $vet = mysqli_fetch_array($rs);
                            ?>
                            <div class="ibox">
                                <div class="ibox-title ui-sortable-handle">
                                    <h5>ANTECEDENTE FAMILIAR</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link ui-sortable">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="font-bold">
                                                Alguém é diabético?
                                            </p>
                                            <select class="form-control" name="diabetico_familiar" id="diabetico_familiar" >
                                                <option value="">Selecione ...</option>
                                                <option value="1" <?=(1 == $vet['diabetico_familiar']) ? 'selected' : ''?>>Sim</option>
                                                <option value="2" <?=(2 == $vet['diabetico_familiar']) ? 'selected' : ''?>>Não</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="font-bold">
                                                Alguém tem pressão alta?
                                            </p>
                                            <select class="form-control" name="pressao_alta_familiar" id="pressao_alta_familiar" >
                                                <option value="">Selecione ...</option>
                                                <option value="1" <?=(1 == $vet['pressao_alta_familiar']) ? 'selected' : ''?>>Sim</option>
                                                <option value="2" <?=(2 == $vet['pressao_alta_familiar']) ? 'selected' : ''?>>Não</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="font-bold">
                                                Alguém tem glaucoma?
                                            </p>
                                            <select class="form-control" name="glaucoma" id="glaucoma" >
                                                <option value="">Selecione ...</option>
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
                                            <select class="form-control" name="estrabismo" id="estrabismo" >
                                                <option value="">Selecione ...</option>
                                                <option value="1" <?=(1 == $vet['estrabismo']) ? 'selected' : ''?>>Sim</option>
                                                <option value="2" <?=(2 == $vet['estrabismo']) ? 'selected' : ''?>>Não</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="font-bold">
                                                Alguém tem catarata?
                                            </p>
                                            <select class="form-control" name="catarata" id="catarata" >
                                                <option value="">Selecione ...</option>
                                                <option value="1" <?=(1 == $vet['catarata']) ? 'selected' : ''?>>Sim</option>
                                                <option value="2" <?=(2 == $vet['catarata']) ? 'selected' : ''?>>Não</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="font-bold">
                                                Alguém usa óculos?
                                            </p>
                                            <select class="form-control" name="oculos" id="oculos" >
                                                <option value="">Selecione ...</option>
                                                <option value="1" <?=(1 == $vet['oculos']) ? 'selected' : ''?>>Sim</option>
                                                <option value="2" <?=(2 == $vet['oculos']) ? 'selected' : ''?>>Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="font-bold" >
                                                Observações
                                            </p>
                                            <textarea name="obs_familiar" id="obs_familiar" class="form-control" rows="5"><?=nl2br(stripslashes($vet['obs_familiar']))?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?
                            $str = "SELECT * FROM prescricoes_anamnese_acuidade WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                            $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                            $vet = mysqli_fetch_array($rs);
                            ?>
                            <div class="ibox">
                                <div class="ibox-title ui-sortable-handle">
                                    <h5>ACUIDADE VISUAL</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link ui-sortable">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <p class="font-bold">
                                                Optotipo
                                            </p>
                                            <input type="text" name="optotipo" id="optotipo" class="form-control" value="<?=stripslashes($vet['optotipo'])?>" >
                                        </div>
                                        <div class="col-md-4">
                                            <p class="font-bold">
                                                Metros
                                            </p>
                                            <input type="text" name="metros" id="metros" class="form-control" value="<?=stripslashes($vet['metros'])?>" >
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="font-bold" >
                                                Observações
                                            </p>
                                            <textarea name="obs_acuidade" id="obs_acuidade" class="form-control" rows="5"><?=nl2br(stripslashes($vet['obs_acuidade']))?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?
                            $str = "SELECT * FROM prescricoes_anamnese_lensometro WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                            $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                            $vet = mysqli_fetch_array($rs);
                            ?>
                            <div class="ibox">
                                <div class="ibox-title ui-sortable-handle">
                                    <h5>LENSOMETRO</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link ui-sortable">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display: none;">
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
                                            <td><input type="number" step="0.25" name="od_esferico" id="od_esferico" class="form-control" value="<?=stripslashes($vet['od_esferico'])?>" ></td>
                                            <td><input type="number" step="0.25" name="od_cilindrico" id="od_cilindrico" class="form-control" value="<?=stripslashes($vet['od_cilindrico'])?>" max="0"></td>
                                            <td><input type="number" step="0.25" name="od_eixo" id="od_eixo" class="form-control" value="<?=stripslashes($vet['od_eixo'])?>" ></td>
                                            <td><input type="number" step="0.25" name="od_adicao" id="od_adicao" class="form-control" value="<?=stripslashes($vet['od_adicao'])?>" ></td>
                                            <td><input type="number" step="0.25" name="od_prisma" id="od_prisma" class="form-control" value="<?=stripslashes($vet['od_prisma'])?>" ></td>
                                            <td><input type="number" step="0.25" name="od_dnpl" id="od_dnpl" class="form-control" value="<?=stripslashes($vet['od_dnpl'])?>" ></td>
                                            <td><input type="number" step="0.25" name="od_dnpp" id="od_dnpp" class="form-control" value="<?=stripslashes($vet['od_dnpp'])?>" ></td>
                                            <td><input type="number" step="0.25" name="od_tipo" id="od_tipo" class="form-control" value="<?=stripslashes($vet['od_tipo'])?>" ></td>
                                        </tr>
                                        <tr>
                                            <td><b>OE</b></td>
                                            <td><input type="number" step="0.25" name="oe_esferico" id="oe_esferico" class="form-control" value="<?=stripslashes($vet['oe_esferico'])?>" ></td>
                                            <td><input type="number" step="0.25" name="oe_cilindrico" id="oe_cilindrico" class="form-control" value="<?=stripslashes($vet['oe_cilindrico'])?>" max="0"></td>
                                            <td><input type="number" step="0.25" name="oe_eixo" id="oe_eixo" class="form-control" value="<?=stripslashes($vet['oe_eixo'])?>" ></td>
                                            <td><input type="number" step="0.25" name="oe_adicao" id="oe_adicao" class="form-control" value="<?=stripslashes($vet['oe_adicao'])?>" ></td>
                                            <td><input type="number" step="0.25" name="oe_prisma" id="oe_prisma" class="form-control" value="<?=stripslashes($vet['oe_prisma'])?>" ></td>
                                            <td><input type="number" step="0.25" name="oe_dnpl" id="oe_dnpl" class="form-control" value="<?=stripslashes($vet['oe_dnpl'])?>" ></td>
                                            <td><input type="number" step="0.25" name="oe_dnpp" id="oe_dnpp" class="form-control" value="<?=stripslashes($vet['oe_dnpp'])?>" ></td>
                                            <td><input type="number" step="0.25" name="oe_tipo" id="oe_tipo" class="form-control" value="<?=stripslashes($vet['oe_tipo'])?>" ></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?
                            $str = "SELECT * FROM prescricoes_anamnese_savaliacao WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                            $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                            $vet = mysqli_fetch_array($rs);
                            ?>
                            <div class="ibox">
                                <div class="ibox-title ui-sortable-handle">
                                    <h5>AVALIAÇÃO SEM CORREÇÃO</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link ui-sortable">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display: none;">
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
                                            <td><input type="number" step="0.25" name="od_longe" id="od_longe" class="form-control" value="<?=stripslashes($vet['od_longe'])?>" ></td>
                                            <td><input type="number" step="0.25" name="od_perto" id="od_perto" class="form-control" value="<?=stripslashes($vet['od_perto'])?>" ></td>
                                            <td><input type="number" step="0.25" name="od_phl" id="od_phl" class="form-control" value="<?=stripslashes($vet['od_phl'])?>" ></td>
                                            <td><input type="number" step="0.25" name="od_php" id="od_php" class="form-control" value="<?=stripslashes($vet['od_php'])?>" ></td>
                                        </tr>
                                        <tr>
                                            <td><b>OE</b></td>
                                            <td><input type="number" step="0.25" name="oe_longe" id="oe_longe" class="form-control" value="<?=stripslashes($vet['oe_longe'])?>" ></td>
                                            <td><input type="number" step="0.25" name="oe_perto" id="oe_perto" class="form-control" value="<?=stripslashes($vet['oe_perto'])?>" ></td>
                                            <td><input type="number" step="0.25" name="oe_phl" id="oe_phl" class="form-control" value="<?=stripslashes($vet['oe_phl'])?>" ></td>
                                            <td><input type="number" step="0.25" name="oe_php" id="oe_php" class="form-control" value="<?=stripslashes($vet['oe_php'])?>" ></td>
                                        </tr>
                                        <tr>
                                            <td><b>Binocular</b></td>
                                            <td><input type="number" step="0.25" name="bin_longe" id="bin_longe" class="form-control" value="<?=stripslashes($vet['bin_longe'])?>" ></td>
                                            <td><input type="number" step="0.25" name="bin_perto" id="bin_perto" class="form-control" value="<?=stripslashes($vet['bin_perto'])?>" ></td>
                                            <td><input type="number" step="0.25" name="bin_phl" id="bin_phl" class="form-control" value="<?=stripslashes($vet['bin_phl'])?>" ></td>
                                            <td><input type="number" step="0.25" name="bin_php" id="bin_php" class="form-control" value="<?=stripslashes($vet['bin_php'])?>" ></td>
                                        </tr>
                                        <tr>
                                            <td><b>Olho dominante</b></td>
                                            <td><input type="number" step="0.25" name="dom_longe" id="dom_longe" class="form-control" value="<?=stripslashes($vet['dom_longe'])?>" ></td>
                                            <td><input type="number" step="0.25" name="dom_perto" id="dom_perto" class="form-control" value="<?=stripslashes($vet['dom_perto'])?>" ></td>
                                            <td><input type="number" step="0.25" name="dom_phl" id="dom_phl" class="form-control" value="<?=stripslashes($vet['dom_phl'])?>" ></td>
                                            <td><input type="number" step="0.25" name="dom_php" id="dom_php" class="form-control" value="<?=stripslashes($vet['dom_php'])?>" ></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?
                            $str = "SELECT * FROM prescricoes_anamnese_cavaliacao WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                            $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                            $vet = mysqli_fetch_array($rs);
                            ?>
                            <div class="ibox">
                                <div class="ibox-title ui-sortable-handle">
                                    <h5>AVALIAÇÃO COM CORREÇÃO</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link ui-sortable">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display: none;">
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
                                            <td><input type="number" step="0.25" name="com_od_longe" id="com_od_longe" class="form-control" value="<?=stripslashes($vet['com_od_longe'])?>" ></td>
                                            <td><input type="number" step="0.25" name="com_od_perto" id="com_od_perto" class="form-control" value="<?=stripslashes($vet['com_od_perto'])?>" ></td>
                                        </tr>
                                        <tr>
                                            <td><b>OE</b></td>
                                            <td><input type="number" step="0.25" name="com_oe_longe" id="com_oe_longe" class="form-control" value="<?=stripslashes($vet['com_oe_longe'])?>" ></td>
                                            <td><input type="number" step="0.25" name="com_oe_perto" id="com_oe_perto" class="form-control" value="<?=stripslashes($vet['com_oe_perto'])?>" ></td>
                                        </tr>
                                        <tr>
                                            <td><b>Binocular</b></td>
                                            <td><input type="number" step="0.25" name="com_bin_longe" id="com_bin_longe" class="form-control" value="<?=stripslashes($vet['com_bin_longe'])?>" ></td>
                                            <td><input type="number" step="0.25" name="com_bin_perto" id="com_bin_perto" class="form-control" value="<?=stripslashes($vet['com_bin_perto'])?>" ></td>
                                        </tr>
                                        <tr>
                                            <td><b>Olho dominante</b></td>
                                            <td><input type="number" step="0.25" name="com_dom_longe" id="com_dom_longe" class="form-control" value="<?=stripslashes($vet['com_dom_longe'])?>" ></td>
                                            <td><input type="number" step="0.25" name="com_dom_perto" id="com_dom_perto" class="form-control" value="<?=stripslashes($vet['com_dom_perto'])?>" ></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?
                            $str = "SELECT * FROM prescricoes_anamnese_reflexos WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                            $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                            $vet = mysqli_fetch_array($rs);
                            ?>
                            <div class="ibox">
                                <div class="ibox-title ui-sortable-handle">
                                    <h5>EXAME REFLEXOS PUPILARES</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link ui-sortable">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display: none;">
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
                                            <td><input type="number" step="0.25" name="od_r_fotomotor" id="od_r_fotomotor" class="form-control" value="<?=stripslashes($vet['od_r_fotomotor'])?>" ></td>
                                            <td><input type="number" step="0.25" name="od_nr_fotomotor" id="od_nr_fotomotor" class="form-control" value="<?=stripslashes($vet['od_nr_fotomotor'])?>" ></td>
                                            <td><input type="number" step="0.25" name="oe_r_fotomotor" id="oe_r_fotomotor" class="form-control" value="<?=stripslashes($vet['oe_r_fotomotor'])?>" ></td>
                                            <td><input type="number" step="0.25" name="oe_nr_fotomotor" id="oe_nr_fotomotor" class="form-control" value="<?=stripslashes($vet['oe_nr_fotomotor'])?>" ></td>
                                        </tr>
                                        <tr>
                                            <td><b>Consensual – LUZ EM UM DOS OLHOS OBSERVA O OUTRO OLHO</b></td>
                                            <td><input type="number" step="0.25" name="od_r_consensual" id="od_r_consensual" class="form-control" value="<?=stripslashes($vet['od_r_consensual'])?>" ></td>
                                            <td><input type="number" step="0.25" name="od_nr_consensual" id="od_nr_consensual" class="form-control" value="<?=stripslashes($vet['od_nr_consensual'])?>" ></td>
                                            <td><input type="number" step="0.25" name="oe_r_consensual" id="oe_r_consensual" class="form-control" value="<?=stripslashes($vet['oe_r_consensual'])?>" ></td>
                                            <td><input type="number" step="0.25" name="oe_nr_consensual" id="oe_nr_consensual" class="form-control" value="<?=stripslashes($vet['oe_nr_consensual'])?>" ></td>
                                        </tr>
                                        <tr>
                                            <td><b>Acomodativo – LUZ NO CENTRO NASAL PEDIR PARA OLHAR PARA LONGE E PERTO OBSERVA AMBUS OLHOS</b></td>
                                            <td><input type="number" step="0.25" name="od_r_acomodativo" id="od_r_acomodativo" class="form-control" value="<?=stripslashes($vet['od_r_acomodativo'])?>" ></td>
                                            <td><input type="number" step="0.25" name="od_nr_acomodativo" id="od_nr_acomodativo" class="form-control" value="<?=stripslashes($vet['od_nr_acomodativo'])?>" ></td>
                                            <td><input type="number" step="0.25" name="oe_r_acomodativo" id="oe_r_acomodativo" class="form-control" value="<?=stripslashes($vet['oe_r_acomodativo'])?>" ></td>
                                            <td><input type="number" step="0.25" name="oe_nr_acomodativo" id="oe_nr_acomodativo" class="form-control" value="<?=stripslashes($vet['oe_nr_acomodativo'])?>" ></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?
                            $str = "SELECT * FROM prescricoes_anamnese_motores WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                            $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                            $vet = mysqli_fetch_array($rs);
                            ?>
                            <div class="ibox">
                                <div class="ibox-title ui-sortable-handle">
                                    <h5>EXAMES MOTORES - BINOCULARES</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link ui-sortable">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display: none;">
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
                                            <td><input type="number" step="0.25" name="h_od_15" id="h_od_15" class="form-control" value="<?=stripslashes($vet['h_od_15'])?>" ></td>
                                            <td><input type="number" step="0.25" name="h_od_30" id="h_od_30" class="form-control" value="<?=stripslashes($vet['h_od_30'])?>" ></td>
                                            <td><input type="number" step="0.25" name="h_od_45" id="h_od_45" class="form-control" value="<?=stripslashes($vet['h_od_45'])?>" ></td>
                                            <td><input type="number" step="0.25" name="h_oe_15" id="h_oe_15" class="form-control" value="<?=stripslashes($vet['h_oe_15'])?>" ></td>
                                            <td><input type="number" step="0.25" name="h_oe_30" id="h_oe_30" class="form-control" value="<?=stripslashes($vet['h_oe_30'])?>" ></td>
                                            <td><input type="number" step="0.25" name="h_oe_45" id="h_oe_45" class="form-control" value="<?=stripslashes($vet['h_oe_45'])?>" ></td>
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
                                            <td><input type="checkbox" name="k_od_k0" id="k_od_k0" value="1" <?=($vet['k_od_k0'] == '1') ? 'checked' : ''?>> K<small>0</small></td>
                                            <td><input type="checkbox" name="k_od_kma" id="k_od_kma" value="1" <?=($vet['k_od_kma'] == '1') ? 'checked' : ''?>> K<small>+</small></td>
                                            <td><input type="checkbox" name="k_od_kme" id="k_od_kme" value="1" <?=($vet['k_od_kme'] == '1') ? 'checked' : ''?>> K<small>-</small></td>
                                            <td><input type="checkbox" name="k_oe_k0" id="k_oe_k0" value="1" <?=($vet['k_oe_k0'] == '1') ? 'checked' : ''?>> K<small>0</small></td>
                                            <td><input type="checkbox" name="k_oe_kma" id="k_oe_kma" value="1" <?=($vet['k_oe_kma'] == '1') ? 'checked' : ''?>> K<small>+</small></td>
                                            <td><input type="checkbox" name="k_oe_kme" id="k_oe_kme" value="1" <?=($vet['k_oe_kme'] == '1') ? 'checked' : ''?>> K<small>-</small></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?
                            $str = "SELECT * FROM prescricoes_anamnese_duccoes WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                            $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                            $vet = mysqli_fetch_array($rs);
                            ?>
                            <div class="ibox">
                                <div class="ibox-title ui-sortable-handle">
                                    <h5>DUCÇÕES - MONOCULAR</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link ui-sortable">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display: none;">
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
                                            <td><input type="checkbox" name="od_suave" id="od_suave" value="1" <?=($vet['od_suave'] == '1') ? 'checked' : ''?>> Suave</td>
                                            <td><input type="checkbox" name="od_completa" id="od_completa" value="1" <?=($vet['od_completa'] == '1') ? 'checked' : ''?>> Completa</td>
                                            <td><input type="checkbox" name="od_continua" id="od_continua" value="1" <?=($vet['od_continua'] == '1') ? 'checked' : ''?>> Contínua</td>
                                            <td><input type="checkbox" name="od_limitacao" id="od_limitacao" value="1" <?=($vet['od_limitacao'] == '1') ? 'checked' : ''?>> Limitação</td>
                                            <td><input type="checkbox" name="oe_suave" id="oe_suave" value="1" <?=($vet['oe_suave'] == '1') ? 'checked' : ''?>> Suave</td>
                                            <td><input type="checkbox" name="oe_completa" id="oe_completa" value="1" <?=($vet['oe_completa'] == '1') ? 'checked' : ''?>> Completa</td>
                                            <td><input type="checkbox" name="oe_continua" id="oe_continua" value="1" <?=($vet['oe_continua'] == '1') ? 'checked' : ''?>> Contínua</td>
                                            <td><input type="checkbox" name="oe_limitacao" id="oe_limitacao" value="1" <?=($vet['oe_limitacao'] == '1') ? 'checked' : ''?>> Limitação</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?
                            $str = "SELECT * FROM prescricoes_anamnese_cover WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                            $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                            $vet = mysqli_fetch_array($rs);
                            ?>
                            <div class="ibox">
                                <div class="ibox-title ui-sortable-handle">
                                    <h5>COVER TESTE</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link ui-sortable">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display: none;">
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
                                            <td><input type="checkbox" name="com_orto40" id="com_orto40" value="1" <?=($vet['com_orto40'] == '1') ? 'checked' : ''?>> Orto</td>
                                            <td><input type="checkbox" name="com_orto20" id="com_orto20" value="1" <?=($vet['com_orto20'] == '1') ? 'checked' : ''?>> Orto</td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox" name="com_endo40" id="com_endo40" value="1" <?=($vet['com_endo40'] == '1') ? 'checked' : ''?>> Endo</td>
                                            <td><input type="checkbox" name="com_endo20" id="com_endo20" value="1" <?=($vet['com_endo20'] == '1') ? 'checked' : ''?>> Endo</td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox" name="com_exo40" id="com_exo40" value="1" <?=($vet['com_exo40'] == '1') ? 'checked' : ''?>> Exo</td>
                                            <td><input type="checkbox" name="com_exo20" id="com_exo20" value="1" <?=($vet['com_exo20'] == '1') ? 'checked' : ''?>> Exo</td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox" name="com_hiper40" id="com_hiper40" value="1" <?=($vet['com_hiper40'] == '1') ? 'checked' : ''?>> Hiper</td>
                                            <td><input type="checkbox" name="com_hiper20" id="com_hiper20" value="1" <?=($vet['com_hiper20'] == '1') ? 'checked' : ''?>> Hiper</td>
                                        </tr>
                                        <tr>
                                            <td><input type="checkbox" name="com_hipo40" id="com_hipo40" value="1" <?=($vet['com_hipo40'] == '1') ? 'checked' : ''?>> Hipo</td>
                                            <td><input type="checkbox" name="com_hipo20" id="com_hipo20" value="1" <?=($vet['com_hipo20'] == '1') ? 'checked' : ''?>> Hipo</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?
                            $str = "SELECT * FROM prescricoes_anamnese_ppc WHERE idempresa = '$adm_empresa' AND idagendamento = '$idagendamento' AND idprescricao = '$idprescricao'";
                            $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                            $vet = mysqli_fetch_array($rs);
                            ?>
                            <div class="ibox">
                                <div class="ibox-title ui-sortable-handle">
                                    <h5>TESTE PPC</h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link ui-sortable">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content" style="display: none;">
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
                                            <td><input type="text" name="ppc_objeto" id="ppc_objeto" class="form-control" value="<?=stripslashes($vet['ppc_objeto'])?>" ></td>
                                            <td><input type="text" name="ppc_luz" id="ppc_luz" class="form-control" value="<?=stripslashes($vet['ppc_luz'])?>" ></td>
                                            <td><input type="text" name="ppc_filtro" id="ppc_filtro" class="form-control" value="<?=stripslashes($vet['ppc_filtro'])?>" ></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12"> 
                                    <button type="submit" class="btn btn-primary" >Salvar</button>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?
    }
    ?>
</div>
<?
include("includes/footer.php");
?>