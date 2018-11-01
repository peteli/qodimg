<?php
require 'qod.php';
?>
<?php
$todqod = new QOD();
$todqod->getQuote();
echo ($todqod->quote);
echo ($todqod->author);
echo ($todqod->background);
$todqod = NULL;
exit;
?>