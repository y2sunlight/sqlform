<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
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

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script type="text/javascript">
    function doFileChange()
    {
        var sqlFile = document.getElementById('sql_files').value;
        if(!sqlFile) return;

        var params = new FormData();
        params.append('cmd', 'read');
        params.append('f', sqlFile);

        axios.post('sqlapi.php', params)
        .then(function (response) {

            document.getElementById('sqlText').value = response.data.text;
        })
        .catch(function (error) {

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
        });
    }

    function doExec()
    {
        var sqlText = document.getElementById('sqlText').value;

        var params = new FormData();
        params.append('cmd', 'execute');
        params.append('t', sqlText);
        // params.append('f', 'sample1.sql');

        axios.post('sqlapi.php', params)
        .then(function (response) {

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
        });
    }
    </script>
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