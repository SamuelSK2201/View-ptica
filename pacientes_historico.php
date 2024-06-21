<?
$page = 'pacientes';
include("includes/header.php");

if($_SESSION["adm_user"] != 2)
    die("Acesso negado!");

if($perm_pacientes != 1)
    die("Acesso negado!");

$id = anti_injection($_REQUEST['idpaciente']);

if($_POST['cmd'] == "edit_pagto")
{
    $idagendamento = $_POST['idagendamento'];
    $valor = str_replace(",", ".", str_replace(".", "", $_POST['valor']));
    $forma_pagto = $_POST['forma_pagto'];
    $parcelas = $_POST['parcelas'];

    $str = "UPDATE agendamentos SET valor = '$valor', forma_pagto = '$forma_pagto', parcelas = '$parcelas', data_pagto = NOW() WHERE codigo = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("pacientes_historico.php?ind_msg=1&idpaciente=$id");
}

if($_GET['cmd'] == "del_pagto")
{
    $idagendamento = anti_injection($_GET['idagendamento']);

    $str = "UPDATE agendamentos SET valor = NULL, forma_pagto = NULL, parcelas = NULL, data_pagto = NULL WHERE codigo = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("pacientes_historico.php?ind_msg=2&idpaciente=$id");
}

if($_GET['cmd'] == "del_consulta")
{
    $idagendamento = anti_injection($_GET['idagendamento']);

    $str = "DELETE FROM agendamentos WHERE codigo = '$idagendamento'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

    redireciona("pacientes_historico.php?ind_msg=3&idpaciente=$id");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Pagamento editado com sucesso!';
elseif($_GET['ind_msg'] == "2")
    $msg = 'Pagamento excluído com sucesso!';
elseif($_GET['ind_msg'] == "3")
    $msg = 'Consulta excluída com sucesso!';

$str = "SELECT * FROM pacientes WHERE idempresa = '$adm_empresa' AND codigo = '$id'";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
$vet = mysqli_fetch_array($rs);
?>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Histórico de pacientes</h2>
        <ol class="breadcrumb">
            <li><a class="btn btn-default" href="pacientes.php?ind=2&codigo=<?=$vet['codigo']?>">Editar paciente</a></li>
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
            <div class="tabs-container">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#tab-1">Informações básicas</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-2">Financeiro</a></li>
                    <li class=""><a data-toggle="tab" href="#tab-3">Consultas</a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane active">
                        <div class="panel-body">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold" >
                                            Nome completo
                                        </p>
                                        <?=stripslashes($vet['nome'])?>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold" >
                                            Nascimento
                                        </p>
                                        <?=($vet['data_nascimento']) ? ConverteData($vet['data_nascimento']) : ''?>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold" >
                                            CPF
                                        </p>
                                        <?=$vet['cpf']?>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold" >
                                            RG
                                        </p>
                                        <?=($vet['rg']) ? $vet['rg'] : 'Não informado'?>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold" >
                                            Telefone 01
                                        </p>
                                        <?=($vet['telefone']) ? '<a href="https://api.whatsapp.com/send?phone=55'.preg_replace('/[^\d]/', '', $vet['telefone']).'" target="_blank">'.$vet['telefone'].'</a>' : 'Não informado'?>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold" >
                                            Telefone 02
                                        </p>
                                        <?=($vet['telefone']) ? '<a href="https://api.whatsapp.com/send?phone=55'.preg_replace('/[^\d]/', '', $vet['telefone2']).'" target="_blank">'.$vet['telefone2'].'</a>' : 'Não informado'?>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold" >
                                            Email
                                        </p>
                                        <?=($vet['email']) ? $vet['email'] : 'Não informado'?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold" >
                                            CEP
                                        </p>
                                        <?=($vet['cep']) ? $vet['cep'] : 'Não informado'?>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold" >
                                            Endereço
                                        </p>
                                        <?=($vet['endereco']) ? stripslashes($vet['endereco']) : 'Não informado'?>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold" >
                                            Número
                                        </p>
                                        <?=($vet['numero']) ? $vet['numero'] : 'Não informado'?>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold" >
                                            Complemento
                                        </p>
                                        <?=($vet['complemento']) ? stripslashes($vet['complemento']) : 'Não informado'?>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold" >
                                            Bairro
                                        </p>
                                        <?=($vet['bairro']) ? stripslashes($vet['bairro']) : 'Não informado'?>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold" >
                                            Cidade
                                        </p>
                                        <?=($vet['cidade']) ? stripslashes($vet['cidade']) : 'Não informado'?>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="font-bold" >
                                            Estado
                                        </p>
                                        <?=($vet['estado']) ? $vet['estado'] : 'Não informado'?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tab-2" class="tab-pane">
                        <div class="panel-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Data do pagamento</th>
                                        <th>Valor da consulta</th>
                                        <th>Forma de pagameto</th>
                                        <th>Parceiro</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?
                                    $str = "SELECT A.*, DATE_FORMAT(A.data_pagto, '%d/%m/%Y %H:%i') AS dt_pagto, B.nome AS otica
                                        FROM agendamentos A
                                        LEFT JOIN oticas B ON A.idotica = B.codigo
                                        WHERE A.idempresa = '$adm_empresa'
                                        AND A.idpaciente = '$id' 
                                        AND A.valor > '0' 
                                        $strWhereP
                                        ORDER BY A.data_pagto";
                                    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                                    
                                    $i = 0;
                                    while($vet = mysqli_fetch_array($rs))
                                    {
                                        $i++;

                                        if($vet['forma_pagto'] == 1)
                                            $forma_pagto = 'Dinheiro';
                                        elseif($vet['forma_pagto'] == 2)
                                            $forma_pagto = 'Cartão';
                                        elseif($vet['forma_pagto'] == 3)
                                            $forma_pagto = 'Dinheiro / Cartão';
                                        else
                                            $forma_pagto = 'Pendente';

                                        if($vet['parcelas'] > 0)
                                            $forma_pagto .= ' ('.$vet['parcelas'].'x)';
                                    ?>
                                    <tr>
                                        <td><?=$i?></td>
                                        <td><?=$vet['dt_pagto']?></td>
                                        <td>R$ <?=number_format($vet['valor'], 2, ',', '.')?></td>
                                        <td><?=$forma_pagto?></td>
                                        <td><?=stripslashes($vet['otica'])?></td>
                                        <td>
                                            <a class="btn btn-warning btn-circle" type="button" title="editar / visualizar" data-toggle="modal" data-target="#financeiro_<?=$vet['codigo']?>"><i class="fa fa-edit"></i></a>
                                            <a class="btn btn-danger btn-circle" type="button" title="excluir" href="pacientes_historico.php?cmd=del_pagto&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a>
                                        </td>
                                    </tr>
                                    <?
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="tab-3" class="tab-pane">
                        <div class="panel-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Data da consulta</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?
                                    $str = "SELECT DISTINCT * FROM agendamentos WHERE idempresa = '$adm_empresa' AND idpaciente = '$id' AND status = '6' $strWhereP ORDER BY data";
                                    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
                                    
                                    $i = 0;
                                    while($vet = mysqli_fetch_array($rs))
                                    {
                                        $i++;
                                    ?>
                                    <tr>
                                        <td><?=$i?></td>
                                        <td><?=ConverteData($vet['data'])?></td>
                                        <td>
                                            <a class="btn btn-info btn-circle" type="button" title="editar / visualizar" href="prescricoes.php?idagendamento=<?=$vet['codigo']?>&s=6"><i class="fa fa-check-square"></i></a>
                                            <a class="btn btn-danger btn-circle" type="button" title="excluir" href="pacientes_historico.php?cmd=del_consulta&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente excluir este registro?')) { return false }"><i class="fa fa-trash-o"></i></a>
                                        </td>
                                    </tr>
                                    <?
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
<?
$str = "SELECT A.*, B.nome AS otica
    FROM agendamentos A
    LEFT JOIN oticas B ON A.idotica = B.codigo
    WHERE A.idempresa = '$adm_empresa'
    AND A.idpaciente = '$id' 
    AND A.valor > '0' 
    ORDER BY A.data_pagto";
$rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));

$i = 0;
while($vet = mysqli_fetch_array($rs))
{
    $i++;
?>
<div class="modal inmodal fade" id="financeiro_<?=$vet['codigo']?>" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Fechar</span></button>
                <h3>Editar pagamento - <?=$i?></h3>
            </div>                                        
            <div class="modal-body">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <form method="post" class="form-inline" name="form_l" id="form_l" enctype="multipart/form-data"> 
                            <input type="hidden" name="cmd" value="edit_pagto">
                            <input type="hidden" name="idpaciente" id="idpaciente" value="<?=$id?>">
                            <input type="hidden" name="idagendamento" id="idagendamento" value="<?=$vet['codigo']?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="font-bold">
                                        Valor da consulta*
                                    </p>
                                    <input class="form-control" type="text" name="valor" id="valor" value="<?=number_format($vet['valor'], 2, ',', '.')?>" required onKeyUp="javascript: return auto_valor('valor');" onKeyPress="javascript: return somenteNumeros(event);">
                                </div>
                                <div class="col-md-6">
                                    <p class="font-bold">
                                        Convênio
                                    </p>
                                    <select name="idotica" id="idotica" required data-placeholder="Selecione um convênio ..." class="chosen-select" tabindex="10" >
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

include("includes/footer.php");
?>