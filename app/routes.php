<?php

foreach (glob(ROOT . '/routes/*.php') as $filename) {
    require $filename;
}