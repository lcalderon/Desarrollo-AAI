<?
     
function FConsultarDatos_Axcelx($var_id_consulta, $var_nro_certificado, $var_id_user, $var_ip_usuario)
{
/*
        Construyendo los argumentos como XML String
*/

    
        $var_tipo_consulta="2";
        $var_cabecera="<STREAM><HEADER><ITRANSACCION>".$var_id_consulta."</ITRANSACCION><TOKEN>CF88E6AEDD9C790C47BDF6CEDB84E9FD</TOKEN><ID>10</ID></HEADER>";

        $var_body="<PARAMETERS><TIPOCONSULTA>".$var_tipo_consulta."</TIPOCONSULTA><NUMID/><NUMPOL/><NUMCERT>".$var_nro_certificado."</NUMCERT></PARAMETERS></STREAM>";

        $var_data_xml_argumentos=$var_cabecera.$var_body;
        $array_argumentos=array("xmlParam"=>$var_data_xml_argumentos);
        $oSoapClient = new SoapClient("http://200.48.86.34/WSSiniestrosWeb/services/ServiciosSiniestros/wsdl/ServiciosSiniestros.wsdl");
        $var_respuesta = $oSoapClient->consultaPoliza($array_argumentos);
        $var_data_xml=$var_respuesta->consultaPolizaReturn;

        $obj_datos = simplexml_load_string($var_data_xml);

        return $obj_datos;

}

function FConsultarDatos_Axcelx_NumidAsegurado($var_id_consulta, $var_nro_id_asegurado, $var_id_user, $var_ip_usuario)
{
        /*
        Construyendo los argumentos como XML String
        */

    
        $var_tipo_consulta="1";
        $var_cabecera="<STREAM><HEADER><ITRANSACCION>".$var_id_consulta."</ITRANSACCION><TOKEN>CF88E6AEDD9C790C47BDF6CEDB84E9FD</TOKEN><ID>10</ID></HEADER>";
        $var_body="<PARAMETERS><TIPOCONSULTA>".$var_tipo_consulta."</TIPOCONSULTA><NUMID>".$var_nro_id_asegurado."</NUMID><NUMPOL/><NUMCERT/></PARAMETERS></STREAM>";

        $var_data_xml_argumentos=$var_cabecera.$var_body;
        $array_argumentos=array("xmlParam"=>$var_data_xml_argumentos);
        $oSoapClient = new SoapClient("http://200.48.86.34/WSSiniestrosWeb/services/ServiciosSiniestros/wsdl/ServiciosSiniestros.wsdl");
        $var_respuesta = $oSoapClient->consultaPoliza($array_argumentos);
        $var_data_xml=$var_respuesta->consultaPolizaReturn;

        $obj_datos = simplexml_load_string($var_data_xml);


        return $obj_datos;

}

function FConsultarDatos_Axcelx_Cliente($var_id_consulta, $var_tipo_documento, $var_nro_documento, $var_id_user, $var_ip_usuario)
{
        /*
        Construyendo los argumentos como XML String
        */
    
        $var_tipo_consulta="1";
        $var_cabecera="<STREAM><HEADER><ITRANSACCION>".$var_id_consulta."</ITRANSACCION><ID>10</ID><TOKEN>CF88E6AEDD9C790C47BDF6CEDB84E9FD</TOKEN></HEADER>";
        $var_body="<PARAMETERS><TIPOCONSULTA>".$var_tipo_consulta."</TIPOCONSULTA><TIPOIDDOC>".$var_tipo_documento."</TIPOIDDOC><NUMIDDOC>".$var_nro_documento."</NUMIDDOC><NUMID/><NOMTER/><APETER/><APEMATTER/></PARAMETERS></STREAM>";

        $var_data_xml_argumentos=$var_cabecera.$var_body;
        $array_argumentos=array("xmlParam"=>$var_data_xml_argumentos);
        $oSoapClient = new SoapClient("http://200.48.86.34/WSSiniestrosWeb/services/ServiciosSiniestros/wsdl/ServiciosSiniestros.wsdl");
        $var_respuesta = $oSoapClient->consultaAsegurado($array_argumentos);
        $var_data_xml=$var_respuesta->consultaAseguradoReturn;

        $obj_datos = simplexml_load_string($var_data_xml);


        return $obj_datos;
}

function FConsultarDatos_Axcelx_ClienteNombres($var_id_consulta, $var_nombre, $var_ap_paterno, $var_ap_materno, $var_id_user, $var_ip_usuario)
{
        /*
        Construyendo los argumentos como XML String
        */
    
        $var_tipo_consulta="5";
        $var_cabecera="<STREAM><HEADER><ITRANSACCION>".$var_id_consulta."</ITRANSACCION><ID>10</ID><TOKEN>CF88E6AEDD9C790C47BDF6CEDB84E9FD</TOKEN></HEADER>";
        $var_body="<PARAMETERS><TIPOCONSULTA>".$var_tipo_consulta."</TIPOCONSULTA><TIPOIDDOC/><NUMIDDOC/><NUMID/><NOMTER>".$var_nombre."</NOMTER><APETER>".$var_ap_paterno."</APETER><APEMATTER>".$var_ap_materno."</APEMATTER></PARAMETERS></STREAM>";

        $var_data_xml_argumentos=$var_cabecera.$var_body;
        //echo $var_data_xml_argumentos;
        $array_argumentos=array("xmlParam"=>$var_data_xml_argumentos);
        $oSoapClient = new SoapClient("http://200.48.86.34/WSSiniestrosWeb/services/ServiciosSiniestros/wsdl/ServiciosSiniestros.wsdl");
        $var_respuesta = $oSoapClient->consultaAsegurado($array_argumentos);
        $var_data_xml=$var_respuesta->consultaAseguradoReturn;

        $obj_datos = simplexml_load_string($var_data_xml);
        
        return $obj_datos;
}
?>

