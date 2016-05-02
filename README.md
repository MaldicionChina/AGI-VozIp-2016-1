# Montaje Aplicación AGI para consulta de Notas

Para incluir el aplicativo consultar notas en su sistema asterik debe
seguir los siguientes pasos

- copiar el archivo connsultarNotas.php al path /var/lib/asterisk/agi-bin/
en el servidor
- cambiar el usuario y el grupo del archivo
 - ``# chown asterisk.asterisk consultarNotas.php``
 - ``# chown asterisk.asterisk definiciones.inc``
- Dar Permisos a la ejecuación del AGI:
 - ``# chmod oug+x udea.php``
- Para probarlo se crea la extension virtual (1200, se puede cambiar) dentro del archivo de configuracion extensions_custom.conf. Para editarlo se debe entrar al Elastix por la siguiente ruta: PBX->Tools->File editor y luego buscar el archivo extensions_custom.conf y escribir al final lo siguiente:

  [ext-local-custom]
  
  exten => 1202,1,Answer()
  
  exten => 1202,n,AGI(udea.php)
  
  exten => 1202,n,Hangup() 
