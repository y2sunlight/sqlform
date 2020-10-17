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
</head>
<body>
    <div style="width:800px;text-align:right;">
        <select name="sql_files" id="sql_files" onchange="doFileChange()">
            <option value="">(選択して下さい)</option>
            <option value="sample.sql">sample.sql</option>
            <option value="syaval1.sql">syaval1.sql</option>
            <option value="syaval2.sql">syaval2.sql</option>
            <option value="syaval3.sql">syaval3.sql</option>
        </select>
        <button type="button" onclick="doExec()">実行</button>
    </div>
    <div style="width:800px;">
        <textarea name="sqlText" id="sqlText" style="width:100%; height:200px">SELECT * FROM syain;</textarea>
        <div id="result" style="overflow:auto; width:100%; height:300px; border:solid 1px #aaa;padding:5px;"></div>
    </div>
</body>
</html>