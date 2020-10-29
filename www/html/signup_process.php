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
  // ログイン済みの場合はトップページにリダイレクト
  redirect_to(HOME_URL);
}

$token = get_post('token');

// post送信されたトークンのチェック
if(is_valid_csrf_token($token) === false) {
  redirect_to(LOGIN_URL);
}

// トークンの破棄
delete_session();

// POSTされたデータを取得
$name = get_post('name');
$password = get_post('password');
$password_confirmation = get_post('password_confirmation');

// PDOを取得
$db = get_db_connect();

// 
try{
  $result = regist_user($db, $name, $password, $password_confirmation);
  // ユーザー登録に失敗した場合、エラーメッセージを表示
  if( $result=== false){
    set_error('ユーザー登録に失敗しました。');
    // ユーザー登録ページにリダイレクト
    redirect_to(SIGNUP_URL);
  }
// データベース接続失敗
}catch(PDOException $e){
  set_error('ユーザー登録に失敗しました。');
  // ユーザー登録ページにリダイレクト
  redirect_to(SIGNUP_URL);
}

// ユーザー登録が完了した場合、メッセージを表示
set_message('ユーザー登録が完了しました。');
// 登録された名前、パスワードでログイン
login_as($db, $name, $password);
// トップページにリダイレクト
redirect_to(HOME_URL);