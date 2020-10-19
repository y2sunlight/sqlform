/**
 * onload
 */
window.onload = function() {
    // SQLファイル一覧
    doList();
};

$("#filename").on('input', function(){
    // ボタンの有効化
    enableButton();
});