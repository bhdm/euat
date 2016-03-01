<?php

move_uploaded_file($_FILES['upload']['tmp_name'], "userfiles/".$name);
$full_path = 'http://вашсайт.ру/userfiles/'.$name;
$message = "Файл ".$_FILES['upload']['name']." загружен";
$size=@getimagesize('userfiles/'.$name);
if($size[0]<50 OR $size[1]<50) {
    unlink('userfiles/' . $name);
}