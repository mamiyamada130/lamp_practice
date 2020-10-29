<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'item.php';

// ログインチェックを行うため、セッションを開始
session_start();

// ログインチェック用関数を利用
if(is_logined() === false){
  // ログインしていない場合はログインページにリダイレクト
  redirect_to(LOGIN_URL);
}

// PDOを取得
$db = get_db_connect();

// PDOを利用してログインユーザーのデータを取得
$user = get_login_user($db);

// 管理者でログインされていない場合、ログインページにリダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

$token = get_post('token');

// post送信されたトークンのチェック
if(is_valid_csrf_token($token) === false) {
  redirect_to(LOGIN_URL);
}

// トークンの破棄
delete_session();

// POSTされたデータを取得
$item_id = get_post('item_id');
$changes_to = get_post('changes_to');

// openが送信された場合
if($changes_to === 'open'){
  // 非公開を公開に変更
  update_item_status($db, $item_id, ITEM_STATUS_OPEN);
  // 成功メッセージを表示
  set_message('ステータスを変更しました。');
  // closeが送信された場合
}else if($changes_to === 'close'){
  // 公開を非公開に変更
  update_item_status($db, $item_id, ITEM_STATUS_CLOSE);
  // 成功メッセージを表示
  set_message('ステータスを変更しました。');
  // openとclose以外が送信された場合
}else {
  // エラーメッセージを表示
  set_error('不正なリクエストです。');
}

// 商品管理ページにリダイレクト
redirect_to(ADMIN_URL);