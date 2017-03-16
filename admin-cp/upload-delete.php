<?php
$fh = fopen('../upload/testa.txt', 'a');
fwrite($fh, '<h1>Hello world!</h1>');
fclose($fh);

unlink('../upload/testa.txt');
unlink('../upload/test.txt');
