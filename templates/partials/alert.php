<?php
/**
 * Fehlermeldung einer Seite (aus $error). Wird direkt nach <main> eingebunden,
 * damit alle Seiten Fehler gleich anzeigen.
 */
if (($error ?? null) !== null): ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars((string) $error) ?>
    </div>
<?php endif;
