<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Logowanie</title>
    <base href="http://localhost/servers/">
    <link rel="stylesheet" href="style/main.css">
</head>
<body>
<form action="index.php/api/login" method="post" class="login__form">
    <p>Rejestracja</p>
    <label>
        <input type="text" name="username" class="form__input" placeholder="username">
    </label>
    <label>
        <input type="text" name="email" class="form__input" placeholder="email">
    </label>
    <label>
        <input type="password" name="password" class="form__input form__input--password" placeholder="password">
    </label>
    <button type="submit" class="form__button">Zarejestruj się</button>
    <div class="form__bottom-section">
        <a href="views/login.php">Logowanie</a>
    </div>
</form>
</body>
</html>