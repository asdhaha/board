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
isset($_POST['email']) ? $email = trim($_POST['email']) : $email = '';
isset($_POST['password']) ? $pass = trim($_POST['password']) : $pass = '';
isset($_POST['pass_check']) ? $pass_check = trim($_POST['pass_check']) : $pass_check = '';

try {
    check($account);
    check($email);
    check($pass);
    check($pass_check);
} catch (Exception $e) {
    $return = [
        "event" => "登入訊息",
        "status" => "error",
        "content" => "有欄位尚未填寫",
    ];
    return $return;
}

$create = new User();
$createStatus = $create->createUser($account, $email, $password);
if ($createStatus) {
    $return = [
        "event" => "註冊成功",
        "status" => "success",
        "content" => "已註冊 # $account ，再請登入",
    ];
    return $return;
} else {
    $return = [
        "event" => "登入訊息",
        "status" => "error",
        "content" => "創建失敗，",
    ];
    return $return;
}


