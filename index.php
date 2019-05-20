<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <title>Document</title>
</head>
<body>
<div class="container">
  <h1 class="text-center pb-5 pt-4">Importador</h1>

  <form action="procesar.php" method="post">
    <div class="form-group">
      <select name="web" class="form-control form-control-lg">
        <option disabled selected>Seleccione la Web</option>
        <option value="mashable">Mashable</option>
        <option value="gizmodo">Gizmodo</option>
      </select>
    </div>
    <div class="form-group">
      <select name="usuario" class="form-control form-control-lg">
        <option disabled selected>Seleccione Usuario</option>
        <option value="2">Valeria Sanchez Cuellar</option>
        <option value="1">Abraham Moises Linares</option>
      </select>
    </div>
    <div class="form-group">
      <input type="text" class="form-control form-control-lg" name="url" placeholder="Coloca la Url a importar:">
    </div>
    <button class="btn btn-primary btn-lg" style="width: 100%">Importar</button>
  </form>
</div>
</body>
</html>