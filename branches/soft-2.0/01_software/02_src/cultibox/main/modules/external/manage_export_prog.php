<?php


if((isset($export))&&(!empty($export))) {
     //Pour l'export d'un programme, le fichier exporté est au format csv (séparé par des ',') et contient time_start,time_stop,value
     //L'id n'est pas utilisée afin de pouvoir appliquer un programme sur plusieurs prises.

     //Export du programme au format csv, création du fichier program_plugX.prg:
     program\export_program($export_selected,$program_index,$main_error);

     $file="tmp/program_plug${export_selected}.prg";
     if (($file != "") && (file_exists("./$file"))) {
        //Si le programme exporté à bien été créé dans un fichier, on lance le téléchargement (fichier se trouvant dans le répertoire tmp de joomla)
        $size = filesize("./$file");
        header("Content-Type: application/force-download; name=\"$file\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: $size");
        header("Content-Disposition: attachment; filename=\"".basename($file)."\"");
        header("Expires: 0");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        readfile("./$file");
        exit();
    }
?>
