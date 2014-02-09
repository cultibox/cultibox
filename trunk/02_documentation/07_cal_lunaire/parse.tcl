
proc perigee {fid mois jour} {

puts $fid {
   <entry>
      <title>Périgée</title>
      <summary>Date des Périgées</summary>
      <updated>2013-04-22T23:19:57+02:00</updated>
      <id>http://www.cultibox.fr/id1</id>
      <category term = "perigee" label = "Perigee" />
      <content type = "text">Perigee: ne pas jardiner</content>
      <cbx_symbol>0xB0</cbx_symbol>
      <duration>0</duration>}
      
puts $fid "      <start>2014-${mois}-${jour}T12:00:00+00:00</start>"
      
puts $fid {      <icon>moon.png</icon>
      <color>red</color>
   </entry>
}

}

proc NouvelleLune {fid mois jour} {

puts $fid {
   <entry>
      <title>Nouvelle Lune</title>
      <summary>Date des Nouvelles Lunes</summary>
      <updated>2013-04-22T23:19:57+02:00</updated>
      <id>http://www.cultibox.fr/id1</id>
      <category term = "nouvelle_lune" label = "Nouvelle lune" />
      <content type = "text">C'est la nouvelle lune</content>
      <cbx_symbol>OxAB</cbx_symbol>
      <duration>0</duration>}
      
puts $fid "      <start>2014-${mois}-${jour}T12:00:00+00:00</start>"
      
puts $fid {      <icon>nouvelle_lune.png</icon>
      <color>#666666</color>
   </entry>
}

}

proc PleineLune {fid mois jour} {

puts $fid {
   <entry>
      <title>Pleine Lune</title>
      <summary>Date des Pleines Lunes</summary>
      <updated>2013-04-22T23:19:57+02:00</updated>
      <id>http://www.cultibox.fr/id1</id>
      <category term = "pleine_lune" label = "pleine lune" />
      <content type = "text">C'est la pleine lune</content>
      <cbx_symbol>OxAA</cbx_symbol>
      <duration>0</duration>}
      
puts $fid "      <start>2014-${mois}-${jour}T12:00:00+00:00</start>"
      
puts $fid {      <icon>pleine_lune.png</icon>
      <color>#989898</color>
   </entry>
}
}

proc NoeudLunaire {fid mois jour} {

puts $fid {
   <entry>
      <title>Noeud lunaire</title>
      <summary>Date des Noeud lunaire</summary>
      <updated>2013-04-16T13:14:57+02:00</updated>
      <id>http://www.cultibox.fr/id1</id>
      <category term = "noeud_lunaire" label = "Noeud lunaire" />
      <content type = "text"> Noeud lunaire: ne pas jardiner</content>
      <cbx_symbol>OxAE</cbx_symbol>
      <duration>0</duration>}
      
puts $fid "      <start>2014-${mois}-${jour}T12:00:00+00:00</start>"
      
puts $fid {      <icon>moon.png</icon>
      <color>red</color>
   </entry>
}
}

foreach file [glob -directory [file dirname [info script]] *.txt] {

    set fid [open $file r]
    
    set outFid [open [string map {".txt" ".xml"} $file] w+]
    
    puts $outFid {<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
   <updated>2013-04-16T13:14:57+02:00</updated>
   <id>http://www.cultibox.fr/</id>
   <title>Calendrier lunaire 2014</title>
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
   </rights>
    }
    
    gets $fid UneLigne
    
    while {[eof $fid] != 1} {
        gets $fid UneLigne
        
        if {$UneLigne != ""} {
            lassign $UneLigne Mois	jour NL	PL	Perigee	Noeud
            
            set Mois [string map {" " "0"} [format "%2.f" $Mois]]
            set jour [string map {" " "0"} [format "%2.f" $jour]]
            
            if {$Perigee != 0} {
                perigee $outFid $Mois $jour
            }
            
            if {$NL != 0} {
                NouvelleLune $outFid $Mois $jour
            }
            
            if {$PL != 0} {
                PleineLune $outFid $Mois $jour
            }
            
            
            if {$Noeud != 0} {
                NoeudLunaire $outFid $Mois $jour
            }            
            
        }
    
    }

    puts $outFid {

   
   <entry>
      <title> Lune montante </title>
      <summary> Lune montante </summary>
      <updated>2013-04-22T23:19:57+02:00</updated>
      <id>http://www.cultibox.fr/id2</id>
      <category term = "lune_montante" label = "lune montante" />
      <content type = "text">C'est la lune montante: 
* Semez vos graines
* Récoltez les fleurs</content>
      <duration>13</duration>
      <start>2014-01-27T17:32:00+00:00</start>
      <period>0000-00-27T07:43:12</period>
      <cbx_symbol>OxAC</cbx_symbol>
      <icon>moon.png</icon>
      <color>#529865</color>
   </entry>
   
   <entry>
      <title> Lune descendante </title>
      <summary> Lune descendante </summary>
      <updated>2013-04-16T13:14:57+02:00</updated>
      <id>http://www.cultibox.fr/id2</id>
      <category term = "lune_descendante" label = "lune descendante" />
      <content type = "text">C'est la lune descendante:
* Repiquez vos plantes
* Bouturez vos plantes
* Taillez vos plantes
* Enrichissez le sol</content>
      <duration>14</duration>
      <start>2014-01-13T09:13:00+00:00</start>
      <period>0000-00-27T07:43:12</period>
      <cbx_symbol>OxAD</cbx_symbol>
      <icon>moon.png</icon>
      <color>#B71F3D</color>
   </entry>

</feed>
    }
    
    close $fid
    close $outFid
}

