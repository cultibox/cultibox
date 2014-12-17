namespace eval ::sql {

}

proc ::sql::query {query} {

    set RC [catch {
        exec c:/cultibox/xampp/mysql/bin/mysql.exe --defaults-extra-file=c:/cultibox/xampp/mysql/bin/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox << "$query"
    } msg] 

    set ret [split $msg "\n"]
    set out ""
    set err ""
    foreach line $ret {
        if {[string first "\t" $line] != -1} {
            lappend out $line
        } else {
            puts "erreur : $line"
        }
    }
    
    return $out
}

proc ::sql::updateSensorType {id type} {
    ::sql::query "UPDATE sensors SET type=${type} WHERE id=${id};"
}

proc ::sql::addPlugState {plgNumber state time} {

    if {$time == ""} {
        set time [clock milliseconds]
        ::piLog::log [clock milliseconds] "error" "::sql::addPlugState : time is not defined -$time-"
    }
    ::piLog::log [clock milliseconds] "info" "::sql::addPlugState : time defined -$plgNumber $state $time-"
    set time [expr $time / 1000]

    set formattedTime [clock format $time -format "%y%m%d0%u%H%M%S"]
    set date_catch [clock format $time -format "%Y-%m-%d"]
    set time_catch [clock format $time -format "%H%M%S"]
    
    if {$state == "on"}  {set state 9990}
    if {$state == "off"} {set state 0}
    
    if {$state == "9990" || $state == "0"} {
        ::sql::query "INSERT INTO power (timestamp, record, plug_number, date_catch, time_catch) VALUES ($formattedTime , $state , $plgNumber , \"$date_catch\" , \"$time_catch\" );"
    } else {
        ::piLog::log [clock milliseconds] "error" "::sql::addPlugState : unknow state $state"
    }
}

proc ::sql::AddSensorValue {sensor val1 val2 time} {

    if {$time == ""} {
        set time [clock milliseconds]
        ::piLog::log [clock milliseconds] "error" "::sql::AddSensorValue : time is not defined -$time-"
    }
    ::piLog::log [clock milliseconds] "info" "::sql::AddSensorValue : time defined -$sensor $val1 $val2 $time-"
    set time [expr $time / 1000]

    set formattedTime [clock format $time -format "%y%m%d0%u%H%M%S"]
    set date_catch [clock format $time -format "%Y-%m-%d"]
    set time_catch [clock format $time -format "%H%M%S"]
    
    if {$val1 == "DEFCOM"} {
        set val1 NULL
    } else {
        set val1 [string map {" " "0"} [format %4.f [expr $val1 * 100]]]
    }
    if {$val2 == "DEFCOM"} {
        set val2 NULL
    } else {
        set val2 [string map {" " "0"} [format %4.f [expr $val2 * 100]]]
    }
    
    ::sql::query "INSERT INTO logs (timestamp, record1, record2, date_catch, time_catch, fake_log, sensor_nb) VALUES ($formattedTime , $val1 , $val2 , \"$date_catch\" , \"$time_catch\" , \"False\" , $sensor);"
}
