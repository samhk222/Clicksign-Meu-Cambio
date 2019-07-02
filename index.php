<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'vendor/autoload.php';
require 'includes/CLICKSIGN.php';
require 'includes/UTILS.php';

use Carbon\Carbon;

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();
$clicksign = new CLICKSIGN();
$content = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_GET['action'] == 'associar') {
        $content = $clicksign->addSignerToDocument($_POST);
    }

    if ($_GET['action'] == 'addSigner') {
        $content = $clicksign->createSigner($_POST);
    }

    if ($_GET['action'] == 'upload') {
        $content = $clicksign->upload($_FILES['document']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POC Clicksign</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style type="text/css">
        .label {
            font-size: 13px;
        }

        .modal-dialog {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .modal-content {
            height: auto;
            min-height: 100%;
            border-radius: 0;
        }
    </style>
</head>


<!-- Modal -->
<div id="modal_container" class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <div id="modal_header"></div>
                </h4>
            </div>
            <div class="modal-body">
                <div id='container' style="height: 600px">Escolha um documento na parte de documentos x signatários</div>
            </div>
        </div>
    </div>
</div>

<body class='container-fluid'>

    <div class="panel panel-warning">
        <div class="panel-heading">
            <h3 class="panel-title">Retorno API</h3>
        </div>
        <div class="panel-body" id="content">
            <?php echo UTILS::handleResponse($content); ?>
        </div>
    </div>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Documentos</h3>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Enviar novo documento</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class='col-md-12'>
                            <form action="?action=upload" enctype="multipart/form-data" method="POST">
                                <div class='col-md-12'>
                                    <input type="file" name="document" id="document" />
                                </div>
                                <div class='col-md-12' style="margin-top:10px;">
                                    <input type='submit' name='btn_' id='btn_' class='btn btn-primary' value='Enviar' />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Documentos cadastrados</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class='col-md-12'>
                            <table width="90%" class="table table-striped table-bordered table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Key</th>
                                        <th>Account</th>
                                        <th>Filename</th>
                                        <th>Upload</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($clicksign->list('documents') as $data) {
                                        $uploaded = (new Carbon($data['uploaded_at']))->format('d/m/Y H:i:s');
                                        echo <<<EOL
                                <tr>
                                    <td class='text-center' width='1%'>{$data['id']}</td>
                                    <td><span class="label label-info">{$data['key']}</span></td>
                                    <td>{$data['account_key']}</td>
                                    <td><a href='{$data['original_file_url']}' target='_blank'>{$data['filename']}</a></td>
                                    <td>{$uploaded}</td>
                                </tr>
EOL;
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

    <a name="signatarios"></a>
    <div class="panel panel-primary">
        <?php
        $faker = Faker\Factory::create('pt_BR');
        ?>
        <div class="panel-heading">
            <h3 class="panel-title">Signatários</h3>
        </div>
        <div class="panel-body">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Listagem</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class='col-md-12'>
                            <table width="90%" class="table table-striped table-bordered table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Key</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($clicksign->list('signers') as $data) {
                                        $created = (new Carbon($data['created_at']))->format('d/m/Y H:i:s');
                                        echo <<<EOL
                                        <tr>
                                            <td class='text-center' width='1%'>{$data['id']}</td>
                                            <td><span class="label label-info">{$data['key']}</span></td>
                                            <td>{$data['name']}</td>
                                            <td>{$data['email']}</td>
                                            <td>{$created}</td>
                                        </tr>
EOL;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Cadastrar</h3>
                </div>
                <div class="panel-body">
                    <form action="?action=addSigner#signatarios" method="POST">
                        <div class="row">
                            <div class='col-md-12'>
                                <label for='name'>Nome</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $faker->name; ?>" />
                            </div>
                        </div>
                        <div class="row" style="margin-top:12px;">
                            <div class='col-md-3'>
                                <label for='email'>Email</label>
                                <input type="text" class="form-control" id="email" name="email" value="<?php echo $faker->email; ?>" />
                            </div>
                            <div class='col-md-3'>
                                <label for='tbl'>Telefone</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo preg_replace('/[^0-9]/', '', $faker->phoneNumber); ?>" />
                            </div>
                            <div class='col-md-3'>
                                <label for='documentation'>CPF</label>
                                <input type="text" class="form-control" id="documentation" name="documentation" value="<?php echo $faker->cpf; ?>" />
                            </div>
                            <div class='col-md-3'>
                                <label for='birthday'>Aniversário</label>
                                <input type="text" class="form-control" id="birthday" name="birthday" value="<?php echo $faker->date('Y-m-d', 'now'); ?>" />
                            </div>
                        </div>
                        <div class="row" style="margin-top:12px;">
                            <div class='col-md-2'>
                                <input type='submit' name='btn_' id='btn_' class='btn btn-default' value='Cadastrar' />
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <a name="associar"></a>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Documento x Signatários</h3>
        </div>



        <div class="panel-body">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Associações</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class='col-md-12'>
                            <table width="90%" class="table table-striped table-bordered table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Arquivo</th>
                                        <th>Signer</th>
                                        <th>Papel</th>
                                        <th>URL</th>
                                        <th>Created</th>
                                        <th>widget</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "
                                    SELECT
                                        ds.id,
                                        d.filename,
                                        s.name,
                                        ds.url,
                                        ds.sign_as,
                                        ds.created_at,
                                        ds.request_signature_key,
                                        r.description
                                    FROM
                                        documents_signers ds
                                        LEFT JOIN documents d on d.\"key\" = ds.document_key
                                        LEFT JOIN signers s on ds.signer_key = s.\"key\"
                                        LEFT JOIN roles r on r.\"role\" = ds.sign_as
                                    ORDER BY
                                        ds.id DESC
                                    ";
                                    $stmt = $clicksign->DB->prepare($sql);
                                    $stmt->execute();
                                    $rows = $stmt->fetchAll();

                                    foreach ($rows as $data) {
                                        $created = (new Carbon($data['created_at']))->format('d/m/Y H:i:s');
                                        echo <<<EOL
                                        <tr>
                                            <td class='text-center' width='1%'>{$data['id']}</td>
                                            <td><span class="label label-info">{$data['filename']}</span></td>
                                            <td>{$data['name']}</td>
                                            <td>{$data['sign_as']} - {$data['description']}</td>
                                            <td><a href='{$data['url']}' target='_blank'>{$data['url']}</a></td>
                                            <td>{$created}</td>
                                            <td class='text-center'><i style="cursor:pointer;" class="fa fa-pencil-square fa-2x fa-border" onclick="openWidget('{$data['request_signature_key']}')"></i> </td>
                                        </tr>
EOL;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Associar</h3>
                </div>
                <div class="panel-body">
                    <form action="?action=associar#associar" method="POST">
                        <div class="row">
                            <div class='col-md-3'>
                                <label for='document'>Documento</label>
                                <select name="document" id="document" class="form-control">
                                    <?php
                                    foreach ($clicksign->list('documents') as $key => $value) {
                                        echo "<option value='{$value['account_key']}:{$value['key']}'>{$value['filename']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class='col-md-3'>
                                <label for='signer_key'>Signatário</label>
                                <select name="signer_key" id="signer_key" class="form-control">
                                    <?php
                                    foreach ($clicksign->list('signers') as $key => $value) {
                                        echo "<option value='{$value['key']}'>{$value['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class='col-md-3'>
                                <label for='role'>Papel</label>
                                <select name="role" id="role" class="form-control">
                                    <?php
                                    $sql = "select * from roles order by `default` ASC";
                                    $stmt = $clicksign->DB->prepare($sql);
                                    $stmt->execute();
                                    $rows = $stmt->fetchAll();

                                    foreach ($rows as $value) {
                                        echo "<option value='{$value['role']}'>{$value['role']} - {$value['description']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class='col-md-3'>
                                <label for='tbl'>&nbsp;</label><br />
                                <input type='submit' name='btn_' id='btn_' class='btn btn-primary' value='Associar' />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src='includes/embedded.js' type='text/javascript'></script>

    <script type='text/javascript'>
        var widget;

        function openWidget(request_signature_key) {
            scroll(0, 0);

            if (widget) {
                widget.umount();
            }

            widget = new Clicksign(request_signature_key);

            widget.endpoint = 'https://sandbox.clicksign.com';
            widget.origin = 'http://0.0.0.0:8085/';
            widget.mount('container');

            widget.on('loaded', function(ev) {
                console.log('loaded!');
            });
            widget.on('signed', function(ev) {
                console.log('signed!');
            });

            $('#modal_header').html("Signature Key: " + request_signature_key);
            $('#modal_container').modal('show');

        }
    </script>

</body>

</html>
