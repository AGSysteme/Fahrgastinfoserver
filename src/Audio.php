<?php

declare(strict_types=1);

namespace Ela;

use PDO;
use RuntimeException;

final class Audio
{
    public function __construct(
        private readonly PDO $db
    ) {
    }

    /**
     * Alle gespeicherten Ansagen.
     */
    public function all(): array
    {
        $stmt = $this->db->query(
            'SELECT *
             FROM audio_files
             ORDER BY created_at DESC, id DESC'
        );

        return $stmt->fetchAll();
    }

    /**
     * Eine Ansage anhand ihrer ID laden.
     */
    public function find(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT *
             FROM audio_files
             WHERE id = :id'
        );

        $stmt->execute([
            'id' => $id
        ]);

        $audio = $stmt->fetch();

        return $audio ?: null;
    }

/**
 * Ansage in der Datenbank speichern.
 */
public function create(
    string $text,
    ?string $ttsText,
    string $voice,
    string $filename,
    string $format,
    int $sampleRate,
    int $channels,
    int $bitDepth,
    ?float $duration,
    ?int $fileSize
): int {

    $text = trim($text);

    $ttsText = $ttsText !== null
        ? trim($ttsText)
        : null;

    $voice = trim($voice);
    $filename = trim($filename);
    $format = strtolower(trim($format));

    if ($text === '') {
        throw new RuntimeException(
            'Der Ansagetext darf nicht leer sein.'
        );
    }

    if ($voice === '') {
        throw new RuntimeException(
            'Eine Stimme muss ausgewählt werden.'
        );
    }

    if ($filename === '') {
        throw new RuntimeException(
            'Der Dateiname darf nicht leer sein.'
        );
    }

    if ($format === '') {
        throw new RuntimeException(
            'Das Audioformat darf nicht leer sein.'
        );
    }

    if ($sampleRate <= 0) {
        throw new RuntimeException(
            'Ungültige Samplerate.'
        );
    }

    if ($channels <= 0) {
        throw new RuntimeException(
            'Ungültige Kanalanzahl.'
        );

    }

    if ($bitDepth <= 0) {
        throw new RuntimeException(
            'Ungültige Bit-Tiefe.'
        );
    }

    $stmt = $this->db->prepare(
        'INSERT INTO audio_files
            (
                text,
                tts_text,
                voice,
                format,
                sample_rate,
                channels,
                bit_depth,
                filename,
                duration,
                file_size
            )
         VALUES
            (
                :text,
                :tts_text,
                :voice,
                :format,
                :sample_rate,
                :channels,
                :bit_depth,
                :filename,
                :duration,
                :file_size
            )'
    );

    $stmt->execute([
        'text' => $text,
        'tts_text' => $ttsText !== ''
            ? $ttsText
            : null,
        'voice' => $voice,
        'format' => $format,
        'sample_rate' => $sampleRate,
        'channels' => $channels,
        'bit_depth' => $bitDepth,
        'filename' => $filename,
        'duration' => $duration,
        'file_size' => $fileSize
    ]);

    return (int) $this->db->lastInsertId();
}

/**
 * Ansage aus der Datenbank löschen.
 *
 * Eine Ansage darf nicht gelöscht werden,
 * wenn sie noch von einer Maßnahme verwendet wird.
 */
public function delete(int $id): void
{
    if ($id <= 0) {
        throw new RuntimeException(
            'Ungültige Ansage.'
        );
    }

    $audio = $this->find($id);

    if (!$audio) {
        throw new RuntimeException(
            'Ansage nicht gefunden.'
        );
    }

    /*
     * Prüfen, ob die Ansage noch in Maßnahmen
     * verwendet wird.
     */
    $stmt = $this->db->prepare(
        'SELECT COUNT(*)
         FROM measures
         WHERE audio_id = :audio_id'
    );

    $stmt->execute([
        'audio_id' => $id
    ]);

    $usageCount = (int) $stmt->fetchColumn();

    if ($usageCount > 0) {

        $measureWord = $usageCount === 1
            ? 'Maßnahme'
            : 'Maßnahmen';

        throw new RuntimeException(
            'Die Ansage kann nicht gelöscht werden, weil sie noch in ' .
            $usageCount .
            ' ' .
            $measureWord .
            ' verwendet wird.'
        );
    }

    /*
     * Ansage löschen.
     */
    $stmt = $this->db->prepare(
        'DELETE FROM audio_files
         WHERE id = :id'
    );

    $stmt->execute([
        'id' => $id
    ]);
}

    /**
     * Eine WAV-Ansage mit Piper erzeugen.
     *
     * Gibt die erzeugten Dateiinformationen zurück.
     */
    public function generate(
        string $text,
        string $voice
    ): array {

        $text = trim($text);
        $voice = trim($voice);

        if ($text === '') {
            throw new RuntimeException(
                'Der Ansagetext darf nicht leer sein.'
            );
        }

        $models = [
            'kerstin' =>
                '/opt/ela/models/de_DE-kerstin-low.onnx',

            'thorsten' =>
                '/opt/ela/models/de_DE-thorsten-medium.onnx'
        ];

        if (!isset($models[$voice])) {
            throw new RuntimeException(
                'Ungültige Stimme.'
            );
        }

        $model = $models[$voice];

        if (!is_file($model)) {
            throw new RuntimeException(
                'Piper-Modell nicht gefunden: ' . $model
            );
        }

        $piper =
            '/opt/ela/piper-venv/bin/piper';

        if (!is_file($piper)) {
            throw new RuntimeException(
                'Piper wurde nicht gefunden.'
            );
        }

        $audioDirectory =
            '/opt/ela/audio/announcements';

        if (!is_dir($audioDirectory)) {

            if (!mkdir(
                $audioDirectory,
                0775,
                true
            ) && !is_dir($audioDirectory)) {

                throw new RuntimeException(
                    'Audio-Verzeichnis konnte nicht erstellt werden.'
                );
            }
        }


        /*
         * Eindeutigen Dateinamen erzeugen.
         */
        $filename =
            'announcement-' .
            date('Ymd-His') .
            '-' .
            bin2hex(random_bytes(4)) .
            '.wav';

        $outputFile =
            $audioDirectory .
            '/' .
            $filename;

        /*
         * Temporäre Textdatei für Piper.
         */
        $inputFile =
            tempnam(
                sys_get_temp_dir(),
                'ela-piper-'
            );

        if ($inputFile === false) {
            throw new RuntimeException(
                'Temporäre Datei konnte nicht erstellt werden.'
            );
        }

        try {

            if (
                file_put_contents(
                    $inputFile,
                    $text . PHP_EOL
                ) === false
            ) {
                throw new RuntimeException(
                    'Ansagetext konnte nicht geschrieben werden.'
                );
            }

            /*
             * Argumente sicher quoten.
             */
            $command =
                escapeshellarg($piper) .
                ' --model ' .
                escapeshellarg($model) .
                ' --input_file ' .
                escapeshellarg($inputFile) .
                ' --output_file ' .
                escapeshellarg($outputFile) .
                ' 2>&1';

            $output = [];

            $returnCode = 0;

            exec(
                $command,
                $output,
                $returnCode
            );

            if ($returnCode !== 0) {

                throw new RuntimeException(
                    'Piper konnte die Ansage nicht erzeugen: ' .
                    implode("\n", $output)
                );
            }

            if (
                !is_file($outputFile) ||
                filesize($outputFile) === false ||
                filesize($outputFile) <= 0
            ) {
                throw new RuntimeException(
                    'Piper hat keine gültige Audiodatei erzeugt.'
                );
            }

            /*
             * WAV-Datei auslesen.
             */
            $wav = $this->readWavInfo(
                $outputFile
            );

            return [
                'filename' => $filename,
                'path' => $outputFile,
                'format' => 'wav',
                'sample_rate' => $wav['sample_rate'],
                'channels' => $wav['channels'],
                'bit_depth' => $wav['bit_depth'],
                'duration' => $wav['duration'],
                'file_size' => filesize($outputFile)
            ];

        } finally {

            if (is_file($inputFile)) {
                unlink($inputFile);
            }
        }
    }

    /**
     * WAV File mit Microfon erzeugen
     */


public function createFromRecording(array $upload, string $title): int
{
    if (
        !isset($upload['tmp_name']) ||
        !is_uploaded_file($upload['tmp_name'])
    ) {
        throw new RuntimeException(
            'Keine Aufnahme empfangen.'
        );
    }

    $audioDirectory =
        '/opt/ela/audio/announcements';

    $filename =
        'announcement-' .
        date('Ymd-His') .
        '-' .
        bin2hex(random_bytes(4)) .
        '.wav';

    $wavFile =
        $audioDirectory . '/' . $filename;

    $cmd = sprintf(
        'ffmpeg -y -i %s -ac 1 -ar 22050 -c:a pcm_s16le %s 2>&1',
        escapeshellarg($upload['tmp_name']),
        escapeshellarg($wavFile)
    );

    exec($cmd, $output, $returnCode);

    if ($returnCode !== 0) {
        throw new RuntimeException(
            implode("\n", $output)
        );
    }

    $wav = $this->readWavInfo($wavFile);

    return $this->create(
        trim($title),
        null,
        'mikrofon',
        $filename,
        'wav',
        (int) $wav['sample_rate'],
        (int) $wav['channels'],
        (int) $wav['bit_depth'],
        isset($wav['duration'])
            ? (float) $wav['duration']
            : null,
        filesize($wavFile)
    );
}



    /**
     * Grundlegende WAV-Informationen auslesen.
     */
    private function readWavInfo(
        string $filename
    ): array {

        $handle = fopen(
            $filename,
            'rb'
        );

        if ($handle === false) {
            throw new RuntimeException(
                'WAV-Datei konnte nicht gelesen werden.'
            );
        }

        try {

            $header = fread(
                $handle,
                44
            );

            if (
                $header === false ||
                strlen($header) < 44
            ) {
                throw new RuntimeException(
                    'Ungültige WAV-Datei.'
                );
            }

            if (
                substr($header, 0, 4) !== 'RIFF' ||
                substr($header, 8, 4) !== 'WAVE'
            ) {
                throw new RuntimeException(
                    'Die erzeugte Datei ist keine gültige WAV-Datei.'
                );
            }

            $channels =
                unpack(
                    'v',
                    substr($header, 22, 2)
                )[1];

            $sampleRate =
                unpack(
                    'V',
                    substr($header, 24, 4)
                )[1];

            $bitDepth =
                unpack(
                    'v',
                    substr($header, 34, 2)
                )[1];

            $dataSize =
                unpack(
                    'V',
                    substr($header, 40, 4)
                )[1];

            $bytesPerSecond =
                $sampleRate *
                $channels *
                ($bitDepth / 8);

            $duration = null;

            if ($bytesPerSecond > 0) {
                $duration =
                    $dataSize /
                    $bytesPerSecond;
            }

            return [
                'sample_rate' => $sampleRate,
                'channels' => $channels,
                'bit_depth' => $bitDepth,
                'duration' => $duration
            ];

        } finally {

            fclose($handle);
        }
    }

}
