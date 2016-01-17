# Chargement des librairies pour le calendrier lunaire
source [file join [file dirname [info script]] parse_lib_cal_lun.tcl]
# Chargement des librairies pour le calendrier des cultures
source [file join [file dirname [info script]] parse_lib_cal_cult.tcl]

#==============================================================================
# \brief Commande pour ajouter un jour dans le calendrier
# \param fid pointeur sur le fichier de sortie
# \param mois Numéro du mois
# \param jour Numéro du jour
#==============================================================================
proc addCalEvent {fid mois jour label symbol icons} {
puts $fid {
   <entry>
      <title>Lune</title>
      <summary>Lune</summary>
      <updated>2013-04-22T23:19:57+02:00</updated>
      <id>http://www.cultibox.fr/id1</id>
      <category term = "fruit" label = "fruit" />}
puts $fid "<content type = \"text\">[join $label "\n"]</content>"
puts $fid "<cbx_symbol>[join $symbol " "]</cbx_symbol>"
puts $fid "<duration>0</duration>"
puts $fid "      <start>2016-${mois}-${jour}T00:00:00</start>"
    set i 0
    foreach icon $icons {
        puts $fid "      <icon${i}>${icon}</icon${i}>"
        incr i
    }

puts $fid {      <color>rgba(170, 170, 170, 0.1)</color>
      <text_color>#000</text_color>
   </entry>
}
}

# Début du code principal
# Pour chacun des fichiers présents dans le répertoire avec l'extension .txt
foreach file [glob -directory [file dirname [info script]] *.txt] {

    # On ouvre le fichier model
    set fid [open $file r]
    
    # En fonction du nom du fichier model, on attribue le type
    set calendrier_type ""
    if {[string first "calendrier_culture" $file] != -1} {
        set calendrier_type "culture"
    }
    if {[string first "calendrier_lunaire" $file] != -1} {
        set calendrier_type "general"
    }
    
    # On cré le fichier de sortie
    set outFid [open [file join [file dirname [info script]] out permanent [string map {".txt" ".xml"} [file tail $file]]] w+]
    
    # On ajoute le header dans le fichier de sortie
    puts $outFid {<?xml version="1.0" encoding="utf-8"?>}
    puts $outFid {<feed xmlns="http://www.w3.org/2005/Atom">}
    puts $outFid "   <updated>[clock format [clock seconds] -format {%Y-%m-%dT%H:%M:%S+02:00}]</updated>"
    puts $outFid {   <id>http://www.cultibox.fr/</id>}
    puts $outFid "   <title>[string map {"_" " "} [file rootname [file tail $file]]]</title>"
    puts $outFid {   <subtitle></subtitle>}
    puts $outFid {   <author>}
    puts $outFid {      <name>Cultibox</name>}
    puts $outFid {      <uri>http://www.cultibox.fr/</uri>}
    puts $outFid {      <email>info@cultibox.fr</email>}
    puts $outFid {   </author>}
    puts $outFid {   <category term = "lunaire" label = "lunaire" />}
    puts $outFid {   <link rel="self" href="www.cultibox.fr/lunaire.xml" />}
    puts $outFid {   <icon></icon>}
    puts $outFid {   <logo></logo>}
    puts $outFid {   <rights type = "text">}
    puts $outFid "    © Cultibox, [clock format [clock seconds] -format {%Y}]"
    puts $outFid {   </rights>}
    
    # On lit la première ligne du fichier model (entete des colonnes)
    gets $fid UneLigne
    
    # On parse tout le fichier
    while {[eof $fid] != 1} {
    
        # On lit une nouvelle ligne
        gets $fid UneLigne
        
        # Si la ligne n'est pas vide
        if {$UneLigne != ""} {
        
            # Les deux premiers éléments sont le mois et le jour
            lassign $UneLigne mois jour autre
            
            set Mois [string map {" " "0"} [format "%2.f" $mois]]
            set Jour [string map {" " "0"} [format "%2.f" $jour]]
                    
            # En fonction du type de calendrier
            switch $calendrier_type {
                "general" {
                
                    # On assigne aux variables les différents éléments
                    lassign $UneLigne na1 na2 fruit racine fleur feuille NL PL Perigee Noeud Montante Descendante

                    set icons ""
                    set label ""
                    set symbol ""
                    
                    if {$Montante != 0} {
                        lappend icons montante.png
                        lappend label "Lune montante : Semez et récoltez"
                        lappend symbol "0xAC"
                    }
                    
                    if {$Descendante != 0} {
                        lappend icons descendante.png
                        lappend label "Lune descendante : Repiquez, bouturez, taillez et nourrissez"
                        lappend symbol "0xAD"
                    }
                    
                    if {$Perigee != 0} {
                        lappend icons perigee.png
                        lappend label "Perigee : ne jardinez pas"
                        lappend symbol "0xB0"
                    }
                    
                    if {$NL != 0} {
                        lappend icons nouvelle_lune.png
                        lappend label "Nouvelle lune"
                        lappend symbol "0xAB"
                    }
                    
                    if {$PL != 0} {
                        lappend icons pleine_lune.png
                        lappend label "Pleine lune"
                        lappend symbol "0xAA"
                    }

                    if {$Noeud != 0} {
                        lappend icons perigee.png
                        lappend label "Noeud lunaire : ne jardinez pas"
                        lappend symbol "0xAE"
                    }     

                    if {$fruit != 0} {
                        lappend icons fruit.png
                        lappend label "Jour fruit"
                        #lappend symbol "0xB1"
                    }
                    
                    if {$racine != 0} {
                        lappend icons carotte.png
                        lappend label "Jour racine"
                        #lappend symbol "0xB2"
                    }
                    
                    if {$fleur != 0} {
                        lappend icons fleur.png
                        lappend label "Jour fleur"
                        #lappend symbol "0xB3"
                    }

                    if {$feuille != 0} {
                        lappend icons feuille.png
                        lappend label "Jour feuille"
                        #lappend symbol "0xB4"
                    }
                    
                    if {$icons != ""} {
                        addCalEvent $outFid $Mois $Jour $label $symbol $icons
                    }
                }
                "lunaire" {

                    # On assigne aux variables les différents éléments
                    lassign $UneLigne na1 na2 NL	PL	Perigee	Noeud

                    if {$Perigee != 0} {perigee $outFid $Mois $Jour}
                    
                    if {$NL != 0} {NouvelleLune $outFid $Mois $Jour}
                    
                    if {$PL != 0} {PleineLune $outFid $Mois $Jour}

                    if {$Noeud != 0} {NoeudLunaire $outFid $Mois $Jour}            

                }
                "culture" {
                    # On assigne aux variables les différents éléments
                    lassign $UneLigne na1 na2 fruit racine fleur feuille

                    if {$fruit != 0} {addFruit $outFid $Mois $Jour}
                    
                    if {$racine != 0} {addRacine $outFid $Mois $Jour}
                    
                    if {$fleur != 0} {addFleur $outFid $Mois $Jour}

                    if {$feuille != 0} {addFeuille $outFid $Mois $Jour}            

                }
            }
            
        }
    
    }

    if {$calendrier_type == "lunaire"} {
    
    # On écrit les lunes montantes et descendantes
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
}
}

    # On ferme l'écriture du fichier de sortie
    puts $outFid {</feed>}
    
    close $fid
    close $outFid
}

