<!DOCTYPE html>
<html lang="de">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Ansagen – ELA Server</title>

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
    border-radius: 14px;
    padding: 28px;

    box-shadow:
        0 2px 8px rgba(0, 0, 0, .08);

    margin-bottom: 24px;
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

.subtitle {
    color: #6b7280;
    margin-top: 5px;
}

.button {
    display: inline-block;

    border: 0;

    padding: 11px 17px;

    border-radius: 9px;

    background: #2563eb;
    color: white;

    text-decoration: none;

    font-weight: 600;

    cursor: pointer;
}

.button:hover {
    background: #1d4ed8;
}

.button-danger {
    background: #dc2626;
}

.button-danger:hover {
    background: #b91c1c;
}

.form-grid {
    display: grid;

    grid-template-columns:
        1fr 260px;

    gap: 22px;
}

.field label {
    display: block;

    font-weight: 600;

    margin-bottom: 8px;
}

textarea,
select {
    width: 100%;

    border: 1px solid #d1d5db;

    border-radius: 9px;

    padding: 12px;

    font: inherit;

    background: white;
}

textarea {
    min-height: 150px;

    resize: vertical;
}

textarea:focus,
select:focus {
    outline: none;

    border-color: #2563eb;

    box-shadow:
        0 0 0 3px rgba(37, 99, 235, .12);
}

.voice-info {
    margin-top: 10px;

    color: #6b7280;

    font-size: 14px;
}

.form-actions {
    margin-top: 22px;

    display: flex;

    justify-content: flex-end;
}

.error {
    background: #fee2e2;
    color: #991b1b;

    border: 1px solid #fecaca;

    padding: 13px 16px;

    border-radius: 9px;

    margin-bottom: 20px;
}

.audio-list {
    display: flex;

    flex-direction: column;

    gap: 12px;
}

.audio-item {
    border: 1px solid #e5e7eb;

    border-radius: 11px;

    padding: 17px;

    background: #f9fafb;

    display: grid;

    grid-template-columns: 1fr auto;

    gap: 20px;

    align-items: center;
}

.audio-text {
    font-weight: 600;

    line-height: 1.5;
}

.audio-meta {
    color: #6b7280;

    font-size: 13px;

    margin-top: 7px;
}

.audio-actions {
    display: flex;

    align-items: center;

    gap: 10px;

    flex-wrap: wrap;
}

audio {
    width: 260px;
    max-width: 100%;
}

.delete-form {
    margin: 0;
}

.empty {
    padding: 35px;

    text-align: center;

    color: #6b7280;

    border: 2px dashed #d1d5db;

    border-radius: 10px;
}

.back {
    margin-top: 20px;
}

.back a {
    color: #2563eb;

    text-decoration: none;

    font-weight: 600;
}

@media (max-width: 800px) {

    .form-grid {
        grid-template-columns: 1fr;
    }

    .audio-item {
        grid-template-columns: 1fr;
    }

    .topbar {
        align-items: flex-start;

        flex-direction: column;
    }

}

</style>

</head>

<body>

<header>

<h1>ELA Server</h1>

</header>

<main>


<!-- =====================================================
     NEUE ANSAGE
     ===================================================== -->

<div class="card">

<div class="topbar">

<div>

<h2>Neue Ansage</h2>

<div class="subtitle">
Text eingeben und mit Piper eine WAV-Ansage erzeugen.
</div>

</div>

</div>


<?php if ($error !== null): ?>

<div class="error">

<?= htmlspecialchars($error) ?>

</div>

<?php endif; ?>


<form method="post">

<input
    type="hidden"
    name="page"
    value="audio"
>

<input
    type="hidden"
    name="action"
    value="generate"
>

<input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($csrfToken) ?>"
>


<div class="form-grid">


<div class="field">

<label for="text">
Ansagetext
</label>

<textarea
    id="text"
    name="text"
    required
    placeholder="z. B. Nächster Halt: Hauptbahnhof. Bitte in Fahrtrichtung rechts aussteigen."
></textarea>

</div>


<div class="field">

<label for="voice">
Stimme
</label>

<select
    id="voice"
    name="voice"
>

<option value="kerstin">
Kerstin
</option>

<option value="thorsten">
Thorsten
</option>

</select>

<div class="voice-info">
Kerstin: 16 kHz, kompakt<br>
Thorsten: 22,05 kHz, höhere Qualität
</div>

</div>


</div>


<div class="form-actions">

<button
    type="submit"
    class="button"
>
    🔊 Ansage erzeugen
</button>

</div>
<br><br>
<div
    class="audio-item" >
<div
    style="
        margin-top:20px;
        margin-bottom:20px;
    "
>
    <label
        for="recording_title"
        style="
            display:block;
            font-weight:600;
            margin-bottom:8px;
        "
    >
        Aufnahmename
    </label>

    <input
        type="text"
        id="recording_title"
        placeholder="z.B. Ablöse Kö A1"
        style="
            width:100%;
            padding:12px;
            border:1px solid #d1d5db;
            border-radius:9px;
            font:inherit;
        "
    >
</div>


<div
    style="
        margin-top:20px;
        text-align:center;
    "
>
    <button
        type="button"
        id="recordButton"
        class="button button-danger"
    >
        🎤 Gedrückt halten zum Aufnehmen
    </button>

    <div
        id="recordStatus"
        style="
            margin-top:10px;
            color:#6b7280;
        "
    >
        Bereit
    </div>
</div>
</div>

</form>

</div>


<!-- =====================================================
     VORHANDENE ANSAGEN
     ===================================================== -->

<div class="card">

<div class="topbar">

<div>

<h2>Vorhandene Ansagen</h2>

<div class="subtitle">
<?= count($audioFiles) ?>
&nbsp;gespeicherte Ansage<?= count($audioFiles) === 1 ? '' : 'n' ?>
</div>

</div>

</div>


<?php if (!$audioFiles): ?>

<div class="empty">

Noch keine Ansagen vorhanden.

</div>

<?php else: ?>

<div class="audio-list">

<?php foreach ($audioFiles as $audio): ?>

<?php

$filename =
    basename(
        (string) $audio['filename']
    );

$audioUrl =
    '/audio/announcements/' .
    rawurlencode($filename);

?>

<div class="audio-item">


<div>

<div class="audio-text">

<?= nl2br(
    htmlspecialchars(
        (string) $audio['text']
    )
) ?>

</div>

<div class="audio-meta">

Stimme:
<strong>
<?= htmlspecialchars(
    ucfirst(
        (string) $audio['voice']
    )
) ?>
</strong>

&nbsp; · &nbsp;

<?= number_format(
    (float) $audio['duration'],
    1,
    ',',
    '.'
) ?>
Sekunden

&nbsp; · &nbsp;

<?= number_format(
    ((int) $audio['file_size']) / 1024,
    0,
    ',',
    '.'
) ?>
KB

</div>

</div>


<div class="audio-actions">

<audio
    controls
    preload="none"
>

<source
    src="<?= htmlspecialchars($audioUrl) ?>"
    type="audio/wav"
>

Dein Browser unterstützt keine Audio-Wiedergabe.

</audio>

<a
    href="/?page=measure-wizard&step=1&audio_id=<?= (int) $audio['id'] ?>"
    class="button"
>
    Maßnahme erstellen
</a>

<form
    method="post"
    class="delete-form"
    onsubmit="return confirm('Diese Ansage wirklich löschen?');"
>

<input
    type="hidden"
    name="page"
    value="audio"
>

<input
    type="hidden"
    name="action"
    value="delete"
>

<input
    type="hidden"
    name="id"
    value="<?= (int) $audio['id'] ?>"
>

<input
    type="hidden"
    name="csrf_token"
    value="<?= htmlspecialchars($csrfToken) ?>"
>

<button
    type="submit"
    class="button button-danger"
>
    Löschen
</button>

</form>

</div>

</div>

<?php endforeach; ?>

</div>

<?php endif; ?>

</div>


<div class="back">

<a href="/">
← Zurück zum Dashboard
</a>

</div>

</main>

<script>

let mediaRecorder;
let chunks = [];
let stream;

const button =
    document.getElementById('recordButton');

const status =
    document.getElementById('recordStatus');

async function startRecording()
{
    const titleField =
        document.getElementById(
            'recording_title'
        );

    const title =
        titleField.value.trim();

    if (title === '') {

        status.innerHTML =
            'Bitte einen Aufnahmenamen eingeben';

        titleField.focus();

        return;
    }

    try {

        stream =
            await navigator
                .mediaDevices
                .getUserMedia({
                    audio: true
                });

        mediaRecorder =
            new MediaRecorder(stream);

        chunks = [];

        mediaRecorder.ondataavailable =
            e => chunks.push(e.data);

        mediaRecorder.onstop =
            async () => {

                const blob =
                    new Blob(
                        chunks,
                        {
                            type: 'audio/webm'
                        }
                    );

                const formData =
                    new FormData();

                formData.append(
                    'title',
                    title
                );

                formData.append(
                    'page',
                    'audio'
                );

                formData.append(
                    'action',
                    'record'
                );

                formData.append(
                    'csrf_token',
                    '<?= htmlspecialchars($csrfToken) ?>'
                );

                formData.append(
                    'audio',
                    blob,
                    'record.webm'
                );

                status.innerHTML =
                    'Speichern ...';

                const response =
                    await fetch(
                        '/?page=audio',
                        {
                            method: 'POST',
                            body: formData
                        }
                    );

                if (response.ok) {

                    status.innerHTML =
                        'Gespeichert';

                    location.reload();

                } else {

                    status.innerHTML =
                        'Fehler beim Speichern';
                }
            };

        mediaRecorder.start();

        button.innerHTML =
            '🔴 Aufnahme läuft';

        status.innerHTML =
            'Aufnahme läuft';

    } catch (e) {

        console.error(e);

        status.innerHTML =
            'Mikrofonzugriff verweigert';
    }
}

function stopRecording()
{
    if (
        mediaRecorder &&
        mediaRecorder.state === 'recording'
    ) {

        mediaRecorder.stop();

        if (stream) {
            stream
                .getTracks()
                .forEach(
                    track => track.stop()
                );
        }

        button.innerHTML =
            '🎤 Gedrückt halten zum Aufnehmen';
    }
}

button.addEventListener(
    'mousedown',
    startRecording
);

button.addEventListener(
    'mouseup',
    stopRecording
);

button.addEventListener(
    'mouseleave',
    stopRecording
);

button.addEventListener(
    'touchstart',
    startRecording
);

button.addEventListener(
    'touchend',
    stopRecording
);

button.addEventListener(
    'touchcancel',
    stopRecording
);

console.log(
    'Audio-Aufnahme JS geladen'
);

</script>

</body>

</html> 

</body>

</html>
