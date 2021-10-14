<?php
    session_start();
    require('db_connect.php');

	// if (!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {

	// 	header("location: index.php");
	// 	exit;
	// }

	function colour_hex_val_gen ($percent, $value) {

        // this handles the green transitioning to yellow
        if ($percent >= 0 && $percent < 0.25) {
            $r_val = round(round($value/256,2)*255,2);

            $hex_val = sprintf("%02x%02x%02x", $r_val, 255, 0);

            // this handles the yellow transitioning to orange
        } else if ($percent >= 0.25 && $percent < 0.5) {
            $g_val = round(round((512-$value)/256,2)*128,2);

            $hex_val = sprintf("%02x%02x%02x", 255, 255-$g_val, 0);

            // this handles the orange transitioning to red
        } else if ($percent >= 0.5 && $percent < 0.75) {
            $g_val = round(round((768-$value)/256,2)*127,2);

            $hex_val = sprintf("%02x%02x%02x", 255, 127-$g_val, 0);

        // this handles the red transitioning to dark-red
        } else if ($percent >= 0.75 && $percent <= 1.0) {
            $r_val = round(round((1025-$value)/256,2)*125,2);

            $hex_val = sprintf("%02x%02x%02x", 255-$r_val, 0, 0);
        }

        return $hex_val;
	}

	$colour;
	// variable to contain the colour percent to aid hex colour generation
	$colour_percent;

	function determine_colour ($value) {
		$colour_hex_values = "0x";
    
    $min_value=0;
    $max_value=1025;

    $colour_percent= round($value/$max_value,2);
    $colour_hex_values .= colour_hex_val_gen($colour_percent, $value);
		return $colour_hex_values;

	}

	function create_arrays($rowx){
		$hexvals1 = array();
		$hexvals2 = array();
		$hexvals3 = array();
		$hexvals4 = array();
		$times = array();

		foreach ($rowx as $result){
			$value = $result["value"];
			$time = $result["time"];
			$sensor = $result["sensor_no"];

			array_push($times, $time);
			// echo(determine_colour($value)."<br>");
			if ($sensor == 1) {
				array_push($hexvals1, determine_colour($value));
			} else if ($sensor == 2) {
				array_push($hexvals2, determine_colour($value));
			} else if ($sensor == 3) {
				array_push($hexvals3, determine_colour($value));
			} else if ($sensor == 4) {
				array_push($hexvals4, determine_colour($value));
			} else {
				echo("error");
			}
		}

		$session_values = array();
		array_push($session_values, $hexvals1);
		array_push($session_values, $hexvals2);
		array_push($session_values, $hexvals3);
		array_push($session_values, $hexvals4);
		array_push($session_values, $times);
		return $session_values;
	}

	if (isset($_POST["single_session"])) {
		$session_id = $_POST["single_session"];
	}

	if (isset($_POST["custom_session"])) {
		$session_id = $_POST["custom_session"];
	}

	if(isset($_POST['compare_session_1'])){
		$session_id = $_POST['compare_session_1'];
	}
	$userid = $_SESSION['UserID'];

	if (isset($_POST['clientid'])) {
		 $userid = $_POST['clientid'];
	}

	$query = "SELECT * FROM `sensor_data` WHERE `user_id` = ". $userid ." AND `session_id` = ".$session_id.";";
	$stmt = $pdo->prepare($query);
	$stmt->execute();
	$row = $stmt->fetchAll();

	$session1_values = create_arrays($row);
	// var_dump($session1_values);
	$session2_values = array();

	if (($session_id > 1 && !isset($_POST['custom_session'])) || isset($_POST['compare_session_2'])){
		if(isset($_POST['compare_session_2'])){
			$session_id = $_POST['compare_session_2'];
			$query = "SELECT * FROM `sensor_data` WHERE `user_id` = ".$userid." AND `session_id` = ".($session_id).";";
		}else{
			$query = "SELECT * FROM `sensor_data` WHERE `user_id` = ".$userid." AND `session_id` = ".($session_id - 1).";";
		}

		$stmt = $pdo->prepare($query);
		$stmt->execute();
		$row2 = $stmt->fetchAll();

		$session2_values = create_arrays($row2);
	}


//


?>



<!--
Code based on https://threejs.org/examples/?q=orb#misc_controls_orbit
-->

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>three.js webgl - orbit controls</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<!-- <link rel="stylesheet" href="css/main.css"> -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
		<link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="./css/3d.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js"></script>
		</head>

	<body>
		<!-- <h1 id="time">TIME</h1> -->
		<a class="nav-link" href="dashboard.php" >Back</a>

		
		<div class="row">
		<script type="module">

            import { Scene, WebGLRenderer, PerspectiveCamera, CylinderGeometry,  MeshPhongMaterial, Mesh, MeshBasicMaterial,
					MeshNormalMaterial, MeshLambertMaterial, PointLight, Color, HemisphereLight, HemisphereLightHelper, DirectionalLight, DirectionalLightHelper } from './js/threejs/build/three.module.js';
            import { MTLLoader } from './js/threejs/examples/jsm/loaders/MTLLoader.js';
            import { OBJLoader } from './js/threejs/examples/jsm/loaders/OBJLoader.js';
			import { OrbitControls } from './js/threejs/examples/jsm/controls/OrbitControls.js';

			let camera, controls, scene, renderer;
			const are_there_two_sessions = <?php echo (isset($session2_values) && $session2_values) ? json_encode($session2_values) : 'false'; ?>;

			init();
			//render(); // remove when using next line for animation loop (requestAnimationFrame)
			animate();


			function init() {

				scene = new Scene();
				scene.background = new Color( 0xFFFFFF );

				renderer = new WebGLRenderer( { antialias: true } );
				renderer.setPixelRatio( window.devicePixelRatio );
				renderer.setSize( window.innerWidth, window.innerHeight );
				document.body.appendChild( renderer.domElement );

				camera = new PerspectiveCamera( 90, window.innerWidth / window.innerHeight, 1, 1000 );
				camera.position.set( 400, 200, 0 );
				// camera.up.set(0, 0, 1);
				camera.lookAt(0, 0, 0);

				// controls

				controls = new OrbitControls( camera, renderer.domElement );
				controls.target.set(0, 100);
				controls.listenToKeyEvents( window ); // optional

				//controls.addEventListener( 'change', render ); // call this only in static scenes (i.e., if there is no animation loop)

				controls.enableDamping = true; // an animation loop is required when either damping or auto-rotation are enabled
				controls.dampingFactor = 0.05;

				controls.screenSpacePanning = false;

				controls.minDistance = 100;
				controls.maxDistance = 300;

				controls.maxPolarAngle = Math.PI / 2;

				// very important pls don't delete xoxoxo
				controls.enablePan = false;

				// spikes

				// const geometry = new CylinderGeometry( 0, 10, 30, 4, 1 );
				// const material = new MeshPhongMaterial( { color: 0xffffff, flatShading: true } );

				// for ( let i = 0; i < 500; i ++ ) {

				// 	const mesh = new Mesh( geometry, material );
				// 	mesh.position.x = Math.random() * 1600 - 800;
				// 	mesh.position.y = 0;
				// 	mesh.position.z = Math.random() * 1600 - 800;
				// 	mesh.updateMatrix();
				// 	mesh.matrixAutoUpdate = false;
				// 	scene.add( mesh );

				// }

                function loadObject ( obj_name, obj_path, obj_color ){
                    var material = new MeshLambertMaterial( { color: obj_color , transparent : true, opacity : 1} );
                    var loader = new OBJLoader();
                    loader.load( obj_path,
                        function( obj ){
                            obj.traverse( function( child ) {
                                if ( child instanceof Mesh ) {
                                    child.material = material;
                                }
                            } );
                            obj.name = obj_name;
                            obj.scale.set(10, 10, 10);
							obj.position.z = 0;
							obj.position.x = 0;
                            scene.add( obj );
                        },
                        function( xhr ){
                            console.log( (xhr.loaded / xhr.total * 100) + "% loaded")
                        },
                        function( err ){
                            console.error( "Error loading " + obj_path)
                        }
                    );
                }

				function loadObjectWithMaterial( obj_name, obj_path, mtl_path ) {
					var mtlLoader = new MTLLoader();
					mtlLoader.load(mtl_path, function (materials) {

						materials.preload();

						// Load the object
						var objLoader = new OBJLoader();
						objLoader.setMaterials(materials);
						objLoader.load(obj_path, function (object) {
							scene.add(object);
                            object.name = obj_name;
							object.scale.set(10, 10, 10);
							object.position.z = 0;
							object.rotation.x = 0;

						});
					});
				}

				if (are_there_two_sessions){
					loadObject("body1_right_quad", "shapes/body1_right_quad/body1_right_quad.obj", 0xB66B3E);
					loadObject("body1_left_quad", "shapes/body1_left_quad/body1_left_quad.obj", 0xB66B3E);
					loadObject("body1_right_hamstring", "shapes/body1_right_hamstring/body1_right_hamstring.obj", 0xB66B3E);
					loadObject("body1_left_hamstring", "shapes/body1_left_hamstring/body1_left_hamstring.obj", 0xB66B3E);
					loadObject("body1", "shapes/body1/body1.obj", 0xB66B3E);
					loadObjectWithMaterial("previous_text", "shapes/previous_text/previous_text.obj", "shapes/previous_text/previous_text.mtl");

					loadObject("body2_right_quad", "shapes/body2_right_quad/body2_right_quad.obj", 0xB66B3E);
					loadObject("body2_left_quad", "shapes/body2_left_quad/body2_left_quad.obj", 0xB66B3E);
					loadObject("body2_right_hamstring", "shapes/body2_right_hamstring/body2_right_hamstring.obj", 0xB66B3E);
					loadObject("body2_left_hamstring", "shapes/body2_left_hamstring/body2_left_hamstring.obj", 0xB66B3E);
					loadObject("body2", "shapes/body2/body2.obj", 0xB66B3E);
					loadObjectWithMaterial("current_text", "shapes/current_text/current_text.obj", "shapes/current_text/current_text.mtl");
				} else {
					loadObject("body3_right_quad", "shapes/body3_right_quad/body3_right_quad.obj", 0xB66B3E);
					loadObject("body3_left_quad", "shapes/body3_left_quad/body3_left_quad.obj", 0xB66B3E);
					loadObject("body3_right_hamstring", "shapes/body3_right_hamstring/body3_right_hamstring.obj", 0xB66B3E);
					loadObject("body3_left_hamstring", "shapes/body3_left_hamstring/body3_left_hamstring.obj", 0xB66B3E);
					loadObject("body3", "shapes/body3/body3.obj", 0xB66B3E);
					loadObjectWithMaterial("current_text_centered", "shapes/current_text_centered/current_text_centered.obj", "shapes/current_text_centered/current_text_centered.mtl");
					// load object 3
				}


				// lights

				// let light, light2, light3, light4;
                // light = new PointLight(0xc4c4c4,1);
                // light.position.set(0,300,500);
                // scene.add(light);
                // light2 = new PointLight(0xc4c4c4,1);
                // light2.position.set(500,100,0);
                // scene.add(light2);
                // light3 = new PointLight(0xc4c4c4,1);
                // light3.position.set(0,100,-500);
                // scene.add(light3);
                // light4 = new PointLight(0xc4c4c4,1);
                // light4.position.set(-500,300,500);
                // scene.add(light4);

				const hemiLight = new HemisphereLight( 0xffffff, 0xffffff, 0.6 );
                hemiLight.color.setHSL( 0.6, 0, 1 );
                hemiLight.groundColor.setHSL( 0.6, 0, 0.75 );
                hemiLight.position.set( -100, 100, 0 );
                scene.add( hemiLight );

                // const hemiLightHelper = new HemisphereLightHelper( hemiLight, 10 );
                // scene.add( hemiLightHelper );

				window.addEventListener( 'resize', onWindowResize );

				const dirLight = new DirectionalLight( 0xffffff, 1 );
                dirLight.color.setHSL( 0.1, 1, 0.95 );
                dirLight.position.set( 10, 17.5, 10 );
                dirLight.position.multiplyScalar( 30 );
                scene.add( dirLight );

                dirLight.castShadow = true;

                dirLight.shadow.mapSize.width = 2048;
                dirLight.shadow.mapSize.height = 2048;

                const d = 50;

                dirLight.shadow.camera.left = - d;
                dirLight.shadow.camera.right = d;
                dirLight.shadow.camera.top = d;
                dirLight.shadow.camera.bottom = - d;

                dirLight.shadow.camera.far = 3500;
                dirLight.shadow.bias = - 0.0001;

                // const dirLightHelper = new DirectionalLightHelper( dirLight, 10 );
                // scene.add( dirLightHelper );

			}

			function onWindowResize() {

				camera.aspect = window.innerWidth / window.innerHeight;
				camera.updateProjectionMatrix();

				renderer.setSize( window.innerWidth, window.innerHeight );

			}

			function animate() {

				requestAnimationFrame( animate );

				controls.update(); // only required if controls.enableDamping = true, or if controls.autoRotate = true

				render();

			}

			function render() {

				renderer.render( scene, camera );

			}

			const session1_values = <?php echo json_encode($session1_values);?>;
			const hex_array1 = session1_values[0];
			const hex_array2 = session1_values[1];
			const hex_array3 = session1_values[2];
			const hex_array4 = session1_values[3];
			const time_stamps = session1_values[4];
			

			var hex_array5 = [];
			var hex_array6 = [];
			var hex_array7 = [];
			var hex_array8 = [];

			if (are_there_two_sessions){
				const session2_values = <?php echo json_encode($session2_values);?>;
				console.log(session2_values);
				hex_array5 = session2_values[0];
				hex_array6 = session2_values[1];
				hex_array7 = session2_values[2];
				hex_array8 = session2_values[3];
			}

			function changeObjectColour( objName, objColor ){
                var obj = scene.getObjectByName( objName );
                obj.traverse( function( child ) {
                    if ( child instanceof Mesh ) {
						child.material.color.setHex(objColor);
                    }
                } );
            }

			function do_timeout(i){
				setTimeout(function() {
					if(are_there_two_sessions){
						changeObjectColour( "body2_left_hamstring", parseInt(hex_array1[i], 16));
						changeObjectColour( "body2_right_hamstring", parseInt(hex_array2[i], 16));
						changeObjectColour( "body2_left_quad", parseInt(hex_array3[i], 16))
						changeObjectColour( "body2_right_quad", parseInt(hex_array4[i], 16));
						// document.getElementById("time").innerHTML = time_array[i];
						changeObjectColour( "body1_left_hamstring", parseInt(hex_array5[i], 16));
						changeObjectColour( "body1_right_hamstring", parseInt(hex_array6[i], 16));
						changeObjectColour( "body1_left_quad", parseInt(hex_array7[i], 16))
						changeObjectColour( "body1_right_quad", parseInt(hex_array8[i], 16));
					} else {
						changeObjectColour( "body3_left_hamstring", parseInt(hex_array1[i], 16));
						changeObjectColour( "body3_right_hamstring", parseInt(hex_array2[i], 16));
						changeObjectColour( "body3_left_quad", parseInt(hex_array3[i], 16))
						changeObjectColour( "body3_right_quad", parseInt(hex_array4[i], 16));
					}
				}, i*500)
			}

			var shortest_array = function(){
				const length1 = hex_array1.length;
				const length2 = hex_array2.length;
				const length3 = hex_array3.length;
				const length4 = hex_array4.length;

				if (are_there_two_sessions){
					const length5 = hex_array5.length;
					const length6 = hex_array6.length;
					const length7 = hex_array7.length;
					const length8 = hex_array8.length;
				}

				const lengths = [length1, length2, length3, length4]
				const minlength = Math.min(...lengths);

				if (minlength == length1){
					return hex_array1;
				} else if (minlength == length2){
					return hex_array2;
				} else if (minlength == length3){
					return hex_array3;
				} else if (minlength == length4){
					return hex_array4;
				} else if (are_there_two_sessions){

					if (minlength == length5){
						return hex_array5;
					} else if (minlength == length6){
						return hex_array6;
					} else if (minlength == length7){
						return hex_array7;
					} else if (minlength == length8){
						return hex_array8;
					}
				}
			}

			const time_array = session1_values[4];

			//console.log(typeof hex_array1[(hex_array1.length)/2]);

			// if x == undefined

			for(var i=0; i<shortest_array().length; i++){
				do_timeout(i);
			}

			for(var i=0; i<time_stamps.length; i++){
				if(hex_array1[i] == undefined){
					hex_array1.splice(i, 1);
					time_stamps.splice(i, 1);
					i--;
				}
			}

			const r_f1 = hex_array1.map(x => x/16320)
			const r_f2 = hex_array2.map(x => x/16320)
			const s_t1 = hex_array3.map(x => x/16320)
			const s_t2 = hex_array4.map(x => x/16320)

			let myChart = document.getElementById('myChart').getContext('2d');
			let myChart2 = document.getElementById('myChart2').getContext('2d');

			let lineChart = new Chart(myChart, {
				type:'line',
				data:{
					labels:time_stamps,
					datasets:[{
						label:'Rectus Femoris',
						fill:false,
						lineTension: 0.3,
						borderColor: "rgba(75,192,192,1)",
						backgroundCOlor:"rgba(75,192,192,1)",
						borderCapStyle: 'but',
						borderDash: [],
						borderDashOffset: 0.0,
						borderJoinStyle: 'miter',
						pointBorderColor: "rgba(75,192,192,1)",
						pointBackgroundColor: "rgba(75,192,192,1)",
						pointBorderWidth: 1,
						pointHoverRadius:5 ,
						pointHoverBackground: "rgba(75,192,192,1)",
						pointHoverBorderColor: "rgba(220,220,220,1)",

						data: r_f1
					},
					{
						label:'Semitendinosus',
						fill:false,
						lineTension: 0.3,
						borderColor: "rgb(255,20,147)",
						backgroundCOlor:"rgb(255,20,147)",
						borderCapStyle: 'but',
						borderDash: [],
						borderDashOffset: 0.0,
						borderJoinStyle: 'miter',
						pointBorderColor: "rgb(255,20,147)",
						pointBackgroundColor: "rgb(255,20,147)",
						pointBorderWidth: 1,
						pointHoverRadius:5 ,
						pointHoverBackground: "rgb(255,20,147)",
						pointHoverBorderColor: "rgba(220,220,220,1)",

						data: s_t1
					}
				
				]
					
				},
				options:{
					
					plugins:{
						legend:{
						position: 'top'
					},
					title:{
						display: true,
						text: "Right Leg",
						fontSize: 50
					}
					}
					
				}
			})

			let lineChart2 = new Chart(myChart2, {
				type:'line',
				data:{
					labels:time_stamps,
					datasets:[{
						label:'Rectus Femoris',
						fill:false,
						lineTension: 0.3,
						borderColor: "rgba(75,192,192,1)",
						backgroundCOlor:"rgba(75,192,192,1)",
						borderCapStyle: 'but',
						borderDash: [],
						borderDashOffset: 0.0,
						borderJoinStyle: 'miter',
						pointBorderColor: "rgba(75,192,192,1)",
						pointBackgroundColor: "rgba(75,192,192,1)",
						pointBorderWidth: 1,
						pointHoverRadius:5 ,
						pointHoverBackground: "rgba(75,192,192,1)",
						pointHoverBorderColor: "rgba(220,220,220,1)",

						data: r_f2
					},
					{
						label:'Semitendinosus',
						fill:false,
						lineTension: 0.3,
						borderColor: "rgb(255,20,147)",
						backgroundCOlor:"rgb(255,20,147)",
						borderCapStyle: 'but',
						borderDash: [],
						borderDashOffset: 0.0,
						borderJoinStyle: 'miter',
						pointBorderColor: "rgb(255,20,147)",
						pointBackgroundColor: "rgb(255,20,147)",
						pointBorderWidth: 1,
						pointHoverRadius:5 ,
						pointHoverBackground: "rgb(255,20,147)",
						pointHoverBorderColor: "rgba(220,220,220,1)",

						data: s_t2
					}
				
				]
					
				},
				options:{
					
					plugins:{
						legend:{
						position: 'top'
					},
					title:{
						display: true,
						text: "Left Leg",
						fontSize: 50
					}
					}
					
				}
			})

		</script>
		</div>
		<div class="container">
		<div class="row">
			<div class="col-10">
				<canvas id="myChart" height="400" width="1200" ></canvas>
			</div>
			<div class="col" style="padding-top: 50px;"> 
			<img src="./Style/Images/gradient_ss.png" alt="Italian Trulli" style="height:250px;" >
			</div>
		</div>
			
			
		<div class="container">
		<div class="row">
			<div class="col-10">
				<canvas id="myChart2" height="400" width="1200" ></canvas>
			</div>
			<div class="col" style="padding-top: 50px;">
			<img src="./Style/Images/gradient_ss.png" alt="Italian Trulli" style="height:250px;" style="padding-top: 100px;">
			</div>
		</div>
	</body>
</html>
