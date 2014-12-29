@ECHO OFF

SET sThisName=blacklists_updating
REM Almacen los tipos de entorno prueba y produccion
REM ::prueba o produccion
SET sEnviroment=prueba 

REM 19/01/2013 http://stackoverflow.com/questions/1192476/windows-batch-script-format-date-and-time
REM DEMO: SET sFechaHoy=%date:~-4%_%date:~3,2%_%date:~0,2%__%time:~0,2%_%time:~3,2%_%time:~6,2%
SET sFechaHoy=%date:~-4%%date:~3,2%%date:~0,2%


ECHO Entorno %sEnviroment%
IF %sEnviroment%==produccion GOTO mrkProduccion
IF %sEnviroment%==prueba GOTO mrkPrueba
GOTO :EOF

REM Entorno de produccion
:mrkProduccion
    ECHO "Entorno Produccion"
    SET sParamController=blacklists
    SET sParamMethod=updating
    SET sPhpFile=index.php
    
    SET sPathRoot=C:\xampp\htdocs\
    SET sProjectFolder=tasks
    SET sPathPhp=C:\xampp\php\
GOTO mrkRun

REM Entorno de pruebas
:mrkPrueba
    ECHO "Entorno prueba"
    SET sParamController=blacklists
    SET sParamMethod=updating
    SET sPhpFile=index.php
   
    SET sPathRoot=C:\inetpub\wwwroot\
    SET sProjectFolder=proy_tasks
    SET sPathPhp=c:\php5.2\
GOTO mrkRun

REM Fin del script
:mrkRun
SET sPathLog=%sPathRoot%%sProjectFolder%\logs\shellscripts\%sThisName%_%sFechaHoy%.log
ECHO.Ejecutando %sPhpFile% ...
ECHO.
REM todos los "echo" realizados en el controlador se vuelcan en el log
%sPathPhp%php.exe %sPathRoot%%sProjectFolder%\the_public\%sPhpFile% %sParamController% %sParamMethod% -debug >> %sPathLog%
ECHO %sPathPhp%php.exe %sPathRoot%%sProjectFolder%\the_public\%sPhpFile% %sParamController% %sParamMethod% LOGS: %sPathLog%

ECHO.
ECHO.fin: %sThisName%.cmd


REM Llamar prueba: 
REM cd C:\Inetpub\wwwroot\proy_tasks\shellscripts\windows
REM blacklists_updating.cmd

REM Llamar produccion: 
REM cd C:\xampp\htdocs\tasks\shellscripts\windows
REM blacklists_updating.cmd