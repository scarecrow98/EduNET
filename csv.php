<pre>
<?php



    require_once 'config.php';

    echo Security::generatePassword();

    if( isset($_FILES['csv']) ){
        $data = FileUploader::parseCSV($_FILES['csv']);
        print_r($data);
    }

?>
</pre>
<form action="" method="POST" enctype="multipart/form-data">
    <input type="file" name="csv">
    <input type="submit" name="submit">
</form>