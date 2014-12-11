#!/bin/sh
#\
exec tclsh "$0" ${1+"$@"}

package provide piTools 1.0

namespace eval ::piTools {
    variable debug 0
}

# Load Cultipi server
proc ::piTools::lindexRobust {str index} {

    set start 0
    set end 0
    set previousIndex 0
    set indexF 0

    # recherche des espaces vides
    for {set i 0} {$i <= $index} {incr i} {
        set previousIndex $indexF
        set indexF [string first " " $str [expr $previousIndex + 1]]
    }
    
    if {$indexF == "-1"} {
        set indexF [string length $str]
    }
    if {$previousIndex != 0} {
        set previousIndex [expr $previousIndex + 1]
    }

    return "[string range $str $previousIndex [expr $indexF - 1]]"
}

