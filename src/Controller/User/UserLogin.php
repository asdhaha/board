<?php

// namespace App\

use App\Model\User;

function check($check)
{
    if ($check != null) {
        $request = trim($check);

        return $request;
    }
    throw new Exception("8能4空值");
}

isset($_POST['account']) ? $account = trim($_POST['account']) : $account = '';
isset($_POST['password']) ? $password = trim($_POST['password']) : $password = '';

try {
    check($account);
    check($password);
} catch (Exception $e) {
    $return = [
        "event" => "登入訊息",
        "status" => "error",
        "content" => "有欄位尚未填寫",
    ];
    return $return;
}

$login = new User();

$data = $login->Login($account, $password);

if ($data) {
    $return = [
        "account" => $account,
        "user_id" => $data['id'],
        "email" => $data['email'],
        "intro" => $data['intro'],
        "event" => "登入訊息",
        "status" => "success",
        "content" => "登入成功，歡迎 $account 登入",
        // "X-Session-Hash" => $md5_session_id
    ];

    return $return;
}

$return = [
    "event" => "登入訊息",
    "status" => "error",
    "content" => "登入失敗，帳號或密碼錯誤",
];
return $return;
