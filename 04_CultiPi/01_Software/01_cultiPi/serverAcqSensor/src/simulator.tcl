
set ::simulator(valueToSend) "0"

proc exec {args} {

    set commande [lindex $args 0]

    switch $commande {
        "i2cset" {

            set module  [lindex $args 3]
            set reg     [lindex $args 4]
            
            if {[expr $module *1.0] > 4 && $module != 0x27} {
                error "Not available in simulator (modul $module [expr $module *1.0])"
            }
            
            if {[array names ::simulator -exact $module] == ""} {
                set ::simulator($module) ""
                set ::simulator($module,val1) [expr 20 + $module * 10]
                set ::simulator($module,val1,sens) "plus"
                set ::simulator($module,val2) [expr 20 + $module * 10]
                set ::simulator($module,val2,sens) "plus"
            } else {
            
                # Pour les capteur 1 2 et 3
                if {$::simulator($module,val1) >= 80} {
                    set ::simulator($module,val1,sens) "moins"
                }
                if {$::simulator($module,val1) < 30} {
                    set ::simulator($module,val1,sens) "plus"
                }
                if {$::simulator($module,val1,sens) == "plus"} {
                    set ::simulator($module,val1) [format %.2f [expr $::simulator($module,val1) + 0.03]]
                }
                if {$::simulator($module,val1,sens) == "moins"} {
                    set ::simulator($module,val1) [format %.2f [expr $::simulator($module,val1) - 0.03]]
                }
                
                if {$::simulator($module,val2) >= 80} {
                    set ::simulator($module,val2,sens) "moins"
                }
                if {$::simulator($module,val2) < 30} {
                    set ::simulator($module,val2,sens) "plus"
                }
                if {$::simulator($module,val2,sens) == "plus"} {
                    set ::simulator($module,val2) [format %.2f [expr $::simulator($module,val2) + 0.07]]
                }
                if {$::simulator($module,val2,sens) == "moins"} {
                    set ::simulator($module,val2) [format %.2f [expr $::simulator($module,val2) - 0.07]]
                }
                
                # Capteur 3 : Moyenne 1 et 2
                if {$module == 3} {
                    set ::simulator($module,val1) [expr ($::simulator(0x01,val1) + $::simulator(0x02,val1) )/2.0]
                    set ::simulator($module,val2) [expr ($::simulator(0x01,val2) + $::simulator(0x02,val2) )/2.0]
                }
                # Capteur 4 : Max 1 et 2
                if {$module == 4} {
                    set ::simulator($module,val1) [expr max($::simulator(0x01,val1),$::simulator(0x02,val1))]
                    set ::simulator($module,val2) [expr max($::simulator(0x01,val2),$::simulator(0x02,val2))]
                }
                # Capteur 5 : Min 1 et 2
                if {$module == 0x27} {
                    set ::simulator($module,val1) [expr min($::simulator(0x01,val1),$::simulator(0x02,val1))]
                    set ::simulator($module,val2) [expr min($::simulator(0x01,val2),$::simulator(0x02,val2))]
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