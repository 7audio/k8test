<?php

function send_email($from, $to, $text): void
{
    sleep(mt_rand(1, 10));
    $path = sprintf(
        '%s/../runtime/email/%s_%d_%s.txt',
        __DIR__,
        $to,
        microtime(),
        uniqid(),
    );
    $content = sprintf(
        "From: %s\nTo: %s\n\n%s",
        $from,
        $to,
        $text,
    );
    file_put_contents($path, $content);
}
