<?php

// Kullanıcı adı ve şifreyi kontrol edin
if (isset($_POST['username']) && isset($_POST['password'])) {
    if ($_POST['username'] === 'ad.soyad' && $_POST['password'] === 'parola') {
        // Giriş başarılı
        echo '<h1>Yönetim Paneline Hoş Geldiniz!</h1>';
    } else {
        // Giriş başarısız
        echo '<p>Hatalı kullanıcı adı veya şifre. Lütfen tekrar deneyin.</p>';
    }
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetim Girişi</title>
</head>
<body>
    <h1>Yönetim Girişi</h1>
    <form method="post">
        <label for="username">Kullanıcı Adı:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Parola:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Giriş Yap</button>
    </form>
</body>
</html>
