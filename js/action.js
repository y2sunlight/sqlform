/**
 * SQLファイル一覧
 */
function doList()
{
    refreshList();
}

/**
 * SQLファイル読み込み
 */
function doRead()
{
    var sqlFile = document.getElementById('sql_files').value;
    if(!sqlFile) return false;
    document.getElementById('filename').value = sqlFile;

    sqlapi('read', {f:sqlFile}, function (response) {
        document.getElementById('sqlText').value = response.data.text;
        enableButton();
    });
}

/**
 * SQLファイル上書き保存
 */
function doSave()
{
    var fileName = document.getElementById('filename').value;
    if(!fileName) return false;
    var sqlText = document.getElementById('sqlText').value;

    sqlapi('write', {f:fileName,t:sqlText,i:1},
        function (response) {
            refreshList(fileName);
        },
        function (response) {
            if (response.data.error===1)
            {
                $('#forcedSaveModal').modal('show');
            }
        }
    );
}

/**
 * SQLファイル削除
 */
function doForcedSave()
{
    var fileName = document.getElementById('filename').value;
    if(!fileName) return false;
    var sqlText = document.getElementById('sqlText').value;

    sqlapi('write', {f:fileName,t:sqlText}, function(response) {
        refreshList(fileName);
    });
}

/**
 * SQLファイル削除
 */
function doDelete()
{
    var fileName = document.getElementById('filename').value;
    if(!fileName) return false;

    sqlapi('delete', {f:fileName}, function (response) {
        document.getElementById('filename').value = "";
        refreshList();
    });
}

/**
 * SQL実行
 */
function doExec()
{
    var sqlText = document.getElementById('sqlText').value;
    document.getElementById('result').innerHTML = '';
    document.getElementById('message').innerHTML = 'Running ...';
    document.getElementById('exec-time').innerHTML = '0.000 sec';

    sqlapi('execute', {t:sqlText}, function (response) {
        var html = "";
        for(var line of response.data.lines) {
            // sql文
            html += `<pre class="type-sql">${line.command}</pre>`;
            switch(line.type) {
                case 1:	// 実行結果
                    html += `<pre class="type-result">Result : ${line.result}</pre>`;
                    break;
                case 2:	// 検索結果

                    if(line.result.length>0)
                    {
                        html += '<table class="type-query">';

                        // カラム名の表示
                        html += '<tr>';
                        for(var key in line.result[0])
                        {
                            html += `<th>${key}</th>`
                        }
                        html += '</tr>';

                        // 行の表示
                        for(var rows of line.result)
                        {
                            html += '<tr>';
                            for(var key in rows)
                            {
                                html += `<td>${rows[key]}</td>`
                            }
                            html += '</tr>';
                        }

                        html += '</table>';
                    }
                    // 件数
                    html += `<pre class="type-result">Count : ${line.result.length}</pre>`

                    break;
                case -1:	// エラー
                    html += `<pre class="type-error">Error : ${line.result}</pre>`;
                    break;
            }
        }
        document.getElementById('result').innerHTML = html;
        showFooter('ok', response.data.execTime);
    });
}

/**
 * SQLファイル一覧のリフレッシュ
 */
function refreshList(fileName)
{
    sqlapi('list', {}, function (response) {
        // オプション作成
        createOption(response);

        if (fileName!==undefined) {
            // ファイル選択
            document.getElementById('sql_files').value = fileName;

            // SQLファイル読み込み
            doRead();
        }
        enableButton();
    });
}

/**
 * selectボックスのoption作成
 * @param response
 * @returns
 */
function createOption(response)
{
    var html = '<option value="">(選択して下さい)</option>';
    for(var file of response.data.files) {
        html += `<option value=${file}>${file}</option>`
    }
    document.getElementById('sql_files').innerHTML = html;
}

/**
 * ボタンの有効化
 */
function enableButton()
{
    var fileName = document.getElementById('filename').value;

    $('#btn_save').prop('disabled',!fileName);
    $('#btn_delete').prop('disabled',!fileName);
}

