#!/bin/sh
#\
exec tclsh "$0" ${1+"$@"}

package provide piTime 1.0

namespace eval ::piTime {

}

proc ::piTime::readSecondsOfTheDay {} {

    set time [clock seconds] 

    set sec [string trimleft [clock format $time -format %S] "0"]
    if {$sec == ""} {set sec 0}
    set min [string trimleft [clock format $time -format %M] "0"]
    if {$min == ""} {set min 0}
    set hour [string trimleft [clock format $time -format %H] "0"]
    if {$hour == ""} {set hour 0}

    return [expr $sec + $min * 60 + $hour * 3600]
}

proc ::piTime::readDay {} {

    return [clock format [clock seconds] -format %d]
}

proc ::piTime::readMonth {} {

    return [clock format [clock seconds] -format %m]
}
