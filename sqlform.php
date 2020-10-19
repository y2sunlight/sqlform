<?php
require 'config.php';
session_start();

require 'utilities.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="api-token" content="<?php echo generate_api_token()?>">
    <title>SqlForm</title>

    <style>
        body{font-family: Monospace;}
        table{border-collapse: collapse;}
        table th, table td {border: solid 1px;}
        .type-sql{}
        .type-result{color:blue}
        .type-query{}
        .type-error{color:red}
    </style>

    <!-- stylesheet -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="<?php echo asset_get('css/main.css')?>" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="<?php echo asset_get('js/main.js')?>"></script>
    <script src="<?php echo asset_get('js/sqlapi.js')?>"></script>
    <script src="<?php echo asset_get('js/action.js')?>"></script>
</head>
<body>
    <nav class="navbar navbar-expand navbar-dark bg-dark">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item mr-1"><select name="sql_files" id="sql_files" class="form-control" onchange="doFileChange()"></select></li>
            <li class="nav-item mr-1"><button type="button" class="btn btn-primary" onclick="alert('新規')">新規</button></li>
            <li class="nav-item mr-1"><button type="button" class="btn btn-primary" onclick="alert('保存')">保存</button></li>
            <li class="nav-item mr-1"><button type="button" class="btn btn-primary" onclick="alert('名前を付けて保存')">名前を付けて保存</button></li>
            <li class="nav-item mr-1"><button type="button" class="btn btn-primary" onclick="alert('削除')">削除</button></li>
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item mr-1"><input type="text" name="filename" id="filename" class="form-control" value=""></li>
            <li class="nav-item"><button type="button" class="btn btn-primary" onclick="doExec()">実行</button></li>
        </ul>
    </nav>

    <div class="container-fluid mt-2">
        <textarea name="sqlText" id="sqlText" class="form-control" style="height:200px;">SELECT * FROM syain;</textarea>
        <div id="result" class="mt-1 border border-secondary rounded p-1" style="overflow:auto; min-height:300px; max-height:500px;"></div>
    </div>
</body>
</html>