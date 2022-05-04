<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Panel</title>
    <base href="http://localhost/servers/">
    <link rel="stylesheet" href="style/main.css">
</head>
<body>
    <nav class="admin__navigation">
        <ul class="navigation__list">
            <li data-section-class="payments" class="admin__panel--section-option">Platności</li>
            <li data-section-class="sold-servers" class="admin__panel--section-option">Serwery</li>
            <li data-section-class="" class="admin__panel--section-option">Zasilacz</li>
        </ul>

        <div>
            <a href="index.php/api/logout">Logout</a>
        </div>
    </nav>
</body>
</html>