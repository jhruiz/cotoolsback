<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cliente Suscrito</title>
</head>
<body>

    <h3>El siguiente cliente se ha suscrito a Torque Racing:</h3>
    <p>
        Identificación: {{ $data->nit }} <br>
        Celular: {{ $data->celular }} <br>
        Email: {{ $data->email }} <br>
        Fecha suscripción: {{ $data->created_at }}
    </p>
    
</body>
</html>