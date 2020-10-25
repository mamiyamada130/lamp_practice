<?php

function get_db_connect(){
  // MySQL用のDSN文字列
  $dsn = 'mysql:dbname='. DB_NAME .';host='. DB_HOST .';charset='.DB_CHARSET;
 
  try {
    // データベースに接続
    $dbh = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    // エラーモードの設定
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // プリペアドステートメントの設定
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // 連想配列をカラム名で取得
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    exit('接続できませんでした。理由：'.$e->getMessage() );
  }
  return $dbh;
}

// 一行だけデータを取得
function fetch_query($db, $sql, $params = array()){
  try{
    // SQL文を実行する準備
    $statement = $db->prepare($sql);
    // SQLを実行
    $statement->execute($params);
    // データの取得
    return $statement->fetch();
    //データベース接続失敗
  }catch(PDOException $e){
    set_error('データ取得に失敗しました。');
  }
  return false;
}

// 配列データを取得
function fetch_all_query($db, $sql, $params = array()){
  try{
    // SQL文を実行する準備
    $statement = $db->prepare($sql);
    // SQLを実行
    $statement->execute($params);
    // データの取得
    return $statement->fetchAll();
    //データベース接続失敗
  }catch(PDOException $e){
    set_error('データ取得に失敗しました。');
  }
  return false;
}

// SQLを実行
function execute_query($db, $sql, $params = array()){
  try{
    // SQL文を実行する準備
    $statement = $db->prepare($sql);
    // SQLを実行
    return $statement->execute($params);
    //データベース接続失敗
  }catch(PDOException $e){
    set_error('更新に失敗しました。');
  }
  return false;
}