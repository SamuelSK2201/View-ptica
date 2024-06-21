<?
include("s_acessos.php");
include("funcoes.php");

$idagendamento = anti_injection($_POST['id_agendamento']);
$status = anti_injection($_POST['status']);  

$str = "SELECT * FROM agendamentos WHERE codigo = '$idagendamento' LIMIT 1";
$rs = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$num = mysqli_num_rows($rs);
$vet = mysqli_fetch_array($rs);

if($num > 0)
{
    $idpaciente = $vet['idpaciente'];

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
<p>
    <b>Nome do Paciente:</b> <?=$vetP['nome'];?><br>
    <b>Data de Nascimento:</b> <?=$data_nascimento;?><br>
    <b>Agendado para:</b> <?=$data;?> das <?=$hora_inicial;?> às <?=$hora_final;?>                                            
</p>

<script>
function exibe_parcelas(value)
{
    if(value == 1)
    {
        document.getElementById('div_parcelas').style.display = 'none';
        document.getElementById('parcelas').value = '';
        document.getElementById('parcelas').required = false;
    }
    else
    {
        document.getElementById('div_parcelas').style.display = 'block';
        document.getElementById('parcelas').value = '';
        document.getElementById('parcelas').required = true;
    }
}
</script>

<form method="post" class="form-inline" name="form_l" id="form_l" enctype="multipart/form-data"> 
    <input type="hidden" name="cmd" value="edit_pagto">
    <input type="hidden" name="idagendamento" id="id_agendamento_modal" value="<?=$idagendamento;?>">
    <input type="hidden" name="status" id="status" value="<?=$status;?>">
    <div class="row">
        <div class="col-md-6">
            <p class="font-bold">
                Valor da consulta*
            </p>
            <input class="form-control" type="text" name="valor" id="valor" value="<?=number_format($vet['valor'], 2, ',', '.')?>" required onKeyUp="javascript: return auto_valor('valor');" onKeyPress="javascript: return somenteNumeros(event);">
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6">
            <p class="font-bold">
                Forma de pagto*
            </p>
            <select class="form-control" name="forma_pagto" id="forma_pagto" required onchange="javascript: exibe_parcelas(this.value)">
                <option value="" <?=(!$vet['forma_pagto']) ? 'selected' : ''?>>Selecione ...</option>
                <option value="1" <?=($vet['forma_pagto'] == 1) ? 'selected' : ''?>>Dinheiro</option>
                <option value="2" <?=($vet['forma_pagto'] == 2) ? 'selected' : ''?>>Cartão</option>
                <option value="3" <?=($vet['forma_pagto'] == 3) ? 'selected' : ''?>>Dinheiro / Cartão</option>
            </select>
        </div>
        <div class="col-md-6" id="div_parcelas" <?=($vet['forma_pagto'] == 1) ? 'style="display: none"' : ''?>>
            <p class="font-bold">
                Parcelas*
            </p>
            <input class="form-control" type="number" name="parcelas" id="parcelas" value="<?=$vet['parcelas']?>" required min="0" >
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-xs-12 text-right">
            <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
    </div>
</form>
<?
}
?>