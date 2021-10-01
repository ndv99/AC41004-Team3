<?php
    session_start();
    require('db_connect.php');

	// if (!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) {

	// 	header("location: index.php");
	// 	exit;
	// }

	$query = "SELECT * FROM `sensor_data` WHERE `user_id` = ".$_SESSION["UserID"]." and `sensor_no` = 1;";
	$stmt = $pdo->prepare($query);
	$stmt->execute();
	$row = $stmt->fetch();
	echo var_dump($row) . "<br>";
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

		<script type="module">

			// import * as THREE from '/js/threejs/build/three.module.js';
            import { Scene, WebGLRenderer, PerspectiveCamera, CylinderGeometry,  MeshPhongMaterial, Mesh, MeshBasicMaterial, 
					MeshNormalMaterial, MeshLambertMaterial, PointLight, Color } from '/AC41004-Team3/js/threejs/build/three.module.js';
            import { MTLLoader } from '/AC41004-Team3/js/threejs/examples/jsm/loaders/MTLLoader.js';
            import { OBJLoader } from '/AC41004-Team3/js/threejs/examples/jsm/loaders/OBJLoader.js';
			import { OrbitControls } from '/AC41004-Team3/js/threejs/examples/jsm/controls/OrbitControls.js';

			let camera, controls, scene, renderer;

			var phpvar = '<?php echo $var ;?>'
			alert(phpvar);

			init();
			//render(); // remove when using next line for animation loop (requestAnimationFrame)
			animate();

            var ourObj2;
            var ourObj3;

			function init() {

				scene = new Scene();
				scene.background = new Color( 0xcccccc );
				// scene.fog = new THREE.FogExp2( 0xcccccc, 0.002 );

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
				controls.listenToKeyEvents( window ); // optional

				//controls.addEventListener( 'change', render ); // call this only in static scenes (i.e., if there is no animation loop)

				controls.enableDamping = true; // an animation loop is required when either damping or auto-rotation are enabled
				controls.dampingFactor = 0.05;

				controls.screenSpacePanning = false;

				controls.minDistance = 300;
				controls.maxDistance = 500;

				controls.maxPolarAngle = Math.PI / 2;

				// very important pls don't delete xoxoxo
				controls.enablePan = false;

				// spikes

				const geometry = new CylinderGeometry( 0, 10, 30, 4, 1 );
				const material = new MeshPhongMaterial( { color: 0xffffff, flatShading: true } );

				for ( let i = 0; i < 500; i ++ ) {

					const mesh = new Mesh( geometry, material );
					mesh.position.x = Math.random() * 1600 - 800;
					mesh.position.y = 0;
					mesh.position.z = Math.random() * 1600 - 800;
					mesh.updateMatrix();
					mesh.matrixAutoUpdate = false;
					scene.add( mesh );

				}

				// Our own code to load in our models
                
                // Create a material
				// var mtlLoader = new MTLLoader();
				// mtlLoader.load('shapes/cylinder_green.mtl', function (materials) {

				// 	materials.preload();

				// 	// Load the object
				// 	var objLoader = new OBJLoader();
				// 	objLoader.setMaterials(materials);
				// 	objLoader.load('shapes/cylinder_green.obj', function (object) {
				// 		scene.add(object);
				// 		ourObj = object;
				// 		object.position.z = 0;
				// 		object.rotation.x = 0;

				// 	});
				// });
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
                            obj.scale.set(0.5, 0.5, 0.5);
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

                loadObject("ourObj", "shapes/cylinder_green.obj", 0x00FF00);
                loadObject("ourObj2", "shapes/polyhedron_red.obj", 0xFF0000);
                loadObject("ourObj3", "shapes/tube_blue.obj", 0x0000FF);

                // ourObj = scene.getObjectByName( "ourObj" );
                
				// lights
				let light, light2, light3, light4;
                light = new PointLight(0xc4c4c4,1);
                light.position.set(0,300,500);
                scene.add(light);
                light2 = new PointLight(0xc4c4c4,1);
                light2.position.set(500,100,0);
                scene.add(light2);
                light3 = new PointLight(0xc4c4c4,1);
                light3.position.set(0,100,-500);
                scene.add(light3);
                light4 = new PointLight(0xc4c4c4,1);
                light4.position.set(-500,300,500);
                scene.add(light4);

				window.addEventListener( 'resize', onWindowResize );

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
                var temp_material = new MeshLambertMaterial( { color: objColor, transparent : true, opacity : 1 } );
                var obj = scene.getObjectByName( objName );
                obj.traverse( function( child ) {
                    if ( child instanceof Mesh ) {
                        child.material = temp_material;
                    }
                } );
            }

            setTimeout(function() {
                changeObjectColour( "ourObj", 0xFF0000);
            }, 3000);

		</script>

	</body>
</html>