<?php
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// dbデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'db.php';

// user_idからユーザーデータを取得
function get_user($db, $user_id){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      user_id = {$user_id}
    LIMIT 1
  ";

  // ユーザーデータ取得
  return fetch_query($db, $sql);
}

// nameからユーザーデータを取得
function get_user_by_name($db, $name){
  // SQL文作成
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      name = '{$name}'
    LIMIT 1
  ";

  // ユーザーデータ取得
  return fetch_query($db, $sql);
}

// PDOを利用して、名前、パスワードのチェック
function login_as($db, $name, $password){
  // PDOを利用して$nameのユーザーデータを取得
  $user = get_user_by_name($db, $name);
  // ユーザーデータが存在しない、またはパスワードが間違っている場合ログイン失敗
  if($user === false || $user['password'] !== $password){
    return false;
  }
  // セッション変数にuser_idを保存
  set_session('user_id', $user['user_id']);
  return $user;
}

// ログインユーザーのデータを取得
function get_login_user($db){
  // ログイン済みのuser_id
  $login_user_id = get_session('user_id');

  // ログイン済みのuser_idからユーザーデータを取得
  return get_user($db, $login_user_id);
}

// 新規ユーザー登録
function regist_user($db, $name, $password, $password_confirmation) {
  // 有効な名前、パスワードでなかった場合、ユーザー登録失敗
  if( is_valid_user($name, $password, $password_confirmation) === false){
    return false;
  }
  // データベースに新規ユーザー追加
  return insert_user($db, $name, $password);
}

// ユーザーが管理者である
function is_admin($user){
  // typeは1
  return $user['type'] === USER_TYPE_ADMIN;
}

// 有効な名前、パスワードかチェック
function is_valid_user($name, $password, $password_confirmation){
  // 短絡評価を避けるため一旦代入。
  $is_valid_user_name = is_valid_user_name($name);
  $is_valid_password = is_valid_password($password, $password_confirmation);
  return $is_valid_user_name && $is_valid_password ;
}

// 有効な名前かチェック
function is_valid_user_name($name) {
  $is_valid = true;
  // 名前が6文字以上100文字以下でない場合、エラーメッセージを表示
  if(is_valid_length($name, USER_NAME_LENGTH_MIN, USER_NAME_LENGTH_MAX) === false){
    set_error('ユーザー名は'. USER_NAME_LENGTH_MIN . '文字以上、' . USER_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  // 名前が有効でない場合、エラーメッセージを表示
  if(is_alphanumeric($name) === false){
    set_error('ユーザー名は半角英数字で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

// 有効なパスワードかチェック
function is_valid_password($password, $password_confirmation){
  $is_valid = true;
  // パスワードが6文字以上100文字以下でない場合、エラーメッセージを表示
  if(is_valid_length($password, USER_PASSWORD_LENGTH_MIN, USER_PASSWORD_LENGTH_MAX) === false){
    set_error('パスワードは'. USER_PASSWORD_LENGTH_MIN . '文字以上、' . USER_PASSWORD_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  // パスワードが有効でない場合、エラーメッセージを表示
  if(is_alphanumeric($password) === false){
    set_error('パスワードは半角英数字で入力してください。');
    $is_valid = false;
  }
  // パスワードが確認用と一致しない場合、エラーメッセージを表示
  if($password !== $password_confirmation){
    set_error('パスワードがパスワード(確認用)と一致しません。');
    $is_valid = false;
  }
  return $is_valid;
}

// usersテーブルに新規ユーザー追加
function insert_user($db, $name, $password){
  // SQL文を作成
  $sql = "
    INSERT INTO
      users(name, password)
    VALUES ('{$name}', '{$password}');
  ";

  // SQL実行
  return execute_query($db, $sql);
}

