/**
 * SQL API 呼び出し
 * @param cmd コマンド
 * @param args コマンド引数
 * @param callback コールバック関数
 */
function sqlapi(cmd, args, on_success, on_failure)
{
    var params = new FormData();
    params.append('cmd', cmd);
    for(key in args) {
        if (args.hasOwnProperty(key)) {
            params.append(key, args[key]);
        }
    }

    axios.post('sqlapi.php', params, {
        headers: {
            'Authorization': 'Bearer '+$('meta[name="api-token"]').attr('content')
        }
    })
    .then(function (response) {
        if (response.data.error===0) {
            if(on_success!==undefined) on_success(response);
        } else {
            if (response.data.error===undefined) {
                errorAlert('Response Error');
            } else {
            	if(on_failure!==undefined ) on_failure(response);
            }
        }
    })
    .catch(function (error) {
    	errorProc(error);
    });
}

/**
 * 共通のエラー処理
 * @param error axios エラーオブジェクト
 */
function errorProc(error)
{
    if (error.response) {
        // サーバがステータスコードでエラー応答
        console.log(error.response.data);
        console.log(error.response.status);
        console.log(error.response.headers);
        var msg = error.response.status + ' ' + error.response.statusText + ': ' + error.response.data;
        errorAlert(msg)
    } else {
        // トリガーしたリクエストの設定に何かしらのエラーがある
        console.log('Error', error.message);
        errorAlert(error.message)
    }
}
