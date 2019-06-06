<?php
/*
	Plugin Name: JUGADORES EVENTOS
	Plugin URI: http://www.jj.com/
	description: Plugin realizado por Joser
	Version: 1.2
	Author: Joser
	Author URI: http://www.jj.com/
	License: GPL2
*/


/*
**
	DEBEMOS DEFINIR EL JS Y LOS ESTILOS
**
*/

add_action('wp_enqueue_scripts', 'callback_for_setting_up_scripts_ss');
function callback_for_setting_up_scripts_ss() {
  wp_enqueue_style( 'relacion', plugins_url( '/css/style.css', __FILE__ ) );
  wp_enqueue_style( 'relacion' );
}
wp_enqueue_style( 'relacion', plugins_url( '/css/style.css', __FILE__ ) );
wp_enqueue_style( 'relacion' );


//PRIMERO CARGAMOS LA HOJA PARA LOS JS DEL CLIENTE
/*
wp_enqueue_script('scrip-approval', plugins_url( '/js/app.js', __FILE__ ), array('jquery'));
wp_localize_script('scrip-approval', 'myScript', array(
    'pluginsUrl' => plugins_url(),
));
*/

add_action( 'wp_enqueue_scripts', 'ajax_scripts_12' );
function ajax_scripts_12() {
	/*
    wp_register_script( 'main-ajax', get_template_directory_uri() . '/assets/js/main-ajax.js', array(), '', true );
    $arr = array(
        'ajaxurl' => admin_url('admin-ajax.php')
    );
    wp_localize_script('main-ajax','ajax',$arr ); 
    wp_enqueue_script('main-ajax');
	*/

	wp_enqueue_script( 'keylimetec', plugins_url( '/js/app.js', __FILE__ ), array('jquery'), '1.0', true );

	wp_localize_script( 'keylimetec', 'postlove', array(
	'ajax_url' => admin_url( 'admin-ajax.php' )
	));
}
//add_action('wp_ajax_my_product_ajaxss', 'my_action_productos_v');
//add_action('wp_ajax_nopriv_my_product_ajaxss', 'my_action_productos_v');


/*
**
	CREAREMOS EL SHORTCODE QUE HARA TODO EL PROCESO
**
*/

function subir_usuarios(){
	global $wpdb;

?>
	<link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.1.3/darkly/bootstrap.min.css">

    <div class="container clearfix">
        <div id="content">
            <h1>Convert CSV To JSON</h1>
            <p>Click here to upload your CSV file:</p>
            <input type="file" id="csv" />
            <br />
            <br />

			<div class="form-group sp_season">
				<label for="inputTitle">Año</label>
				<?php  /*echo "<h1>SELECT t.* FROM wp_9_terms AS t WHERE t.term_id = (SELECT s.term_id FROM wp_9_term_taxonomy AS s WHERE s.taxonomy = 'sp_season'</h1>";*/ ?>
				<!--<input type="text" class="form-control" name="titulo" id="inputTitle" placeholder="Enter Title">-->
				<select class="form-control" name="sp_season">
					<option value="">Seleccione</option>
					<?php
						$equipos = $wpdb->get_results("SELECT s.term_id AS term_id, (SELECT t.name FROM {$wpdb->prefix}terms AS t WHERE t.term_id = s.term_id) AS name FROM {$wpdb->prefix}term_taxonomy AS s WHERE s.taxonomy = 'sp_season' ");

						foreach ($equipos as $key) {
							echo "<option value='{$key->term_id}'>{$key->name}</option>";
						}
					?>
					<!--
					<option value="otro">Otro</option>
					-->
				</select>
				<input type="hidden" class="form-control" name="sp_season_nuevo" placeholder="Año nuevo" />
				<small id="title" class="form-text text-muted">Año en que se hace la liga.</small>
			</div>

			<!-- IMAGEN DE LA FIRMA DEL INSTRUCTOR -->
			<div class="form-group sp_league">
				<label for="inputTitle">Competicion</label>
				<!--<input type="text" class="form-control" name="titulo" id="inputTitle" placeholder="Enter Title">-->
				<select class="form-control" name="sp_league">
					<option value="">Seleccione</option>
					<?php
						$equipos = $wpdb->get_results("SELECT s.term_id AS term_id, (SELECT t.name FROM {$wpdb->prefix}terms AS t WHERE t.term_id = s.term_id) AS name FROM {$wpdb->prefix}term_taxonomy AS s WHERE s.taxonomy = 'sp_league' ");

						foreach ($equipos as $key) {
							echo "<option value='{$key->term_id}'>{$key->name}</option>";
						}
					?>
					<!--<option value="otro">Otro</option>-->
				</select>
				<input type="hidden" class="form-control" name="sp_league_nuevo" placeholder="Competicion nueva" />
				<small id="title" class="form-text text-muted">Competicion (Apertura - Cierre).</small>
			</div>

			<br />
			<br />
            <p>Select your delimiter:</p>
            <select id="delimiter">
                <option id="comma" value=",">,</option>
                <option id="pipe" value="|">|</option>
            </select>
            <br />
            <br />
            <button class="btn btn-danger" id="convert">Convert</button>
            <br />
            <br />
            <textarea disabled id="json" class="textareasize"></textarea>
            <br/>
            <!--
            <button class="btn btn-danger" id="download">Download JSON Results</button>
            -->
        </div>
    </div>

<?php
	if(isset($_POST['accion_del_jugador_j']) && $_POST['accion_del_jugador_j'] != ""){

		$jugadores = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'sp_player'  ");
		foreach ($jugadores as $key) {
			$id_jugador = $key->ID;

			//YA CON EL ID DEL JUGADOR PROCEDEMOS A QUITAR TODAS SUS RELACIONES CON EQUIPOS
			$wpdb->delete(
					$wpdb->prefix.'postmeta', 
					array( 
						'post_id' 	=> $id_jugador,
						'meta_key' 	=> 'sp_team'
					) 
				);


			$wpdb->update( 
				$wpdb->prefix."postmeta",
				array( 
					'meta_value' => ""
				), 
				array( 
					'post_id' 	=> $id_jugador,
					'meta_key' 	=> 'sp_leagues'
					)
				);
		}


	}
?>
    <div style="clear: both;">
    	<form method="post">
    		<input type="text" value="borra_equipos_jugador" name="accion_del_jugador_j" />

    		<input class="submit" type="submit" value="Enviar" />
    	</form>
    </div>

<?php

}
add_shortcode('subirusuarios', 'subir_usuarios'); 



/*
**
	FUNCIONES DE AJAX
**
*/
add_action( 'wp_ajax_my_action_jugador', 'my_action_jugador' );
add_action( 'wp_ajax_nopriv_my_action_jugador', 'my_action_jugador' );
function my_action_jugador() {
    global $wpdb;

    $jugadores = $_POST['jugadores'];
	$sp_season = $_POST['sp_season'];
	$sp_league = $_POST['sp_league'];

	//echo $jugadores[0]['nombre']." ".$jugadores[0]['apellido']." ".$jugadores[0]['apellido_materno']." ".$jugadores[0]['email'];

    //
    for ($i=0; $i < count($jugadores); $i++) { 
		//email
		//elige_tu_equipo
		//nombre
		//apellido
		//apellido_materno
		//apodo
		
		$email				= $jugadores[$i]['email'];
		$elige_tu_equipo	= $jugadores[$i]['elige_tu_equipo'];
		$nombre				= $jugadores[$i]['nombre'];
		$apellido			= $jugadores[$i]['apellido'];
		$apellido_materno	= $jugadores[$i]['apellido_materno'];
		$apodo				= $jugadores[$i]['apodo'];
		$dorsalJ			= $jugadores[$i]['camisa'];
    	//echo $jugadores[$i]['email'];

    	//CONSULTAMOS ESTE USUARIO EN LA WEB
    	$nombre_j = $nombre." ".$apellido." ".$apellido_materno;

    	echo "<h1>{$nombre_j}</h1>";

    	$id_equipo = "";

    	//AQUI CONSULTAMOS EL ID DEL EQUIPOS
    	$equipos = $wpdb->get_results("SELECT p.* FROM {$wpdb->prefix}posts AS p WHERE p.post_type = 'sp_team' AND p.post_title = '{$elige_tu_equipo}' ");
		foreach ($equipos as $key) {
			$id_equipo = $key->ID;
		}

		//LUEGO DE ESTO CONSULTAMOS EL ANO
		$season = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}term_relationships AS r WHERE r.object_id = {$id_equipo} AND r.term_taxonomy_id = {$sp_season} ");
		if($season <= 0){//SI NO EXISTE ESTA TAXONOMIA LA CREAMOS
			$wpdb->insert( 
				$wpdb->prefix."term_relationships", 
				array( 
					'object_id' 		=> $id_equipo,
					'term_taxonomy_id' 	=> $sp_season,
					'term_order' 		=> '0'
				) 
			);

			//echo "relacionando";
		}

		//LUEGO DE ESTO CONSULTAMOS LA LIGA
		$league = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}term_relationships AS r WHERE r.object_id = {$id_equipo} AND r.term_taxonomy_id = {$sp_league} ");
		if($league <= 0){//SI NO EXISTE ESTA TAXONOMIA LA CREAMOS
			$wpdb->insert( 
				$wpdb->prefix."term_relationships", 
				array( 
					'object_id' 		=> $id_equipo,
					'term_taxonomy_id' 	=> $sp_league,
					'term_order' 		=> '0'
				) 
			);

			//echo "relacionando";
		}

		$id_jugador = 0;
		//LEUGO DE ESTO PROCEDEMOS A CONSULTAR SI ESTE JUGADOR EXISTE
		$equipos = $wpdb->get_results("SELECT p.* FROM {$wpdb->prefix}posts AS p WHERE p.post_type = 'sp_player' AND p.post_title = '{$nombre_j}' ");
		foreach ($equipos as $key) {
			$id_jugador = $key->ID;
		}

		//SI CON ESTE NOMBRE NO EXISTE PROCEDEMOS A REVISAR SI ESTE USUARIO YA CUENTA CON UN JUGADOR ASIGNADO
		if($id_jugador == 0){
			$id_usuario = 0;
			//PRIMERO DEBEMOS CONSULTAR EL ID DEL USUARIO
			$usuario = $wpdb->get_results("SELECT * FROM wp_users WHERE user_email = '{$email}' ");
			foreach ($usuario as $key) {
				$id_usuario = $key->ID;
			}

			echo "------------ Este jugador no lo encontramos (".$nombre_j.") (SELECT p.* FROM {$wpdb->prefix}posts AS p WHERE p.post_type = 'sp_player' AND p.post_title = '{$nombre_j}') ----------------";

			if($id_usuario != 0){
				//ESTO ES PORQUE EL USUARIO EXISTE
				$consultaJugador = $wpdb->get_results("SELECT pos.* FROM {$wpdb->prefix}posts AS pos WHERE pos.post_type = 'sp_player' AND pos.post_author = '{$id_usuario}' ");
				foreach ($consultaJugador as $key) {
					$id_jugador = $key->ID;

					echo " (El jugador existe como: {$id_jugador}) ";
					consultar_ligas_competicion_j($id_jugador, $sp_season, $sp_league, $dorsalJ, $id_equipo, $nombre_j);

				}

				//SI EL ID SIGUE SIENDO 0 ES PORQUE POR NADA DEL MUNDO SE ENCONTRO, DEBEMOS INSERTARLO
				//if($id_jugador == 0){
					$competicionJ = 0;
					$temporadaJ = 0;


					$competicionJ2 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}terms WHERE term_id = '".$sp_league."'");
			    	foreach ($competicionJ2 as $key) {
			    		$competicionJ = $key->name;
			    	}


			    	$competicionJ2 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}terms WHERE term_id = '".$sp_season."'");
			    	foreach ($competicionJ2 as $key) {
			    		$temporadaJ = $key->name;
			    	}

					$nombreCompleto = $nombre_j;
					$equipoJ = $id_equipo;
					$dorsalJ = $dorsalJ;
					$nacionalidadJ = "";
					$competicionJ = $competicionJ;
					$backCompeticion = $competicionJ;
					//echo "<h1>----> {$competicionJ}</h1>";
					$temporadaJ = $temporadaJ;


					$datosJugador = [];

					array_push($datosJugador, $nombreCompleto);
					array_push($datosJugador, $equipoJ);
					array_push($datosJugador, $dorsalJ);
					array_push($datosJugador, $nacionalidadJ);
					array_push($datosJugador, $competicionJ);
					array_push($datosJugador, $backCompeticion);
					array_push($datosJugador, $temporadaJ);

					$idRed = 7;
					if($wpdb->prefix == "wp_7_"){
						$idRed = 7;
					}else if($wpdb->prefix == "wp_8_"){
						$idRed = 8;
					}

					crearJugadorJj(1, $datosJugador, $idRed);
				//}

			}else{
				//ERROR SI EL USUARIO NO EXISTE
				echo "-------- (El usuario con el email ".$email." no existe) ----------";
			}
		}else{
			//ESTO ES SI EL JUGADOR SI SE ENCONTRO

			echo "pasa por aqui el usuario ({$nombre_j}) -> equipo: {$id_equipo}";

			//PROCEDEMOS A REVISAR SI PERTENECE A LA LIGA Y LA COMPETICION ADECUADAS
			consultar_ligas_competicion_j($id_jugador, $sp_season, $sp_league, $dorsalJ, $id_equipo, $nombre_j);
		}

    }

    //echo count($_POST['jugadores']);

    //return $jugadores['']
}


function consultar_ligas_competicion_j($id_jugador, $sp_season, $sp_league, $dorsalJ, $id_equipo, $nombre_j){
	global $wpdb;

	//REVISAMOS SI ESTE USUARIO TIENE RELACIONADO ESTE EQUIPO
	$equipo = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}postmeta AS r WHERE r.post_id = {$id_jugador} AND r.meta_key = 'sp_team' ");// AND r.meta_value = {$id_equipo}
	if($equipo <= 0){//SI NO EXISTE ESTA TAXONOMIA LA CREAMOS
		$wpdb->insert( 
			$wpdb->prefix."postmeta", 
			array( 
				'post_id' 		=> $id_jugador,
				'meta_key' 		=> 'sp_team',
				'meta_value' 	=> $id_equipo
			) 
		);

		//echo "relacionando";
	}else{
		$wpdb->update( 
			$wpdb->prefix."postmeta",
			array( 
				'meta_value' => $id_equipo
			), 
			array( 
				'post_id' 	=> $id_jugador,
				'meta_key' 	=> 'sp_team'
				)
			);
	}

	//LUEGO DE ESTO CONSULTAMOS EL ANO
	$season = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}term_relationships AS r WHERE r.object_id = {$id_jugador} AND r.term_taxonomy_id = {$sp_season} ");
	if($season <= 0){//SI NO EXISTE ESTA TAXONOMIA LA CREAMOS
		$wpdb->insert( 
			$wpdb->prefix."term_relationships", 
			array( 
				'object_id' 		=> $id_jugador,
				'term_taxonomy_id' 	=> $sp_season,
				'term_order' 		=> '0'
			) 
		);

		//echo "relacionando";
	}

	//LUEGO DE ESTO CONSULTAMOS LA LIGA
	$league = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}term_relationships AS r WHERE r.object_id = {$id_jugador} AND r.term_taxonomy_id = {$sp_league} ");
	if($league <= 0){//SI NO EXISTE ESTA TAXONOMIA LA CREAMOS
		$wpdb->insert( 
			$wpdb->prefix."term_relationships", 
			array( 
				'object_id' 		=> $id_jugador,
				'term_taxonomy_id' 	=> $sp_league,
				'term_order' 		=> '0'
			) 
		);

		//echo "relacionando";
	}

	//LUEGO CAMBIAMOS LA INFORMACION DE LA CAMISA
	#sp_number  Numero de la camiseta
	/*
	$wpdb->insert(
            $wpdb->prefix."postmeta",
            array(
                'post_id' 				=>	$id_jugador,
                'meta_key'				=>	'sp_number',
                'meta_value'			=>	$dorsalJ
              )
            );
    */
	$wpdb->update( 
			$wpdb->prefix."postmeta",
			array( 
				'meta_value' => $dorsalJ
			), 
			array( 
				'post_id' 	=> $id_jugador,
				'meta_key' 	=> 'sp_number'
				)
			);
	$tamano_equipo = count($id_equipo);

	$ligaDeljugador = 'a:2:{i:'.$sp_league.';a:1:{i:'.$sp_season.';s:'.$tamano_equipo.':"'.$id_equipo.'";}i:0;a:1:{i:'.$sp_season.';s:1:"1";}}';

	/*
    $wpdb->insert(
            "wp_{$idRed}_postmeta",
            array(
                'post_id'               =>  $idJugador,
                'meta_key'              =>  'sp_leagues',
                'meta_value'            =>  $ligaDeljugador
              )
            );
    */

    $wpdb->update( 
			$wpdb->prefix."postmeta",
			array( 
				'meta_value' => $ligaDeljugador
			), 
			array( 
				'post_id' 	=> $id_jugador,
				'meta_key' 	=> 'sp_leagues'
				)
			);

	echo "Jugador ".$id_jugador." ({$nombre_j}) actualizado";
}



function crearJugadorJj($userId, $datosJugador, $idRed = ""){
	//echo "<h1>Aqui si llega: ".$userId."</h1>";
	//VARIABLES NECESARIAS PARA EL INSERT
	date_default_timezone_set('UTC-5');
	$diaHoy = date("Y-m-d");

	#h:i:s
	$horaActual = date("H:i:s");
	$formatoDia = $diaHoy." ".$horaActual;
	global $wpdb;
	//echo "<h1>llegas {$wpdb->prefix}</h1>";
	//nombreCompleto
	//equipoJ
	//dorsalJ
	//nacionalidadJ
	//competicionJ
	//temporadaJ

	$nombreCompleto = $datosJugador[0];
	$equipoJ = $datosJugador[1];
	//COMENTAR ESTO YA QUE EN JLEAGUE SI ENVIA DIRECTAMENTE EL ID DEL EQUIPOS
	/*

	$idEquipo = $consultaJugador = $wpdb->get_results("SELECT pos.* FROM {$wpdb->prefix}posts AS pos WHERE pos.post_type = 'sp_team' AND pos.post_title = '".$equipoJ."'");
    foreach ($consultaJugador as $key) {
    	$equipoJ = $key->ID;
    }
    */
	$dorsalJ = $datosJugador[2];
	$nacionalidadJ = $datosJugador[3];
    $competicionJ = $datosJugador[4];
	$backCompeticion = $competicionJ;
	//echo "<h1>----> {$competicionJ}</h1>";
	$temporadaJ = $datosJugador[5];

	/*
    $wpdb->insert(
            "{$wpdb->prefix}df_tags",
            array(
                'tags'             =>  'JOSER',
                'value'      =>  $competicionJ
              )
            );
	*/

	//CONSULTO EL ID TANTO DE LA TEMPORADA COMO DE LA COMPETICION
    if(strpos($competicionJ, "-") === false){
    	$competicionJ2 = $wpdb->get_results("SELECT * FROM wp_{$idRed}_terms WHERE name = '".$competicionJ."'");
    	foreach ($competicionJ2 as $key) {
    		$competicionJ = $key->term_id;
    	}
    }

	//echo "<h1>Compericion = ".$competicionJ."</h1>";

	$temporadaJ2 = $wpdb->get_results("SELECT * FROM wp_{$idRed}_terms WHERE name = '".$temporadaJ."'");
	foreach ($temporadaJ2 as $key) {
		$temporadaJ = $key->term_id;
	}

    

	//echo "<h1>{$equipoJ} {$competicionJ} {$temporadaJ}</h1>";

	$nombreJugadorCorregir = str_replace(" ","-",$nombreCompleto);

	//
	//ID,post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_password,post_name,to_ping,pinged,post_modified,post_modified_gmt,post_content_filtered,post_parent,guid,menu_order,post_type,post_mime_type,comment_count
//'','$nombreJugador','','publish','closed','closed','','$nombreJugadorCorregir','','','$formatoDia','$formatoDia','','0','$rutaLiga','0','sp_player','','0'


	//DB DONDE ESTA PARADO WORDPRESS
	//$tablaDB = "{$wpdb->prefix}posts";
	//TABLA DONDE SE MONTARA EL EVENTO
	$tablaDB = "wp_{$idRed}_posts";
	$wpdb->insert(
                $tablaDB,
                array(
                    'post_author'				=> $userId,
                    'post_date' 				=>	$formatoDia,
                    'post_date_gmt'				=>	$formatoDia,
                    'post_content'				=>	'',
                    'post_title'				=>	$nombreCompleto,
                    'post_excerpt'				=>	'',
                    'post_status'				=>	'publish',
                    'comment_status'			=>	'closed',
                    'ping_status'				=>	'closed',
                    'post_password'				=>	'',
                    'post_name'					=>	$nombreJugadorCorregir,
					'to_ping'					=>	'',
					'pinged'					=>	'',
					'post_modified'				=>	$formatoDia,
					'post_modified_gmt'			=>	$formatoDia,
					'post_content_filtered'		=>	'',
					'post_parent'				=>	'0',
					'guid'						=>	'',
					'menu_order'				=>	'0',
					'post_type'					=>	'sp_player',
					'post_mime_type'			=>	'',
					'comment_count'				=>	'0'
                  )
                );

	if(count($wpdb->insert_id) > 0){
		//MODIFICAMOS LA RUTA DEL JUGADOR
		$idJugador = $wpdb->insert_id;

		//echo "<h1>El id del jugador es: ".$idJugador."</h1>";

		//$rutaLiga = "http://localhost/primerplugin/?post_type=sp_player&#038;p=".$idJugador;//ESTO SE DEBE CORREGUIR CON LA RUTA REAL DEL CLIENTE
		//http://jleague.keylimetest.com/football-masculino/?post_type=sp_player&#038;p=
        $rutaLiga = "http://jleague.keylimetest.com/liga-jleague/?post_type=sp_player&#038;p=".$idJugador;
        if($idRed == 7){
            $rutaLiga = "http://jleague.keylimetest.com/liga-jleague/?post_type=sp_player&#038;p=".$idJugador;
        }else if($idRed == 8){
            $rutaLiga = "http://jleague.keylimetest.com/liga-pro/?post_type=sp_player&#038;p=".$idJugador;
        }else{
            $rutaLiga = "http://jleague.keylimetest.com/liga-jleague/?post_type=sp_player&#038;p=".$idJugador;
        }

		//MODIFICAMOS EL JUGADOR CREADO PARA PODER MODIFICAR SU PROPIA RUTA
		$wpdb->update( 
                  $tablaDB, 
                  array( 
                    'guid' => $rutaLiga 
                  ), 
                  array( 
                    'ID' => $idJugador
                    ) 
                );

		//YA CON LA RUTA O UR DEL JUGADOR PROCEDEMOS A REALIZAR TODAS LAS RELACIONES DEL CLIENTE

		//wp_postmeta
		#_edit_last  1

		$wpdb->insert(
                "wp_{$idRed}_postmeta",
                array(
                    'post_id' 				=>	$idJugador,
                    'meta_key'				=>	'_edit_last',
                    'meta_value'			=>	'1'
                  )
                );

		#sp_twitter  ''
		$wpdb->insert(
                "wp_{$idRed}_postmeta",
                array(
                    'post_id' 				=>	$idJugador,
                    'meta_key'				=>	'sp_twitter',
                    'meta_value'			=>	''
                  )
                );

		#sp_number  Numero de la camiseta
		$wpdb->insert(
                "wp_{$idRed}_postmeta",
                array(
                    'post_id' 				=>	$idJugador,
                    'meta_key'				=>	'sp_number',
                    'meta_value'			=>	$dorsalJ
                  )
                );

		#sp_metrics  a:2:{s:6:"height";s:0:"";s:6:"weight";s:0:"";}
		$metricasJugador = 'a:2:{s:6:"height";s:0:"";s:6:"weight";s:0:"";}';

		$wpdb->insert(
                "wp_{$idRed}_postmeta",
                array(
                    'post_id' 				=>	$idJugador,
                    'meta_key'				=>	'sp_metrics',
                    'meta_value'			=>	$metricasJugador
                  )
                );

		//DEBEMOS CONSULTAR LA TEMPORADA Y LA COMPETICION DE ESTE EQUIPO
		// ----> ESTE ES PARA PODER VER QUE CATEGORIAS SON wp_term_taxonomy
		//ESTAMOS BUSCANDO EL ID DEL POST 72
		//SELECT rela.*, term.*, tax.* FROM wp_term_relationships AS rela , wp_terms AS term, wp_term_taxonomy AS tax WHERE rela.object_id = 72 AND term.term_id = rela.term_taxonomy_id AND term.term_id = tax.term_id

		#sp_leagues  a:2:{i:3;a:1:{i:2;s:3:"146";}i:0;a:1:{i:2;s:1:"1";}}
					       //a:2:{i:10;a:1:{i:11;s:4:"3432";}i:0;a:1:{i:11;s:1:"1";}}
		//a:2:{i:$competicion;a:1:{i:$temporada;s:4:"$equipoJ";}i:0;a:1:{i:$temporada;s:1:"1";}}

		//AQUI DEBEMOS CONSEGUIR LA COMPETICION Y TEMPORADA DEL EQUIPO
		//$competicionJ
		//$temporadaJ
		
        //REVISAREMOS SI SON 2 O SOLO ES UNA
        if(strpos($backCompeticion, "-") != false){
            $competi = explode(" - ", $backCompeticion);

            $backTemporada = $competicionJ;

            for ($isst=0; $isst <= count($competi); $isst++) { 

                $temporadaJ2 = $wpdb->get_results("SELECT * FROM wp_{$idRed}_terms WHERE name = '".$competi[$isst]."'");
                foreach ($temporadaJ2 as $key) {
                    $competicionJ = $key->term_id;
                }

                $ligaDeljugador = 'a:2:{i:'.$competicionJ.';a:1:{i:'.$temporadaJ.';s:4:"'.$equipoJ.'";}i:0;a:1:{i:'.$temporadaJ.';s:1:"1";}}';

                //$ligaDeljugador = 'a:1:{i:0;a:1:{i:'.$temporadaJ.';s:1:"1";}}';
                $wpdb->insert(
                        "wp_{$idRed}_postmeta",
                        array(
                            'post_id'               =>  $idJugador,
                            'meta_key'              =>  'sp_leagues',
                            'meta_value'            =>  $ligaDeljugador
                          )
                        );
            }



        }else{
            $ligaDeljugador = 'a:2:{i:'.$competicionJ.';a:1:{i:'.$temporadaJ.';s:4:"'.$equipoJ.'";}i:0;a:1:{i:'.$temporadaJ.';s:1:"1";}}';

            $wpdb->insert(
                    "wp_{$idRed}_postmeta",
                    array(
                        'post_id'               =>  $idJugador,
                        'meta_key'              =>  'sp_leagues',
                        'meta_value'            =>  $ligaDeljugador
                      )
                    );
        }

		#sp_statistics  a:2:{i:3;a:2:{i:0;a:8:{s:5:"goals";s:0:"";s:7:"assists";s:0:"";s:11:"yellowcards";s:0:"";s:8:"redcards";s:0:"";s:11:"appearances";s:0:"";s:8:"winratio";s:0:"";s:9:"drawratio";s:0:"";s:9:"lossratio";s:0:"";}i:2;a:8:{s:5:"goals";s:0:"";s:7:"assists";s:0:"";s:11:"yellowcards";s:0:"";s:8:"redcards";s:0:"";s:11:"appearances";s:0:"";s:8:"winratio";s:0:"";s:9:"drawratio";s:0:"";s:9:"lossratio";s:0:"";}}i:0;a:2:{i:0;a:8:{s:5:"goals";s:0:"";s:7:"assists";s:0:"";s:11:"yellowcards";s:0:"";s:8:"redcards";s:0:"";s:11:"appearances";s:0:"";s:8:"winratio";s:0:"";s:9:"drawratio";s:0:"";s:9:"lossratio";s:0:"";}i:2;a:8:{s:5:"goals";s:0:"";s:7:"assists";s:0:"";s:11:"yellowcards";s:0:"";s:8:"redcards";s:0:"";s:11:"appearances";s:0:"";s:8:"winratio";s:0:"";s:9:"drawratio";s:0:"";s:9:"lossratio";s:0:"";}}}
		$statistJugador = 'a:2:{i:3;a:2:{i:0;a:8:{s:5:"goals";s:0:"";s:7:"assists";s:0:"";s:11:"yellowcards";s:0:"";s:8:"redcards";s:0:"";s:11:"appearances";s:0:"";s:8:"winratio";s:0:"";s:9:"drawratio";s:0:"";s:9:"lossratio";s:0:"";}i:2;a:8:{s:5:"goals";s:0:"";s:7:"assists";s:0:"";s:11:"yellowcards";s:0:"";s:8:"redcards";s:0:"";s:11:"appearances";s:0:"";s:8:"winratio";s:0:"";s:9:"drawratio";s:0:"";s:9:"lossratio";s:0:"";}}i:0;a:2:{i:0;a:8:{s:5:"goals";s:0:"";s:7:"assists";s:0:"";s:11:"yellowcards";s:0:"";s:8:"redcards";s:0:"";s:11:"appearances";s:0:"";s:8:"winratio";s:0:"";s:9:"drawratio";s:0:"";s:9:"lossratio";s:0:"";}i:2;a:8:{s:5:"goals";s:0:"";s:7:"assists";s:0:"";s:11:"yellowcards";s:0:"";s:8:"redcards";s:0:"";s:11:"appearances";s:0:"";s:8:"winratio";s:0:"";s:9:"drawratio";s:0:"";s:9:"lossratio";s:0:"";}}}';

		$wpdb->insert(
                "wp_{$idRed}_postmeta",
                array(
                    'post_id' 				=>	$idJugador,
                    'meta_key'				=>	'sp_leagues',
                    'meta_value'			=>	$statistJugador
                  )
                );

		#slide_template  default

		$wpdb->insert(
                "wp_{$idRed}_postmeta",
                array(
                    'post_id' 				=>	$idJugador,
                    'meta_key'				=>	'slide_template',
                    'meta_value'			=>	'default'
                  )
                );

		#sp_nationality  ''

		$wpdb->insert(
                "wp_{$idRed}_postmeta",
                array(
                    'post_id' 				=>	$idJugador,
                    'meta_key'				=>	'sp_nationality',
                    'meta_value'			=>	''
                  )
                );

		#sp_current_team  dependiendo del ID del equipo

		$wpdb->insert(
                "wp_{$idRed}_postmeta",
                array(
                    'post_id' 				=>	$idJugador,
                    'meta_key'				=>	'sp_current_team',
                    'meta_value'			=>	$equipoJ
                  )
                );

		#sp_team  dependiendo del ID del equipo

		$wpdb->insert(
                "wp_{$idRed}_postmeta",
                array(
                    'post_id' 				=>	$idJugador,
                    'meta_key'				=>	'sp_team',
                    'meta_value'			=>	$equipoJ
                  )
                );


		#RELACIONANDO EL JUGADOR CON LA TEMPORADA Y CON EL EQUIPO
        //df_tags
        $wpdb->insert(
            "{$wpdb->prefix}df_tags",
            array(
                'tags'             =>  'JOSER 2',
                'value'      =>  $competicionJ
              )
            );


        if(strpos($backCompeticion, "-") != false){
            $competi = explode(" - ", $backCompeticion);

            $backTemporada = $competicionJ;

            for ($isst=0; $isst <= count($competi); $isst++) { 

                $temporadaJ2 = $wpdb->get_results("SELECT * FROM wp_{$idRed}_terms WHERE name = '".$competi[$isst]."'");
                foreach ($temporadaJ2 as $key) {
                    $competicionJ = $key->term_id;
                }

                $wpdb->insert(
                        "wp_{$idRed}_term_relationships",
                        array(
                            'object_id'             =>  $idJugador,
                            'term_taxonomy_id'      =>  $competicionJ,
                            'term_order'            =>  '0'
                          )
                        );

                $wpdb->insert(
                        "{$wpdb->prefix}df_tags",
                        array(
                            'tags'             =>  'JOSER 3',
                            'value'      =>  $competicionJ
                          )
                        );

            }

        }else{
    		$wpdb->insert(
                    "wp_{$idRed}_term_relationships",
                    array(
                        'object_id'				=>	$idJugador,
                        'term_taxonomy_id'		=>	$competicionJ,
                        'term_order'			=>	'0'
                      )
                    );
        }

		$wpdb->insert(
                "wp_{$idRed}_term_relationships",
                array(
                    'object_id'				=>	$idJugador,
                    'term_taxonomy_id'		=>	$temporadaJ,
                    'term_order'			=>	'0'
                  )
                );

		echo "-----------------------   -------------------------";
		//consultar_ligas_competicion_j($idJugador, $competicionJ, $temporadaJ, $dorsalJ, $equipoJ, $nombreCompleto);
		//return $idJugador;

		//echo "<h1>".$competicionJ." ".$temporadaJ."</h1>";
	}else{
		//echo "<h1>No se creo el jugador adecuadamente, por favor contactar al administrador</h1>";
	}
}