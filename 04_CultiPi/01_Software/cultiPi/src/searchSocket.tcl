
# Cette procédure cherche un socket disponible
proc findAvailableSocket {start} {

    for {set index 0} {$index < 1000} {incr index} {
    
        set RC [catch {
            set sock [socket -server [list testForfindAvailableSocket [clock seconds]] [expr $start + $index]]
        } msg]
        
        if {$RC != 1} {
            close $sock
            return [expr $start + $index]
        }
        
    }

    
    return "No socket found"
}

proc testForfindAvailableSocket {test} {

}