; Script generated by the Inno Setup Script Wizard.
; SEE THE DOCUMENTATION FOR DETAILS ON CREATING INNO SETUP SCRIPT FILES!


; #####################################################################################
;       Changer les chemins de la section [Files] pour rendre le script portable
; #####################################################################################


#define MyAppName "Cultibox"
#define MyAppVersion "1.0.306"
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
OutputBaseFilename=CultiBox_{#MyAppVersion}-windows7
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



[Languages]
Name: "english"; MessagesFile: "compiler:Default.isl"
Name: "french"; MessagesFile: "compiler:Languages\French.isl"


[CustomMessages]
french.ContinueInstall=La version que vous tentez d'installer n'est pas la m�me que celle d�ja install�e sur votre ordinateur. Si vous continuez, vos anciennes donn�es (logs,programmes...) ne seront peut �tre pas disponible. Pour mettre � jour votre logiciel merci de visiter le site de la cultibox (http://cultibox.fr/) ou d'utiliser la fonction de mise � jour de votre logiciel. Voulez vous continuer tout de m�me?
english.ContinueInstall=Version of the software is not the same that the current version your are trying to installed. If you continue, your datas (logs,plugs configuration, programs...) may not be available. If you want to upgrade your software please visit the website of the cultibox ou use the update function in the software. Do you want to continue?

french.SaveDatas=Voulez-vous sauvegarder les logs et programmes de votre ancienne version?
english.SaveDatas=Do you want to save logs and programms of your old CultiBox software?

french.LoadDatas=Chargement de votre ancienne configuration dans la nouvelle installation
english.LoadDatas=Loading your old datas in the new software installation

french.ForceLoadDatas=Voulez-vous essayer de charger vos anciennes donn�es dans le nouveau logiciel?
english.ForceLoadDatas=Do you want to try to load your old datas in the new software?

french.TryLoadDatas=Une ancienne sauvegarde des donn�es (logs,programmes, configuration...) a �t� trouv� sur votre ordinateur. Vous pouvez essayer de les charger sur votre nouvelle installation mais cela pourrait l'endommager. Voulez-vous essayer de charger vos anciennes donn�es dans le nouveau logiciel?
english.TryLoadDatas=An old backup has been found in your computer. You can try to load your old data but this can destroy your current installation. Do you want to try to load your old datas in the new software?

french.RestartServices=Red�marrer les services (Executer en tant qu'admin)
english.RestartServices=Restart services (Run as admin)
[code]
var 
  reload: boolean;
var 
  ForceInstall: boolean;
function InitializeSetup():boolean;
var
  ResultCode: integer;

begin 
  if FileExists(ExpandConstant('{sd}\{#MyAppName}\xampp\VERSION_{#MyAppVersion}.txt')) then
  begin
     reload:=true;
  end else begin
     reload:=false;
  end;

  if FileExists(ExpandConstant('{sd}\{#MyAppName}\unins000.exe')) then
  begin
     if not(reload) then
     begin
       // Ask the user a Yes/No question, defaulting to No
       if MsgBox(ExpandConstant('{cm:ContinueInstall}'), mbConfirmation, MB_YESNO or MB_DEFBUTTON2) = IDYES then                                                                                                                                 
       begin
           // user clicked Yes
           ForceInstall := True;
       end else 
       begin
           ForceInstall := False;
       end; 
    end;

    if(reload) or (ForceInstall) then
    begin
       if MsgBox(ExpandConstant('{cm:SaveDatas}'), mbConfirmation, MB_YESNO or MB_DEFBUTTON2) = IDYES then                                                                                                                                 
       begin                                                      
        Exec (ExpandConstant ('{cmd}'), '/C net start cultibox_apache', '', SW_SHOW, ewWaitUntilTerminated, ResultCode);
        Exec (ExpandConstant ('{cmd}'), '/C net start cultibox_mysql', '', SW_SHOW, ewWaitUntilTerminated, ResultCode);
       
        ExtractTemporaryFile ('backup.bat');

        Exec (ExpandConstant ('{cmd}'), ExpandConstant ('/C copy backup.bat {sd}\{#MyAppName}\backup.bat'), ExpandConstant ('{tmp}'), SW_SHOW, ewWaitUntilTerminated, ResultCode);

        Exec (ExpandConstant ('{cmd}'), '/C backup.bat', ExpandConstant ('{sd}\{#MyAppName}'), SW_SHOW, ewWaitUntilTerminated, ResultCode);

       end;
       Exec (ExpandConstant ('{cmd}'), '/C net stop cultibox_apache', '', SW_SHOW, ewWaitUntilTerminated, ResultCode);
       Exec (ExpandConstant ('{cmd}'), '/C net stop cultibox_mysql', '', SW_SHOW, ewWaitUntilTerminated, ResultCode);

       Exec(ExpandConstant('{sd}\{#MyAppName}\unins000.exe'), '/SILENT /NOCANCEL', '', SW_SHOW,
         ewWaitUntilTerminated, ResultCode);
      
    end;

    if(reload) or (ForceInstall) then
    begin
       Result := True;
    end else 
    begin
       Result := False;
    end;
  end else 
  begin
    Result := True;
  end;
end;

function getLanguage(s : string) : string;
    var langage : string;
begin
    case ActiveLanguage() of
        'english' : langage := 'cultibox_en.sql';
        'french' : langage := 'cultibox_fr.sql';
    end;
    Result := langage;
end;

procedure CurStepChanged(CurStep: TSetupStep);
var
  ResultCode: integer;

var 
  ForceLoad: boolean;

begin
  if(CurStep=ssDone) then
  begin
    if FileExists(ExpandConstant('{sd}\{#MyAppName}\backup\backup.sql')) then
    begin
       if(ForceInstall) or (reload) then
       begin
          // Ask the user a Yes/No question, defaulting to No
          if MsgBox(ExpandConstant('{cm:ForceLoadDatas}'), mbConfirmation, MB_YESNO or MB_DEFBUTTON2) = IDYES then                                                                                                                                 
          begin
                ForceLoad := True;
          end else 
          begin
                ForceLoad := False;
                reload := False;
          end; 
   
          if(reload) or (ForceLoad) then
          begin
           if not (ForceLoad) then
           begin
              MsgBox(ExpandConstant('{cm:LoadDatas}'), mbInformation, MB_OK);
           end;
           Exec (ExpandConstant ('{cmd}'), '/C net start cultibox_apache', '', SW_SHOW, ewWaitUntilTerminated, ResultCode);
           Exec (ExpandConstant ('{cmd}'), '/C net start cultibox_mysql', '', SW_SHOW, ewWaitUntilTerminated, ResultCode);

           ExtractTemporaryFile ('load.bat');
           Exec (ExpandConstant ('{cmd}'), ExpandConstant ('/C copy load.bat {sd}\{#MyAppName}\load.bat'), ExpandConstant ('{tmp}'), SW_SHOW, ewWaitUntilTerminated, ResultCode);
           Exec (ExpandConstant ('{cmd}'), '/C load.bat', ExpandConstant ('{sd}\{#MyAppName}'), SW_SHOW, ewWaitUntilTerminated, ResultCode);
           Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox -e "UPDATE `cultibox`.`configuration` SET `VERSION` = ''{#MyAppVersion}'' WHERE `configuration`.`id` =1;"'), ExpandConstant ('{sd}\{#MyAppName}'), SW_SHOW, ewWaitUntilTerminated, ResultCode);


          end;
       end else
       begin
           // Ask the user a Yes/No question, defaulting to No
          if MsgBox(ExpandConstant('{cm:TryLoadDatas}'), mbConfirmation, MB_YESNO or MB_DEFBUTTON2) = IDYES then                                                                                                                                 
          begin  
              Exec (ExpandConstant ('{cmd}'), '/C net start cultibox_apache', '', SW_SHOW, ewWaitUntilTerminated, ResultCode);
              Exec (ExpandConstant ('{cmd}'), '/C net start cultibox_mysql', '', SW_SHOW, ewWaitUntilTerminated, ResultCode);

              ExtractTemporaryFile ('load.bat');
              Exec (ExpandConstant ('{cmd}'), ExpandConstant ('/C copy load.bat {sd}\{#MyAppName}\load.bat'), ExpandConstant ('{tmp}'), SW_SHOW, ewWaitUntilTerminated, ResultCode);
              Exec (ExpandConstant ('{cmd}'), '/C load.bat', ExpandConstant ('{sd}\{#MyAppName}'), SW_SHOW, ewWaitUntilTerminated, ResultCode);
              Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox -e "UPDATE `cultibox`.`configuration` SET `VERSION` = ''{#MyAppVersion}'' WHERE `configuration`.`id` =1;"'), ExpandConstant ('{sd}\{#MyAppName}'), SW_SHOW, ewWaitUntilTerminated, ResultCode);
          end;
       end;  
    end;
  end;


  case ActiveLanguage() of  { ActiveLanguage() retourne la langue chosie }
        'french' :   Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox -e "UPDATE `cultibox`.`configuration` SET `LANG` = ''fr_FR'' WHERE `configuration`.`id` =1;"'), ExpandConstant ('{sd}\{#MyAppName}'), SW_SHOW, ewWaitUntilTerminated, ResultCode);
        'english' :   Exec (ExpandConstant ('{cmd}'), ExpandConstant('/C xampp\mysql\bin\mysql.exe -u root -h localhost --port=3891 -pcultibox -e "UPDATE `cultibox`.`configuration` SET `LANG` = ''en_GB'' WHERE `configuration`.`id` =1;"'), ExpandConstant ('{sd}\{#MyAppName}'), SW_SHOW, ewWaitUntilTerminated, ResultCode);
  end;
end;


procedure CurUninstallStepChanged(CurUninstallStep: TUninstallStep);
var
  ResultCode: Integer;
begin
   
   if CurUninstallStep = usUninstall then
   begin
      Exec (ExpandConstant ('{cmd}'), '/C net stop cultibox_apache', ExpandConstant ('{tmp}'), SW_SHOW, ewWaitUntilTerminated, ResultCode);
      Exec (ExpandConstant ('{cmd}'), '/C net stop cultibox_mysql', ExpandConstant ('{tmp}'), SW_SHOW, ewWaitUntilTerminated, ResultCode);
      Exec (ExpandConstant ('{cmd}'), '/C apache_uninstallservice.bat', ExpandConstant ('{sd}\{#MyAppName}\xampp\apache'), SW_SHOW, ewWaitUntilTerminated, ResultCode);
      Exec (ExpandConstant ('{cmd}'), '/C mysql_uninstallservice.bat', ExpandConstant ('{sd}\{#MyAppName}\xampp\mysql'), SW_SHOW, ewWaitUntilTerminated, ResultCode);
   end;
end;

[Files]
; Backup file. Used in pre install
Source: "C:\users\yann\Desktop\Project\cultibox\01_software\01_install\02_windows\conf-script\backup.bat"; \
  DestDir: "{app}\run"; \
  DestName: "backup.bat"; \
  Flags: ignoreversion recursesubdirs createallsubdirs
; load file. Used in post install
Source: "C:\users\yann\Desktop\Project\cultibox\01_software\01_install\02_windows\conf-script\load.bat"; \
  DestDir: "{app}\run"; \
  Flags: ignoreversion recursesubdirs createallsubdirs
Source: "C:\users\yann\Desktop\Project\cultibox\01_software\01_install\01_src\01_xampp\*"; \
  DestDir: "{app}\xampp"; \
  Flags: ignoreversion recursesubdirs createallsubdirs
Source: "C:\users\yann\Desktop\Project\cultibox\01_software\01_install\01_src\02_sql\*"; DestDir: "{app}\xampp\sql_install"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "C:\users\yann\Desktop\Project\cultibox\01_software\01_install\01_src\03_sd\*"; DestDir: "{app}\sd"; Flags: ignoreversion recursesubdirs createallsubdirs
;Source: "C:\users\yann\Desktop\Project\cultibox\01_software\01_install\01_src\04_run\*"; DestDir: "{app}\run"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "C:\users\yann\Desktop\Project\cultibox\02_documentation\02_userdoc\*"; DestDir: "{app}\doc"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "C:\users\yann\Desktop\Project\cultibox\01_software\01_install\02_windows\conf-script\cultibox.bat"; DestDir: "{app}"; Flags: ignoreversion
Source: "C:\users\yann\Desktop\Project\cultibox\01_software\01_install\02_windows\conf-lampp\httpd.conf"; DestDir: "{app}\xampp\apache\conf"; Flags: ignoreversion
Source: "C:\users\yann\Desktop\Project\cultibox\01_software\01_install\02_windows\conf-lampp\php.ini"; DestDir: "{app}\xampp\php"; Flags: ignoreversion
Source: "C:\users\yann\Desktop\Project\cultibox\01_software\01_install\02_windows\conf-lampp\my.ini"; DestDir: "{app}\xampp\mysql\bin\"; Flags: ignoreversion
Source: "C:\users\yann\Desktop\Project\cultibox\01_software\01_install\01_src\03_sd\firm.hex"; DestDir: "{app}\xampp\htdocs\cultibox\tmp"; Flags: ignoreversion
Source: "C:\users\yann\Desktop\Project\cultibox\01_software\01_install\01_src\03_sd\emmeteur.hex"; DestDir: "{app}\xampp\htdocs\cultibox\tmp"; Flags: ignoreversion
; NOTE: Don't use "Flags: ignoreversion" on any shared system files


[Icons]
Name: "{group}\Cultibox"; Filename: "http://localhost:6891/cultibox"; Comment: "Run cultibox"; IconFilename: "{app}\sd\cultibox.ico"; AppUserModelID: "Cultibox.Cultibox"
Name: "{group}\{cm:UninstallProgram,{#MyAppName}}"; Filename: {uninstallexe}; Comment: "Uninstall cultibox"

[Run]
Filename: "{app}\xampp\setup_xampp.bat";Description: "Change path";
Filename: "{app}\xampp\apache\apache_uninstallservice.bat";Description: "Uninstall apache service"
Filename: "{app}\xampp\apache\apache_installservice.bat";Description: "Install apache service"
Filename: "{app}\xampp\mysql\mysql_uninstallservice.bat";Description: "Uninstall mysql service"
Filename: "{app}\xampp\mysql\mysql_installservice.bat";Description: "Install mysql service"
Filename: "{app}\xampp\mysql\bin\mysqladmin.exe"; \
  Parameters: " -u root -h localhost  --port=3891 password cultibox"; \
  WorkingDir: "{app}"; \
  Description: "Change root password";
Filename: "{app}\xampp\mysql\bin\mysql.exe"; \
  Parameters: " -u root -h localhost --port=3891 -pcultibox -e ""source xampp\sql_install\user_cultibox.sql""" ; \
  WorkingDir: "{app}"; \
  Description: "Install user base";
Filename: "{app}\xampp\mysql\bin\mysql.exe"; \
  Parameters: " -u root -h localhost --port=3891 -pcultibox -e ""source xampp\sql_install\joomla.sql"""; \
  WorkingDir: "{app}"; \
  Description: "Install joomla base";
Filename: "{app}\xampp\mysql\bin\mysql.exe"; \
  Parameters: " -u root -h localhost --port=3891 -pcultibox -e ""source xampp\sql_install\{code:getLanguage}"""; \
  WorkingDir: "{app}"; \
  Description: "Install cultibox base";
Filename: "{app}\xampp\mysql\bin\mysql.exe"; \
  Parameters: " -u root -h localhost --port=3891 -pcultibox -e ""source xampp\sql_install\fake_log.sql"""; \
  WorkingDir: "{app}"; \
  Description: "Install log base";

[UninstallDelete]
Type: filesandordirs; Name: "{app}\xampp"
