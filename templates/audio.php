<?php
/**
 * @var array $audioFiles
 * @var string|null $error
 * @var string $csrfToken
 */
?>
<!DOCTYPE html>
<html lang="de" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ansagen – ELA Server</title>

    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>

<body>

<header class="py-4 px-4 px-md-5">
    <h1 class="h3 mb-0">ELA Server</h1>
</header>

<main class="container-fluid py-4 px-3 px-md-4" style="max-width: 1200px;">

    <!-- =====================================================
         NEUE ANSAGE
         ===================================================== -->

    <div class="card shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">

            <div class="mb-4">
                <h2 class="h4 mb-1">Neue Ansage</h2>

                <div class="text-muted small">
                    Text eingeben und mit Piper eine WAV-Ansage erzeugen.
                </div>
            </div>

            <!-- Fehlermeldung -->
            <?php if ($error !== null): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post">

                <input type="hidden" name="page" value="audio">
                <input type="hidden" name="action" value="generate">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($csrfToken) ?>"
                >

                <div class="row g-3">

                    <div class="col-12 col-md">
                        <label for="text" class="form-label">
                            Ansagetext
                        </label>

                        <textarea
                            id="text"
                            name="text"
                            class="form-control"
                            rows="6"
                            required
                            placeholder="z. B. Nächster Halt: Hauptbahnhof. Bitte in Fahrtrichtung rechts aussteigen."
                        ></textarea>
                    </div>

                    <div class="col-12 col-md-4 col-xl-3">
                        <label for="voice" class="form-label">
                            Stimme
                        </label>

                        <select
                            id="voice"
                            name="voice"
                            class="form-select"
                        >
                            <option value="kerstin">Kerstin</option>
                            <option value="thorsten">Thorsten</option>
                        </select>

                        <div class="form-text">
                            Kerstin: 16 kHz, kompakt<br>
                            Thorsten: 22,05 kHz, höhere Qualität
                        </div>
                    </div>

                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        🔊 Ansage erzeugen
                    </button>
                </div>

                <hr class="my-4">

                <!-- Aufnahme über das Mikrofon -->
                <div class="row g-3 align-items-end">

                    <div class="col-12 col-md">
                        <label for="recording_title" class="form-label">
                            Aufnahmename
                        </label>

                        <input
                            type="text"
                            id="recording_title"
                            class="form-control"
                            placeholder="z.B. Ablöse Kö A1"
                        >
                    </div>

                    <div class="col-12 col-md-auto text-center">

                        <button
                            type="button"
                            id="recordButton"
                            class="btn btn-danger w-100"
                        >
                            🎤 Gedrückt halten zum Aufnehmen
                        </button>

                        <div id="recordStatus" class="text-muted small mt-2">
                            Bereit
                        </div>

                    </div>

                </div>

            </form>

        </div>
    </div>

    <!-- =====================================================
         VORHANDENE ANSAGEN
         ===================================================== -->

    <div class="card shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">

            <div class="mb-4">
                <h2 class="h4 mb-1">Vorhandene Ansagen</h2>

                <div class="text-muted small">
                    <?= count($audioFiles) ?>
                    gespeicherte Ansage<?= count($audioFiles) === 1 ? '' : 'n' ?>
                </div>
            </div>

            <?php if (!$audioFiles): ?>

                <div class="text-center text-muted py-5 border border-2 border-dashed rounded-3">
                    Noch keine Ansagen vorhanden.
                </div>

            <?php else: ?>

                <div class="d-flex flex-column gap-3">

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

                        <div class="card">
                            <div class="card-body p-3 p-md-4">

                                <div class="row g-3 align-items-center">

                                    <div class="col-12 col-lg">

                                        <div class="fw-semibold lh-base text-break">
                                            <?= nl2br(
                                                htmlspecialchars(
                                                    (string) $audio['text']
                                                )
                                            ) ?>
                                        </div>

                                        <div class="text-muted small mt-2">

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

                                    <div class="col-12 col-lg-auto">

                                        <div class="d-flex flex-wrap align-items-center gap-2">

                                            <audio
                                                controls
                                                preload="none"
                                                class="w-100"
                                                style="max-width: 260px;"
                                            >

                                                <source
                                                    src="<?= htmlspecialchars($audioUrl) ?>"
                                                    type="audio/wav"
                                                >

                                                Dein Browser unterstützt keine Audio-Wiedergabe.

                                            </audio>

                                            <a
                                                href="/?page=measure-wizard&step=1&audio_id=<?= (int) $audio['id'] ?>"
                                                class="btn btn-sm btn-primary"
                                            >
                                                Maßnahme erstellen
                                            </a>

                                            <form
                                                method="post"
                                                class="m-0"
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

                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    Löschen
                                                </button>

                                            </form>

                                        </div>

                                    </div>

                                </div>

                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </div>
    </div>

    <div class="mt-4">
        <a href="/" class="text-decoration-none">
            &larr; Zurück zum Dashboard
        </a>
    </div>

</main>

<script src="/assets/js/bootstrap.bundle.min.js"></script>

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
