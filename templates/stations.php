<!DOCTYPE html>
<html lang="de">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Haltestellen – ELA Server</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;

    font-family:
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;

    background: #f3f4f6;
    color: #111827;
}

header {
    background: #111827;
    color: white;
    padding: 20px 30px;
}

header h1 {
    margin: 0;
}

main {
    max-width: 1200px;
    margin: 30px auto;
    padding: 0 20px;
}

.card {
    background: white;
    border-radius: 12px;
    padding: 24px;

    box-shadow:
        0 2px 8px rgba(0, 0, 0, .08);
}

.topbar {
    display: flex;

    justify-content: space-between;
    align-items: center;

    gap: 20px;

    margin-bottom: 25px;
}

.topbar h2 {
    margin: 0;
}

.button {
    display: inline-block;

    padding: 10px 16px;

    background: #2563eb;
    color: white;

    text-decoration: none;

    border-radius: 8px;

    font-weight: 600;
}

.button:hover {
    background: #1d4ed8;
}

.search {
    display: flex;

    gap: 10px;

    margin-bottom: 25px;
}

.search input {
    flex: 1;

    min-width: 0;

    padding: 11px 13px;

    border: 1px solid #d1d5db;

    border-radius: 8px;

    font-size: 15px;
}

.search input:focus {
    outline: none;

    border-color: #2563eb;

    box-shadow:
        0 0 0 3px rgba(37, 99, 235, .12);
}

.search button {
    padding: 10px 18px;

    border: 0;

    border-radius: 8px;

    background: #374151;

    color: white;

    font-weight: 600;

    cursor: pointer;
}

.search button:hover {
    background: #1f2937;
}

.table-wrap {
    overflow-x: auto;

    border: 1px solid #e5e7eb;

    border-radius: 10px;
}

table {
    width: 100%;

    border-collapse: collapse;

    background: white;
}

thead {
    background: #f9fafb;
}

th {
    text-align: left;

    padding: 14px 16px;

    color: #374151;

    font-size: 13px;

    text-transform: uppercase;

    letter-spacing: .03em;

    border-bottom: 1px solid #e5e7eb;
}

td {
    padding: 15px 16px;

    border-bottom: 1px solid #f0f0f0;

    vertical-align: middle;
}

tbody tr:last-child td {
    border-bottom: 0;
}

tbody tr:hover {
    background: #f9fafb;
}

.code {
    font-family:
        ui-monospace,
        SFMono-Regular,
        Menlo,
        Monaco,
        Consolas,
        monospace;

    font-size: 13px;

    color: #4b5563;
}

.station-name {
    font-weight: 700;
}

.station-short {
    color: #6b7280;

    font-size: 13px;

    margin-top: 2px;
}

.coordinates {
    color: #6b7280;

    font-size: 13px;

    white-space: nowrap;
}

.badge {
    display: inline-block;

    padding: 5px 9px;

    border-radius: 999px;

    font-size: 12px;

    font-weight: 700;
}

.badge-active {
    background: #dcfce7;
    color: #166534;
}

.badge-inactive {
    background: #fee2e2;
    color: #991b1b;
}

.actions {
    white-space: nowrap;
}

.action {
    display: inline-block;

    padding: 7px 10px;

    border-radius: 7px;

    text-decoration: none;

    font-size: 13px;

    font-weight: 600;
}

.action-edit {
    background: #eff6ff;
    color: #1d4ed8;
}

.action-edit:hover {
    background: #dbeafe;
}

.action-delete {
    background: #fef2f2;
    color: #b91c1c;

    margin-left: 5px;
}

.action-delete:hover {
    background: #fee2e2;
}

.empty {
    padding: 40px;

    text-align: center;

    color: #6b7280;
}

@media (max-width: 800px) {

    .topbar {
        align-items: flex-start;

        flex-direction: column;
    }

    .search {
        flex-direction: column;
    }

    .search button {
        width: 100%;
    }

    th:nth-child(5),
    td:nth-child(5) {
        display: none;
    }

}

</style>

</head>

<body>

<header>

<h1>ELA Server</h1>

</header>

<main>

<div class="card">

<div class="topbar">

<h2>
Haltestellen
</h2>

<a
    class="button"
    href="/?page=stations&action=new"
>
+ Neue Haltestelle
</a>

</div>


<form
    method="get"
    class="search"
>

<input
    type="hidden"
    name="page"
    value="stations"
>

<input
    type="text"
    name="search"
    value="<?= htmlspecialchars(
        (string) $search
    ) ?>"
    placeholder="Haltestelle suchen..."
>

<button type="submit">
Suchen
</button>

</form>


<?php if (!$stations): ?>

<div class="empty">

<h3>
Keine Haltestellen gefunden
</h3>

<p>
Es wurden keine passenden Haltestellen gefunden.
</p>

</div>

<?php else: ?>

<div class="table-wrap">

<table>

<thead>

<tr>

<th>
Code
</th>

<th>
Haltestelle
</th>

<th>
Koordinaten
</th>

<th>
Status
</th>

<th>
Aktionen
</th>

</tr>

</thead>

<tbody>

<?php foreach ($stations as $station): ?>

<tr>

<td>

<?php if ($station['station_code'] !== null): ?>

<span class="code">
<?= htmlspecialchars(
    (string) $station['station_code']
) ?>
</span>

<?php else: ?>

<span class="code">
–
</span>

<?php endif; ?>

</td>


<td>

<div class="station-name">

<?= htmlspecialchars(
    (string) $station['name']
) ?>

</div>

<?php if ($station['short_name'] !== null): ?>

<div class="station-short">

<?= htmlspecialchars(
    (string) $station['short_name']
) ?>

</div>

<?php endif; ?>

</td>


<td>

<div class="coordinates">

<?php if (
    $station['latitude'] !== null &&
    $station['longitude'] !== null
): ?>

<?= htmlspecialchars(
    (string) $station['latitude']
) ?>

<br>

<?= htmlspecialchars(
    (string) $station['longitude']
) ?>

<?php else: ?>

–

<?php endif; ?>

</div>

</td>


<td>

<?php if (
    (int) $station['active'] === 1
): ?>

<span class="badge badge-active">
Aktiv
</span>

<?php else: ?>

<span class="badge badge-inactive">
Inaktiv
</span>

<?php endif; ?>

</td>


<td class="actions">

<a
    class="action action-edit"
    href="/?page=stations&action=edit&id=<?= (int) $station['id'] ?>"
>
Bearbeiten
</a>

<a
    class="action action-delete"
    href="/?page=stations&action=delete&id=<?= (int) $station['id'] ?>"
    onclick="return confirm('Haltestelle wirklich löschen?');"
>
Löschen
</a>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

<?php endif; ?>


<p style="margin-top: 25px;">

<a href="/">
← Zurück zum Dashboard
</a>

</p>

</div>

</main>

</body>

</html>
