#!/bin/sh
#\
exec tclsh "$0" ${1+"$@"}

package provide piLog 1.0

namespace eval ::piLog {
    variable port ""
    variable channel ""
    variable module ""
    variable traceLevel "debug"
    variable outputType "file"
}

proc ::piLog::openLog {portNumber moduleName {level debug}} {
    variable port
    variable channel
    variable module
    variable traceLevel
    
    # On initialise avec les parametres d'entrée de la fonction
    set port $portNumber
    set host localhost
    set module $moduleName
    set traceLevel $level
    
    # Ouverture du socket
    set rc [catch { set channel [socket $host $port] } msg]
    
    # S'il y a une erreur lors de l'ouverture du socket
    if {$rc == 1} {
        puts $msg
        return $msg
    }
    
    return 0
}

# Cette proc permet de gérer les logs sous la forme d'un puts à la con
# Si type est puts : affiche à l'écran
# si type est none : pas de sortie
proc ::piLog::openLogAs {type} {
    variable outputType

    set outputType $type

    return 0
}

proc ::piLog::closeLog {} {
    variable channel
    close $channel

    return 0
}

proc ::piLog::log {time traceType trace} {
    variable module
    variable channel
    variable traceLevel
    variable outputType

    if {$outputType == "none"} {
        return 0
    }
    
    if {$outputType == "puts"} {
        puts "$time $traceType $trace"
        return 0
    }
    
    set StringToWrite "<${time}><${module}><${traceType}><${trace}>"
    
    # En fonction du niveau de trace demandé, on envoi ou pas
    set toSend 0
    switch $traceLevel {
        "debug" {
            set toSend 1
        }
        "info" {
            if {$traceType == "info" || $traceType == "warning"  || $traceType == "error" || $traceType == "error_critic"} {
                set toSend 1
            }
        }
        "warning" {
            if {$traceType == "warning"  || $traceType == "error" || $traceType == "error_critic"} {
                set toSend 1
            }
        }
        "error" {
            if {$traceType == "error" || $traceType == "error_critic"} {
                set toSend 1
            }
        }
        "error_critic" {
            if {$traceType == "error_critic"} {
                set toSend 1
            }
        }
        default {
            set StringToWrite "<${time}><${module}><error><trace level not good : ${trace}>"
            set toSend 1
        }
    }

    if {$toSend == 1} {
        if {$channel == ""} {
            # If channel is not open, log in local
            if { [expr [string compare "$::tcl_platform(platform)" "windows" ] == 0] } {
                set env_cpi  [file join [file dirname [file dirname [info script]]]]
            } else {
                set env_cpi  [file join $::env(HOME) cultipi]
            }
            
            set env_home [file join $env_cpi ${module}]
            
            # On crée le dossier s'il n'existe pas
            if {[file isdirectory $env_cpi] == 0} {
                file mkdir $env_cpi
            }
            if {[file isdirectory $env_home] == 0} {
                file mkdir $env_home
            }
            
            set fid [open [file join $env_home log_local.txt ] a+]
            puts $fid $StringToWrite
            close $fid
        } else {
            set rc [catch \
            {
                puts $channel $StringToWrite
                flush $channel
            } msg]
            if {$rc == 1} { return $msg }
        }
    }

    

    return 0
}

# lappend auto_path {D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\module\lib\tcl}
# package require piLog
# ::piLog::openLog 6000 "moduleTest"
# ::piLog::log [clock milliseconds] "Trace type" "ma trace"
# ::piLog::closeLog 