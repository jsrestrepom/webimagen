<?php
	
	
	require_once ('../model/class/BaseDatos_postgresql.php');
	require_once ('../model/class/Usuario.php');
	
	$db = new BaseDatos();
    //$usuario = new Usuario();

	session_start();


	if ( isset( $_SESSION['user'] ) ) {
		$usuario = $_SESSION['user'];
        if (isset($_POST['bttn_logout'])) {
            session_destroy();
            header ("Location: ../view/login.php");
            exit();
        } else if ( isset($_POST['bttn_upload']) ) {var_dump($_FILES);
            if ( ($_FILES['upload_photo']['type'] == "image/gif") || ($_FILES['upload_photo']['type'] == "image/jpeg") || ($_FILES['upload_photo']['type'] == "image/png") || ($_FILES['upload_photo']['type'] == "image/pjpeg") ) {
                $upload_max_filesize = ini_get('upload_max_filesize');
                $upload_max_filesize_bytes = return_bytes($upload_max_filesize);
                if ( $_FILES['upload_photo']['size'] < $upload_max_filesize_bytes ) {       // $upload_max_filesize_bytes: valor maximo de un archivo en bytes, segun configuracion
                    $username = $_SESSION['user']->getNombre();
                    $id_user = $_SESSION['user']->getId();
                    $tmp = $_FILES['upload_photo']['tmp_name'];
                    $extension = end(explode(".", $_FILES['upload_photo']['name']));
                    $date_imagen = time();
                    $new_name_imagen = "img-$date_imagen";
                    $path = $_SERVER['DOCUMENT_ROOT'] . "/webimagen/photos/$username/$new_name_imagen.$extension";
                    if( move_uploaded_file($tmp, $path) ) {
                        $query = "
                            INSERT INTO tbl_imagen
                            (cod_imagen,id_user,name_imagen,date_imagen,type_imagen)
                            VALUES
                            (nextval('seq_tbl_imagen_cod'),$id_user,'$new_name_imagen',$date_imagen,'$extension')
                        ";
                        $db->query ( $query );
                    }
                } else {
                    // foto excede el tamaño
                }
            }//exit();
        }        
        header ("Location: ../view/startPage.php");
        //exit();    
	} else {
		if ( isset ( $_POST['username'] ) && isset ( $_POST['password'] ) ) {
			$username = $_POST['username'];
			$password = md5( $_POST['password'] );
            header("Location: ../model/model.php?usr=$username&pwd=$password" );
            exit();
        }
        header ("Location: ../view/login.php");
        exit();
	}
	
    // dado un valor en Gb, Mb o Kb restorna el valor en Bytes
    function return_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        switch($last) {
            // El modificador 'G' está disponble desde PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }
    
?>