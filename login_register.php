<?php

session_start();
require_once 'config.php';


if (isset($_POST['register'])) {
    $name = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $checkusername = $conn->query("SELECT username FROM users WHERE username = '$name'");

    if ($checkusername->num_rows > 0) {
        $_SESSION['register_error'] = 'Username is already registered!';
        $_SESSION['shown_form'] = 'register';
    } else {
        $conn->query("INSERT INTO users (username, password, role) VALUES ('$name', '$password', '$role')");
    }

    header("Location: index.php");
    exit();
}


if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Έλεγχος username
    $result = $conn->query("SELECT * FROM users WHERE username = '$username'");

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        // Έλεγχος password
        if ($password === $user['password']) {

            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin_page.php");

            } elseif ($user['role'] === 'user') {
                header("Location: role_selection.php");
            }
            exit();
        }
    }

    // Αν δεν υπάρχει username ή το password είναι λάθος
    $_SESSION['login_error'] = 'Incorrect username or password';
    $_SESSION['shown_form'] = 'login';

    header("Location: index.php");
    exit();
}

?>
