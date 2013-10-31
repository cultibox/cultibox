set outPutDir [file join [file dirname [info script] ] out]
set outPutDoc [file join [file dirname [info script] ] gui_soft_cal_engrais.wiki]

set inputFile [file join [file dirname [info script] ] engrais.txt]
set inputFileRef [file join [file dirname [info script] ] engrais_ref.txt]


proc initFile {fid marque substrat programme} {
	puts $fid {<?xml version="1.0" encoding="utf-8"?>}
	puts $fid {<feed xmlns="http://www.w3.org/2005/Atom">}
	puts $fid "   <updated>[clock format [clock seconds] -format {%Y-%m-%dT%H:%M:%S+02:00}]/updated>"
	puts $fid {   <id>http://www.cultibox.fr/</id>}
	puts $fid "   <title>${marque} ${programme} ${substrat}</title>"
	puts $fid {   <subtitle></subtitle>}
	puts $fid {   <author>}
	puts $fid {      <name>Cultibox</name>}
	puts $fid {      <uri>http://www.cultibox.fr/</uri>}
	puts $fid {      <email>info@cultibox.fr</email>}
	puts $fid {   </author>}
	puts $fid {   <category term = "engrais" label = "engrais">}
	puts $fid "    <substrat>${substrat}</substrat>"
	puts $fid "    <periode>${programme}</periode>"
	puts $fid "    <marque>${marque}</marque>"
	puts $fid {   </category>}
	puts $fid "   <link rel=\"self\" href=\"www.cultibox.fr/${marque}_${substrat}.xml\" />"
	puts $fid {   <icon></icon>}
	puts $fid {   <logo></logo>}
	puts $fid {   <rights type = "text">}
	puts $fid {    © Cultibox, 2013}
	puts $fid {   </rights>}
}

proc closFile {fid} {
	puts $fid {</feed>}
}

proc addNutriment {fid name dose} {
	puts $fid {      <nutriment>}
	puts $fid "        <name>${name}</name>"
	puts $fid "        <dosage>${dose}</dosage>"
	puts $fid {      </nutriment>}
}

proc startEntry {fid start name remarque ec facultative} {
	puts $fid {    <entry>}
	puts $fid "      <title>S[expr ${start} / 7 + 1] ${name}</title>"
	puts $fid "      <summary>S[expr ${start} / 7 + 1] ${name}</summary>"
	puts $fid {      <updated>2008-05-29T13:14:57+02:00</updated>}
	puts $fid {      <id>http://www.cultibox.fr/id1</id>}
	puts $fid {      <category term = "vegetation" label = "Vegetation" />}
	puts $fid "      <content type = \"text\">${remarque}</content>"
	puts $fid "      <ec>${ec}</ec>"
	puts $fid {      <duration>7</duration>}
	puts $fid "      <start>${start}</start>"
	puts $fid "      <facultative>${facultative}<falcutative>"
}

proc closeEntry {fid marque color} {
	puts $fid "      <color>${color}</color>"
	puts $fid "      <icon>[string map {" " "_"} ${marque}].png</icon>"
	puts $fid {   </entry>}
}

set fidIn [open $inputFile r]

set oldProgramme ""
set oldSubstrat ""

set fid ""
gets $fidIn UneLigne
set EngraisMarque [split $UneLigne "\t"]

gets $fidIn UneLigne
set EngraisName [split $UneLigne "\t"]


while {[eof $fidIn] != 1} {
	gets $fidIn UneLigne
	set UneLigne [split $UneLigne "\t"]
	
	set marque [lindex $UneLigne 0]
	set nomProgramme [lindex $UneLigne 1]
	set Substrat  [lindex $UneLigne 2]
	set phase [lindex $UneLigne 3]
	set remarque [lindex $UneLigne 4]
	set ec [lindex $UneLigne 5]
    set utilise [lindex $UneLigne 6]
	set facultative [lindex $UneLigne 7]
	
    if {$utilise == 1} {

        if {$nomProgramme != $oldProgramme || $Substrat != $oldSubstrat} {
            if {$fid != ""} {
                closFile $fid
                close $fid
                set fid ""
            }
            if {$nomProgramme != ""} {
                set fid [open [file join $outPutDir "[string map {" " "_"} ${marque}]_[string map {" " "_"} ${nomProgramme}]_[string map {" " "_"} $Substrat].xml"] w+]
                initFile $fid $marque ${Substrat} $nomProgramme
            }
        }
        
        if {$fid != ""} {

            
        
            switch $phase {
                "S1" -
                "Graines" {
                    set start 0
                    set phase Croissance
                    set color greenyellow
                }
                "S2" -
                "Plantules" {
                    set start 7
                    set phase Croissance
                    set color yellowgreen
                }
                "S3" -
                "Boutures" {
                    set start 14
                    set phase Croissance
                    set color darkgreen
                }
                "S4" -
                "Veg 18 h" {
                    set start 21
                    set phase Floraison
                    set color khaki
                }
                "S5" -
                "Pass 12/12 " {
                    set start 28
                    set phase Floraison
                    set color wheat
                }
                "S6" -
                "Strech" {
                    set start 35
                    set phase Floraison
                    set color tan
                }		
                "S7" -
                "1ers pistils" {
                    set start 42
                    set phase Floraison
                    set color goldenrod
                }		
                "S8" -			
                "Debut Flo" {
                    set start 49
                    set phase Floraison
                    set color peru
                }
                "S9" -
                "Floraison" {
                    set start 56
                    set phase Floraison
                    set color chocolate
                }
                "S10" -
                "Boom floral" {
                    set start 63
                    set phase Floraison
                    set color brown
                }
                "S11" -
                "Fin floraison" {
                    set start 70
                    set phase Floraison
                    set color maroon
                }
                "S12" -			
                "Noir" {
                    set start 77
                    set phase Rincage
					set remarque Rincage
                    set color black
                }			
            }

            startEntry $fid $start $phase $remarque $ec $facultative
            
            set idx 8
            foreach Dosage [lrange $UneLigne 8 end] {
                
                if {$Dosage != 0} {
                    set unit "ml/l"
					switch [lindex $EngraisName $idx] {
						"Mineral Magic" -
						"Piranha" -
						"Carboload" -
						"Bud Blood" -
						"Big Bud" {
							set unit "g/l"
						}
						"SuperVit" {
							set unit "goutte/4,5l"
						}
					}
                    addNutriment $fid [lindex $EngraisName $idx] "${Dosage} ${unit}"
                }
                incr idx
            }
            closeEntry $fid $marque $color
        }
        
        
        set oldProgramme $nomProgramme
        set oldSubstrat $Substrat
    
    }
	
	
}

if {$fid != ""} {
	closFile $fid
	close $fid
	set fid ""
}

close $fidIn


# Génération de la doc

# Chargement du fichier de référence sur les engrais
set fidIn [open $inputFileRef r]
gets $fidIn UneLigne
while {[eof $fidIn] != 1} {
	gets $fidIn UneLigne
    set UneLigne [split $UneLigne "\t"]
    set marque [lindex $UneLigne 0]
    set utilise [lindex $UneLigne 1]
	set source [lindex $UneLigne 2]
	set engref(${marque},source) $source
}
close $fidIn


set fid [open $outPutDoc w+]
set fidIn [open $inputFile r]

gets $fidIn UneLigne
set EngraisMarque [split $UneLigne "\t"]

gets $fidIn UneLigne
set EngraisName [split $UneLigne "\t"]

puts $fid {#summary Calendrier des engrais

= Sommaire =
<wiki:toc max_depth="3" />

= Introduction =

Le logiciel Cultibox vous permet de planifier les engrais que vous souhaitez appliquer.

Ci-dessous vous trouverez tout les schéma de culture utilisé:

}

set oldProgramme ""
set oldSubstrat ""
set oldmarque ""
set semaine 1
    
while {[eof $fidIn] != 1} {
	gets $fidIn UneLigne
    set UneLigne [split $UneLigne "\t"]
    set marque [lindex $UneLigne 0]
    set nomProgramme [lindex $UneLigne 1]
    set Substrat  [lindex $UneLigne 2]
    set phase [lindex $UneLigne 3]
    set remarque [lindex $UneLigne 4]
    set ec [lindex $UneLigne 5]
    set utilise [lindex $UneLigne 6]
        
    
        
    if {$utilise == 1 || [eof $fidIn]} {
    
        # Si ça change on écrit le vecteur
        if {$Substrat != $oldSubstrat || $marque != $oldmarque || $nomProgramme != $oldProgramme } {
            if {[array exists eng]} {
                set change 0
                
                set listEngrais ""
                set listSemaine ""
                foreach name [array names eng] {
                    if {[lindex [split $name ","] 1] != "remarque" && [lindex [split $name ","] 1] != "ec"} { 
                    lappend listSemaine [lindex [split $name ","] 0]
                    lappend listEngrais [lindex [split $name ","] 1]
                    }
                }
                set listEngrais [lsort -unique $listEngrais]
                set listSemaine [lsort -integer -unique $listSemaine]
                
                puts -nonewline $fid "|| *Semaine* || *Remarque* || *EC* "
                foreach Engrais $listEngrais {
                    puts -nonewline $fid "|| *${Engrais}* "
                }
                puts -nonewline $fid "||"
                puts $fid ""
                    
                foreach Semaine $listSemaine {
                    puts -nonewline $fid "|| $Semaine || $eng(${Semaine},remarque) || $eng(${Semaine},ec)"
                    foreach Engrais $listEngrais {
                        if {[array names eng ${Semaine},${Engrais}] != ""} {
                            puts -nonewline $fid "|| $eng(${Semaine},${Engrais})"
                        } else {
                            puts -nonewline $fid "||  "
                        }
                    }
                    puts -nonewline $fid "||"
                    puts $fid ""
                }
				puts $fid ""
                array unset eng
                

            }
        }
    
        if {$marque != $oldmarque && $marque != ""} {
            puts $fid "= ${marque} ="
            puts $fid ""
			
			if {[array names engref ${marque},source] != ""} {
				puts $fid "Source : \[$engref(${marque},source) Schéma de culture\]"
				puts $fid ""
			}
			
        }
        
        if {$nomProgramme != $oldProgramme && $nomProgramme != ""} {
            puts $fid "== ${nomProgramme} =="
            puts $fid ""
			set oldSubstrat ""
        }
        
        if {$Substrat != $oldSubstrat && $Substrat != ""} {

            set semaine 1

            puts $fid "=== ${Substrat} ==="
            puts $fid ""
            
        }
        

        set idx 7
        set eng(${semaine},ec) $ec
        set eng(${semaine},remarque) $remarque
        foreach Dosage [lrange $UneLigne 7 end] {
            
            if {$Dosage != 0} {
                set unit "ml/l"
                if {[lindex $EngraisName $idx] == "Mineral Magic" || [lindex $EngraisName $idx] == "Piranha"} {
                    set unit "g/l"
                }
				if {[lindex $EngraisName $idx] == "SuperVit"} {
					set unit "goutte/4,5l"
				}
                set eng(${semaine},[lindex $EngraisName $idx]) "${Dosage} ${unit}"
            }
            incr idx
        }
        incr semaine
        
                set oldProgramme $nomProgramme
                set oldSubstrat $Substrat
                set oldmarque $marque
    }


}

close $fid
close $fidIn


