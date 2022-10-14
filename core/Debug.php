<?php declare(strict_types=1);

namespace Homestead\Core;

final class Debug {
    static function inject(float $seconds, array $warnings): void {
        echo '<section id="homestead-debug" style="background:#eee; border: 1px solid #ccc; font-size: 13px; padding: 5px; font-family: sans-serif; margin-top: 20px;">';
        echo "{$seconds}s<br><br>";
        if(count($warnings)) {
            echo '<b>Warnings</b><br>';
        }
        foreach($warnings as $warning) {
            echo "$warning<br>";
        }
        echo '<section>';
    }
}