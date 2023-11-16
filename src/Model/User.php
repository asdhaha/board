<?php

namespace App\Model;

use App\Config\Database;
use PDO;
use Exception;
use PDOException;

class user
{
    public function __construct()
    {
    }

    public function dbConnect()
    {

        $db_type = Database::DATABASE_INFO['db_type'];
        $db_host = Database::DATABASE_INFO['db_host'];
        $db_name = Database::DATABASE_INFO['db_name'];
        $db_user = Database::DATABASE_INFO['db_user'];
        $db_pass = Database::DATABASE_INFO['db_pass'];
        $connect = $db_type . ":host=" . $db_host . ";dbname=" . $db_name;

        try {
            $db = new PDO($connect, $db_user, $db_pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->query("SET NAMES UTF8");
        } catch (PDOException $e) {
            die("錯誤:" . $e->getMessage() . '<br>');
        }
        return $db;
    }

    public function checkEmailName(string $account, string $email)
    {
        $db = $this->dbConnect();
        $sql = "SELECT IF( EXISTS(
                            SELECT account
                            FROM users
                            WHERE account = ?), 1, 0) as name_RESULT,
                        IF( EXISTS(
                            SELECT email
                            FROM users
                            WHERE email = ?), 1, 0) as email_RESULT;";
        $statement = $db->prepare($sql);
        $statement->execute([$account, $email]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
    public function createUser(string $account, string $email, string $password)
    {
        $db = $this->dbConnect();
        $check = $this->checkEmailName($account, $email);
        if ($check) {
            return 0;
        }
        $stat = $db->prepare("INSERT INTO `users`(`account`, `email`, `password`) VALUES (?,?,?)");
        $stat->execute([$account, $email, $password]);

        return 1;
    }
    public function Login(string $account, string $password)
    {
        $db = $this->dbConnect();
        $stat = $db->prepare("SELECT `password` from users WHERE `account`=?");
        $stat->execute([$account, $password]);
        $password_hash = $stat->fetch(PDO::FETCH_ASSOC);
        $verify = password_verify($password, $password_hash);

        if ($verify) {
            $req = $this->selectUser($account);

            return $req;
        }
        return false;
    }
    public function selectUser(string $account)
    {
        $db = $this->dbConnect();
        $stat = $db->prepare("SELECT * from users WHERE `account`=?");
        $stat->execute([$account]);
        $userData = $stat->fetch(PDO::FETCH_ASSOC);

        return $userData;
    }
}
