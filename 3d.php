<?php
    session_start();
    require('db_connect.php');

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
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
		<link href="https://fonts.googleapis.com/css2?family=Anton&display=swap" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="./css/3d.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js"></script>
		</head>

	<body id="body">

		<nav id="nav" class="navbar navbar-dark">
			<div class="container-fluid">
				<ul class="nav mr-auto">
					<li class="nav-item">
						<a class="nav-link" href="dashboard.php" >Back</a>
					</li>
					<li class="nav-item">
						<button id="showhide">Show/Hide Graphs</button>
					</li>
				</ul>
			</div>

		</nav>

		<hr style="clear: both;">

		<script type="module">

            import { Scene, WebGLRenderer, PerspectiveCamera, CylinderGeometry,  MeshPhongMaterial, Mesh, MeshBasicMaterial,
					MeshNormalMaterial, MeshLambertMaterial, PointLight, Color, HemisphereLight, HemisphereLightHelper, DirectionalLight, DirectionalLightHelper } from './js/threejs/build/three.module.js';
            import { MTLLoader } from './js/threejs/examples/jsm/loaders/MTLLoader.js';
            import { OBJLoader } from './js/threejs/examples/jsm/loaders/OBJLoader.js';
			import { OrbitControls } from './js/threejs/examples/jsm/controls/OrbitControls.js';

			let camera, controls, scene, renderer;
			const are_there_two_sessions = <?php echo (isset($session2_values) && $session2_values) ? json_encode($session2_values) : 'false'; ?>;

			init();
			animate();


			function init() {

				scene = new Scene();
				scene.background = new Color( 0xFFFFFF );

				renderer = new WebGLRenderer( { antialias: true } );
				renderer.setPixelRatio( window.devicePixelRatio );
				renderer.setSize( window.innerWidth, window.innerHeight );

				document.body.appendChild(renderer.domElement);

				camera = new PerspectiveCamera( 90, window.innerWidth / window.innerHeight, 1, 1000 );
				camera.position.set( 400, 200, 0 );
				camera.lookAt(0, 0, 0);

				// controls

				controls = new OrbitControls( camera, renderer.domElement );
				controls.target.set(0, 100);
				controls.listenToKeyEvents( window ); // optional

				controls.enableDamping = true; // an animation loop is required when either damping or auto-rotation are enabled
				controls.dampingFactor = 0.05;

				controls.screenSpacePanning = false;

				controls.minDistance = 150;
				controls.maxDistance = 250;

				controls.maxPolarAngle = Math.PI / 2;

				// very important pls don't delete xoxoxo
				// basically this just disables panning so the user can't lost the 3D model, and it just stays centered.
				controls.enablePan = false;


				// nice little method to load objects in and give them a name and colour
                function loadObject ( obj_name, obj_path, obj_color ){
                    var material = new MeshLambertMaterial( { color: obj_color , transparent : true, opacity : 1} );
                    var loader = new OBJLoader();
                    loader.load( obj_path,
                        function( obj ){
                            obj.traverse( function( child ) {
                                if ( child instanceof Mesh ) {
                                    child.material = material; // assign new material to object
                                }
                            } );
                            obj.name = obj_name;
                            obj.scale.set(10, 10, 10); // 10x scale works nicely for our model
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


				// another function to load objects, however this is for objects with premade materials e.g. the text above the bodies
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

				// makes sure the correct models are loaded in depending on if it's one or two sessions
				// we know having all these separate models is space-inefficient but it works

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
				}


				// lights
				const hemiLight = new HemisphereLight( 0xffffff, 0xffffff, 0.6 );
                hemiLight.color.setHSL( 0.6, 0, 1 );
                hemiLight.groundColor.setHSL( 0.6, 0, 0.75 );
                hemiLight.position.set( -100, 100, 0 );
                scene.add( hemiLight );

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

				// make sure everything keeps working when the window is resized
				window.addEventListener( 'resize', onWindowResize );

			}

			// make sure everything keeps working when the window is resized
			function onWindowResize() {

				camera.aspect = window.innerWidth / window.innerHeight;
				camera.updateProjectionMatrix();

				renderer.setSize( window.innerWidth, window.innerHeight );

			}

			// begin the animation
			function animate() {

				requestAnimationFrame( animate );

				controls.update(); // only required if controls.enableDamping = true, or if controls.autoRotate = true

				render();

			}

			// render the scene
			function render() {

				renderer.render( scene, camera );

			}

			// get the hex values for the first session
			const session1_values = <?php echo json_encode($session1_values);?>;
			const hex_array1 = session1_values[0];
			const hex_array2 = session1_values[1];
			const hex_array3 = session1_values[2];
			const hex_array4 = session1_values[3];
			const time_stamps = session1_values[4];
			
			// empty arrays in case there's another session
			var hex_array5 = [];
			var hex_array6 = [];
			var hex_array7 = [];
			var hex_array8 = [];

			// fetch hex values for second session if there is a second session
			if (are_there_two_sessions){
				const session2_values = <?php echo json_encode($session2_values);?>;
				console.log(session2_values);
				hex_array5 = session2_values[0];
				hex_array6 = session2_values[1];
				hex_array7 = session2_values[2];
				hex_array8 = session2_values[3];
			}

			// change the object colour in real time
			function changeObjectColour( objName, objColor ){
                var obj = scene.getObjectByName( objName );
                obj.traverse( function( child ) {
                    if ( child instanceof Mesh ) {
						child.material.color.setHex(objColor);
                    }
                } );
            }

			// this is the method that animates everything nicely
			function do_timeout(i){
				setTimeout(function() {
					if(are_there_two_sessions){
						changeObjectColour( "body2_left_hamstring", parseInt(hex_array1[i], 16));
						changeObjectColour( "body2_right_hamstring", parseInt(hex_array2[i], 16));
						changeObjectColour( "body2_left_quad", parseInt(hex_array3[i], 16))
						changeObjectColour( "body2_right_quad", parseInt(hex_array4[i], 16));

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
				}, i*500) // animates in 2x real time but shhhhhh it looks better and works fine
			}

			// finds the shortest hex array so none of the muscles run out of colours
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

			// timestamps
			const time_array = session1_values[4];

			for(var i=0; i<shortest_array().length; i++){
				do_timeout(i);
			}

			// Since sessions are split into 3 sub sessions, the hex arrays are approximately 1/3rd of the size 
			// of the timestamps. To avoid accessing undefined values from the hex_array when displaying the array, 
			// we remove all values of the timestamps after the index corresponding to the last value on the hex_array.
			// This way having as many timestamps as there are sensor readings for each graph
			for(var i=0; i<time_stamps.length; i++){
				if(hex_array1[i] == undefined){
					hex_array1.splice(i, 1);
					time_stamps.splice(i, 1);
					i--;
				}
			}

			// scale hex_array values so that they correspond to the same ones from the CSV files
			const r_f1 = hex_array1.map(x => x/16320)
			const r_f2 = hex_array2.map(x => x/16320)
			const s_t1 = hex_array3.map(x => x/16320)
			const s_t2 = hex_array4.map(x => x/16320)

			// creat graphs
			let myChart = document.getElementById('myChart').getContext('2d');
			let myChart2 = document.getElementById('myChart2').getContext('2d');

			//set up line graphs
			let lineChart = new Chart(myChart, {
				type:'line',
				data:{
					labels:time_stamps,
					datasets:[{
						label:'Rectus Femoris',
						fill:false,
						lineTension: 0.3,
						borderColor: "rgba(3,151,162,1)",
						backgroundCOlor:"rgba(3,151,162,1)",
						borderCapStyle: 'but',
						borderDash: [],
						borderDashOffset: 0.0,
						borderJoinStyle: 'miter',
						pointBorderColor: "rgba(3,151,162,1)",
						pointBackgroundColor: "rgba(3,151,162,1)",
						pointBorderWidth: 1,
						pointHoverRadius:5 ,
						pointHoverBackground: "rgba(3,151,162,1)",
						pointHoverBorderColor: "rgba(220,220,220,1)",
						
						// use sensor readings from the rectus femoris of right leg
						data: r_f1
					},
					{
						label:'Semitendinosus',
						fill:false,
						lineTension: 0.3,
						borderColor: "rgb(242,109,28)",
						backgroundCOlor:"rgb(242,109,28)",
						borderCapStyle: 'but',
						borderDash: [],
						borderDashOffset: 0.0,
						borderJoinStyle: 'miter',
						pointBorderColor: "rgb(242,109,28)",
						pointBackgroundColor: "rgb(242,109,28)",
						pointBorderWidth: 1,
						pointHoverRadius:5 ,
						pointHoverBackground: "rgb(242,109,28)",
						pointHoverBorderColor: "rgba(220,220,220,1)",
						
						// use sensor readings from the semitendinosus of right leg
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
							// label graph
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
						borderColor: "rgba(3,151,162,1)",
						backgroundCOlor:"rgba(3,151,162,1)",
						borderCapStyle: 'but',
						borderDash: [],
						borderDashOffset: 0.0,
						borderJoinStyle: 'miter',
						pointBorderColor: "rgba(3,151,162,1)",
						pointBackgroundColor: "rgba(3,151,162,1)",
						pointBorderWidth: 1,
						pointHoverRadius:5 ,
						pointHoverBackground: "rgba(3,151,162,1)",
						pointHoverBorderColor: "rgba(220,220,220,1)",

						// use sensor readings from the rectus femoris of left leg
						data: r_f2
					},
					{
						label:'Semitendinosus',
						fill:false,
						lineTension: 0.3,
						borderColor: "rgb(242,109,28)",
						backgroundCOlor:"rgb(242,109,28)",
						borderCapStyle: 'but',
						borderDash: [],
						borderDashOffset: 0.0,
						borderJoinStyle: 'miter',
						pointBorderColor: "rgb(242,109,28)",
						pointBackgroundColor: "rgb(242,109,28)",
						pointBorderWidth: 1,
						pointHoverRadius:5 ,
						pointHoverBackground: "rgb(242,109,28)",
						pointHoverBorderColor: "rgba(220,220,220,1)",

						// use sensor readings from the semitendinosus of left leg
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

		<!-- style the containers which hold graphs and gradient bar image -->
		<div class="container" id="charts" style="display: none; flex-direction: column;">
			<div class="row">
				<div class="col-11">
					<canvas id="myChart" height="400" width="1200" ></canvas>
				</div>
				<div class="col-1"> 
					<img  src="./Style/Images/gradient_ss.png" alt="Italian Trulli" />
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-11">
					<canvas id="myChart2" height="400" width="1200" ></canvas>
				</div>
				<div class="col-1">
					<img src="./Style/Images/gradient_ss.png" alt="Italian Trulli"/>
				</div>
			</div>
		</div>

		<!-- wee script to make sure that the "show/hide charts" button works -->
		
		<script>
			let show = true;
			document.getElementById("showhide").addEventListener('click', () => {
				show = !show;
				if (show){
					document.getElementById("charts").style.display="none";
				} else {
					document.getElementById("charts").style.display="flex";
				}
			})
		</script>

	</body>
</html>
