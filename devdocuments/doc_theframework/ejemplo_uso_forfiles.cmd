//Borra los ficheros de m�s de un mes de antig�edad
"D:\software\win64app\dts\forfiles" -p"D:\software\win64app\dts\Thyssen\Thyssen\logs" -s -m*.* -d-30 -c"cmd /c del @FILE"
