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
set dailyPhotoIsTake 0

# On charge le XML
# On initialise la conf XML
array set configXML {
    verbose                 debug
    timeBeforeTwoSnapshots  5
    dailySnapshotHour       12
    dailySnapshotMin        0
    nbWebcam                0
    lock_snapshotFile       /var/lock/culticam_snapshot
    lock_reloadConfFile     /var/lock/culticam_reloadConf
    
}
set RC [catch {
    array set configXML [::piXML::convertXMLToArray $confXML]
} msg]
if {$RC != 0} {
    puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Error during loading XML , error : $msg"
}
puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : XML Infos : [array get configXML]"

# On prend une photo toutes les X secondes
proc takePhoto {webcamIndex} {

    # On la sauvegarde dans les photos journalière une fois par jour
    set Hour [string trimleft [clock format [clock seconds] -format "%H"] 0]
    if {$Hour == ""} {set Hour 0}
    set Minute [string trimleft [clock format [clock seconds] -format "%M"] 0]
    if {$Minute == ""} {set Minute 0}
    set date [string trimleft [clock format [clock seconds] -format "%y%m%d"] 0]
    if {$Hour > $::configXML(dailySnapshotHour) && 
        $Minute > $::configXML(dailySnapshotMin) && 
        $::dailyPhotoIsTake != $date} {
        
        # On prend un image
        set RC [catch {
            exec sudo fswebcam -c  $::configXML(confPathWebcam,${webcamIndex})
        } msg]
        if {$RC != 0} {
            puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Error during taking snapshot webcam ${webcamIndex} , error : $msg"
        }
        
        set RC [catch {
            file copy -force /var/www/cultibox/tmp/webcam_${webcamIndex}_temp.jpg  /var/www/cultibox/tmp/webcam_${webcamIndex}_${date}.jpg 
        } msg]
        if {$RC != 0} {
            puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Error during saving daily snapshot webcam ${webcamIndex} , error : $msg"
        }
        
        
        set ::dailyPhotoIsTake ${date}
    }


    if {[file exists $::configXML(lock_snapshotFile)] != 1} {
    
        # Le fichier n'est pas présent, on ne fait donc pas de snapshot
        after 1000 takePhoto $webcamIndex
        
    }

    puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Take Snapshot webcam ${webcamIndex}" ; update

    if {[array names ::configXML -exact "confPathWebcam,${webcamIndex}"] == ""} {
        puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Error conf of webcam doesnot exists"
        return
    }
    
    # On prend un image
    set RC [catch {
        exec sudo fswebcam -c  $::configXML(confPathWebcam,${webcamIndex})
    } msg]
    if {$RC != 0} {
        puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Error during taking snapshot webcam ${webcamIndex} , error : $msg"
    }
    
    # On la sauvegarde
    #set RC [catch {
    #    file copy -force /var/www/cultibox/tmp/webcam${webcamIndex}.jpg  /var/www/cultibox/tmp/webcam_${webcamIndex}.jpg 
    #} msg]
    #if {$RC != 0} {
    #    puts "[clock format [clock seconds] -format "%Y %b %d %H:%M:%S"] : cultiCam : Error during copying snapshot webcam ${webcamIndex} , error : $msg"
    #}

    after 20 takePhoto $webcamIndex
}

# Pour chaque webcam, on lance la boucle d'acquisition
for {set i 0} {$i < $::configXML(nbWebcam)} {incr i} {
    takePhoto $i
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
}
reloadXML

# On attend indéfiniment
vwait forever

# tclsh "C:\cultibox\04_CultiPi\01_Software\09_cultiCam\cultiCam.tcl" "C:\cultibox\04_CultiPi\01_Software\09_cultiCam\exemple_conf.xml"
# tclsh /opt/culticam/cultiCam.tcl /etc/culticam/conf.xml
