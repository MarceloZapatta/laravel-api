<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Compra BTC!</title>
</head>
<body>
    <p>
        Sucesso!
    </p>
    <p>
        Sua compra de <strong>{{ $cryptQuantity }}</strong> BTC foi realizada com sucesso!
    </p>
    <p>
        Valor investido: R$ {{ number_format($amount, 2, ',', '.') }}
    </p>
</body>
</html>