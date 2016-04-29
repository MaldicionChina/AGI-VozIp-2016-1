#!/usr/bin/php -q
<?php
/**
 * @package phpAGI_examples
 * @version 2.0
 */
     
set_time_limit(30);
$param_error_log = '/tmp/udea.log';
$param_debug_on = 1;
require('phpagi.php');
error_reporting(E_ALL);

  
$agi = new AGI();
$agi->answer();
   
$agi->text2wav("Bienvenido al servicio telefonico de la plataforma mares");
$agi->text2wav("mediante esta plataforma podra consultar notas y solicitar revisiones");
require("deficiones_mares.inc");
$link = mysql_connect(MAQUINA, USUARIO,CLAVE); 

if(!$link) { // Hubo fallos en la conexión a la base de datos
    $agi->text2wav("Estamos presentando errores, por favor contacte un administrador");
    die('No pudo conectarse: ' . mysql_error());
}

mysql_select_db(DB, $link); 

$repeticiones = 5;

do
 {
   
   $agi->text2wav('Ingrese su numero de cedula y luego presione numeral , presione 99 para salir de llamada');
   $result = $agi->get_data('beep', 5000, 15);
   sleep(1);
   $cedula = $result['result'];

   $resultNombre = mysql_query(" SELECT * FROM estudiantes WHERE nroCedula=".$cedula, $link);
           
   if( $rowNombre = mysql_fetch_array($resultNombre) ){ // Existe algún registro con la cedula ingresada ?   
        $nombre = $rowNombre['nombre'];	
        
        
        $flagUserService = 0;
        $flagCounter = 0;
        do
        {
            $agi->text2wav("$nombre bienvenido a mares");  
            sleep(1);
            
            $agi->text2wav("Para consular notas presione 1, para solicitar revision de una materia presiones 2");
            sleep(1);
            $resultServicio = $agi->get_data('beep', 5000, 2);
            sleep(1);
            $servicio = $resultServicio['result'];
            
            if($servicio == 1){ // ¿ Eligio consultar notas ?

                $agi->text2wav("Si conoce el codigo de la materia ingreselo y presione numeral sino en segundos escuchara todas sus materias");
                sleep(1);
                $resultCodMateria = $agi->get_data('beep', 5000, 2);
                sleep(1);
                $codigoMateria = 0;
                $codigoMateria = $resultCodMateria['result'];
                
                if($codigoMateria != 0) { // Ingreso algún código de materia ? 
                    $materia = mysql_query(" SELECT nombreMateria,idMateria,nota FROM materia WHERE fk_nroCedula=".$cedula." AND idMateria=".$codigoMateria, $link);
                    
                    if($rowMateria = mysql_fetch_array($materia)){ // ¿ Existe la materia con ese código ?  
                    
                        $nombreMateria = $rowMaterias['nombreMateria'];
                        $idMateria = $rowMaterias['idMateria'];
                        $notaMateria = $rowMaterias['nota'];
                        sleep(1);
                    
                        $agi->text2wav("Materia ".$rowMaterias['nombreMateria']." Codigo ".$idMateria." Nota ".$notaMateria);
                        sleep(1);
                    }
                    $flagUserService = 1;
                
                } else {
                    $materias = mysql_query(" SELECT nombreMateria,idMateria,nota FROM materia WHERE fk_nroCedula=".$cedula, $link);  
                    
                    $agi->text2wav("A continuacione escuchara las materias que tiene matriculadas, el codigo y la nota");
                    sleep(1);

                    while ($rowMaterias = mysql_fetch_array($materias)){ 
                        $nombreMateria = $rowMaterias['nombreMateria'];
                        $idMateria = $rowMaterias['idMateria'];
                        $notaMateria = $rowMaterias['nota'];
                    
                        $agi->text2wav("Materia ".$nombreMateria." Codigo ".$idMateria." Nota ".$notaMateria);
                        sleep(1);
                    }  
                    
                    $flagUserService = 1;
                }
            } else if ($servicio == 2) { // ¿ Eligio solicitar revision materia ?
            
            
            $counterIngresoCodigoMateria = 0;            
            do{
                
                $agi->text2wav("Por favor ingrese el codigo de la materia de la cual desea pedir resivion");
                sleep(1);
                $resultCodMateriaRevision = $agi->get_data('beep', 5000, 2);
                sleep(1);
                $codigoMateriaRevision = 0;
                $codigoMateriaRevision = $resultCodMateriaRevision['result'];
                
                    if($codigoMateriaRevision != 0) { // ¿ Ingresó algún código de materia ?
                        $materiaResivion = mysql_query(" SELECT nombreMateria,idMateria,nota FROM materia WHERE fk_nroCedula=".$cedula." AND idMateria=".$codigoMateriaRevision, $link);
                        
                        if($rowMateriaRevision = mysql_fetch_array($materiaRevision)){ // ¿ Existe la materia con ese código
                            
                                $nombreMateriaRevision = $rowMateriaRevision['nombreMateria'];
                                $textoRevision = "NADA";
                                
                                $agi->text2wav("Usted ha seleccionado ".$nombreMateriaRevision);
                                sleep(1);
                                $agi->text2wav("Presione 1 para pedir revision por inconformidad, presione 2 para pedir revision para correccion");             
                                
                                $resultModoRevision = $agi->get_data('beep', 5000, 2);
                                sleep(1);
                                $modoRevision = $resultModoRevision['result'];
                                
                                if($modoRevision == 1) { // ¿ Es revision por inconformidad ?
                                
                                    $textoRevision = "INCONFORMIDAD";
                                                                
                                } else if ($modoRevision == 2) { // ¿ Es revision para correccion ?
                                
                                    $textoRevision = "CORRECCION";
                                        
                                } else {
                                
                                }   
                                
                                if($textoRevision != "NADA"){
                                    $sql = "INSERT INTO revision ".
                                        "(fk_idMateria, motivoRevision) ".
                                        "VALUES ".
                                        "(".$rowMateriaRevision['idMateria'].",".$textoRevision.")";
                                    
                                    $retval = mysql_query( $sql, $link );
                                    
                                    if(! $retval )
                                    {
                                        die('No se puedo insertar la informacion: ' . mysql_error());
                                    } else {
                                        $agi->text2wav("Revision solicitada con exito el prontamente recibira un correo con la hor ay fecha para su revision");
                                    }
                                }
                            
                        } else {
                            $agi->text2wav("El codigo de la materia no existe");
                        }   
                        
                        $codigoMateriaRevision = 1;
                        
                    } else {
                        $agi->text2wav("No ingreso codigo de materia");
                    }
                    $counterIngresoCodigoMateria = $counterIngresoCodigoMateria + 1;
                }while(($codigoMateriaRevision == 1) && ($counterIngresoCodigoMateria < $repeticiones));
                
            } else {
                $agi->text2wav("Elija un servicio");
            }
            
            $flagCounter = $flagCounter + 1;
            sleep(1);
       } while(($flagUserService == 0) && ($flagCounter < $repeticiones));    
   }

} while($keys != '99');


 $agi->text2wav('Hasta Pronto');
 $agi->hangup();
?>
