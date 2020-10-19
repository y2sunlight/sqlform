/**
 * SQLファイル一覧
 */
function doSqlFiles()
{
    sqlapi('list', {}, function (response) {
        var html = '<option value="">(選択して下さい)</option>';
        for(var file of response.data.files)
        {
            html += '<option value=' + file + '>' + file + '</option>'
        }
        document.getElementById('sql_files').innerHTML = html;
    });
}

/**
 * SQLファイル変更
 */
function doFileChange()
{
    var sqlFile = document.getElementById('sql_files').value;
    if(!sqlFile) return;
    document.getElementById('filename').value = sqlFile;

    sqlapi('read', {f:sqlFile}, function (response) {
        document.getElementById('sqlText').value = response.data.text;
    });
}

/**
 * SQL実行
 */
function doExec()
{
    var sqlText = document.getElementById('sqlText').value;
    sqlapi('execute', {t:sqlText}, function (response) {
        var html = "";
        for(var line of response.data.lines)
        {
            switch(line.type)
            {
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
