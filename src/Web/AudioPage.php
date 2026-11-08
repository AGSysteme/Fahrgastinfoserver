<?php

declare(strict_types=1);

namespace Ela\Web;

use Ela\Config;
use RuntimeException;

final class AudioPage extends Page
{
    private const VOICES = ['kerstin', 'thorsten'];

    public function post(string $action): void
    {
        $audio = $this->services->audio;

        if ($action === 'generate') {
            $text = $this->text($_POST, 'text');
            $voice = $this->text($_POST, 'voice');

            if ($text === '') {
                throw new RuntimeException('Bitte einen Ansagetext eingeben.');
            }

            if (!in_array($voice, self::VOICES, true)) {
                throw new RuntimeException('Ungültige Stimme.');
            }

            $generated = $audio->generate($text, $voice);

            $audio->create(
                $text,
                $text,
                $voice,
                $generated['filename'],
                $generated['format'],
                (int) $generated['sample_rate'],
                (int) $generated['channels'],
                (int) $generated['bit_depth'],
                isset($generated['duration']) ? (float) $generated['duration'] : null,
                isset($generated['file_size']) ? (int) $generated['file_size'] : null
            );

            $this->redirect('/?page=audio');
        }

        if ($action === 'record') {
            if (
                !isset($_FILES['audio']) ||
                $_FILES['audio']['error'] !== UPLOAD_ERR_OK
            ) {
                throw new RuntimeException('Keine Aufnahme hochgeladen.');
            }

            $title = $this->text($_POST, 'title');

            if ($title === '') {
                throw new RuntimeException('Name der Aufnahme fehlt.');
            }

            $audio->createFromRecording($_FILES['audio'], $title);

            http_response_code(200);

            exit;
        }

        if ($action === 'delete') {
            $id = $this->number($_POST, 'id');

            if ($id <= 0) {
                throw new RuntimeException('Ungültige Ansage.');
            }

            $file = $audio->find($id);

            if (!$file) {
                throw new RuntimeException('Ansage nicht gefunden.');
            }

            $audio->delete($id);

            $path = Config::audioDir() . '/' . basename((string) $file['filename']);

            if (is_file($path)) {
                unlink($path);
            }

            $this->redirect('/?page=audio');
        }
    }

    public function get(string $action): void
    {
        $this->render('audio.php', [
            'audioFiles' => $this->services->audio->all(),
        ]);
    }
}
