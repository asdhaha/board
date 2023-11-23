<?php

namespace App\Data;

require '../../../vendor/autoload.php';

use App\Model\User;
use Exception;

define('RSA_PUBLIC', '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCmkANmC849IOntYQQdSgLvMMGm
8V/u838ATHaoZwvweoYyd+/7Wx+bx5bdktJb46YbqS1vz3VRdXsyJIWhpNcmtKhY
inwcl83aLtzJeKsznppqMyAIseaKIeAm6tT8uttNkr2zOymL/PbMpByTQeEFlyy1
poLBwrol0F4USc+owwIDAQAB
-----END PUBLIC KEY-----');
define('RSA_PRIVATE', '-----BEGIN PRIVATE KEY-----
MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAKaQA2YLzj0g6e1h
BB1KAu8wwabxX+7zfwBMdqhnC/B6hjJ37/tbH5vHlt2S0lvjphupLW/PdVF1ezIk
haGk1ya0qFiKfByXzdou3Ml4qzOemmozIAix5ooh4Cbq1Py6202SvbM7KYv89syk
HJNB4QWXLLWmgsHCuiXQXhRJz6jDAgMBAAECgYAIF5cSriAm+CJlVgFNKvtZg5Tk
93UhttLEwPJC3D7IQCuk6A7Qt2yhtOCvgyKVNEotrdp3RCz++CY0GXIkmE2bj7i0
fv5vT3kWvO9nImGhTBH6QlFDxc9+p3ukwsonnCshkSV9gmH5NB/yFoH1m8tck2Gm
BXDj+bBGUoKGWtQ7gQJBANR/jd5ZKf6unLsgpFUS/kNBgUa+EhVg2tfr9OMioWDv
MSqzG/sARQ2AbO00ytpkbAKxxKkObPYsn47MWsf5970CQQDIqRiGmCY5QDAaejW4
HbOcsSovoxTqu1scGc3Qd6GYvLHujKDoubZdXCVOYQUMEnCD5j7kdNxPbVzdzXll
9+p/AkEAu/34iXwCbgEWQWp4V5dNAD0kXGxs3SLpmNpztLn/YR1bNvZry5wKew5h
z1zEFX+AGsYgQJu1g/goVJGvwnj/VQJAOe6f9xPsTTEb8jkAU2S323BG1rQFsPNg
jY9hnWM8k2U/FbkiJ66eWPvmhWd7Vo3oUBxkYf7fMEtJuXu+JdNarwJAAwJK0YmO
LxP4U+gTrj7y/j/feArDqBukSngcDFnAKu1hsc68FJ/vT5iOC6S7YpRJkp8egj5o
pCcWaTO3GgC5Kg==
-----END PRIVATE KEY-----');

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
    print_r($return);

    return $return;
}

$login = new User();

$data = $login->Login($account, $password);

if ($data) {

    // 加密
    $public_key = openssl_pkey_get_public(RSA_PUBLIC);

    if (!$public_key) {
        die(1);
    }

    $return_en = openssl_public_encrypt($data['uuid'], $crypted, $public_key);
    if (!$return_en) {
        return (2);
    }
    $eb64_cry = base64_encode($crypted);
    echo "<br>";
    echo "加密： " . $eb64_cry;
    echo "<br>";

    $private_key = openssl_pkey_get_private(RSA_PRIVATE);
    $return_de = openssl_private_decrypt(base64_decode($eb64_cry), $decrypted, $private_key);
    if (!$return_de) {
        return (3);
    }
    echo "<br>";
    echo "---------------------------------------------------";
    echo "<br>";
    echo "解密:" . $decrypted;
    echo "<br>";
    die();


    $return = [
        "account" => $account,
        "user_id" => $data['id'],
        "email" => $data['email'],
        "intro" => $data['intro'],
        "event" => "登入訊息",
        "status" => "success",
        "content" => [
            "X-Session-Hash" => $md5_session_id,
        ],
    ];
    print_r($return);

    return $return;
}

$return = [
    "event" => "登入訊息",
    "status" => "error",
    "content" => "登入失敗，帳號或密碼錯誤",
];
http_response_code(500);
print_r($return);

return $return;
