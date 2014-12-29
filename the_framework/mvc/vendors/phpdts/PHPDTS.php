<?php
/**
 * PHPDTS v.1.0
 * 
Copyright (C) 2013 MAM (migmam@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * Description of PHPDTS
 *
 * @author MAM
 * class DTSPHP
 */
define("LF","\n");

class PHPDTS {
    private $vlog;
    private $array_connections;
    private $array_processes;
    private $array_operations;
    private $array_tables;
    private $current_operation;
    private $path_files;
    
    
    
    public function __construct() {
        //Inicializamos los arrays que vamos a utilizar
        $this->array_connections = array();
        $this->array_processes   = array();
        $this->array_operations  = array();
        $this->array_tables      = array();
        $this->path_files        = "";
        
        //$this->vlog = new cLog("path");
      
    }
    public function configPath($path)
    {
        $this->path_files = $path;
    }
    //--------------------------------------------------------------------------
    /*
     * Carga fichero de conexión
     * Se puede hacer publico este método y cargar desde fuera de la clase
     * ejemplo: $newObj->loadConnection("conn_conexion1.xml");
     */
    private function loadConnection($connection_file)
    {
        

         $xml_connection=simplexml_load_file($this->path_files.$connection_file);
         $name      =   (string) $xml_connection->name;
         $host      =   (string) $xml_connection->host;
         $engine    =   (string) $xml_connection->engine;
         $user      =   (string) $xml_connection->user;
         $password  =   (string) $xml_connection->password;
         $database  =   (string) $xml_connection->database;
         $local_connection = array(
         //   "name"      =>  $name,
            "host"      =>  $host,
            "engine"    =>  $engine,
            "user"      =>  $user,
            "password"  =>  $password,
            "database"  =>  $database,
           );
          //print_r($local_connection);
          //array_push($this->array_connections,$local_connection);
          $this->array_connections[$name]=$local_connection;
         
         //echo $xml_connection["logfile"];
         
    }
    //--------------------------------------------------------------------------    
    /*
     * Carga fichero de proceso
     */
    public function loadProcess($process_file)
    {
         $xml_operation=simplexml_load_file($this->path_files.$process_file);
         foreach($xml_operation->process as $data)
         {
            $operation     =   (string) $data->operation;
            $success       =   (string) $data->success;
            $failure       =   (string) $data->failure;
            $completion    =   (string) $data->completion;
            $process = array(
                "operation"    =>  $operation,
                "success"      =>  $success,
                "failure"      =>  $failure,
                "completion"   =>  $completion,
            );
            //Rules to avoid a non-ending call
            //An operation can't call itself
            //An operation can call to an operation already loaded
            foreach ($this->array_processes as $keyprocess=>$valueprocess)
            {
                if($valueprocess["operation"] == $success || $valueprocess["operation"] == $failure || $valueprocess["operation"] == $completion )
                {
                    die("An operation can't call other operation already loaded");
                }
                    
            }
            if($operation != $success && $operation != $failure && $operation != $completion)
                $this->array_processes[]=$process;
            else
                die("An operation can't call itself");
             
             
         }
         //print_r($process);
         //echo $xml_connection["logfile"];
         
    }
    //--------------------------------------------------------------------------    
    /*
     * Carga fichero de operación
     * Se puede hacer publico este método y cargar desde fuera de la clase
     */
    private function loadOperation($operation_file)
    {
         $fields = array();

         $xml_operation=simplexml_load_file($this->path_files.$operation_file);
         $name              =   (string) $xml_operation["name"];
         $type              =   (string) $xml_operation["type"];
         $connection        =   (string) $xml_operation["connection"];
         $output_table      =   (string) $xml_operation["output_table"];
         $input_table       =   (string) $xml_operation["input_table"];
         $file              =   (string) $xml_operation["file"];
         $update            =   (string) $xml_operation["update_if_exists"];
         $separator         =   (string) $xml_operation["separator"];
         $truncate_table    =   (string) $xml_operation["truncate_table"];
         $lengths           =   explode(",",(string) $xml_operation["lengths"]);
         $sentence = "";
         //print_r($xml_operation);
         switch($type)
         {
            case "sql":
                $sentence   =   (string) $xml_operation->sentence;
            break;
        
            case "input_file":
                
                $field  = array();
                foreach($xml_operation as $data)
                {
                    $column = (string) $data["column"];
                    $field["field"] = (string) $data;
                    $fields[$column]     = $field;
                     
                }
            break;
            
            case "output_file":
               
                $field  = array();
                foreach($xml_operation as $data)
                {
                    $column = (string) $data["column"];
                    //Export attributes
                    $field["mask"]   = (string) $data["mask"];
                    $field["align"]  = (string) $data["align"];
                    //End Export attributes
                    $field["field"] = (string) $data;
                    $fields[$column]     = $field;
                     
                }
            break;
         }
  
         $local_operation = array(
           // "name"              =>  $name,
            "sentence"          =>  $sentence,
            "type"              =>  $type,
            "connection"        =>  $connection, //Only used in sql type. In other type of operations it is defined in the corresponding table file
            "input_table"       =>  $input_table,
            "output_table"      =>  $output_table,
            "file"              =>  $file,
            "update"            =>  $update,
            "separator"         =>  $separator,
            "truncate_table"    =>  $truncate_table,
            "lengths"           =>  $lengths,
            "fields"            =>  $fields,
           );
          print_r($local_operation);
          $this->array_operations[$name]=$local_operation;
          //array_push($this->array_operations,$local_operation);
         
         //echo $xml_connection["logfile"];
         
    }
     //--------------------------------------------------------------------------    
    /*
     * Carga fichero de operación
     * Se puede hacer publico este método y cargar desde fuera de la clase
     */
    private function loadTable($table_file) 
    {
         $fields = array(); 

         $xml_table=simplexml_load_file($this->path_files.$table_file);
         $name              =   (string) $xml_table["name"];
         $table_name        =   (string) $xml_table["table_name"];
         $connection        =   (string) $xml_table["connection"];

          $field  = array();
          foreach($xml_table as $data) 
          {

                $number          =  (string) $data["number"]; //Attributes
                $field["name"]   =  (string) $data->name; //elements
                $field["type"]   =  (string) $data->type; 
                $field["length"] =  (string) $data->length; 
                $field["pk"]     =  (string) $data->pk; 
                $fields[$number] = $field;
          }
          

  
         $local_table = array(
            "table_name"        =>  $table_name, 
            "connection"        =>  $connection, 
            "fields"            =>  $fields,
           );
          //print_r($local_table);// exit;
          $this->array_tables[$name]=$local_table; 
          //Index name could be different than the table name.
          //This is because you can have same table names (from different BDs) so the best way to manage them
          //is with another associated name.
          //
          //array_push($this->array_tables,$local_table);
          print_r($this->array_tables);

         
    }
    /*
     * Ejecuta el proceso cargado
     */
    //--------------------------------------------------------------------------
    public function execute_global()
    {
        echo LF."PHPDTS v.1.0".LF;
        
         $this->current_operation = 0;
         $this->execute_operation();
         //print_r($this->array_processes);
        // print_r($this->array_operations);
         die("Done".LF);
      
         
    }
    
    /*
     * Ejecuta cada una de las operaciones de proceso cargado
     */
    //--------------------------------------------------------------------------
     private function execute_operation()
    {
         $local_current_operation = $this->current_operation;
         echo "Executing proccess...".$this->array_processes[$this->current_operation]["operation"].LF;
         
         //Aquí se cargaría el fichero del proceso.  
         //Comprobar antes si no se ha cargado ya.
         if(!array_key_exists($this->array_processes[$this->current_operation]["operation"], $this->array_operations))
             $this->loadOperation("op_".$this->array_processes[$this->current_operation]["operation"].".xml");
         
         $current_operation = $this->array_operations[$this->array_processes[$this->current_operation]["operation"]];
         switch($current_operation["type"])
         {
             case "input_file":
                 $this->execute_operation_input_file();
             break;
             case "output_file":
                 $this->execute_operation_output_file();
             break;
             case "sql":
                 $this->execute_operation_sql();
             break;
                 
             
         }
         
         
        
         foreach($this->array_processes as $key=>$value)
         {
             if($value["operation"]==$this->array_processes[$this->current_operation]["completion"])
             {
                 $this->current_operation =  $key;
                 break;
             
             }elseif($value["operation"]==$this->array_processes[$this->current_operation]["success"]){
                 $this->current_operation =  $key;
                 break;
                 
             }
         }
         if($local_current_operation != $this->current_operation)
         {
            echo "Siguiente proceso: ".$this->array_processes[$this->current_operation]["operation"].LF;
            $this->execute_operation();
         }else{
             echo "Fin de los procesos";
             return true;
         }
         
    }
    //--------------------------------------------------------------------------    
    private function execute_operation_input_file()
    {
        $dataFromCsv = array();
        $row = 0;
        $csv_fields = 0;
        $resourceFile = null;
        $finalData = array();
        
        echo "Executing input file".LF;
        
        $current_operation = $this->array_operations[$this->array_processes[$this->current_operation]["operation"]];
        //Text file with defined separator
        if($current_operation["separator"]!="")
        {
            $row = 1;
            $columns_values = -1;
            if (($resourceFile = fopen($this->path_files.$current_operation["file"], "r")) !== FALSE) {
                while (($dataFromCsv = fgetcsv($resourceFile, 1000, $current_operation["separator"])) !== FALSE) {
                    $csv_fields = count($dataFromCsv);
                    
                    $row++;
                    $field_values = array();
                    for ($c=0; $c < $csv_fields; $c++) {
                        $field_values[$c+1] = $dataFromCsv[$c];
                        //echo $data[$c] .LF;
                    }
                    //We check if the previous number of values is equal to the current one
                    if($columns_values != -1){
                        if($csv_fields != $columns_values)
                        {
                            die("Number of values is different in csv file");
                        }else{
                           $columns_values = $csv_fields;
                        }
                    }else{
                        $columns_values = $csv_fields;
                    }
                    array_push($finalData,$field_values);
                }
                fclose($resourceFile);
            }
        }else{ 
            //Text file with differents lengths
        }
        //At this point we have the csv data loaded in the array $finalData
        
        
        
        //The table is loaded (unless it has not been already loaded).
        if(!array_key_exists($current_operation["output_table"], $this->array_tables))
            $this->loadTable("tbl_".$current_operation["output_table"].".xml");
        
        
         $columns=$this->array_tables[$current_operation["output_table"]]["fields"];
        //We have values, table fields and columns definition.
        //
        //Let's check if everything have the same number of items
        if(count($current_operation["fields"])!=$columns_values || count($columns)!=$columns_values || count($current_operation["fields"])!=count($columns))
            die("Number of csv values is different than number of fields defined");
        
        //Now we begin to create the sql sentence
        $sql_text_columns = "INSERT INTO ".$this->array_tables[$current_operation["output_table"]]["table_name"]. " (";

        //print_r($columns);
        //print_r($current_operation);
        //
        //First the table columns
        foreach($current_operation["fields"] as $key_column=>$key_field)
        {
            $sql_text_columns .= $columns[$key_field["field"]]["name"].",";

        }
        $sql_text_columns = substr($sql_text_columns, 0, -1); //Delete the last comma
        //Now the values
        
        foreach($finalData as $values){
            $sql_text_values = ") values (";
            foreach($current_operation["fields"] as $key_column=>$key_field)
            {
            
            
                $sql_text_values .= "'".$values[$key_column]."',";
            
            }
            $sql_text_values = substr($sql_text_values, 0, -1).")"; //Delete the last comma
            $sql_final[] = $sql_text_columns.$sql_text_values;
        }
        //print_r($finalData);
        //print_r($sql_final);
        //
        //Now we have the sql sentences ready to be executed in the array $sql_final
        //So let's execute the setences!!
        
        $connection = $this->array_tables[$current_operation["output_table"]]["connection"]; 
        $conn_link = $this->connect($connection);
        
        foreach ($sql_final as $sentence){
            $this->execute_sql($conn_link, $sentence, $this->array_connections[$connection]["engine"]);
        }
        
        
        
    }
    //--------------------------------------------------------------------------
    private function execute_operation_output_file()
    {
        echo "Executing output file".LF;;
    }
    //--------------------------------------------------------------------------
    private function execute_operation_sql()
    {
        echo "Executing sql".LF;
        $current_operation = $this->array_operations[$this->array_processes[$this->current_operation]["operation"]];
        $connection = $current_operation["connection"]; 
        $conn_link = $this->connect($connection);
        $this->execute_sql($conn_link, $current_operation["sentence"], $this->array_connections[$connection]["engine"]);
        
    }
    //--------------------------------------------------------------------------
    //Se pasa el nombre de la conexión
    private function connect($connection)
    {

        
        echo "Using connection: ".$connection.LF;
        
        //Se carga la conexión. Se comprueba antes si ya ha sido cargada.
        if(!array_key_exists($connection, $this->array_connections))
            $this->loadConnection("conn_".$connection.".xml");

        switch($this->array_connections[$connection]["engine"])
        {
            case "mysql":
                //echo "Using mysql".LF;
                $link = new mysqli($this->array_connections[$connection]["host"], $this->array_connections[$connection]["user"], $this->array_connections[$connection]["password"], $this->array_connections[$connection]["database"]);
                if ($link->connect_errno) {
                    echo "Fallo al contenctar a MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
                }else{
                    //Conectado
                }
            break;
            case "mssql":
                // Connect to MSSQL
                $link = mssql_connect($this->array_connections[$connection]["host"], $this->array_connections[$connection]["user"], $this->array_connections[$connection]["password"]);
                if (!$link || !mssql_select_db($this->array_connections[$connection]["database"], $link)) {
                    echo 'No se puede conectar o seleccionar una base de datos!';
                }else{
                    //Conectado
                    //mssql_query($this->array_operations[$this->array_processes[$this->current_operation]["operation"]]["sentence"]);
                }
            break;

        }
        return $link;
        
        
    }
    //--------------------------------------------------------------------------
    //Parámetros. Enlace conexión a BD, setencia SQL, motor.
    private function execute_sql($link,$sentence,$engine)
    {
          switch($engine)
        {
            case "mysql":
                    if(!$link->query($sentence))
                            echo $link->errno . " " . $link->error;
                
            break;
            case "mssql":
                   mssql_query($sentence);
            break;

        }
        
        
    }
    //--------------------------------------------------------------------------    
    


}
class cLog {
    private $file;
    public function __construct($path){
        $this->file = fopen($path, "a");
        
    }
    public function write($text)
    {
        fwrite($this->file, $text);
    }

}

?>
