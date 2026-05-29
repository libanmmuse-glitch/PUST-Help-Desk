<?php
/**
 * Remove outer white/light background from PUST logo (JPEG or PNG).
 */

function cleanPustLogoBackground(string $sourcePath, string $outputPath, int $whiteMin = 235): bool
{
    if (!extension_loaded('gd') || !is_file($sourcePath)) {
        return false;
    }

    $blob = @file_get_contents($sourcePath);
    if ($blob === false) {
        return false;
    }

    $img = @imagecreatefromstring($blob);
    if (!$img) {
        return false;
    }

    $w = imagesx($img);
    $h = imagesy($img);
    if ($w < 1 || $h < 1) {
        imagedestroy($img);
        return false;
    }

    if (!imageistruecolor($img)) {
        imagepalettetotruecolor($img);
    }

    imagesavealpha($img, true);
    imagealphablending($img, false);

    $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);

    // Edge-connected background removal (queue stores packed y*w+x, no huge PHP arrays).
    $visited = str_repeat("\0", (int) (($w * $h + 7) / 8));
    $isVisited = static function (int $i) use ($visited): bool {
        return (ord($visited[$i >> 3]) & (1 << ($i & 7))) !== 0;
    };
    $setVisited = static function (int $i) use (&$visited): void {
        $visited[$i >> 3] = chr(ord($visited[$i >> 3]) | (1 << ($i & 7)));
    };

    $queue = [];
    $head = 0;
    $push = static function (int $x, int $y) use (&$queue, $w, $h, $isVisited, $setVisited): void {
        if ($x < 0 || $y < 0 || $x >= $w || $y >= $h) {
            return;
        }
        $i = $y * $w + $x;
        if ($isVisited($i)) {
            return;
        }
        $setVisited($i);
        $queue[] = $i;
    };

    for ($x = 0; $x < $w; $x++) {
        $push($x, 0);
        $push($x, $h - 1);
    }
    for ($y = 0; $y < $h; $y++) {
        $push(0, $y);
        $push($w - 1, $y);
    }

    $isLight = static function (int $r, int $g, int $b) use ($whiteMin): bool {
        return $r >= $whiteMin && $g >= $whiteMin && $b >= $whiteMin;
    };

    while ($head < count($queue)) {
        $i = $queue[$head++];
        $x = $i % $w;
        $y = intdiv($i, $w);
        $c = imagecolorat($img, $x, $y);
        $r = ($c >> 16) & 0xFF;
        $g = ($c >> 8) & 0xFF;
        $b = $c & 0xFF;
        if (!$isLight($r, $g, $b)) {
            continue;
        }
        imagesetpixel($img, $x, $y, $transparent);
        $push($x + 1, $y);
        $push($x - 1, $y);
        $push($x, $y + 1);
        $push($x, $y - 1);
    }

    // Soft fringe: light pixels touching transparency.
    for ($pass = 0; $pass < 2; $pass++) {
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $c = imagecolorat($img, $x, $y);
                if ((($c >> 24) & 0x7F) >= 120) {
                    continue;
                }
                $r = ($c >> 16) & 0xFF;
                $g = ($c >> 8) & 0xFF;
                $b = $c & 0xFF;
                if ($r < 220 || $g < 220 || $b < 220) {
                    continue;
                }
                $touch = false;
                foreach ([[1, 0], [-1, 0], [0, 1], [0, -1]] as [$dx, $dy]) {
                    $nx = $x + $dx;
                    $ny = $y + $dy;
                    if ($nx < 0 || $ny < 0 || $nx >= $w || $ny >= $h) {
                        continue;
                    }
                    if (((imagecolorat($img, $nx, $ny) >> 24) & 0x7F) >= 120) {
                        $touch = true;
                        break;
                    }
                }
                if ($touch) {
                    imagesetpixel($img, $x, $y, $transparent);
                }
            }
        }
    }

    $dir = dirname($outputPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $ok = imagepng($img, $outputPath, 6);
    imagedestroy($img);
    return $ok;
}
