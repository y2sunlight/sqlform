/**
 * onload
 */
window.onload = function() {
    // footer初期化
    showFooter();
    // SQLファイル一覧
    doList();
    // 初期SQLテキスト表示
    showInitialText();
};

$("#filename").on('input', function(){
    // ボタンの有効化
    enableButton();
});

/**
 * エラーアラート
 * @param msg
 * @returns
 */
function errorAlert(msg)
{
    alert(msg);
    showFooter(msg,undefined,'error');
}

/**
 * footer表示
 * @param msg
 * @param execTime
 */
function showFooter(msg, execTime, cssClass)
{
    if(msg === undefined) msg = "";
    if(execTime === undefined) execTime = "0.000";
    if(cssClass === undefined) cssClass = "no-error";

    document.getElementById('message').innerHTML = `<span class="${cssClass}">${msg}</span>`;
    document.getElementById('exec-time').innerHTML = `${execTime} sec`;
}

/**
 * 初期SQLテキスト表示
 */
function showInitialText()
{
    document.getElementById('sqlText').innerHTML = "SELECT 'Hello, World!' as phrase;";
}