<?php

for ($i = 1; $i <= 30; $i++) {

    echo "Just some random data: ".md5($i)."\n";

    sleep(1);

}