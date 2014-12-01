# ###############
#
# server side
#
# ###############
set port [lindex $argv 0]
set logDir [lindex $argv 1]

# client connection

proc server {channel host port} \
{
    # save client info
    set ::($channel:host) $host
    set ::($channel:port) $port
    # log
    log "<[clock milliseconds]><serveurlog><info><opened - $channel>"
    set rc [catch \
    {
        # set call back on reading
        fileevent $channel readable [list input $channel]
    } msg]
    if {$rc == 1} \
    {
        # i/o error -> log
        log "<[clock milliseconds]><serveurlog><erreur><i/o error - $msg>"
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
        log "<[clock milliseconds]><serveurlog><erreur><${msg}>"
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
        if {[lindex $data 1] == "stop"} {
            log "<[clock milliseconds]><serveurlog><info><stopping log server>"
            set ::forever 1
        } else {
            log $data
        }
      }
    }
}

# log

proc log {msg {init 0}} {
    if {$init != 0} {
        set fid [open [file join $::logDir "log[clock format [clock seconds] -format %d].txt"] w+]
    } else {
        set fid [open [file join $::logDir "log[clock format [clock seconds] -format %d].txt"] a+]
    }

    # Format the string
    set Splitted [split $msg "<>"]
    # Convert time
    set Time ""
    set rc [catch {
        set Time "[clock format [expr [lindex $Splitted 1] / 1000] -format "%d/%m/%Y %H:%M:%S."][expr [lindex $Splitted 1] % 1000]"
    }]
    #puts $fid "$::($channel:host):$::($channel:port): $msg"
    if {$rc != 1} {
        puts $fid "${Time}\t[lindex $Splitted 3]\t[lindex $Splitted 5]\t[lindex $Splitted 7]"
    } else {
        puts $fid "Server log : Folowing message could not be resolved -${msg}-"
    }
    close $fid
}

proc bgerror {message} {
    log "<[clock milliseconds]><serveurlog><erreur_critique><bgerror in $::argv '$message'>"
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

log "<[clock milliseconds]><serveurlog><info><server log started>" init

# enter event loop

vwait forever

# tclsh "D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\module\serverLog\serveurLog.tcl" 6000 "D:\DONNEES\GR08565N\Mes documents\cbx\culti_pi\log.txt"