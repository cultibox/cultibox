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
    lock_reloadConfFile     /var/lock/culticam_reloadConf
    lock_start_service       /var/lock/culticam_enable 
    lock_stop_service        /var/lock/culticam_disable     
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


# Pour chaque webcam, on lance la boucle d'acquisition
#for {set i 0} {$i < $::configXML(nbWebcam)} {incr i} {

    # Initialisation de la variable qui sauvegarde quand la photo journalière est faite
 #   set ::dailyPhotoIsTake($i) ""
    
    # Démarrage de l'acquisition
#    after [expr $i * 1000] "takePhoto $i"
#    update
#}


# On vérifie régulièrement s'il faut recharger le fichier de conf 
proc reloadXML {} {

    if {[file exists $::configXML(lock_reloadConfFile)] == 1} {
        puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : reloadXML: Restart mjpg service with new conf..."
        set RC [catch {
            exec sudo /etc/init.d/mjpg_streamer force-reload
        } msg]
        after 2000 puts "...ok"
		
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


proc mjpgCheck {} {

    if {[file exists $::configXML(lock_start_service)] == 1} {
        puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Start Video Service..." 

        # On lance le service
        set RC [catch {
            exec sudo /etc/init.d/mjpg_streamer start
        } msg]
        after 2000 puts "...ok"
        puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : $msg"

        # Suppression du fichier de lock
        file delete -force $::configXML(lock_start_service)
    }
    
    # Fermeture du service
    if {[file exists $::configXML(lock_stop_service)] == 1} {
        puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Stop Video Service..." 

        set RC [catch {
            exec sudo /etc/init.d/mjpg_streamer stop
        } msg]
        puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : $msg"

        # Suppression du fichier de lock
        after 2000 puts "...ok"
        file delete -force $::configXML(lock_stop_service)
    }

    after 50 mjpgCheck
}

puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : reloadXML : Starting mjpg_streamer check" 
mjpgCheck

# On attend indéfiniment
vwait forever

# tclsh "C:\cultibox\04_CultiPi\01_Software\09_cultiCam\cultiCam.tcl" "C:\cultibox\04_CultiPi\01_Software\09_cultiCam\exemple_conf.xml"
# tclsh /opt/culticam/cultiCam.tcl /etc/culticam/conf.xml
