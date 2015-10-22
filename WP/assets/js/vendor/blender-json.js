var jsonToThree = function( container, jsonLocation) {
  var camera, controls, scene, renderer;
  var mesh;
  var helper;
  var mouseX = 0, mouseY = 0;
  var clock = new THREE.Clock();
  var windowHalfX = window.innerWidth / 2;
  var windowHalfY = window.innerHeight / 2;
  init( container);
  animate( container);
  function init( container) {
    camera = new THREE.PerspectiveCamera( 60, window.innerWidth / window.innerHeight, 1, 100000 );
    camera.position.set( 0, 200, 300 );
    scene = new THREE.Scene();
    // ambient light
    ambLight = new THREE.AmbientLight( 0x444444 );
    scene.add( ambLight );
    // directional - KEY LIGHT
    keyLight = new THREE.DirectionalLight( 0xdddddd, .7 );
    keyLight.position.set( -80, 60, 80 );
    scene.add( keyLight );

    fillLight = new THREE.DirectionalLight( 0xdddddd, .3 );
    fillLight.position.set( 80, 40, 40 );
    scene.add( fillLight );

    rimLight = new THREE.DirectionalLight( 0xdddddd, .6 );
    rimLight.position.set( -20, 80, -80 );
    scene.add( rimLight );


     var material = new THREE.MeshBasicMaterial({ color: 0x000, transparent: true, opacity: 0.8 });
    // var material = new THREE.MeshBasicMaterial({ color: 0x333333, wireframe: true });
    // var material = new THREE.MeshPhongMaterial({ color: 0xcccccc, ambient: 0x444444, side: THREE.DoubleSide });
    var loader = new THREE.JSONLoader();


    loader.load( jsonLocation, function( geometry, materials ) {
      mesh = new THREE.Mesh( geometry, material );
      mesh.scale.set(4,4,4);
      mesh.position.y = -30;
      scene.add( mesh );
    });

    renderer = new THREE.WebGLRenderer( { antialias: true, alpha: true } );
    renderer.setClearColor( 0x000000, 0 ); // the default
    renderer.setSize( container.offsetWidth, container.offsetWidth * 0.66);
    container.appendChild( renderer.domElement );
    controls = new THREE.OrbitControls( camera, renderer.domElement );
    controls.noKeys = true;

    window.addEventListener( 'resize', onWindowResize, false );
  }
  function setupGui() {
    // dat.GUI debugging -----------------------------------------
    var gui = new dat.GUI();
    var f1 = gui.addFolder('meshOuter rotation');
    f1.add(mesh.position, 'x', 0, 2*Math.PI);
    f1.add(mesh.position, 'y', 0, 2*Math.PI);
    f1.add(mesh.position, 'z', 0, 2*Math.PI);
    f1.open();
  }
  function onWindowResize() {
    windowHalfX = window.innerWidth / 2;
    windowHalfY = window.innerHeight / 2;
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize( container.offsetWidth, container.offsetWidth * 0.66);
  }
  function onDocumentMouseMove( event ) {
    mouseX = ( event.clientX - windowHalfX );
    mouseY = ( event.clientY - windowHalfY );
    // document.getElementById('mouse').innerText = mouseX + ', ' + mouseY;
  }
  //
  var paused = false;
  function animate( container) {
    requestAnimationFrame( animate );
    if (!paused) {
      render();
      //controls.update();
    }
  }
  function render() {
    var delta = clock.getDelta();
    if (mesh) {
      mesh.rotation.y -= 0.5 * delta;
    }
    renderer.render( scene, camera );
  }
  var step = 1;
  function applyStep(step) {
    // console.log(step);
    if      (step == 1) {
    }
    else if (step == 2) {
    }
    else if (step == 3) {
    }
  }
  onmessage = function(e) {
    // paused = (e.data == 'pause' || e.data == 'slide:stop');
    step = e.data.director.args[0];
    applyStep( step );
  };
  function forward() {
    step++;
    if (step > 3) { step = 3; return; }
    applyStep(step);
  }
  function back() {
    step--;
    if (step <= 0) { step = 1; return; }
    applyStep(step);
  }
  // Controls for stand-alone
  window.addEventListener('keydown', function (e) {
    if (e.keyCode == 38 || e.keyCode == 37) back();
    if (e.keyCode == 40 || e.keyCode == 39) forward();
  });

}
