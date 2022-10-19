<?php declare(strict_types=1);

namespace Homestead\Core;

final class Debug {
    static function inject(float $seconds, array $warnings): void {
        $content = '<section id="homestead-debug" style="position: fixed; bottom: 0; left: 0; width: 100%; background:#111; font-size: 13px; padding: 20px; color: #eee; font-family: sans-serif; z-index: 999999;">';
        $content .= "{$seconds}s<br><br>";
        if(count($warnings)) {
            $content .= '<b>Warnings</b><br>';
        }
        foreach($warnings as $warning) {
            $content .= "$warning<br>";
        }
        $content .= '</section>';

        echo $content;
    }
}