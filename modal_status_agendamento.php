<?
include("s_acessos.php");
include("funcoes.php");

$idagendamento = anti_injection($_POST['id_agendamento']);  

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
<form method="post" class="form-inline" name="form_a" id="form_a" enctype="multipart/form-data"> 
    <input type="hidden" name="cmd" value="edit_status">
    <input type="hidden" name="idagendamento" value="<?=$idagendamento;?>">
    <div class="row">
        <div class="col-md-12">
            <p class="font-bold">
                Status do agendamento
            </p>
            <select class="form-control" name="status" id="status" required>
                <option value="1" <?=($vet['status'] == 1) ? 'selected' : ''?>>Agendado</option>
                <option value="2" <?=($vet['status'] == 2) ? 'selected' : ''?>>Reagendado</option>
                <option value="3" <?=($vet['status'] == 3) ? 'selected' : ''?>>Confirmado</option>
                <option value="4" <?=($vet['status'] == 4) ? 'selected' : ''?>>Fila de espera</option>
                <option value="5" <?=($vet['status'] == 5) ? 'selected' : ''?>>Não compareceu</option>
                <option value="6" <?=($vet['status'] == 6) ? 'selected' : ''?>>Atendido</option>
                <option value="7" <?=($vet['status'] == 7) ? 'selected' : ''?>>Aguardando confirmação</option>
            </select>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-xs-12 text-right">
            <a href="javascript:;" onclick="modalLancarRecebimentos('<?=$idagendamento;?>', document.form_a.status.value);" class="btn btn-default"><i class="fa fa-money"></i> Lançar Recebimento</a>
            <a class="btn btn-warning" type="button" title="editar / visualizar" href="agendamentos.php?ind=2&codigo=<?=$idagendamento?>"><i class="fa fa-edit"></i> Editar</a>
            <a class="btn btn-danger" type="button" title="excluir" href="agendamentos.php?cmd=del&codigo=<?=$idagendamento?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i> Excluir</a> 
            <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
    </div>
</form>
<?
}
?>