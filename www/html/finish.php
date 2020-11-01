<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'item.php';
// cartデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'cart.php';
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

// ログインユーザーのカートデータを取得
$carts = get_user_carts($db, $user['user_id']);

// 商品の購入
if(purchase_carts($db, $carts) === false){
  // 購入に失敗した場合、エラーメッセージを表示
  set_error('商品が購入できませんでした。');
  // カートページにリダイレクト
  redirect_to(CART_URL);
} 

// カート内の合計金額を取得
$total_price = sum_carts($carts);

if(add_order_transaction($db, $user['user_id'], $total_price, $carts) === false){
  redirect_to(CART_URL);
}

// ビューの読み込み
include_once '../view/finish_view.php';