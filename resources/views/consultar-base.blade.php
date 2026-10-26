<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Consultar base de deudores</title>
    <style>
        body { font-family: sans-serif; max-width: 320px; margin: 40px auto; }
        label { display: block; margin-top: 12px; font-size: 14px; }
        input, select, textarea { width: 100%; padding: 6px; margin-top: 4px; box-sizing: border-box; font-family: inherit; }
        button { margin-top: 16px; padding: 8px 16px; }
    </style>
</head>
<body>
    <h3>Consultar base</h3>

    <form action="{{ route('consultar-base') }}" method="GET">
        <label for="provincia">Provincia</label>
        <input type="text" id="provincia" name="provincia" placeholder="Buenos Aires">

        <label for="moraMinima">Mora mínima (días)</label>
        <input type="number" id="moraMinima" name="moraMinima" min="0">

        <label for="estado">Estado</label>
        <select id="estado" name="estado">
            <option value="">Todos</option>
            <option value="al_dia">Al día</option>
            <option value="en_mora">En mora</option>
            <option value="en_gestion">En gestión</option>
            <option value="incobrable">Incobrable</option>
        </select>

        <button type="submit">Consultar</button>
    </form>

    <hr style="margin-top: 24px;">

    <h3>Preguntarle al agente</h3>

    <form action="{{ route('preguntar') }}" method="GET">
        <label for="pregunta">Pregunta</label>
        <textarea id="pregunta" name="pregunta" rows="3" placeholder="¿Quiénes están en mora en Buenos Aires?" required></textarea>

        <button type="submit">Preguntar</button>
    </form>
</body>
</html>
