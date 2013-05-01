
# Définition des fichiers
set outFileName [file join [file dirname [info script]] calendrier_lunaire.xml]
set apogee_perigeeFileName [file join [file dirname [info script]] apogee_perigee.txt]
# set apogee_perigeeFileName {F:\Cultibox_web\02_documentation\03_admindoc\calendrier_lunaire\apogee_perigee.txt} 
set nouvelle_pleine_luneFileName [file join [file dirname [info script]] nouvelle_pleine_lune.txt]
# set nouvelle_pleine_luneFileName {F:\Cultibox_web\02_documentation\03_admindoc\calendrier_lunaire\nouvelle_pleine_lune.txt} 

set Year [clock format [clock seconds] -format %y]

# Lecture des infos du fichier de perigée (copier coller du site http://www.uppp.free.fr/Ciel9.htm)
set fid [open $apogee_perigeeFileName r+]
set numLigne 0
set perigee ""
set apogee ""
while {[eof $fid] != 1} {
   gets $fid UneLigne

   # On ignore les deux premieres ligne
   if {$numLigne > 1 && $UneLigne != ""} {
      set month1 [string range $UneLigne 0 2]
      set month1 [string map {Fev Feb Avr Apr Mai May Jui Jun Aou Aug} $month1]
      set day1   [string range $UneLigne 4 5]
      set hour1  [string range $UneLigne 7 8]
      set min1   [string range $UneLigne 10 11]
      if {$month1 != ""} {
         puts "$Year $month1 $day1 $hour1 $min1"
         lappend perigee [clock scan "$Year $month1 $day1 $hour1 $min1" -format {%y %b %e %H %M}]
      }

      set month2 [string range $UneLigne 36 38]
      set month2 [string map {Fev Feb Avr Apr Mai May Jui Jun Aou Aug} $month2]
      set day2   [string range $UneLigne 40 41]
      set hour2  [string range $UneLigne 43 44]
      set min2   [string range $UneLigne 46 47]
      if {$month2 != ""} {
         puts "$Year $month2 $day2 $hour2 $min2"
         lappend apogee [clock scan "$Year $month2 $day2 $hour2 $min2" -format {%y %b %e %H %M}]
      }
      

   }
   
   incr numLigne
}
close $fid

# Lecture des infos de pleine lune
set fid [open $nouvelle_pleine_luneFileName r+]
set numLigne 0
set nouvelle ""
set pleine ""
while {[eof $fid] != 1} {
   gets $fid UneLigne

   # On ignore la premiere ligne
   if {$numLigne > 0 && $UneLigne != ""} {
      set Year1  [string range $UneLigne 5 6]
      set month1 [string range $UneLigne 8 10]
      set month1 [string map {Fev Feb Avr Apr Mai May Jui Jun Aou Aug} $month1]
      set day1   [string range $UneLigne 12 13]
      set hour1  [string range $UneLigne 15 16]
      set min1   [string range $UneLigne 18 19]
      if {$month1 != "   "} {
         puts "$Year1 -$month1- $day1 $hour1 $min1 --";update
         lappend nouvelle [clock scan "$Year1 $month1 $day1 $hour1 $min1" -format {%y %b %e %H %M}]
      }
      
      set Year2  [string range $UneLigne 30 31]
      set month2 [string range $UneLigne 33 35]
      set month2 [string map {Fev Feb Avr Apr Mai May Jui Jun Aou Aug} $month2]
      set day2   [string range $UneLigne 37 38]
      set hour2  [string range $UneLigne 40 41]
      set min2   [string range $UneLigne 43 44]
      if {$month2 != "   " && $month2 != ""} {
         puts "$Year2 -$month2- $day2 $hour2 $min2";update
         lappend pleine [clock scan "$Year2 $month2 $day2 $hour2 $min2" -format {%y %b %e %H %M}]
      }
      

   }
   
   incr numLigne
}
close $fid

set fid [open $outFileName w+]

puts $fid {<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
   <updated>2013-04-16T13:14:57+02:00</updated>
   <id>http://www.cultibox.fr/</id>
   <title>Calendrier lunaire</title>
   <subtitle></subtitle>
   <author>
      <name>Cultibox</name>
      <uri>http://www.cultibox.fr/</uri>
      <email>info@cultibox.fr</email>
   </author>
   <category term = "lunaire" label = "lunaire" />
   <link rel="self" href="www.cultibox.fr/lunaire.xml" />
   <icon></icon>
   <logo></logo>
   <rights type = "text">
    © Cultibox, 2013
   </rights>}

foreach p $perigee {
   puts $fid {   <entry>}
   puts $fid {      <title>Périgée</title>}
   puts $fid {      <summary>Date des Périgées</summary>}
   puts $fid {      <updated>2013-04-22T23:19:57+02:00</updated>}
   puts $fid {      <id>http://www.cultibox.fr/id1</id>}
   puts $fid {      <category term = "perigee" label = "Perigee" />}
   puts $fid {      <content type = "text">}
   puts $fid {         Perigee: ne pas jardiner.}
   puts $fid {      </content>}
   puts $fid {      <duration>0</duration>}
   puts $fid "      <start>[clock format $p -format {%Y-%m-%dT%H:%M:%S+00:00}]</start>"
   puts $fid {      <icon>moon.png</icon>}
   puts $fid {   </entry>}
}

foreach a $apogee {
   puts $fid {   <entry>}
   puts $fid {      <title>Apogée</title>}
   puts $fid {      <summary>Date des Apogées</summary>}
   puts $fid {      <updated>2013-04-22T23:19:57+02:00</updated>}
   puts $fid {      <id>http://www.cultibox.fr/id1</id>}
   puts $fid {      <category term = "apogee" label = "apogee" />}
   puts $fid {      <content type = "text">}
   puts $fid {         Apogée: ne pas jardiner.}
   puts $fid {      </content>}
   puts $fid {      <duration>0</duration>}
   puts $fid "      <start>[clock format $a -format {%Y-%m-%dT%H:%M:%S+00:00}]</start>"
   puts $fid {      <icon>moon.png</icon>}
   puts $fid {   </entry>}
}

foreach p $pleine {
   puts $fid {   <entry>}
   puts $fid {      <title>Pleine Lune</title>}
   puts $fid {      <summary>Date des Pleines Lunes</summary>}
   puts $fid {      <updated>2013-04-22T23:19:57+02:00</updated>}
   puts $fid {      <id>http://www.cultibox.fr/id1</id>}
   puts $fid {      <category term = "pleine_lune" label = "pleine lune" />}
   puts $fid {      <content type = "text">}
   puts $fid {         C'est la pleine lune.}
   puts $fid {      </content>}
   puts $fid {      <duration>0</duration>}
   puts $fid "      <start>[clock format $p -format {%Y-%m-%dT%H:%M:%S+00:00}]</start>"
   puts $fid {      <icon>moon.png</icon>}
   puts $fid {   </entry>}
}

foreach p $nouvelle {
   puts $fid {   <entry>}
   puts $fid {      <title>Nouvelle Lune</title>}
   puts $fid {      <summary>Date des Nouvelles Lunes</summary>}
   puts $fid {      <updated>2013-04-22T23:19:57+02:00</updated>}
   puts $fid {      <id>http://www.cultibox.fr/id1</id>}
   puts $fid {      <category term = "nouvelle_lune" label = "Nouvelle lune" />}
   puts $fid {      <content type = "text">}
   puts $fid {         C'est la nouvelle lune.}
   puts $fid {      </content>}
   puts $fid {      <duration>0</duration>}
   puts $fid "      <start>[clock format $p -format {%Y-%m-%dT%H:%M:%S+00:00}]</start>"
   puts $fid {      <icon>moon.png</icon>}
   puts $fid {   </entry>}
}

puts $fid {   <entry>
      <title>Noeud lunaire</title>
      <summary>Date des Noeud lunaire</summary>
      <updated>2013-04-16T13:14:57+02:00</updated>
      <id>http://www.cultibox.fr/id1</id>
      <category term = "noeud_lunaire" label = "Noeud lunaire" />
      <content type = "text">
         Noeud lunaire: ne pas jardiner.
      </content>
      <duration>0</duration>
      <start>2013-01-07T06:00:00+02:00</start>
      <period>0000-00-13T17:32:48+00:00</period>
      <icon>moon.png</icon>
   </entry>
   <entry>
      <title> Lune montante </title>
      <summary> Lune montante </summary>
      <updated>2013-04-22T23:19:57+02:00</updated>
      <id>http://www.cultibox.fr/id2</id>
      <category term = "lune_montante" label = "lune montante" />
      <content type = "text">
         C'est la lune montante:
         Semez vos graines
         Récoltez les fleurs
      </content>
      <duration>13</duration>
      <start>2013-04-28T21:07:00+00:00</start>
      <period>0000-00-27T07:43:12</period>
      <icon>moon.png</icon>
      <color>green</color>
   </entry>

   <entry>
      <title> Lune descendante </title>
      <summary> Lune descendante </summary>
      <updated>2013-04-16T13:14:57+02:00</updated>
      <id>http://www.cultibox.fr/id2</id>
      <category term = "lune_descendante" label = "lune descendante" />
      <content type = "text">
         C'est la lune descendante:
         Repiquez vos plantes
         Bouturez vos plantes
         Taillez vos plantes
         Enrichissez le sol
      </content>
      <duration>13</duration>
      <start>2013-03-18T22:50:00+00:00</start>
      <period>0000-00-27T07:43:12</period>
      <icon>moon.png</icon>
      <color>red</color>
   </entry>}

puts $fid {</feed>}
   
   