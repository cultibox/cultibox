
<?php

   // Définition des variables
   $wikiDir = "main/modules/wiki/";

   // Nom de la page qui effectue le parser
   $parserPage = "/cultibox/index.php/help";

   // Nom de la page par défaut
   $wikiDefaultPage = "Introduction.wiki";

   // Ancien lien vers SVN
   $wikiOldLink = "http://cultibox.googlecode.com/svn/wiki/";
   // Nouveau lien
   $wikiNewLink = "main/modules/wiki/";

   // Filtre qui permet de reconnaitre si le lien est un liens vers une image
   $imageLinkFilter = "svn/wiki/img/";

   // Définition des fonctions
   function parse_char ($line, $char, $array) {

      // Définition du compteur
      $count = 0;

      // On cherche le caractere a remplacer
      $pos = strpos($line, $char);

      // Il nous faut au moins deux exemplaires sur la même ligne
      if (strpos($line, $char, $pos + 1) === false) {
         return $line;
      }

      while ($pos !== false) {

         $line = substr_replace($line, $array[$count % count($array)], $pos, strlen($char));

         $count = $count + 1 ;

         $pos = strpos($line, $char , $pos);
      }

      return $line;
   }

   // Cette fonction permet de parser les titres
   function parse_title ($line, $char, $number) {

      // Définition du compteur
      $count = 0;

      // On cherche le caractere a remplacer
      $pos = strpos($line, $char);

      // Il nous faut au moins deux exemplaires sur la même ligne
      if (strpos($line, $char, $pos + 1) === false) {
         return $line;
      }

      // On récupérer le nom du titre
      $title = trim(str_replace("=","",$line));

      $line = "<br /><br /><p class='title_help'>" . $title . "</p><br />\n";

      return $line;
   }

   // Cette fonction parse les les listes
   $listeIn = 0;
   $listeOldLevel = 0;
   $listeOldNuerot = "";
   function parse_list ($line) {
      global $listeIn;
      global $listeOldLevel;
      global $listeOldNuerot;

      // On regarde si la chaine de caractere commence par une étoile
      $pos = strpos(trim($line), "*");
      $posSsTrim = strpos($line, "*");
      $numerotee = "ul";
      # On recherche l'autre liste
      if ($pos === false) {
         $pos = strpos(trim($line), "#");
         $posSsTrim = strpos($line, "#");
         $numerotee = "ol";
      }

      // Le premier caractere est une étoile
      if ($pos == 0 && $pos !== false) {

         // On remplace le premier charactere
         $line = substr_replace($line, "<li> \n", strpos($line, "*"), 1);
         //$line = " <li> " . $line . " </li>";

         // On ajoute en fin de ligne le dernier caractere
         $line = $line . " </li> \n";

         // Il faut créer le tag si le niveau change

         if ($posSsTrim > $listeOldLevel ) {
            $line = "<" . $numerotee . "> \n " . $line;
            $listeIn = 1;
            $listeOldLevel = $posSsTrim;
         }

         // On ferme la balise si on redescend d'un niveau
         if ($posSsTrim < $listeOldLevel ) {
            $line = "</" . $numerotee . "> \n " . $line;
            $listeIn = 1;
            $listeOldLevel = $posSsTrim;
         }

         $listeOldNuerot = $numerotee;

      } else {
         // On était dans une liste mais on ne l'est plus
         if ($listeIn == 1) {
            $line = "</" . $listeOldNuerot . "> \n " . $line;
            $listeIn = 0;
         }
      }

      return $line;
   }



   // Cette fonction parse les liens
   function parse_link ($line, $parser,$filter) {

      // Recherche du début et de la fin du lien
      $posStart = strpos($line, "[");
      $posEnd = strpos($line, "]");

      // Un lien est présent dans la page
      if ($posStart !== false && $posEnd !== false) {
         // On remplace le crochet ouvrant
         $line = substr_replace($line, '<a href="'.$parser.'?page=', $posStart, 1);

         // On remplace le premier espace
         $line = substr_replace($line, '.wiki"> ', strpos($line, " ", $posStart + 3), 1);

         // On remplace le crochet fermant
         $line = substr_replace($line, "</a>", strpos($line, "]"), 1);

      }

      // recherche des lien direct
      $posStart = strpos($line, "http");

      if ($posStart !== false) {

         // Calcul de la position de fin du texte
         $posEnd = strpos($line, " ", $posStart);

         if ($posEnd === false) {
            // C'est le dernier caractere affiché
            $stringLength = strLen($line) - $posStart;
         } else {
            $stringLength = $posEnd - $posStart;
         }

         // On récupérer le texte
         $textLink = substr($line , $posStart , $stringLength);

         // Vérification de la présence d'une nouvelle ligne : dans ce cas, on la met à la fin
         $nouvelleLigne= strpos($textLink, "\n");

         // On recherche si le lien pointe vers une image
         $image= strpos($textLink, $filter);

         if ($image === false) {
            // Ce n'est pas une image
            if ($nouvelleLigne === false) {
               // On insére le lien
               $line = substr_replace($line, '<a href="' . $textLink . '"> ' . $textLink . '</a>', $posStart, $stringLength);
            } else {
               $textLink = str_replace("\n","",$textLink);
               // On insére le lien
               $line = substr_replace($line, '<a href="' . $textLink . '"> ' . $textLink . "</a> \n", $posStart, $stringLength);
            }
         } else {
            // C'est une image
            if ($nouvelleLigne === false) {
               // On insére le lien
               $line = substr_replace($line, '<img src="' . $textLink . '" /> ' . "<br />", $posStart, $stringLength);
            } else {
               $textLink = str_replace("\n","",$textLink);
               // On insére le lien
               $line = substr_replace($line, '<img src="' . $textLink . '" />' . "<br />" . "\n", $posStart, $stringLength);
            }
         }
      }

      return $line;
   }

   // Cette fonction permet de gérer les tables
   // Cette fonction parse les liens
   $tableIn = 0;
   function parse_table ($line) {
      global $tableIn;

      // On regarde si la chaine de caractere commence par un ||
      $pos = strpos($line, "||");

      // Le premier caractere est une étoile
      if ($pos !== false) {

         // On remplace les ||
         $inCase = 0;
         while ($pos !== false) {
            // C'est le premier de la ligne qu'on remplace
            if ($inCase == 0) {
               $line = substr_replace($line, '<tr> <td style="border: 1px solid #ccc; padding: 5px;"> ', $pos, 2);
               $inCase = 1;
            } else {
               // Si ce n'est pas le dernier
               $NewPos = strpos($line, "||", $pos + 1);
               if ($NewPos !== false) {
                  $line = substr_replace($line, '</td> <td style="border: 1px solid #ccc; padding: 5px;">', $pos, 2);
               } else {
                  // C'est le dernier
                  $line = substr_replace($line, '</td> </tr>', $pos, 2);
               }
            }
            $pos = strpos($line, "||" , $pos);
         }

         // On est pas encore dans une table, il faut créer le tag
         if ($tableIn == 0) {
            $line = '<table style="border: 1px solid rgb(70, 70, 70) border-spacing: 2px;"> ' . $line;
            $tableIn = 1;
         }

      } else {
         // On était dans une liste mais on ne l'est plus
         if ($tableIn == 1) {
            $line = "</table> " . $line;
            $tableIn = 0;
         }
      }

      return $line;

   }


   // Cette fonction retourne un sommaire
   $sumUp = array();
   $pageTitle = "";
   function make_summary ($page) {

      global $sumUp;
      global $pageTitle;

     // Ouverture du fichier en lecture seule
      $handle = fopen($page, 'r');

      $titreNumber = 0;

      // Si on a réussi à ouvrir le fichier
      if ($handle)
      {
      	// Tant que l'on est pas à la fin du fichier
      	while (!feof($handle))
      	{
      		// On lit la ligne courante
      		$buffer = fgets($handle);

            $sumUp["nombreTitre"] = $titreNumber;

      		// On recherche les titres
      		if (strpos($buffer, "=====") !== false && strpos($buffer, "=====",strpos($buffer, "=====") + 1) !== false) {
               $sumUp[$titreNumber]["niveau"] = 5;
               $sumUp[$titreNumber]["titre"] = trim(str_replace("=","",$buffer));
               $titreNumber = $titreNumber + 1;
            } elseif (strpos($buffer, "====") !== false && strpos($buffer, "====",strpos($buffer, "====") + 1) !== false) {
               $sumUp[$titreNumber]["niveau"] = 4;
               $sumUp[$titreNumber]["titre"] = trim(str_replace("=","",$buffer));
               $titreNumber = $titreNumber + 1;
            } elseif (strpos($buffer, "===") !== false && strpos($buffer, "===",strpos($buffer, "===") + 1) !== false) {
               $sumUp[$titreNumber]["niveau"] = 3;
               $sumUp[$titreNumber]["titre"] = trim(str_replace("=","",$buffer));
               $titreNumber = $titreNumber + 1;
            } elseif (strpos($buffer, "==") !== false && strpos($buffer, "==",strpos($buffer, "==") + 1) !== false) {
               $sumUp[$titreNumber]["niveau"] = 2;
               $sumUp[$titreNumber]["titre"] = trim(str_replace("=","",$buffer));
               $titreNumber = $titreNumber + 1;
            } elseif (strpos($buffer, "=") !== false && strpos($buffer, "=",strpos($buffer, "=") + 1) !== false) {
               $sumUp[$titreNumber]["niveau"] = 1;
               $sumUp[$titreNumber]["titre"] = trim(str_replace("=","",$buffer));
               $titreNumber = $titreNumber + 1;
            }

            // On récupérere le titre de la page
            if (strpos($buffer, "#summary") !== false) {
               $pageTitle = trim(str_replace("#summary","",$buffer));
            }

      	}
      	// On ferme le fichier
      	fclose($handle);

      }
      return "";
   }

   // Cette fonction ajoute le sommaire si nécéssaire
   function parse_summary ($line) {
      global $sumUp;

      // On regarde si la chaine de caractere commence par un ||
      $pos = strpos($line, "wiki:toc");

      if ($pos !== false) {

         // On recherche le nombre de niveau
         $pos = strpos($line, "max_depth=");
         $nbIndex = substr($line, $pos + 11, 1);

         $level = 0;

         $line = "";

         for ($i = 0 ; $i < $sumUp["nombreTitre"] ; $i++) {
            // Si le niveau est inférieur au max
            if ($sumUp[$i]["niveau"] <= $nbIndex) {

               // Si on chnage de niveau il faut ouvrir la balise ul
               if ($sumUp[$i]["niveau"] > $level) {
                  $line = $line . "<ul>";
               }
               $line = $line . "\n" . '<li> <a href="#' . str_replace(" " , "_" , $sumUp[$i]["titre"]) . '">' . $sumUp[$i]["titre"] . "</a> </li> \n";

               // Si on chnage de niveau il faut fermer la balise ul
               if ($sumUp[$i]["niveau"] < $level) {
                  $line = $line . "\n </ul>";
               }
               $level = $sumUp[$i]["niveau"];
            }
         }

         // Fermeture de la liste
         $line = $line . " </ul> \n";
      }

      return $line;
   }

   // On récupére la page à afficher
   if (isset ($_GET['page'])) {
      // On lit la page demandé
      $wikipage = $_GET['page'];
   } else {
      // Aucune page n'est demandé, on redirige vers la page principale
      $wikipage = $wikiDefaultPage;
   }

   // On crée le chemin vers le fichier
   $wikipage = $wikiDir . $wikipage;

   // Préparation des éléments haut niveau
   // On analyse la page pour faire un sommaire
   make_summary($wikipage);

   // Analyse du fichier et création de la page
   // Ouverture du fichier en lecture seule
   $handle = fopen($wikipage, 'r');
   // Si on a réussi à ouvrir le fichier
   if ($handle)
   {
   	// Tant que l'on est pas à la fin du fichier
   	while (!feof($handle))
   	{
   		// On lit la ligne courante
   		$buffer = fgets($handle);

   		// Remplecement des listes
   		$buffer = parse_list($buffer,$parserPage);

   		// Recherche du bold
   		$array = array('<b>', '</b>');
   		$buffer = parse_char($buffer,"*",$array);

   		// Recherche des titres
   		$buffer = parse_title($buffer,"=====",6);
   		$buffer = parse_title($buffer,"=====",5);
   		$buffer = parse_title($buffer,"====",4);
   		$buffer = parse_title($buffer,"===",3);
   		$buffer = parse_title($buffer,"==",2);
   		$buffer = parse_title($buffer,"=",1);

   		// On parse les liens
   		$buffer = parse_link($buffer,$parserPage,$imageLinkFilter);

   		// On parse les tables
   		$buffer = parse_table($buffer);

   		// On remplace les liens en dur
   		$buffer = str_replace($wikiOldLink,$wikiNewLink,$buffer);

   		// On enleve la ligne de summary
         if (strpos($buffer, "#summary") !== false) {
            $buffer = "";
         }

         // On cherche si une table de sommaire est définie
         $buffer = parse_summary($buffer);

   		// On l'affiche
   		echo $buffer;
   	}
   	// On ferme le fichier
   	fclose($handle);
   } else {
      echo "La page demandée n'existe pas";
   }

   // Création du bas de page

   echo '  </body>' . "\n";
   echo '</html>' . "\n";

?>

