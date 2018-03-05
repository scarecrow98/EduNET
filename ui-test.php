<?php

    require_once 'config.php';

    $html1 = UIDrawer::quizResult(1, 1);
    echo $html1.'<br>';

    $html2 = UIDrawer::trueFalseResult(1, 0);
    echo $html2.'<br>';

    $html3 = UIDrawer::pairingResult(1, null);
    echo $html3.'<br>';
?>