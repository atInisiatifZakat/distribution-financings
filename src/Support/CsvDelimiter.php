<?php

declare(strict_types=1);

namespace Inisiatif\Distribution\Financings\Support;

final class CsvDelimiter
{
    public static function detect(string $path): string
    {
        $handle = \fopen($path, 'rb');

        if ($handle === false) {
            return ';';
        }

        $line = (string) \fgets($handle);
        \fclose($handle);

        return self::fromLine($line);
    }

    public static function fromLine(string $line): string
    {
        $line = \preg_replace('/^\xEF\xBB\xBF/', '', $line) ?? $line;

        $best = ';';
        $bestCount = 0;

        foreach (["\t", ';', ','] as $delimiter) {
            $count = \substr_count($line, $delimiter);

            if ($count > $bestCount) {
                $best = $delimiter;
                $bestCount = $count;
            }
        }

        return $best;
    }
}
