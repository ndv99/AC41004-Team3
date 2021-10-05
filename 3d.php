<?php
    session_start();
    require('db_connect.php');

	// if (!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {

	// 	header("location: index.php");
	// 	exit;
	// }

	$query = "SELECT * FROM `sensor_data` WHERE `user_id` = ".$_SESSION["UserID"].";";
	$stmt = $pdo->prepare($query);
	$stmt->execute();
	$row = $stmt->fetchAll();

	function colour_hex_val_gen ($current_colour, $percent) {
        $hex_val;

        if ($current_colour=="green") {
          if ($percent>=0 && $percent<=0.2) {
            $hex_val = "FEFB01";
          } else if ($percent>=0.21 && $percent<=0.4) {
            $hex_val = "CEFB02";
          } else if ($percent>=0.41 && $percent<=0.6) {
            $hex_val = "87FA00";
          } else if ($percent>=0.61 && $percent<=0.8) {
            $hex_val = "3AF901";
          } else if ($percent>=0.81 && $percent<=1) {
            $hex_val = "00ED01";
          }
        } else if ($current_colour=="yellow") {
          if ($percent>=0 && $percent<=0.2) {
            $hex_val = "FFF600";
          } else if ($percent>=0.21 && $percent<=0.4) {
            $hex_val = "FFCF07";
          } else if ($percent>=0.41 && $percent<=0.6) {
            $hex_val = "FA80F";
          } else if ($percent>=0.61 && $percent<=0.8) {
            $hex_val = "FE8116";
          } else if ($percent>=0.81 && $percent<=1) {
            $hex_val = "FE5A1D";
          }
        } else if ($current_colour=="orange") {
          if ($percent>=0 && $percent<=0.2) {
            $hex_val = "FA6F01";
          } else if ($percent>=0.21 && $percent<=0.4) {
            $hex_val = "F55301";
          } else if ($percent>=0.41 && $percent<=0.6) {
            $hex_val = "F03801";
          } else if ($percent>=0.61 && $percent<=0.8) {
            $hex_val = "EB1C01";
          } else if ($percent>=0.81 && $percent<=1) {
            $hex_val = "E60001";
          }
        } else if ($current_colour=="red") {
          if ($percent>=0 && $percent<=0.25) {
            $hex_val = "FF0000";
          } else if ($percent>=0.26 && $percent<=0.5) {
            $hex_val = "BF0000";
          } else if ($percent>=0.51 && $percent<=0.75) {
            $hex_val = "800000";
          } else if ($percent>=0.76 && $percent<=1) {
            $hex_val = "400000";
          }
        }

        return $hex_val;
	}

	$colour;
	// variable to contain the colour percent to aid hex colour generation
	$colour_percent;

	function determine_colour ($value) {
		$colour_hex_values = "0x";
		$colour_code= "Colour code: ";

		if ($value >=0 && $value <= 256) {
			$colour_percent= round($value/256, 2);
			$colour="green";

			// the generated hex value for the colours
			$colour_hex_values .= colour_hex_val_gen($colour, $colour_percent);


		//   echo "$colour <br />\n";
		//   echo "$colour_code $colour_hex_values <br />\n";
		} else if ($value >= 257 && $value <= 512) {
			$colour_percent= round((512-$value)/256, 2);
			$colour="yellow";

			// the generated hex value for the colours
			$colour_hex_values .= colour_hex_val_gen($colour, $colour_percent);

		//   echo "$colour <br />\n";
		//   echo "$colour_code $colour_hex_values <br />\n";
		} else if ($value >= 513 && $value <= 768) {
			$colour_percent = round((768-$value)/256, 2);
			$colour="orange";

			// the generated hex value for the colours
			$colour_hex_values .= colour_hex_val_gen($colour, $colour_percent);

		//   echo "$colour <br />\n";
		//   echo "$colour_code $colour_hex_values <br />\n";
		} else if ($value >= 769 && $value <= 1025) {
			$colour_percent = round((1025 - $value)/256, 2);
			$colour="red";

			// the generated hex value for the colours
			$colour_hex_values .= colour_hex_val_gen($colour, $colour_percent);

		//   echo "$colour <br />\n";
		//   echo "$colour_code $colour_hex_values <br />\n";
		}
		return $colour_hex_values;

	}

	$hexvals1 = array();
	$hexvals2 = array();
	$hexvals3 = array();
	$hexvals4 = array();
	$times = array();
	
	foreach ($row as $result){
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
		<link rel="stylesheet" href="css/main.css">
		</head>

	<body>
		<h1 id="time">TIME</h1>

		<script type="module">

            import { Scene, WebGLRenderer, PerspectiveCamera, CylinderGeometry,  MeshPhongMaterial, Mesh, MeshBasicMaterial, 
					MeshNormalMaterial, MeshLambertMaterial, PointLight, Color, HemisphereLight, HemisphereLightHelper, DirectionalLight, DirectionalLightHelper } from '/AC41004-Team3/js/threejs/build/three.module.js';
            import { MTLLoader } from '/AC41004-Team3/js/threejs/examples/jsm/loaders/MTLLoader.js';
            import { OBJLoader } from '/AC41004-Team3/js/threejs/examples/jsm/loaders/OBJLoader.js';
			import { OrbitControls } from '/AC41004-Team3/js/threejs/examples/jsm/controls/OrbitControls.js';

			let camera, controls, scene, renderer;

			init();
			//render(); // remove when using next line for animation loop (requestAnimationFrame)
			animate();

			function init() {

				scene = new Scene();
				scene.background = new Color( 0x777777 );

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

				loadObject("body1_right_quad", "shapes/body1_right_quad/body1_right_quad.obj", 0xB66B3E);
				loadObject("body1_left_quad", "shapes/body1_left_quad/body1_left_quad.obj", 0xB66B3E);
				loadObject("body1_right_hamstring", "shapes/body1_right_hamstring/body1_right_hamstring.obj", 0xB66B3E);
				loadObject("body1_left_hamstring", "shapes/body1_left_hamstring/body1_left_hamstring.obj", 0xB66B3E);
                loadObject("body1", "shapes/body1/body1.obj", 0xB66B3E);
				loadObjectWithMaterial("current_text", "shapes/current_text/current_text.obj", "shapes/current_text/current_text.mtl");

				loadObject("body2_right_quad", "shapes/body2_right_quad/body2_right_quad.obj", 0xB66B3E);
				loadObject("body2_left_quad", "shapes/body2_left_quad/body2_left_quad.obj", 0xB66B3E);
				loadObject("body2_right_hamstring", "shapes/body2_right_hamstring/body2_right_hamstring.obj", 0xB66B3E);
				loadObject("body2_left_hamstring", "shapes/body2_left_hamstring/body2_left_hamstring.obj", 0xB66B3E);
                loadObject("body2", "shapes/body2/body2.obj", 0xB66B3E);
				loadObjectWithMaterial("previous_text", "shapes/previous_text/previous_text.obj", "shapes/previous_text/previous_text.mtl");
                
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
					changeObjectColour( "body2_left_hamstring", parseInt(hex_array1[i], 16));
					changeObjectColour( "body2_right_hamstring", parseInt(hex_array2[i], 16));
					changeObjectColour( "body2_left_quad", parseInt(hex_array3[i], 16))
					changeObjectColour( "body2_right_quad", parseInt(hex_array4[i], 16));
					document.getElementById("time").innerHTML = time_array[i];
				}, i*500)
			}
			
			const hex_array1 = <?php echo json_encode($hexvals1); ?>;
			const hex_array2 = <?php echo json_encode($hexvals2); ?>;
			const hex_array3 = <?php echo json_encode($hexvals3); ?>;
			const hex_array4 = <?php echo json_encode($hexvals4); ?>;

			var shortest_array = function(){
				const length1 = hex_array1.length;
				const length2 = hex_array2.length;
				const length3 = hex_array3.length;
				const length4 = hex_array4.length;

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
				} 
			}

			const time_array = <?php echo json_encode($times); ?>;

			for(var i=0; i<shortest_array().length; i++){
				do_timeout(i);
			}

		</script>

	</body>
</html>