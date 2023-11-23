<?php

namespace App\Data;

use App\Model\User;
use Exception;

require '../../../vendor/autoload.php';
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
        "event" => "創建訊息",
        "status" => "error",
        "content" => $e->getMessage(),
    ];

    print_r($return);
    return $return;
}

$create = new User();

$createStatus = $create->createUser($account, $email, $pass);
if ($createStatus) {
    $return = [
        "event" => "註冊成功",
        "status" => "success",
        "content" => "已註冊 # $account ，再請登入",
    ];
    print_r($return);

    return $return;
} else {
    $return = [
        "event" => "創建訊息",
        "status" => "error",
        "content" => "創建失敗，信箱或是帳號已被使用",
    ];
    print_r($return);

    return $return;
}


