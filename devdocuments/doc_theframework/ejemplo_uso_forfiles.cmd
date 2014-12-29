//Borra los ficheros de más de un mes de antigüedad
"D:\software\win64app\dts\forfiles" -p"D:\software\win64app\dts\Thyssen\Thyssen\logs" -s -m*.* -d-30 -c"cmd /c del @FILE"
