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
 * SQLファイル書き込み
 */
function doWrite()
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
                // 上書き確認
                if(confirm(`${fileName} は既に存在します。上書保存しますか。`) == true) {
                    sqlapi('write', {f:fileName,t:sqlText}, function(response) {
                        refreshList(fileName);
                    });
                }
            }
        }
    );
}

/**
 * SQLファイル削除
 */
function doDelete()
{
    var fileName = document.getElementById('filename').value;
    if(!fileName) return false;

    if(confirm(fileName + ' を削除します。よろしいですか？') == true) {
        sqlapi('delete', {f:fileName}, function (response) {
            document.getElementById('filename').value = "";
            refreshList();
        });
    }
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
        html += '<option value=' + file + '>' + file + '</option>'
    }
    document.getElementById('sql_files').innerHTML = html;
}

function enableButton()
{
    var fileName = document.getElementById('filename').value;

    $('#btn_save').prop('disabled',!fileName);
    $('#btn_delete').prop('disabled',!fileName);
}

/**
 * SQL実行
 */
function doExec()
{
    var sqlText = document.getElementById('sqlText').value;
    sqlapi('execute', {t:sqlText}, function (response) {
        var html = "";
        for(var line of response.data.lines) {
            switch(line.type) {
                case 0:	// sql文
                    html += '<pre class="type-sql">' + line.line + '</pre>';
                    break;
                case 1:	// 実行結果
                    html += '<pre class="type-result">Result : ' + line.line + '</pre>';
                    break;
                case 2:	// 検索結果

                    if(line.line.length>0)
                    {
                        html += '<table class="type-query">';

                        // カラム名の表示
                        html += '<tr>';
                        for(var key in line.line[0])
                        {
                            html += '<th>' + key + '</th>'
                        }
                        html += '</tr>';

                        // 行の表示
                        for(var rows of line.line)
                        {
                            html += '<tr>';
                            for(var key in rows)
                            {
                                html += '<td>' + rows[key] + '</td>'
                            }
                            html += '</tr>';
                        }

                        html += '</table>';
                    }
                    // 件数
                    html += '<pre class="type-result">Result : ' + line.line.length + '件</pre>'

                    break;
                case -1:	// エラー
                    html += '<pre class="type-error">Error : ' + line.line + '</pre>';
                    break;
            }
        }
        document.getElementById('result').innerHTML = html;
    });
}
