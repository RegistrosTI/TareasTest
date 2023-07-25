<?php
$gantt['factor']            = array('min' => 1, 'max' => 100, 'valor' => 70);
$gantt['altura lineas']     = 20;
$gantt['inicio gantt']      = 91;
$gantt['grid']              = true;
$gantt['ancho']             = 0;
$gantt['lista_usuarios']    = null;
$gantt['horas']             = array('Inicio' => 1, 'Fin' => 1);
$gantt['fechas']            = array('Inicio' => spb_gantt_strtotime(date("d/m/Y",strtotime('monday this week'))), 'Fin' => spb_gantt_strtotime(date('d/m/Y', strtotime(date("Y-m-t",strtotime('+1 month')) . ' + '.(7-date("w",spb_gantt_strtotime(date("t/m/Y",strtotime('+1 month'))))).' days'))));
//$gantt['fechas']            = array('Inicio' => spb_gantt_strtotime("22/12/14"), 'Fin' => spb_gantt_strtotime("24/12/14"));

 


	
		


