<?php
require 'config.php';
session_start();

require 'utilities.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="api-token" content="<?php echo generate_api_token()?>">
    <title>SqlForm</title>

    <!-- stylesheet -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="<?php echo asset_get('css/main.css')?>" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <script type="module" src="https://unpkg.com/ionicons@5.2.3/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule="" src="https://unpkg.com/ionicons@5.2.3/dist/ionicons/ionicons.js"></script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="<?php echo asset_get('js/main.js')?>" defer></script>
    <script src="<?php echo asset_get('js/sqlapi.js')?>" defer></script>
    <script src="<?php echo asset_get('js/action.js')?>" defer></script>
</head>
<body>
    <nav class="navbar navbar-expand navbar-dark bg-primary">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item mr-1"><select name="sql_files" id="sql_files" class="form-control" onchange="doRead()"></select></li>
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item mr-1">
                <input type="text" name="filename" id="filename" class="form-control" placeholder="untitled.sql">
            </li>
            <li class="nav-item">
                <button type="button" id="btn_save" class="btn btn-primary pb-0" onclick="doSave()"><ion-icon name="save"></ion-icon></button>
            </li>
            <li class="nav-item mr-1">
                <button type="button" id="btn_delete" class="btn btn-primary pb-0" data-toggle="modal" data-target="#deleteModal"><ion-icon name="trash"></ion-icon></button>
            </li>
            <li class="nav-item">
                <button type="button" id="btn_exec" class="btn btn-primary pb-0" onclick="doExec()"><ion-icon name="play"></ion-icon></button>
            </li>
        </ul>
    </nav>

    <div class="container-fluid mt-2 mb-5">
        <textarea name="sqlText" id="sqlText" class="form-control"></textarea>
        <div id="result" class="mt-1 border border-secondary rounded p-1"></div>
    </div>

    <footer class="fixed-bottom bg-light border p-2">
        <div class="row">
            <div class="col-8" id="message"></div>
            <div class="col-4 text-right">
                <ion-icon name="time-outline" class="ion-icon"></ion-icon>
                <span id="exec-time" class="ion-text"></span>
            </div>
        </div>
    </footer>

    <!-- 削除Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-body">
              <span id="delete-dialog-msg"></span>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" id="confirm-dialog-callback" class="btn btn-primary" onclick="doDelete();$('#deleteModal').modal('hide')">OK</button>
          </div>
        </div>
      </div>
    </div>

    <!-- 上書き保存Modal -->
    <div class="modal fade" id="forcedSaveModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-body">
              <span id="save-dialog-msg"></span>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" id="confirm-dialog-callback" class="btn btn-primary" onclick="doForcedSave();$('#forcedSaveModal').modal('hide')">OK</button>
          </div>
        </div>
      </div>
    </div>

</body>
</html>