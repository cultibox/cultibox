
proc perigee {fid mois jour} {

puts $fid {
   <entry>
      <title>Périgée</title>
      <summary>Date des Périgées</summary>
      <updated>2013-04-22T23:19:57+02:00</updated>
      <id>http://www.cultibox.fr/id1</id>
      <category term = "perigee" label = "Perigee" />
      <content type = "text">Perigee: ne pas jardiner</content>
      <duration>0</duration>}
      
puts $fid "      <start>2014-${mois}-${jour}T12:00:00+00:00</start>"
      
puts $fid {
      <icon>moon.png</icon>
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
      <duration>0</duration>}
      
puts $fid "      <start>2014-${mois}-${jour}T12:00:00+00:00</start>"
      
puts $fid {
      <icon>moon.png</icon>
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
      <duration>0</duration>}
      
puts $fid "      <start>2014-${mois}-${jour}T12:00:00+00:00</start>"
      
puts $fid {
      <icon>moon.png</icon>
   </entry>
}


foreach file [glob -directory [file dirname [info script]] *] {

    set fid [open $file r]
    
    set outFid [open [string map {".txt" ".xml"} $file} w+]
    
    gets $fid UneLigne
    
    while {[eof $fid] != 1} {
        gets $fid UneLigne
        
        if {$UneLigne != ""} {
            lassign $UneLigne Mois	jour NL	PL	Perigee	Nœud
            
            if {$Perigee != 0} {
                perigee $outFid $Mois $jour
            }
            
            if {$NL != 0} {
                NouvelleLune $outFid $Mois $jour
            }
            
            if {$PL != 0} {
                PleineLune $outFid $Mois $jour
            }
            
        }
    
    }
    
    
    close $fid
    close $outFid


}