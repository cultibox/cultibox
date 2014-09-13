<?php

// {{{  getmicrotime()
// ROLE    send a time to compute page loading
//  IN     none
// RET     time elapsed 
/* USAGE
    $debut = getmicrotime();
    $fin = getmicrotime();
    echo "Page générée en ".round($fin-$debut, 3) ." secondes.<br />";
*/
function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}
// }}}


// {{{  convert_SIZE()
// ROLE    convert octet into kB,MB,GB
//  IN     size to be converted
// RET     size converted 
function convert_SIZE($size) {
    $unite = array('B','kB','MB','GB');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unite[$i];
}
// }}}


// {{{  aff_variables()
// ROLE    display all variables used and its memory
//  IN     none
// RET     none
function aff_variables() {
   echo '<br/>';
   global $datas ;
   foreach($GLOBALS as $Key => $Val) { //Pour chaque variables globales
      if ($Key != 'GLOBALS') {
         echo' <br/>'. $Key .' &asymp; '.sizeofvar( $Val ); //Affichage de la variable et de sa taille
      }
   }
    echo' <br/>';
}
// }}}


//{{{  Same as aff_variables but for a single variable
function sizeofvar($var) {
  $start_memory = memory_get_usage();
  $temp =unserialize(serialize($var ));
  $taille = memory_get_usage() - $start_memory;
  return convert_SIZE($taille) ;
}
// }}}


// {{{  memory_stat()
// ROLE    display memory usage for a PHP script
// IN     none
// RET     none
function memory_stat() {
   echo  'Mémoire -- Utilisé : '. convert_SIZE(memory_get_usage(false)) .
   ' || Alloué : '.
   convert_SIZE(memory_get_usage(true)) .
   ' || MAX Utilisé  : '.
   convert_SIZE(memory_get_peak_usage(false)).
   ' || MAX Alloué  : '.
   convert_SIZE(memory_get_peak_usage(true)).
   ' || MAX autorisé : '.
   ini_get('memory_limit') ;  ;
}
// }}}


//{{{ deb()
// ROLE write a debug text in a file
// IN txt   text to be written
// OUT none
function deb($txt) {
    $path="";
    $os=php_uname('s'); //Récupération de l'OS hôte
    switch($os) {
        case 'Linux': //Pour linux et mac le chemin du fichier de debug sera /tmp
        case 'Mac':
        case 'Darwin':
            $path="/tmp";
            break;
        case 'Windows NT': //Pour windows le chemin du fichier sera c:
            $path="c:";
        break;
    }
    $handle=fopen("$path/debug.txt", 'a+');
    fwrite($handle,$txt . "\n");
    fclose($handle);
}
//}}}
?>

