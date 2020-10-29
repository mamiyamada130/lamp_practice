<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';

// ログインチェックを行うため、セッションを開始
session_start();

// ログインチェック用関数を利用
if(is_logined() === true){
  // ログイン済みの場合はトップページにリダイレクト
  redirect_to(HOME_URL);
}

// トークンを生成
$token = get_csrf_token();

// ビューの読み込み
include_once VIEW_PATH . 'signup_view.php';



