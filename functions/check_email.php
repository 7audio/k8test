<?php

function check_email($email): int
{
    $validProbability = 75;
    if (!getenv('QUICK')) {
        sleep(mt_rand(1, 60));
    }
    return (mt_rand(1, 100) <= $validProbability) ? 1 : 0;
}
