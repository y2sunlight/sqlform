/**
 * SQL API 呼び出し
 * @param cmd コマンド
 * @param args コマンド引数
 * @param callback コールバック関数
 */
function sqlapi(cmd, args, callback)
{
    var params = new FormData();
    params.append('cmd', cmd);
    for(key in args) {
        if (args.hasOwnProperty(key))
        {
            params.append(key, args[key]);
        }
    }

    axios.post('sqlapi.php', params, {
        headers: {
            'Authorization': 'Bearer '+$('meta[name="api-token"]').attr('content')
        }
    })
    .then(function (response) {
        if (!checkResult(response)) return;
        callback(response);
    })
    .catch(function (error) {
        errorAlert(error);
    });
}

/**
 * 共通のレスポンスチェック
 * @param response axiosレスポンスオブジェクト
 * @returns boolean チェックの成否
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
 * @param error axios エラーオブジェクト
 */
function errorAlert(error)
{
    if (error.response)
    {
        // サーバがステータスコードでエラー応答
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
