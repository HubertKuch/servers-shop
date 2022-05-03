<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Logowanie</title>
    <link rel="stylesheet" href="style/main.css">
</head>
<body>
    <form action="index.php/api/login" method="post" class="login__form">
        <p>Logowanie</p>
        <label>
            <input type="text" name="username" class="form__input">
        </label>
        <label>
            <input type="password" name="password" class="form__input form__input--password">
        </label>
        <button type="submit" class="form__button">Log in</button>
        <div class="form__bottom-section">
            <a href="views/register.php">Nie masz konta? Założ je</a>
        </div>
    </form>
</body>
</html>