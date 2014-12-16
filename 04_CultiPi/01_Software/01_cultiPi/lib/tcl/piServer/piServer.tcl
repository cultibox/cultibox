#!/bin/sh
#\
exec tclsh "$0" ${1+"$@"}

package provide piServer 1.0

namespace eval ::piServer {
    variable callBackMessage ""
    variable debug 0
}

# Load Cultipi server
proc ::piServer::server {channel host port} \
{
    variable debug

    # save client info
    set ::($channel:host) $host
    set ::($channel:port) $port
    # log
    if {$debug == 1} {
        ::piLog::log [clock milliseconds] "debug" "Ouverture connexion par $host - socket $channel"
    }
    set rc [catch \
    {
        # set call back on reading
        fileevent $channel readable [list ::piServer::input $channel]
    } msg]
    if {$rc == 1} \
    {
        # i/o error -> log
        ::piLog::log [clock milliseconds] "erreur" "i/o error - $msg"
    }
}

proc ::piServer::input {channel} {
    variable callBackMessage
    variable debug

    if {[eof $channel]} \
    {
        # client closed -> log & close
        if {$debug == 1} {
            ::piLog::log [clock milliseconds] "debug" "closed $channel"
        }
        
        catch { close $channel }
    } \
    else \
    {
        # receiving
        set rc [catch { set count [gets $channel data] } msg]
        if {$rc == 1} \
        {
            # i/o error -> log & close
            ::piLog::log [clock milliseconds] "erreur" "${msg}"
            catch { close $channel }
        } \
        elseif {$count == -1} \
        {
            # client closed -> log & close
            if {$debug == 1} { 
                ::piLog::log [clock milliseconds] "debug" "closed $channel"
            }
            catch { close $channel }
        } \
        else \
        {
            if {$debug == 1} { 
                ::piLog::log [clock milliseconds] "debug" "message received -${data}- send by $channel"
            }
            # got data -> do some thing
            ::${callBackMessage} ${data}
        }
    }
}

proc ::piServer::start {callBackMessageIn portIn} {
    variable callBackMessage
    variable debug

    set callBackMessage $callBackMessageIn

    set rc [catch \
    {
        set channel [socket -server ::piServer::server $portIn]
        if {$portIn == 0} \
        {
            set portIn [lindex [fconfigure $channel -sockname] end]
            ::piLog::log [clock milliseconds] "debug" "--> server port: $portIn"
        }
    } msg]
    if {$rc == 1} \
    {
        ::piLog::log [clock milliseconds] "erreur" "erreur exiting $msg"
        exit
    }
}

proc ::piServer::sendToServer {portNumber message} {
    variable debug

    set channel ""

    set rc [catch { set channel [socket localhost $portNumber] } msg]
    if {$rc == 1} {
        ::piLog::log [clock milliseconds] "erreur" "try to open socket to -$portNumber- - erreur :  -$msg-"
    }

    set rc [catch \
    {
        puts $channel "$message"
        flush $channel
    } msg]
    if {$rc == 1} {
        ::piLog::log [clock milliseconds] "erreur" "try to send message to -$portNumber- - erreur :  -$msg-"
    } else {
        if {$debug == 1} { 
            ::piLog::log [clock milliseconds] "debug" "message send to -$portNumber- message : -$message-"
        }
    }

    set rc [catch \
    {
        close $channel
    } msg]
    if {$rc == 1} \
    {
        ::piLog::log [clock milliseconds] "erreur" "erreur closing channel -$channel-"
    }
    
    
}
