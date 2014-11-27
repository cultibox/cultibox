#!/bin/sh
#\
exec tclsh "$0" ${1+"$@"}

package provide piLog 1.0

namespace eval ::piLog {
    variable port ""
    variable channel ""
    variable module ""
}

proc ::piLog::openLog {portNumber moduleName} {
    variable port
    variable channel
    variable module
    set port $portNumber
    set host localhost
    set module $moduleName
    
    # open socket
    set rc [catch { set channel [socket $host $port] } msg]
    
    if {$rc == 1} {
        puts $msg
        return $msg
    }
    
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
    
    set StringToWrite "<${time}><${module}><${traceType}><${trace}>"
    
    # If channel is not open, log in local
    if {$channel == ""} {
        set fid [open [file join [file dirname [info script]] log_local.txt ] a+]
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
    

    return 0
}

# lappend auto_path {D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\module\lib\tcl}
# package require piLog
# ::piLog::openLog 6000 "moduleTest"
# ::piLog::log [clock milliseconds] "Trace type" "ma trace"
# ::piLog::closeLog 