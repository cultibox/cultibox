# Cette feuille contient toutes les procédures pour le calendrier lunaire


# Commande pour ajouter un jour périgée dans le calendrier
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