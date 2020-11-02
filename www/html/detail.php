<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// orderデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'order.php';

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

$token = get_post('token');

// post送信されたトークンのチェック
if(is_valid_csrf_token($token) === false) {
  redirect_to(LOGIN_URL);
}

// トークンの破棄
delete_session();

// POSTされたorder_idを取得
$order_id = get_post('order_id');

// ログインユーザーの購入明細データを取得
$details = get_details($db, $order_id);

// 該当の購入履歴を取得
$orders = get_order($db, $order_id);

// ビューの読み込み
include_once '../view/detail_view.php';