<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';

// ログインチェックを行うため、セッションを開始
session_start();

// ログインチェック用関数を利用
if(is_logined() === true){
  // ログインしていない場合はログインページにリダイレクト
  redirect_to(HOME_URL);
}

$token = get_post('token');

// post送信されたトークンのチェック
if(is_valid_csrf_token($token) === false) {
  redirect_to(LOGIN_URL);
}

// トークンの破棄
delete_session();

// POSTされたnameを取得
$name = get_post('name');
// POSTされたpasswordを取得
$password = get_post('password');

// PDOを取得
$db = get_db_connect();

// PDOを利用して、名前とパスワードを取得
$user = login_as($db, $name, $password);
if( $user === false){
  set_error('ログインに失敗しました。');
  // ログインに失敗した場合はログインページにリダイレクト
  redirect_to(LOGIN_URL);
}

set_message('ログインしました。');
// typeが1の場合管理者ページにリダイレクト
if ($user['type'] === USER_TYPE_ADMIN){
  redirect_to(ADMIN_URL);
}
// トップページにリダイレクト
redirect_to(HOME_URL);