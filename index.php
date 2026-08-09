<?php

session_start();

$errors = [
    'login' => $_SESSION['login_error' ] ?? '',
    'register' => $_SESSION['register_error' ] ?? ''
];
$shownForm = $_SESSION['shown_form' ] ?? 'login';

session_unset();

function showError($error) {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';

}
function isShownForm($formName, $shownForm) {
    return $formName === $shownForm ? 'shown' : '';
}

?>  




<!DOCTYPE html>
<html lang="gr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width", initial-scale="1.0">
    <title> Project 2026</title>
    <link rel="stylesheet" href="style.css"

</head>

<body class="login-page">   
     
    <div class="container">
        <div class="form-box <?= isShownForm('login', $shownForm) ?>" id="login-form">
            <form action="login_register.php" method="post">
                <h2>Σύνδεση</h2>
                <?=  showError($errors['login']); ?>
                <input type="username" name="username" placeholder="Όνομα χρήστη" required>
                <input type="password" name="password" placeholder="Κωδικός" required>
                <button type="submit" name="login">Σύνδεση</button>
                <p>Δεν έχεις λογαρισμό; <a href="#" onclick="showForm('register-form')">Εγγραφή</a></p>
            </form>
        </div>

        <div class="form-box <?= isShownForm('register', $shownForm) ?>" id="register-form">
            <form action="login_register.php" method="post">
                <h2>Εγγραφή</h2>
                <?=  showError($errors['register']); ?>
                <input type="username" name="username" placeholder="Όνομα χρήστη" required>
                <input type="password" name="password" placeholder="Κωδικός" required>
                <select name = "role" required>
                    <option value = "">--Επέλεξε Ρόλο--</option>
                    <option value = "user">Χρήστης</option>
                    <option value = "admin">Admin</option>
                </select>
                <button type="submit" name="register">Εγγραφή</button>
                <p>Έχεις ήδη λογαρισμό; <a href="#" onclick="showForm('login-form')">Σύνδεση</a></p>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>


</html>