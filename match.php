<?php

$path = __DIR__."/uploads/";
$target_file =  $path.time()."_".basename($_FILES["file"]["name"]);
$file=$_FILES['file']['name'];    
$result = move_uploaded_file($_FILES['file']['tmp_name'],$target_file.".wav");
if ($result) {
    // echo $target_file;
    
 

$res = exec("python3 ./app.py $target_file.wav 2>&1");
 
echo $res;

unlink("$target_file.wav");
}
 

