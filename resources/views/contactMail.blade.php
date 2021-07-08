<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,400;1,300&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Rubik', sans-serif;
            font-weight: 300;
        }

        .button {
            padding: 10px 20px;
            background-color: #0077BD;
            color: white;
            text-decoration: none;
        }

        .button:hover {
            background-color: #00629b;
            text-decoration: none;

        }

        a {
            color: #0077BD;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<h1 style="text-align: center">Nova poruka sa sajta</h1>
<br>
<br>

<br>
<p></p>
<p>Ime: {{$name}}</p>
<br>
<p>E-mail: {{$email}}</p>
<br>
<p>Poruka: {{$messageS}}</p>
<br>
<br>


<p>CV priča tim</p>

<br>
<br>

</body>
</html>



