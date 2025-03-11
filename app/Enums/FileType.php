<?php

namespace App\Enums;

use App\Enums\Traits\NamedOption;

enum FileType: string
{
    use NamedOption;

    case Archive = 'zip';
    case Audio = 'audio';
    case Image = 'image';
    case PDF = 'pdf';
    case Presentation = 'presentation';
    case Spreadsheet = 'spreadsheet';
    case Text = 'text';
    case Video = 'video';

    public function getIconClass(): string
    {
        return 'fa fa-fw ' . match ($this) {
            self::Archive => 'fa-file-zipper',
            self::Audio => 'fa-file-audio',
            self::Image => 'fa-file-image',
            self::PDF => 'fa-file-pdf',
            self::Presentation => 'fa-file-powerpoint',
            self::Spreadsheet => 'fa-file-excel',
            self::Text => 'fa-file-lines',
            self::Video => 'fa-file-video',
        };
    }

    /**
     * @return string[]
     */
    public function getExtensions(): array
    {
        return match ($this) {
            self::Archive => [
                '7z',
                'tar',
                'tar.gz',
                'zip',
            ],
            self::Audio => [
                'aac',
                'mp3',
                'ogg',
                'wav',
            ],
            self::Image => [
                'bmp',
                'jpeg',
                'jpg',
                'png',
                'svg',
            ],
            self::PDF => [
                'pdf',
            ],
            self::Presentation => [
                'odp',
                'ppt',
                'pptx',
            ],
            self::Spreadsheet => [
                'csv',
                'ods',
                'xls',
                'xlsx',
            ],
            self::Text => [
                'doc',
                'docx',
                'md',
                'odt',
                'txt',
            ],
            self::Video => [
                'mp4',
                'ogv',
                'webm',
            ],
        };
    }

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Archive => __('archive file'),
            self::Audio => __('audio'),
            self::Image => __('image'),
            self::PDF => 'PDF',
            self::Presentation => __('presentation'),
            self::Spreadsheet => __('spreadsheet'),
            self::Text => __('text'),
            self::Video => __('video'),
        };
    }

    /**
     * @return string[]
     */
    public static function extensions(): array
    {
        return array_merge(
            ...array_map(
                static fn (FileType $fileType) => $fileType->getExtensions(),
                self::cases()
            )
        );
    }

    public static function extensionsForHtmlAccept(): string
    {
        return implode(
            ',',
            array_map(
                fn (string $extension) => '.' . $extension,
                self::extensions()
            )
        );
    }

    public static function tryFromExtension(string $extension): ?FileType
    {
        foreach (self::cases() as $fileType) {
            if (in_array($extension, $fileType->getExtensions(), true)) {
                return $fileType;
            }
        }

        return null;
    }
}
