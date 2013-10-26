set outPutDir [file join [file dirname [info script] ] out]

set inputFile [file join [file dirname [info script] ] engrais.txt]

proc initFile {fid marque substrat} {
	puts $fid {<?xml version="1.0" encoding="utf-8"?>}
	puts $fid {<feed xmlns="http://www.w3.org/2005/Atom">}
	puts $fid {   <updated>2013-04-16T13:14:57+02:00</updated>}
	puts $fid {   <id>http://www.cultibox.fr/</id>}
	puts $fid {   <title>House Garden Terre Floraison</title>}
	puts $fid {   <subtitle></subtitle>}
	puts $fid {   <author>}
	puts $fid {      <name>Cultibox</name>}
	puts $fid {      <uri>http://www.cultibox.fr/</uri>}
	puts $fid {      <email>info@cultibox.fr</email>}
	puts $fid {   </author>}
	puts $fid {   <category term = "engrais" label = "engrais">}
	puts $fid "    <substrat>${substrat}</substrat>"
	puts $fid {    <periode>floraison</periode>}
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

proc startEntry {fid start name remarque ec} {
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
	
	
	if {$nomProgramme != $oldProgramme || $Substrat != $oldSubstrat} {
		if {$fid != ""} {
			closFile $fid
			close $fid
			set fid ""
		}
		if {$nomProgramme != ""} {
			set fid [open [file join $outPutDir "[string map {" " "_"} ${marque}]_[string map {" " "_"} ${nomProgramme}]_[string map {" " "_"} $Substrat].xml"] w+]
			initFile $fid $marque ${Substrat}
		}
	}
	
	if {$fid != ""} {

		
	
		switch $phase {
			"S1" -
			"Graines" {
				set start 0
				set phase Croissance
				set color green
			}
			"S2" -
			"Plantules" {
				set start 7
				set phase Croissance
				set color green
			}
			"S3" -
			"Boutures" {
				set start 14
				set phase Croissance
				set color green
			}
			"S4" -
			"Veg 18 h" {
				set start 21
				set phase Floraison
				set color orange
			}
			"S5" -
			"Pass 12/12 " {
				set start 28
				set phase Floraison
				set color orange
			}
			"S6" -
			"Strech" {
				set start 35
				set phase Floraison
				set color orange
			}		
			"S7" -
			"1ers pistils" {
				set start 42
				set phase Floraison
				set color orange
			}		
            "S8" -			
			"Debut Flo" {
				set start 49
				set phase Floraison
				set color orange
			}
			"S9" -
			"Floraison" {
				set start 56
				set phase Floraison
				set color orange
			}
			"S10" -
			"Boom floral" {
				set start 63
				set phase Floraison
				set color orange
			}
			"S11" -
			"Fin floraison" {
				set start 70
				set phase Floraison
				set color orange
			}
            "S12" -			
			"Noir" {
				set start 73
				set phase Rincage
				set color black
			}			
		}

		startEntry $fid $start $phase $remarque $ec
		
		set idx 6
		foreach Dosage [lrange $UneLigne 6 end] {
			
			if {$Dosage != 0} {
				set unit "ml/l"
				if {[lindex $EngraisName $idx] == "Mineral Magic"} {
					set unit "g/l"
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

if {$fid != ""} {
	closFile $fid
	close $fid
	set fid ""
}

close $fidIn

