# Init directory
set rootDir [file dirname [file dirname [info script]]]

# Load lib
lappend auto_path [file join $rootDir lib tcl]
package require piTools
package require piServer
package require piXML


# ###############
#
# server side
#
# ###############
set port [lindex $argv 0]
set logDir [lindex $argv 1]

set logFile "/var/logs/cultipi/cultipi.log"

if {$logDir != "" && [file isdirectory $logDir]} {
    set logFile [file join $logDir cultipi.log]
}

set actualDay ""

# client connection
proc server {channel host port} \
{
    # save client info
    set ::($channel:host) $host
    set ::($channel:port) $port
    # log
    log "<[clock milliseconds]><serveurlog><info><opened - $channel - port $port>"
    set rc [catch \
    {
        # set call back on reading
        fileevent $channel readable [list input $channel]
    } msg]
    if {$rc == 1} \
    {
        # i/o error -> log
        log "<[clock milliseconds]><serveurlog><error><i/o error - $msg>"
    }
}

# client e/s

proc input {channel} \
{
    if {[eof $channel]} \
    {
      # client closed -> log & close
      log "<[clock milliseconds]><serveurlog><info><closed $channel>"
      catch { close $channel }
    } \
    else \
    {
      # receiving
      set rc [catch { set count [gets $channel data] } msg]
      if {$rc == 1} \
      {
        # i/o error -> log & close
        log "<[clock milliseconds]><serveurlog><error><${msg}>"
        catch { close $channel }
        } \
        elseif {$count == -1} \
        {
            # client closed -> log & close
            log "<[clock milliseconds]><serveurlog><info><closed $channel>"
            catch { close $channel }
        } \
        else \
        {
            # got data -> do some thing
            switch [::piTools::lindexRobust $data 0] {
                "stop" {
                    log "<[clock milliseconds]><serveurlog><info><stopping log server>"
                    set ::forever 1
                }
                "pid" {
                    log "<[clock milliseconds]><serveurlog><info><Asked pid>"
                    set serverForResponse [::piTools::lindexRobust $data 1]
                    ::piServer::sendToServer $serverForResponse "pid serverPlugUpdate [pid]"
                }
                default {
                    log $data
                }
            }
        }
    }
}

# log

proc log {msg} {

    set fid [open $::logFile a+]

    # Format the string
    set Splitted ""
    set rc [catch {
        set Splitted [split $msg "<>"]
    } msgErr]
    if {$rc} {
        log "<[clock milliseconds]><serveurlog><info><could not split $msg error : $msgErr>"
    }
    
    # Convert time
    set Time ""
    set rc [catch {
        set Time "[clock format [expr [lindex $Splitted 1] / 1000] -format "%d/%m/%Y %H:%M:%S."][expr [lindex $Splitted 1] % 1000]"
    } msgErr]
    if {$rc} {
        log "<[clock milliseconds]><serveurlog><info><log:: could not compute time error : $msgErr - message : $msg>"
    }
    
    
    #puts $fid "$::($channel:host):$::($channel:port): $msg"
    puts $fid "${Time}\t[lindex $Splitted 3]\t[lindex $Splitted 5]\t[lindex $Splitted 7]"

    # Cas spécial dans le cas ou c'est cultipi qui demande l'arret du serveur log
    if {[lindex $Splitted 3] == "cultipi" && [lindex $Splitted 5] == "debug" && [lindex $Splitted 7] == "stop"} {
        puts $fid "${Time}\tserveurlog\tinfo\tAsk to close serverLog by cultipi"
        set ::forever 1
    }
    
    close $fid
}

proc bgerror {message} {
    log "<[clock milliseconds]><serveurlog><error_critic><bgerror in $::argv - pid [pid] - $message>"
}

# ===================
# start
# ===================

# open socket

catch { console show }
catch { wm protocol . WM_DELETE_WINDOW exit }
#set port 6000 ;# 0 if no known free port
set rc [catch \
{
    set channel [socket -server server $port]
    if {$port == 0} \
    {
        set port [lindex [fconfigure $channel -sockname] end]
        puts "--> server port: $port"
    }
} msg]
if {$rc == 1} \
{
    log "<[clock milliseconds]><serveurlog><info><exiting $msg>"
    exit
}
set (server:host) server
set (server:port) $port

log "<[clock milliseconds]><serveurlog><info><server log started - PID : [pid]>"
log "<[clock milliseconds]><serveurlog><info><server log socket - $port>"

# enter event loop

vwait forever

# tclsh "C:\cultibox\04_CultiPi\01_Software\01_cultiPi\serverLog\serveurLog.tcl" 6000 "C:\cultibox\04_CultiPi"