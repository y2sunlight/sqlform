/**
 * SQLファイル変更
 */
function doFileChange()
{
    var sqlFile = document.getElementById('sql_files').value;
    if(!sqlFile) return;

    var params = new FormData();
    params.append('cmd', 'read');
    params.append('f', sqlFile);

    axios.post('sqlapi.php', params, {
        headers: {
            'Authorization': 'Bearer '+$('meta[name="api-token"]').attr('content')
        }
    })
    .then(function (response) {
        if (!checkResult(response)) return;
        document.getElementById('sqlText').value = response.data.text;
    })
    .catch(function (error) {
        errorAlert(error);
    });
}

/**
 * SQL実行
 */
function doExec()
{
    var sqlText = document.getElementById('sqlText').value;

    var params = new FormData();
    params.append('cmd', 'execute');
    params.append('t', sqlText);
    // params.append('f', 'sample1.sql');

    axios.post('sqlapi.php', params, {
        headers: {
            'Authorization': 'Bearer '+$('meta[name="api-token"]').attr('content')
        }
    })
    .then(function (response) {
        if (!checkResult(response)) return;

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
    })
    .catch(function (error) {
        errorAlert(error);
    });
}

/**
 * 共通のレスポンスチェック
 * @param response
 * @returns
 */
function checkResult(response)
{
    if (response.data.error!==0)
    {
        alert('Response Error');
        return false;
    }
    return true;
}

/**
 * 共通のエラー処理
 * @param error
 * @returns
 */
function errorAlert(error)
{
    if (error.response)
    {
        // サーバがステータスコードで応答
        console.log(error.response.data);
        console.log(error.response.status);
        console.log(error.response.headers);
        var msg = error.response.status + ' : ' + error.response.statusText + '\n' + error.response.data;
        alert(msg);
    }
    else
    {
        // トリガーしたリクエストの設定に何かしらのエラーがある
        console.log('Error', error.message);
        alert(error.message);
    }
}
