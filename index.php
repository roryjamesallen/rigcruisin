<html>
    <head>
    </head>

    <style>
     html {
	 font-size: 20px;
     }
     .coverAll {
	 position: absolute;
	 top: 0;
	 left: 0;
	 width: 100vw;
	 height: 100vh;
	 justify-content: center;
	 align-items: center;
	 background: white;
	 display: none;
     }
     .warning {
	 color: red;
	 cursor: pointer;
     }
     #gearbox {
	 display: flex;
	 gap: 0.5rem;
     }
     #gearbox > span {
	 width: 1rem;
	 height: 1rem;
	 display: flex;
	 justify-content: center;
	 align-items: center;
	 border: 1px solid black;
	 border-radius: 0.5rem;
	 aspect-ratio: 1 / 1;
     }
     #gearbox > .gear-active {
	 cursor: pointer;
	 border-color: red;
	 border-width: 2px;
     }
    </style>
    
    <body>
	<span id="game-state" class="js"></span>
	<br><span id="tape-deck" class="warning js"></span>
	<br><span id="engine-light" class="warning js"></span>
	<div id="engine-bay" class="coverAll js">ENGINE BAY<br>> FIX <</div>
	<div id="game-over" class="coverAll js">GAME OVER</div>
	<div id="gearbox"><span>R</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span></div>
    </body>
</html>

<script>
 // Game states: S: Stopped, D: Driving, I: Cabin interruption ongoing, E: Repairing engine

 const js_elements = document.getElementsByClassName('js'); // Get every element marked with the JS class
 for (let i=0; i<js_elements.length; ++i){ // Loop through them all
     const element = js_elements[i]; // Get the actual element
     const var_name = element.id.replace('-','_'); // Create the JS variable name by swapping - for _
     window[var_name] = element; // Create the global JS variable for the element
 }
 
 const game = {
     // Game initial settings
     STARTUP_S: 3,
     INTERRUPT_S: 0.5,
     INTERRUPT_CHANCE: 0.8,
     INTERRUPTS: {
	 bad_song: {
	     active: false,
	     text: "This song STINKS",
	     init: function(){
		 game.INTERRUPTS.bad_song.active = true;
		 countdown = game.addTimeout(game.endGame, 3000, "I'm too angry to drive now. This road trip is RUINED!!");
		 tape_deck.innerText = 'err click me to change the song..';
		 tape_deck.addEventListener('click', function(){
		     clearTimeout(countdown);
		     tape_deck.innerText = '';
		     game.INTERRUPTS.bad_song.active = false;
		 });
	     }
	 },
	 oil_leak: {
	     active: false,
	     text: "There's something oily wrong with the engine...",
	     init: function(){
		 game.INTERRUPTS.oil_leak.active = true;
		 //countdown = setTimeout(game.endGame, 3000, "All the oil leaked out - She's a goner");
		 countdown = game.addTimeout(game.endGame, 3000, "All the oil leaked out - She's a goner");
		     engine_light.innerText = 'ENGINE PROBLEM';
		     engine_light.addEventListener('click', function(){
		     engine_bay.style.display = 'flex';
		     engine_bay.addEventListener('click', function(){
			 clearTimeout(countdown);
			 engine_light.innerText = '';
			 engine_bay.style.display = 'none';
			 game.INTERRUPTS.oil_leak.active = false;
		     });
		 });
	     }
	 },
	 change_gears: {
	     active: false,
	     text: "I need a smoke, shift the gears for me",
	     init: function(){
		 game.INTERRUPTS.change_gears.active = true;
		 const gear_index = Math.floor(Math.random() * 6);
		 const gear = gearbox.children[gear_index];
		 gear.classList.add('gear-active');
		 countdown = game.addTimeout(game.endGame, 3000, "You mashed it! The gearbox is fucked!!");
		 gearbox.style.display = 'flex';
		 gear.addEventListener('click', function changeGear(){
		     gear.classList.remove('gear-active');
		     clearTimeout(countdown);
		     game.INTERRUPTS.change_gears.active = false;
		     gear.removeEventListener('click', changeGear);
		 });
	     }
	 }
     },

     // Live game variables
     current_tick: 0,
     state: 'S',
     timeouts: [],

     addTimeout(func, delay, text){
	 timeout = setTimeout(func, delay, text);
	 game.timeouts.push(timeout);
	 return timeout;
     },
     clearTimeouts(){
	 for (let i=0; i<game.timeouts.length; ++i){
	     clearTimeout(game.timeouts[i]);
	 }
     },
     startAnimation: function(secs){
	 for (let count=secs; count>0; --count){
	     setTimeout(() => { game_state.innerText = count; }, (secs-count)*1000);
	 }
     },
     potentialInterruption: function(){ // Random chance to start/queue a new interruption
	 if (game.INTERRUPT_CHANCE > Math.random()){
	     const interrupt_keys = Object.keys(game.INTERRUPTS);
	     const new_interrupt = game.INTERRUPTS[interrupt_keys[interrupt_keys.length * Math.random() << 0]]; // Pick a random interrupt to happen
	     if (!new_interrupt.active){ // Can't init an interrupt that's already happening
		 new_interrupt.init(); // Start the interrupt's init (change graphics if relevant)
		 console.log('initialised ' + new_interrupt.text);
	     }
	 }
     },
     tick: function() {
	 game.current_tick += 1;
	 game_state.innerText = game.current_tick;
	 game.potentialInterruption();
     },
     begin: function(){
	 this.startAnimation(this.STARTUP_S);
	 setTimeout(() => {
	     game_state.innerText = 'GAME ON'; this.state = 'D';
	     this.interval = setInterval(this.tick, (this.INTERRUPT_S*1000));
	 }, (this.STARTUP_S*1000));
	 
     },
     endGame: function(text=null){
	 if (text != null){
	     game_over.innerText = text;
	 }
	 game_over.style.display = 'flex';
	 clearInterval(game.interval);
	 game.clearTimeouts();
     }
 };

 game.begin();
</script>
