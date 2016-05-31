<?php
function getex($filename) {
    return end(explode(".", $filename));
}
if($_FILES['upload'])
{
    if (($_FILES['upload'] == "none") OR (empty($_FILES['upload']['name'])) )
    {
        $message = "Вы не выбрали файл";
    }
    else if ($_FILES['upload']["size"] == 0 OR $_FILES['upload']["size"] > 20500000)
    {
        $message = "Размер файла не соответствует нормам";
    }
//    else if (($_FILES['upload']["type"] != "image/jpeg") && ($_FILES['upload']["type"] != "image/png"))
//    {
//        $message = "Допускается загрузка только картинок JPG и PNG.";
//    }
    else if (!is_uploaded_file($_FILES['upload']["tmp_name"]))
    {
        $message = "Что-то пошло не так. Попытайтесь загрузить файл ещё раз.";
    }
    else{
        $name =time().'.'.getex($_FILES['upload']['name']);
        move_uploaded_file($_FILES['upload']['tmp_name'], $_SERVER['DOCUMENT_ROOT']."/upload/images/".$name);
        $full_path = $_SERVER['HTTP_ORIGIN'].'/upload/images/'.$name;
//        $message = "Файл ".$_SERVER['DOCUMENT_ROOT']."/upload/images/".$name.' '.$_FILES['upload']['name']." загружен";
        $message = '';
    }
    $callback = $_REQUEST['CKEditorFuncNum'];
    echo '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction("'.$callback.'", "'.$full_path.'", "'.$message.'" );</script>';
}else
{
//    echo 'fuck;';
}
