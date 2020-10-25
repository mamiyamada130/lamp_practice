<?php
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// dbデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'db.php';

// DB利用

// item_idの商品データを取得
function get_item($db, $item_id){
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      item_id = {$item_id}
  ";

  return fetch_query($db, $sql);
}

// 
function get_items($db, $is_open = false){
  $sql = '
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
  ';
  if($is_open === true){
    $sql .= '
      WHERE status = 1
    ';
  }

  return fetch_all_query($db, $sql);
}

// itemsテーブルの全商品データを取得
function get_all_items($db){
  return get_items($db);
}

// itemsテーブルのステータスが公開の商品データを取得
function get_open_items($db){
  return get_items($db, true);
}

// itemsテーブルに商品を登録
function regist_item($db, $name, $price, $stock, $status, $image){
  $filename = get_upload_filename($image);
  // パラメータの整合性をチェック
  if(validate_item($name, $price, $stock, $filename, $status) === false){
    return false;
  }
  // itemsテーブルに商品データと画像を追加
  return regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename);
}

// 商品データと画像を追加
function regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename){
  // トランザクション開始
  $db->beginTransaction();
  // itemsテーブルに商品追加
  if(insert_item($db, $name, $price, $stock, $filename, $status) 
    && save_image($image, $filename)){
    // コミット処理
    $db->commit();
    return true;
  }
  // ロールバック処理
  $db->rollback();
  return false;
  
}

// itemsテーブルに商品追加
function insert_item($db, $name, $price, $stock, $filename, $status){
  // ステータスは0,1両方取得
  $status_value = PERMITTED_ITEM_STATUSES[$status];
  // SQL文作成
  $sql = "
    INSERT INTO
      items(
        name,
        price,
        stock,
        image,
        status
      )
    VALUES('{$name}', {$price}, {$stock}, '{$filename}', {$status_value});
  ";

  // SQL実行
  return execute_query($db, $sql);
}

// itemsテーブルのステータスを変更
function update_item_status($db, $item_id, $status){
  // SQL文作成
  $sql = "
    UPDATE
      items
    SET
      status = {$status}
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  // SQL実行
  return execute_query($db, $sql);
}

// itemsテーブルの商品在庫を変更
function update_item_stock($db, $item_id, $stock){
  // SQL文作成
  $sql = "
    UPDATE
      items
    SET
      stock = {$stock}
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  // SQL実行
  return execute_query($db, $sql);
}

// 商品をitemsテーブルから削除
function destroy_item($db, $item_id){
  // item_idの商品データを取得
  $item = get_item($db, $item_id);
  // 商品データ取得失敗
  if($item === false){
    return false;
  }
  // トランザクション開始
  $db->beginTransaction();
  if(delete_item($db, $item['item_id'])
    && delete_image($item['image'])){
    // コミット処理
    $db->commit();
    return true;
  }
  // ロールバック処理
  $db->rollback();
  return false;
}

// item_idの商品データを削除
function delete_item($db, $item_id){
  // SQL文作成
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  // SQL実行
  return execute_query($db, $sql);
}


// 非DB

// 公開されている商品
function is_open($item){
  return $item['status'] === 1;
}

// パラメータの整合性をチェック
function validate_item($name, $price, $stock, $filename, $status){
  // 商品名をチェック
  $is_valid_item_name = is_valid_item_name($name);
  // 値段をチェック
  $is_valid_item_price = is_valid_item_price($price);
  // 在庫数をチェック
  $is_valid_item_stock = is_valid_item_stock($stock);
  // ファイル名をチェック
  $is_valid_item_filename = is_valid_item_filename($filename);
  // ステータスをチェック
  $is_valid_item_status = is_valid_item_status($status);

  return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}

// 名前の整合性をチェック
function is_valid_item_name($name){
  $is_valid = true;
  // 文字列の長さが1以上100以下でない場合
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    // エラーメッセージを表示
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  return $is_valid;
}

// 値段の整合性をチェック
function is_valid_item_price($price){
  $is_valid = true;
  // 値段が有効な数値でない場合
  if(is_positive_integer($price) === false){
    // エラーメッセージを表示
    set_error('価格は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

// 在庫数の整合性をチェック
function is_valid_item_stock($stock){
  $is_valid = true;
  // 在庫数が有効な数値でない場合
  if(is_positive_integer($stock) === false){
    // エラーメッセージを表示
    set_error('在庫数は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

// ファイル名の整合性をチェック
function is_valid_item_filename($filename){
  $is_valid = true;
  // ファイルが選択されていない場合、失敗
  if($filename === ''){
    $is_valid = false;
  }
  return $is_valid;
}

// ステータスの整合性をチェック
function is_valid_item_status($status){
  $is_valid = true;
  // ステータスがopen、close以外選択されていた場合、失敗
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    $is_valid = false;
  }
  return $is_valid;
}