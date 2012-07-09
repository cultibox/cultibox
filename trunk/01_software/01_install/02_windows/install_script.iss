; Script generated by the Inno Setup Script Wizard.
; SEE THE DOCUMENTATION FOR DETAILS ON CREATING INNO SETUP SCRIPT FILES!

#define MyAppName "Cultibox"
#define MyAppVersion "1.1.193"
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
OutputBaseFilename=setup_cultibox_{#MyAppVersion}
VersionInfoVersion={#MyAppVersion}
Compression=lzma
SolidCompression=yes
 ; Pas de warning si le dossier existe d�j�
DirExistsWarning=no
; Interdiction de changer le path
DisableDirPage=yes

[Languages]
Name: "english"; MessagesFile: "compiler:Default.isl"
Name: "french"; MessagesFile: "compiler:Languages\French.isl"

[code]
function InitializeSetup():boolean;
var
  ResultCode: integer;
begin
  if FileExists(ExpandConstant('{sd}\{#MyAppName}\unins000.exe')) then
  begin

    MsgBox('Sauvegarde de vos logs et programme puis d�sinstallation de votre ancien logiciel', mbInformation, MB_OK);
    Exec(ExpandConstant('{sd}\{#MyAppName}\xampp\xampp_start.exe'), '', '', SW_SHOW,
       ewWaitUntilTerminated, ResultCode);

    ExtractTemporaryFile ('backup.bat');

    Exec (ExpandConstant ('{cmd}'), ExpandConstant ('/C copy backup.bat {sd}\{#MyAppName}\backup.bat'), ExpandConstant ('{tmp}'), SW_SHOW, ewWaitUntilTerminated, ResultCode);

    Exec (ExpandConstant ('{cmd}'), '/C backup.bat', ExpandConstant ('{sd}\{#MyAppName}'), SW_SHOW, ewWaitUntilTerminated, ResultCode);

    Exec(ExpandConstant('{sd}\{#MyAppName}\xampp\xampp_stop.exe'), '', '', SW_SHOW,
       ewWaitUntilTerminated, ResultCode);

    Exec(ExpandConstant('{sd}\{#MyAppName}\unins000.exe'), '/SILENT /NOCANCEL', '', SW_SHOW,
       ewWaitUntilTerminated, ResultCode);
  end;
  Result := True;
end;

[Files]
; Backup file. Used in pre install
Source: "F:\Cultibox_web\01_software\01_install\02_windows\backup.bat"; \
  DestDir: "{app}\run"; \
  DestName: "backup.bat"; \
  Flags: ignoreversion recursesubdirs createallsubdirs
; load file. Used in post install
Source: "F:\Cultibox_web\01_software\01_install\02_windows\load.bat"; \
  DestDir: "{app}\run"; \
  Flags: ignoreversion recursesubdirs createallsubdirs
Source: "F:\Cultibox_web\01_software\01_install\01_src\01_xampp\*"; \
  DestDir: "{app}\xampp"; \
  Flags: ignoreversion recursesubdirs createallsubdirs
Source: "F:\Cultibox_web\01_software\01_install\01_src\02_sql\*"; DestDir: "{app}\xampp\sql_install"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "F:\Cultibox_web\01_software\01_install\01_src\03_sd\*"; DestDir: "{app}\sd"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "F:\Cultibox_web\01_software\01_install\01_src\04_run\*"; DestDir: "{app}\run"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "F:\Cultibox_web\02_documentation\02_userdoc\*"; DestDir: "{app}\doc"; Flags: ignoreversion recursesubdirs createallsubdirs
Source: "F:\Cultibox_web\01_software\01_install\02_windows\cultibox.bat"; \
  DestDir: "{app}"; \
  Flags: ignoreversion
Source: "F:\Cultibox_web\01_software\01_install\02_windows\httpd.conf"; DestDir: "{app}\xampp\apache\conf"; Flags: ignoreversion
Source: "F:\Cultibox_web\01_software\01_install\01_src\03_sd\firm.hex"; DestDir: "{app}\xampp\htdocs\cultibox\tmp"; Flags: ignoreversion
; NOTE: Don't use "Flags: ignoreversion" on any shared system files

[Icons]
Name: "{group}\Cultibox"; Filename: "{app}\cultibox.bat"; Comment: "Run cultibox"; IconFilename: "{app}\sd\cultibox.ico"; AppUserModelID: "Cultibox.Cultibox"
Name: "{group}\{cm:UninstallProgram,{#MyAppName}}"; Filename: {uninstallexe}; Comment: "Uninstall cultibox"

[Run]
Filename: "{app}\xampp\setup_xampp.bat";Description: "Change path"
Filename: "{app}\xampp\xampp_start.exe";Description: "Run Xampp";
Filename: "{app}\xampp\mysql\bin\mysqladmin.exe"; \
  Parameters: " -u root -h localhost password cultibox"; \
  WorkingDir: "{app}"; \
  Description: "Change root password";
Filename: "{app}\xampp\mysql\bin\mysql.exe"; \
  Parameters: " -u root -h localhost -pcultibox -e ""source xampp\sql_install\user_cultibox.sql""" ; \
  WorkingDir: "{app}"; \
  Description: "Install user base";
Filename: "{app}\xampp\mysql\bin\mysql.exe"; \
  Parameters: " -u root -h localhost -pcultibox -e ""source xampp\sql_install\joomla.sql"""; \
  WorkingDir: "{app}"; \
  Description: "Install joomla base";
Filename: "{app}\xampp\mysql\bin\mysql.exe"; \
  Parameters: " -u root -h localhost -pcultibox -e ""source xampp\sql_install\cultibox.sql"""; \
  WorkingDir: "{app}"; \
  Description: "Install cultibox base";
Filename: "{app}\xampp\mysql\bin\mysql.exe"; \
  Parameters: " -u root -h localhost -pcultibox -e ""source xampp\sql_install\fake_log.sql"""; \
  WorkingDir: "{app}"; \
  Description: "Install log base";
Filename: "{cmd}"; \
  Parameters: "/C ""{app}\run\load.bat"""; \
  WorkingDir: "{app}\run"; \
  Description: "Install log user base";
Filename: "{app}\xampp\xampp_stop.exe";Description: "Kill Xampp";

[UninstallDelete]
Type: filesandordirs; Name: "{app}\xampp"
