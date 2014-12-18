
set ::simulator(valueToSend) "0"

proc exec {args} {

    set commande [lindex $args 0]

    switch $commande {
        "i2cset" {

            set module  [lindex $args 3]
            set reg     [lindex $args 4]
            
            if {[expr $module *1.0] > 4} {
                error "Not available in simulator (modul $module [expr $module *1.0])"
            }
            
            if {[array names ::simulator -exact $module] == ""} {
                set ::simulator($module) ""
                set ::simulator($module,val1) [format %.2f [expr rand() * 100]]
                set ::simulator($module,val1,sens) "plus"
                set ::simulator($module,val2) [format %.2f [expr rand() * 100]]
                set ::simulator($module,val2,sens) "plus"
            } else {
            
                if {$::simulator($module,val1) >= 100} {
                    set ::simulator($module,val1,sens) "moins"
                }
                if {$::simulator($module,val1) < -10} {
                    set ::simulator($module,val1,sens) "plus"
                }
                if {$::simulator($module,val1,sens) == "plus"} {
                    set ::simulator($module,val1) [format %.2f [expr $::simulator($module,val1) + 0.01]]
                }
                if {$::simulator($module,val1,sens) == "moins"} {
                    set ::simulator($module,val1) [format %.2f [expr $::simulator($module,val1) - 0.01]]
                }
                
                if {$::simulator($module,val2) >= 100} {
                    set ::simulator($module,val2,sens) "moins"
                }
                if {$::simulator($module,val2) < -10} {
                    set ::simulator($module,val2,sens) "plus"
                }
                if {$::simulator($module,val2,sens) == "plus"} {
                    set ::simulator($module,val2) [format %.2f [expr $::simulator($module,val2) + 0.01]]
                }
                if {$::simulator($module,val2,sens) == "moins"} {
                    set ::simulator($module,val2) [format %.2f [expr $::simulator($module,val2) - 0.01]]
                }
            
                switch $reg {
                    "0x76" {
                        # Minor
                        set ::simulator(valueToSend) 2
                    }
                    "0x56" {
                        # Major
                        set ::simulator(valueToSend) 1
                    }
                    "0x43" {
                        # CHip
                        set ::simulator(valueToSend) "S"
                    }
                    "0x20" {
                        # SENSOR_GENERIC_HP_ADR
                        set ::simulator(valueToSend) [expr (100.0 * $::simulator($module,val1)) / 256]
                    }
                    "0x21" {
                        # SENSOR_GENERIC_LP_ADR
                        set ::simulator(valueToSend) [expr round(100.0 * $::simulator($module,val1)) % 256]
                    }
                    "0x22" {
                        # SENSOR_GENERIC_HP2_ADR
                        set ::simulator(valueToSend) [expr (100.0 * $::simulator($module,val2)) / 256]
                    }
                    "0x23" {
                        # SENSOR_GENERIC_LP2_ADR
                        set ::simulator(valueToSend) [expr round(100.0 * $::simulator($module,val2)) % 256]
                    }
                    default {
                        error "$args not undertsand" 
                    }
                }
            }
        }
        "i2cget" {
            set module  [lindex $args 4]
            return $::simulator(valueToSend)
        }
        default {
        
        }        
    }
    

}