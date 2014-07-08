; Script generated by the Inno Setup Script Wizard.
; SEE THE DOCUMENTATION FOR DETAILS ON CREATING INNO SETUP SCRIPT FILES!


; #####################################################################################
;       Changer les chemins de la section [Files] pour rendre le script portable
; #####################################################################################


#define MyAppName "cultibox"
#define MyAppVersion "1.1.2"
#define MyAppPublisher "Green Box SAS"
#define MyAppURL "http://www.cultibox.fr/"

[Setup]
; NOTE: The value of AppId uniquely identifies this application.
; Do not use the same AppId value in installers for other applications.
; (To generate a new GUID, click Tools | Generate GUID inside the IDE.)
AppId={{E8DC2CCC-6FD8-4FA2-B11A-4CF206BA4C60}
AppName={#MyAppName}
AppVersion={#MyAppVersion}
;AppVerName={#MyAppName} {#MyAppVersion}
AppPublisher={#MyAppPublisher}
AppPublisherURL={#MyAppURL}
AppSupportURL={#MyAppURL}
AppUpdatesURL={#MyAppURL}
DefaultDirName={sd}\{#MyAppName}
DefaultGroupName={#MyAppName}
AllowNoIcons=yes
OutputBaseFilename=cultibox-windows7_{#MyAppVersion}
VersionInfoVersion={#MyAppVersion}
Compression=lzma
SolidCompression=yes
;Pas de warning si le dossier existe d�j�
DirExistsWarning=no
; Interdiction de changer le path
DisableDirPage=yes
;Minimal disk space requiered: 400Mo
ExtraDiskSpaceRequired=419430400
;Desactive le choix de creation dans le menu demarrer
DisableProgramGroupPage=yes
LicenseFile=conf-package\lgpl3.txt
SetupLogging=yes



[Languages]
Name: "english"; MessagesFile: "compiler:Default.isl"
Name: "french"; MessagesFile: "compiler:Languages\French.isl"
Name: "italian"; MessagesFile: "compiler:Languages\Italian.isl"
Name: "german"; MessagesFile: "compiler:Languages\German.isl"
Name: "spanish"; MessagesFile: "compiler:Languages\Spanish.isl"

[Tasks]
Name: "desktopicon"; Description: "{cm:CreateDesktopIcon}"; GroupDescription: "{cm:AdditionalIcons}"; Flags: unchecked

[CustomMessages]
french.StartCultibox=Voulez-vous ex�cuter le logiciel Cultibox imm�diatement?
english.StartCultibox=Do you want to execute the Cultibox software immediatly?
italian.StartCultibox=Vuoi eseguire il software Cultibox immediatamente?
german.StartCultibox=Wollen Sie die Cultibox Software sofort auszuf�hren?
spanish.StartCultibox=�Desea ejecutar el software Cultibox inmediatamente?

french.UpgradeCultibox=Une ancienne version du logiciel Cultibox a �t� d�tect�. Si vous continuez l'installation, le logiciel sera mis � jour.%nMerci de prendre note des informations suivantes avant de lancer la mise � jour:%n%nLe passage vers une version ant�rieure n'est pas assur�e par le logiciel. Ceci peut �tre r�alis� � vos risques et p�rils, sans garantis de succ�s.%n%nSi vous avez modifi� manuellement certains fichiers, les changements peuvent �tre perdues durant la mise � jour. Les donn�es et la configuration de votre logiciel seront toujours op�rationnel dans la nouvelle version.%n%nAfin de r�aliser une mise � jour, le logiciel actuel doit �tre pleinement fonctionnel.%n%n%nVoulez-vous continuer l'installation de la mise � jour du logiciel Cultibox?
english.UpgradeCultibox=An older version of Cultibox software was detected. If you continue the installation, the software will be updated.%nPlease note the following information before starting the update.%n%nThe shift to an earlier version is not provided by the software . This can be done at your own risk, without guaranteed success.%n%nIf you have changed some files manually, changes may be lost during the update. Data and configuration of your software will always be operational in the new version.%n%nTo perform an update, the current software must be fully functional.%n%n%nDo you want to continue installing the update Cultibox software update?
italian.UpgradeCultibox=� stata rilevata una versione precedente del software Cultibox. Se si continua l'installazione, il software verr� aggiornato%nSi prega di notare le seguenti informazioni prima di avviare l'aggiornamento.%n%nIl passaggio a una versione precedente non � fornito dal software . Questo pu� essere fatto a proprio rischio e pericolo, senza successo garantito.%n%nSe hai modificato alcuni file manualmente, le modifiche possono essere perse durante l'aggiornamento. I dati e la configurazione del software saranno sempre operativi nella nuova versione.%n%nPer eseguire l'aggiornamento, il software corrente deve essere pienamente funzionale.%n%n%nVuoi continuare l'installazione dell'aggiornamento aggiornamento software Cultibox?
german.UpgradeCultibox=Eine �ltere Version der Software Cultibox erkannt. Wenn Sie die Installation fortsetzen, wird die Software aktualisiert werden%nBitte beachten Sie die folgenden Informationen, bevor Sie das Update.%n%nDer Wechsel zu einer fr�heren Version wird von der Software nicht vorgesehen . Dies kann auf eigene Gefahr durchgef�hrt werden, ohne Erfolgsgarantie.%n%nWenn Sie einige Dateien manuell ge�ndert haben, k�nnen sich �nderungen w�hrend der Aktualisierung verloren. Daten und Konfiguration Ihrer Software immer funktionsf�hig sein in der neuen Version.%n%nUm ein Update durchzuf�hren, muss die aktuelle Software voll funktionsf�hig.%n%n%nwerden Sie um die Installation des Update wollen Cultibox Software-Update?                                                               
spanish.UpgradeCultibox=Se ha detectado una versi�n anterior del software Cultibox. Si contin�a con la instalaci�n, el software se actualizar�%nTenga en cuenta la siguiente informaci�n antes de iniciar la actualizaci�n.%n%nEl cambio a una versi�n anterior no es proporcionado por el software . Esto se puede hacer por su cuenta y riesgo, y sin garant�a de �xito.%n%nSi ha cambiado algunos archivos manualmente, los cambios pueden perderse durante la actualizaci�n. Los datos y la configuraci�n de su software siempre estar�n en funcionamiento en la nueva versi�n.%n%nPara realizar una actualizaci�n, el software actual debe ser completamente funcional.%n%n%n�Desea continuar con la instalaci�n de la actualizaci�n actualizaci�n de software Cultibox?


french.Uncompat=Cette installation n'est pas compatible pour le syst�me Windows XP. Vous pouvez t�l�charger une version compatible pour votre syst�me sur le site http://cultibox.fr
english.Uncompat=This installation is not compatible for Windows XP. You can download a compatible version for your system on the site http://cultibox.fr
italian.Uncompat=Questa installazione non � compatibile per Windows XP. � possibile scaricare una versione compatibile per il sistema sul sito http://cultibox.fr
german.Uncompat=Diese Installation ist f�r Windows XP kompatibel. Sie k�nnen eine kompatible Version f�r Ihr System auf der Website herunterladen http://cultibox.fr
spanish.Uncompat=Esta instalaci�n no es compatible para Windows XP. Puede descargar una versi�n compatible para su sistema en el sitio http://cultibox.fr

[code]
var 
  ForceInstall: boolean;
function InitializeSetup():boolean;
var
  ResultCode: integer;
  Version: TWindowsVersion;

begin
  GetWindowsVersionEx(Version);


   // On Windows XP:
  if Version.NTPlatform and
     (Version.Major = 5) and
     (Version.Minor = 1) then
  begin

         MsgBox(ExpandConstant('{cm:Uncompat}'),mbCriticalError, MB_OK);
         Result := False;
         Exit 
  end;

  Result := True;  
  if FileExists(ExpandConstant('{sd}\{#MyAppName}\unins000.exe')) then
  begin
       ForceInstall := True;      
  end;

  if(ForceInstall) then
  begin
       if MsgBox(ExpandConstant('{cm:UpgradeCultibox}'), mbConfirmation, MB_YESNO or MB_DEFBUTTON2) <> IDYES then                                                                                                                                 
       begin
           Result := False;
       end; 


       if (Result) then
       begin 
            Exec (ExpandConstant ('{cmd}'), '/C net stop cultibox_apache', '', SW_HIDE, ewWaitUntilTerminated, ResultCode);
            Exec (ExpandConstant ('{cmd}'), '/C net stop cultibox_mysql', '', SW_HIDE, ewWaitUntilTerminated, ResultCode); 
            Exec (ExpandConstant ('{cmd}'), ExpandConstant ('/C del /F /Q {sd}\{#MyAppName}\xampp\install\install.sys'), '', SW_HIDE, ewWaitUntilTerminated, ResultCode);
      end;
    end;  
end;


procedure CurStepChanged(CurStep: TSetupStep);
var
  ResultCode: integer;

 begin
  if(CurStep=ssPostInstall) then
  begin   
    Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C setup_xampp.bat'), ExpandConstant ('{sd}\{#MyAppName}\xampp'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
    Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C apache_uninstallservice.bat'), ExpandConstant ('{sd}\{#MyAppName}\xampp\apache'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
    Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C apache_installservice.bat'), ExpandConstant ('{sd}\{#MyAppName}\xampp\apache'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
    Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C mysql_uninstallservice.bat'), ExpandConstant ('{sd}\{#MyAppName}\xampp\mysql'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
    Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C mysql_installservice.bat'), ExpandConstant ('{sd}\{#MyAppName}\xampp\mysql'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
    Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C mysqladmin.exe -u root -h 127.0.0.1  --port=3891 password cultibox'), ExpandConstant ('{sd}\{#MyAppName}\xampp\mysql\bin'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
    Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C mysql.exe --defaults-extra-file={sd}\{#MyAppName}\xampp\mysql\bin\my-extra.cnf -h 127.0.0.1 --port=3891 -e "source {sd}\{#MyAppName}\xampp\sql_install\user_cultibox.sql"'), ExpandConstant ('{sd}\{#MyAppName}\xampp\mysql\bin'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
    Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C mysql.exe --defaults-extra-file={sd}\{#MyAppName}\xampp\mysql\bin\my-extra.cnf -h 127.0.0.1 --port=3891 mysql < "{sd}\{#MyAppName}\xampp\sql_install\five-tables.sql"'), ExpandConstant ('{sd}\{#MyAppName}\xampp\mysql\bin\'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
 
        
    if not (ForceInstall) then
    begin
        Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C mysql.exe --defaults-extra-file={sd}\{#MyAppName}\xampp\mysql\bin\my-extra.cnf -h 127.0.0.1 --port=3891 -e "source {sd}\{#MyAppName}\xampp\sql_install\joomla.sql"'), ExpandConstant ('{sd}\{#MyAppName}\xampp\mysql\bin'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
        
        case ActiveLanguage() of  { ActiveLanguage() retourne la langue chosie }
        'french' :  
            begin
              Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C mysql.exe --defaults-extra-file={sd}\{#MyAppName}\xampp\mysql\bin\my-extra.cnf -h 127.0.0.1 --port=3891 -e "source {sd}\{#MyAppName}\xampp\sql_install\cultibox_fr.sql"'), ExpandConstant ('{sd}\{#MyAppName}\xampp\mysql\bin\'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
            end;
        'english' :
            begin
              Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C mysql.exe --defaults-extra-file={sd}\{#MyAppName}\xampp\mysql\bin\my-extra.cnf -h 127.0.0.1 --port=3891 -e "source {sd}\{#MyAppName}\xampp\sql_install\cultibox_en.sql"'), ExpandConstant ('{sd}\{#MyAppName}\xampp\mysql\bin\'), SW_HIDE, ewWaitUntilTerminated, ResultCode);         
            end;
         'italian' :
            begin
              Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C mysql.exe --defaults-extra-file={sd}\{#MyAppName}\xampp\mysql\bin\my-extra.cnf -h 127.0.0.1 --port=3891 -e "source {sd}\{#MyAppName}\xampp\sql_install\cultibox_it.sql"'), ExpandConstant ('{sd}\{#MyAppName}\xampp\mysql\bin\'), SW_HIDE, ewWaitUntilTerminated, ResultCode);         
            end;
         'german' :
            begin
              Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C mysql.exe --defaults-extra-file={sd}\{#MyAppName}\xampp\mysql\bin\my-extra.cnf -h 127.0.0.1 --port=3891 -e "source {sd}\{#MyAppName}\xampp\sql_install\cultibox_de.sql"'), ExpandConstant ('{sd}\{#MyAppName}\xampp\mysql\bin\'), SW_HIDE, ewWaitUntilTerminated, ResultCode);         
            end;
         'spanish' :
            begin
              Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C mysql.exe --defaults-extra-file={sd}\{#MyAppName}\xampp\mysql\bin\my-extra.cnf -h 127.0.0.1 --port=3891 -e "source {sd}\{#MyAppName}\xampp\sql_install\cultibox_es.sql"'), ExpandConstant ('{sd}\{#MyAppName}\xampp\mysql\bin\'), SW_HIDE, ewWaitUntilTerminated, ResultCode);         
            end;
         end;

         Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C mysql.exe --defaults-extra-file={sd}\{#MyAppName}\xampp\mysql\bin\my-extra.cnf -h 127.0.0.1 --port=3891 -e "source {sd}\{#MyAppName}\xampp\sql_install\fake_log.sql"'), ExpandConstant ('{sd}\{#MyAppName}\xampp\mysql\bin'), SW_HIDE, ewWaitUntilTerminated, ResultCode);

     end;


     if (ForceInstall) then 
     begin
        Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C {sd}\{#MyAppName}\xampp\sql_install\update_sql.bat'), ExpandConstant ('{sd}\{#MyAppName}\xampp\mysql\bin'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
        Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C del {sd}\{#MyAppName}\xampp\htdocs\cultibox\main\templates_c\*.ser'), ExpandConstant ('{sd}\{#MyAppName}\xampp'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
     end;     
  end;

  if(CurStep=ssDone) then
  begin
      if MsgBox(ExpandConstant('{cm:StartCultibox}'), mbConfirmation, MB_YESNO or MB_DEFBUTTON2) = IDYES then                                                                                                                                 
      begin
          Exec (ExpandConstant ('{cmd}'), '/C start http://localhost:6891/cultibox', ExpandConstant ('{tmp}'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
       end; 
  end;
end; 



procedure CurUninstallStepChanged(CurUninstallStep: TUninstallStep);
var
  ResultCode: Integer;
begin
   
   if CurUninstallStep = usUninstall then
   begin
      Exec (ExpandConstant ('{cmd}'), '/C net stop cultibox_apache', ExpandConstant ('{tmp}'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
      Exec (ExpandConstant ('{cmd}'), '/C net stop cultibox_mysql', ExpandConstant ('{tmp}'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
      Exec (ExpandConstant ('{cmd}'), '/C apache_uninstallservice.bat', ExpandConstant ('{sd}\{#MyAppName}\xampp\apache'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
      Exec (ExpandConstant ('{cmd}'), '/C mysql_uninstallservice.bat', ExpandConstant ('{sd}\{#MyAppName}\xampp\mysql'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
   end;
end;



procedure CancelButtonClick(CurPageID: Integer; var Cancel, Confirm: Boolean);
var
  ResultCode: Integer;
begin
  Confirm := False;
  if (MsgBox (SetupMessage(msgExitSetupMessage),mbConfirmation, MB_YESNO) = IDYES) then
  begin
      Exec (ExpandConstant ('{cmd}'), '/C net start cultibox_apache', ExpandConstant ('{tmp}'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
      Exec (ExpandConstant ('{cmd}'), '/C net start cultibox_mysql', ExpandConstant ('{tmp}'), SW_HIDE, ewWaitUntilTerminated, ResultCode);
  end;  
end;


[Files]
; Backup file. Used in pre install
Source: "conf-script\load.bat"; DestDir: "{app}\run"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "conf-script\backup.bat"; DestDir: "{app}\run"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "conf-script\get_version.bat"; DestDir: "{app}\run"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "..\..\..\01_software\01_install\01_src\01_xampp\cultibox\*"; DestDir: "{app}\xampp"; Flags: ignoreversion recursesubdirs createallsubdirs 
Source: "..\..\..\01_software\01_install\01_src\03_sd\*"; DestDir: "{app}\sd"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "..\..\..\02_documentation\02_userdoc\*"; DestDir: "{app}\doc"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "conf-lampp\my-extra.cnf"; DestDir: "{app}\xampp\mysql\bin\"; Flags: ignoreversion
; NOTE: Don't use "Flags: ignoreversion" on any shared system files


[Icons]
Name: "{group}\Cultibox"; Filename: "http://localhost:6891/cultibox"; Comment: "Run cultibox"; IconFilename: "{app}\sd\cultibox.ico"; AppUserModelID: "Cultibox.Cultibox"
Name: "{group}\{cm:UninstallProgram,{#MyAppName}}"; Filename: {uninstallexe}; Comment: "Uninstall cultibox"
Name: "{commondesktop}\Cultibox"; Filename: "http://localhost:6891/cultibox"; IconFilename: "{app}\sd\cultibox.ico"; Tasks: desktopicon


[UninstallDelete]
Type: filesandordirs; Name: "{app}\xampp"
