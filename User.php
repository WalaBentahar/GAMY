<?php
class User {
    // For future authentication
    public static function authenticate($password) {
        return $password === "adminsupport";
    }
}
?>