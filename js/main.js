/**
 * onload
 */
$(function(){
    // footer初期化
    showFooter();
    // SQLファイル一覧
    doList();
    // 初期SQLテキスト表示
    showInitialText();
    // ファイル名ボックスinputイベント
    $("#filename").on('input', function(){
        // ボタンの有効化
        enableButton();
    });
    // 削除ダイアログshowイベント
    $('#deleteModal').on('show.bs.modal', function(e){
        $('#delete-dialog-msg').text($('#filename').val() + ' を削除します。よろしいですか？');
    });
    // 上書き保存ダイアログイベント
    $('#forcedSaveModal').on('show.bs.modal', function(e){
        $('#save-dialog-msg').text($('#filename').val() + ' は既に存在します。上書き保存しますか？');
    });
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

    $('#message').html(`<span class="${cssClass}">${msg}</span>`);
    $('#exec-time').html(`${execTime} sec`);
}

/**
 * 初期SQLテキスト表示
 */
function showInitialText()
{
    $('#sqlText').html('SELECT \'Hello, World!\' as phrase;');
}