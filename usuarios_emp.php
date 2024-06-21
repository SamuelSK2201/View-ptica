<?
$page = 'usuarios_emp';
include("includes/header.php");

if($_SESSION["adm_user"] != 1)
    die("Acesso negado!");

if($_GET['codigo'] == TRUE)
    $codigo = anti_injection($_GET['codigo']); 
else 
    $codigo = anti_injection($_POST['codigo']);

if($_GET['cmd'] == "perm")
{
    $str = "SELECT * FROM usuarios WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $vet = mysqli_fetch_array($rs);

    $idempresa = $vet['idempresa'];

    $str = "INSERT INTO usuarios_tipos (idempresa, nome) VALUES ('$idempresa', 'Administrador')";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    $idtipo = mysqli_insert_id($conexao);

    $str = "UPDATE usuarios SET idtipo = '$idtipo' WHERE codigo = '$codigo'";
    $rs  = mysqli_query($conexao, $str) or die(mysqli_error($conexao));
    
    redireciona("usuarios_emp.php?ind_msg=1");
}

$msg = '';

if($_GET['ind_msg'] == "1")
    $msg = 'Permissão total dada ao usuário com sucesso!';

$title = "'View Optica<br>Usuários (empresas)'";
$columns = '0, 1, 2, 3';
$order = ',order: [[ 0, "asc" ]]';
?>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Usuários (empresas)</h2>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <?
    $str = "SELECT A.*, C.razao_social 
        FROM usuarios A 
        INNER JOIN empresas C ON A.idempresa = C.codigo
        ORDER BY A.nome";
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
                    <h5>Lista de usuários (empresa) cadastrados no sistema</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table id="list" class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Empresa</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                while($vet = mysqli_fetch_array($rs))
                                {
                                    if($vet['status'] == 1)
                                        $status = '<span class="label label-success">Ativo</span>';
                                    else
                                        $status = '<span class="label label-danger">Inativo</span>';
                                ?>
                                <tr class="gradeX">
                                    <td><?=stripslashes($vet['nome'])?></td>
                                    <td><?=stripslashes($vet['razao_social'])?></td>
                                    <td><?=$vet['email']?></td>
                                    <td><?=$status?></td>
                                    <td>
                                        <a class="btn btn-info btn-circle" type="button" title="permissão total" href="usuarios_emp.php?cmd=perm&codigo=<?=$vet['codigo']?>" onclick="javascript: if(!confirm('Deseja realmente dar permissão total a este usuário?')) { return false }"><i class="fa fa-magic"></i></a>
                                    </td>
                                </tr>
                                <?
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Nome</th>
                                    <th>Empresa</th>
                                    <th>Email</th>
                                    <th>Status</th>
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
                Nenhum usuário encontrado no sistema.
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