#!/bin/sh
#\
exec tclsh "$0" ${1+"$@"}

package provide piXML 1.0

namespace eval ::piXML {
    variable debug 0
}

# Ouverture d'un fichier XML
proc ::piXML::open_xml {fichier {doctype ""}} {
    variable debug
    
    if {![file readable $fichier]} {return}
    set res ""
    set fin [open $fichier r]
    # On contrôle que c'est un xml valide :
    set ctrl [gets $fin]
    if {[lindex $ctrl 0] eq "<?xml" && [lindex $ctrl end] eq "?>"} {
        foreach paire [split [string range $ctrl 6 end-3] " "] {
            foreach "id val" [split $paire "="] {
                set $id [string tolower [lindex $val 0]]
            }
        }
        if {$version ne "1.0" || $standalone ne "yes" || ![info exists encoding]} {
            # Pas un XML valide :
            if {$debug == 1} {puts "Pas un XML valide"}
            return ""
        }
    }
    # C'est tout bon, on peut parser le reste :
    set data [encoding convertfrom $encoding [read $fin]]
    close $fin
    return [::piXML::xml2list $data]
}

# Conversion de données XML en liste tcl :
proc ::piXML::xml2list {xml} {
    variable debug

    regsub -all {>\s*<} [string trim $xml " \n\t<>"] "\} \{" xml
    set xml [string map {> "\} \{\x0E " < "\} \{" "?xml" \x0F}  $xml]
    set res ""
    set stack {}
    set rest {}
    foreach item "{$xml}" {
        switch -regexp -- $item {
            ^\x0F {
                # Déclaration XML :
                continue
            }
            ^!DOCTYPE {
                # Déclaration type de document :
                continue
            }
            ^!-- {
                # Commentaire :
                continue
            }
            ^\x0E {
                append res [string range $item 2 end]
            }
            ^/ {
                regexp {/(.+)} $item -> tagname
                set expected [lindex $stack end]
                if {$tagname!=$expected} {error "$item != $expected"}
                set stack [lrange $stack 0 end-1]
                append res "\}\} "
            }
            /$ {
                regexp {([^ ]+)( (.+))?/$} $item -> tagname - rest
                set rest [lrange [string map {= " "} $rest] 0 end]
                append res "{$tagname [list $rest] {}} "
            }
            default {
                set tagname [lindex [string map {{ "} \" {" } \"} $item] 0]
                set rest [lrange [string map {= " "} $item] 1 end]
                lappend stack $tagname
                append res "\{$tagname [list $rest] \{"
            }
        }
        if {[llength $rest]%2} {
            # Le XML n'est pas valide : clé sans valeur
            if {$debug == 1} {puts "Le XML n'est pas valide : clé sans valeur"}
            return ""
        }
    }
    if {[llength $stack]} {
        # Le XML n'est pas valide : il reste des tags non fermés
        if {$debug == 1} {puts "Le XML n'est pas valide : il reste des tags non fermés"}
        return ""
    }

    return [string map {"\} \}" "\}\}"} [lindex $res 0]]
}

# Proc used to search an element with is name
proc ::piXML::searchItemByName {name xmlList} {
    foreach elem $xmlList {
        set arg [lindex $elem 1]
        if {[lsearch $arg "name"] != -1} {
            if {[lindex $arg [expr [lsearch $arg "name"] + 1]] == $name} {
                return $elem
            }
        }    
    }
}

proc ::piXML::searchOptionInElement {optionName element} {
    set options [lindex $element 1]
    if {[lsearch $options $optionName] != -1} {
        return [lindex $options [expr [lsearch $options $optionName] + 1]]
    }
    return ""
}

# Cette procédure converti un fichier XML en array
proc ::piXML::convertXMLToArray {XMLFile} {

    if {[file exists $XMLFile] != 1} {
        error "::piXML::convertXMLToArray $XMLFile doesnot exists"
        return 0
    }
    
    set XMLarray(filename) $XMLFile
    
    set XMLList [::piXML::open_xml $XMLFile]
    
    foreach item [lindex $XMLList 2] {
    
        set parameters [lindex $item 1]
        
        set XMLarray([lindex $parameters 1]) [lindex $parameters 3]
    
    }
    
    return [array get XMLarray]
    
}
