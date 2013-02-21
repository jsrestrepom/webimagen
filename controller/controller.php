<?php
	//echo md5('jsrm');
	
	require_once ('../model/class/BaseDatos_postgresql.php');
	require_once ('../model/class/Usuario.php');
	
	$db = new BaseDatos();
    $usuario = new Usuario();

	session_start();


	if ( isset( $_SESSION['user'] ) ) {
		$usuario = $_SESSION['user'];
        if (isset($_POST['bttn_logout'])) {
            session_destroy();
            header ("Location: ../view/login.html");exit();
        } else if ( isset($_POST['bttn_upload']) ) {
            if ((($_FILES['upload_photo']['type'] == "image/gif") || ($_FILES['upload_photo']['type'] == "image/jpeg") || ($_FILES['upload_photo']['type'] == "image/png") || ($_FILES['upload_photo']['type'] == "image/pjpeg")) && ($_FILES['upload_photo']['type'] < 20000)) {
                $username = $_SESSION['user']->getNombre();
                $id_user = $_SESSION['user']->getId();
                $tmp = $_FILES['upload_photo']['tmp_name'];
                $extension = end(explode(".", $_FILES['upload_photo']['name']));
                $date_imagen = time();
                $new_name_imagen = "img-$date_imagen";
                $path = $_SERVER['DOCUMENT_ROOT'] . "/photos/$username/$new_name_imagen.$extension";
                if( move_uploaded_file($tmp, $path) ) {
                    $query = "
                        INSERT INTO tbl_imagen
                        (cod_imagen,id_user,name_imagen,date_imagen)
                        VALUES
                        (nextval('seq_tbl_imagen_cod'),$id_user,'$new_name_imagen',$date_imagen)
                    ";
                    $db->query ( $query );
                }
            }//exit();
        }        
        header ("Location: ../view/photos.html");//exit();    
	} else {
		if ( isset ( $_POST['username'] ) && isset ( $_POST['password'] ) ) {
			$username = $_POST['username'];
			$password = md5($_POST['password']);
			$query = "
					SELECT 
					 	id_user,username_user
					FROM
						tbl_user
					WHERE
						username_user='$username'
					AND
						password_user='$password'
					LIMIT 1	
				";
			$db->query( $query );
			if ( $db->numRows() > 0 ) {
				$datos = $db->fetchArray();
				session_cache_expire(1800);			
				$sessionId = session_id();
				$ip = $_SERVER["REMOTE_ADDR"];
				$usuario->setNombre( $datos['username_user'] );
				$usuario->setCodigo( $datos['id_user'] );
				$usuario->setSession( $sessionId );
				$usuario->setIp( $ip );			
							
				$_SESSION['user'] = $usuario;
				header ("Location: ../view/photos.html");
			} else {
				header ("Location: ../view/login.html?");
				echo "Usuario y/o Contrase&ntilde;a invalidos";
			}
		} else {
			header ("Location: ../view/login.html");
		}
	}
	
?>