<?php

    require_once 'config.php';

    echo StatX::testInstancesCount().'<br>';
    echo StatX::taskCount().'<br>';
    echo StatX::studentCount().'<br>';
    echo StatX::teacherCount().'<br>';
    echo StatX::groupCount().'<br>';

?>
<pre>
    <?php print_r(StatX::resultsByTestInstance(11)); ?>
</pre>