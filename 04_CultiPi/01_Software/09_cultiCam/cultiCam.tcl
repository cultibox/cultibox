# Ce script permet de piloter les webcam

# Source files
set rootDir [file dirname [info script]]

# On ajoute le path des outils
if {$::tcl_platform(os) == "Windows NT"} {
    lappend auto_path [file join $rootDir .. 01_cultiPi lib tcl]
    puts [file join $rootDir .. 01_cultiPi lib tcl]
} else {
    lappend auto_path [file join $rootDir .. cultipi lib tcl]
}

package require piXML

set confXML  [lindex $argv 0]

# Démarrage
puts  "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Démarrage"

# On charge le XML
# On initialise la conf XML
array set configXML {
    verbose                 debug
    timeBeforeTwoSnapshots  1
    dailySnapshotHour       12
    dailySnapshotMin        0
    nbWebcam                0
    lock_snapshotFile       /var/lock/culticam_snapshot
    lock_reloadConfFile     /var/lock/culticam_reloadConf
    lock_start_stream       /var/lock/culticam_stream 
    lock_stop_stream        /var/lock/culticam_disable     
}
set RC [catch {
    array set configXML [::piXML::convertXMLToArray $confXML]
} msg]
if {$RC != 0} {
    puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Error during loading XML , error : $msg"
}


foreach name [array names configXML] {
    puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : XML Infos : $name - $::configXML($name)"
}


# On prend une photo toutes les X secondes
proc takePhoto {webcamIndex} {

    # On vérifie la présence du fichier /dev/videoX
    if {[file exists /dev/video${webcamIndex}] != 1} {
        # Le fichier n'est pas présent, on ne fait donc pas de snapshot
        update
        after 1000 takePhoto $webcamIndex
        return
    }

    # On la sauvegarde dans les photos journalière une fois par jour
    set Hour [string trimleft [clock format [clock seconds] -format "%H"] 0]
    if {$Hour == ""} {set Hour 0}
    set Minute [string trimleft [clock format [clock seconds] -format "%M"] 0]
    if {$Minute == ""} {set Minute 0}
    set date [string trimleft [clock format [clock seconds] -format "%y%m%d"] 0]
    if {$Hour > $::configXML(dailySnapshotHour) && 
        $Minute > $::configXML(dailySnapshotMin) && 
        $::dailyPhotoIsTake($webcamIndex) != $date} {
        
        puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Take daily photo webcam $webcamIndex"
        
        # On prend une image
        puts "exec sudo fswebcam -c $::configXML(confPathWebcam,${webcamIndex})"
        set RC [catch {
            exec sudo fswebcam -c  $::configXML(confPathWebcam,${webcamIndex})
        } msg]
        if {$RC != 0} {
            puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Error during taking snapshot webcam ${webcamIndex} , error : $msg"
        } else {
            # Il n'y a pas eu d'erreur
            
            set RC [catch {
                file copy -force /var/www/cultibox/tmp/webcam${webcamIndex}.jpg  /var/www/cultibox/tmp/webcam${webcamIndex}_${date}.jpg 
            } msg]
            if {$RC != 0} {
                puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Error during saving daily snapshot webcam ${webcamIndex} , error : $msg"
            }
            
        }
       
        set ::dailyPhotoIsTake($webcamIndex) ${date}
    }

    update
    after 20 "takePhoto $webcamIndex"
    return
}


# On prend une photo toutes les X secondes
proc takePhotoOnDemand {} {
     # Prise de snapshot a la demande
    if {[file exists $::configXML(lock_snapshotFile)] == 1} {
        set webIndex 1 
        for {set i 0} {$i < 3} {incr i} {
            set RC [catch {
                set fp [open $::configXML(lock_snapshotFile) r]
                set confFile [read $fp]
                close $fp
                set webIndex [expr $confFile * 1]
            } msg]
            
            if {$RC != 0} {
                puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Error during opening file - $::configXML(lock_snapshotFile) - try [expr $i + 1] / 3 , error : $msg"
                after 20
                update
            } else {
                break
            }
            
        }


        puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Take Snapshot webcam" ; update

        # On prend un image
        set RC [catch {
            exec sudo fswebcam -c "/etc/culticam/webcam${webIndex}.conf"
        } msg]
        puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Taking snapshot webcam $webIndex : $msg"
        after 5000 file delete -force $::configXML(lock_snapshotFile)
    }

    update
    after 20 "takePhotoOnDemand"
    return
}

takePhotoOnDemand

# Pour chaque webcam, on lance la boucle d'acquisition
for {set i 0} {$i < $::configXML(nbWebcam)} {incr i} {

    # Initialisation de la variable qui sauvegarde quand la photo journalière est faite
    set ::dailyPhotoIsTake($i) ""
    
    # Démarrage de l'acquisition
    after [expr $i * 1000] "takePhoto $i"
    update
}

# On vérifie régulièrement s'il faut recharger le fichier de conf 
proc reloadXML {} {

    if {[file exists $::configXML(lock_reloadConfFile)] == 1} {
        puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : reloadXML: Reload conf file"
        array set ::configXML [::piXML::convertXMLToArray $::confXML]
        
        set RC [catch {
            file delete -force  $::configXML(lock_reloadConfFile)
        } msg]
        if {$RC != 0} {
            puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : reloadXML : Error during deleting lock file (path : $::configXML(lock_reloadConfFile)) , error : $msg"
        }
        
    }

    after 250 reloadXML
    update
}


puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : reloadXML : Starting reload XML"
reloadXML


proc streamCheck {} {

    # Ouverture du flux video
    if {[file exists $::configXML(lock_start_stream)] == 1} {
    
        # Le fichier est présent, on fait donc un snapshot
        puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Start Video Stream" ; update

        # On lance le flux video
        set RC [catch {
            exec sudo /etc/init.d/motion start
        } msg]
        puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : $msg"

        # Suppression du fichier de lock
        after 5000 file delete -force $::configXML(lock_start_stream)
    }
    
    # Fermeture du flux video
    if {[file exists $::configXML(lock_stop_stream)] == 1} {
        puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Stop Video Stream" ; update

        # On stop le flux video
        set RC [catch {
            exec sudo /etc/init.d/motion stop
        } msg]
        puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : $msg"

        # Suppression du fichier de lock
        file delete -force $::configXML(lock_stop_stream)
    }

    after 50 streamCheck
}

puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : reloadXML : Starting stream check" 
streamCheck

# On attend indéfiniment
vwait forever

# tclsh "C:\cultibox\04_CultiPi\01_Software\09_cultiCam\cultiCam.tcl" "C:\cultibox\04_CultiPi\01_Software\09_cultiCam\exemple_conf.xml"
# tclsh /opt/culticam/cultiCam.tcl /etc/culticam/conf.xml
