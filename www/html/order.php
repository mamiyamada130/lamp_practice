<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// orderデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'order.php';

// ログインチェックをするためセッションを開始
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

// adminでログインした場合、全購入履歴データ取得
if(is_admin($user) === true){
  $orders = get_all_orders($db);
 // admin以外でログインした場合、該当ユーザーのデータのみ取得
} else {
  $orders = get_user_orders($db, $user['user_id']);
}

// ビューの読み込み
include_once VIEW_PATH . 'order_view.php';