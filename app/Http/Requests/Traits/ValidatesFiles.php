<?php

namespace App\Http\Requests\Traits;

use Symfony\Component\Mime\MimeTypes;

trait ValidatesFiles
{
    public static function getMaxFileSizeInBytes(): int
    {
        return min(
            /** @phpstan-ignore argument.type */
            ini_parse_quantity(ini_get('upload_max_filesize')),
            /** @phpstan-ignore argument.type */
            ini_parse_quantity(ini_get('post_max_size'))
        );
    }

    public static function getMaxFileSizeInKiloBytes(): float
    {
        return self::getMaxFileSizeInBytes() / 1024.0;
    }

    public static function getMaxFileSizeInMegaBytes(): float
    {
        return self::getMaxFileSizeInKiloBytes() / 1024.0;
    }

    public static function getMaxFileSizeRule(): string
    {
        $maxFileSize = (int) self::getMaxFileSizeInKiloBytes();
        return 'max:' . $maxFileSize;
    }

    /**
     * @param array<int, string> $extensions
     * @return array<int, string>
     */
    public static function getMimeTypesFromExtensions(array $extensions): array
    {
        return array_unique(
            array_merge(
                ...array_map(
                    static fn (string $extension) => MimeTypes::getDefault()->getMimeTypes($extension),
                    $extensions
                )
            )
        );
    }
}
