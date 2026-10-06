<?php

namespace App\Core;

/**
 * Shrinks receipt photos before they are stored. Phone photos (3–8 MB) usually end
 * up around 150–300 KB while staying readable.
 */
class ReceiptImage
{
    private const MAX_EDGE   = 1600;
    private const QUALITY    = 78;
    private const MAX_PIXELS = 40_000_000; // skip anything that could exhaust memory_limit

    /**
     * @return string|null Path to a compressed JPEG temp file, or null to keep the original.
     */
    public static function compress(string $srcPath, string $mime): ?string
    {
        if (!extension_loaded('gd') || !in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return null;
        }

        $info = @getimagesize($srcPath);
        if (!$info || $info[0] < 1 || $info[1] < 1 || $info[0] * $info[1] > self::MAX_PIXELS) {
            return null;
        }
        [$width, $height] = $info;

        $previousLimit = ini_get('memory_limit');
        @ini_set('memory_limit', '256M');

        try {
            $src = match ($mime) {
                'image/jpeg' => @imagecreatefromjpeg($srcPath),
                'image/png'  => @imagecreatefrompng($srcPath),
                'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($srcPath) : false,
            };
            if (!$src) {
                return null;
            }

            $rotated = false;
            if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
                $exif  = @exif_read_data($srcPath);
                $angle = match ((int) ($exif['Orientation'] ?? 1)) { 3 => 180, 6 => -90, 8 => 90, default => 0 };
                if ($angle !== 0 && ($turned = imagerotate($src, $angle, 0))) {
                    imagedestroy($src);
                    $src     = $turned;
                    $width   = imagesx($src);
                    $height  = imagesy($src);
                    $rotated = true;
                }
            }

            $scale     = min(1, self::MAX_EDGE / max($width, $height));
            $newWidth  = max(1, (int) round($width * $scale));
            $newHeight = max(1, (int) round($height * $scale));

            $dst = imagecreatetruecolor($newWidth, $newHeight);
            imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255)); // flatten PNG transparency onto white
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($src);

            $out = tempnam(sys_get_temp_dir(), 'rcpt');
            $ok  = $out && imagejpeg($dst, $out, self::QUALITY);
            imagedestroy($dst);
            if (!$ok) {
                return null;
            }

            // Keep the original if re-encoding gained nothing (already small, not resized or rotated)
            if ($scale == 1 && !$rotated && filesize($out) >= filesize($srcPath)) {
                @unlink($out);
                return null;
            }
            return $out;
        } catch (\Throwable $e) {
            error_log('Receipt compression failed: ' . $e->getMessage());
            return null;
        } finally {
            @ini_set('memory_limit', $previousLimit);
        }
    }
}
